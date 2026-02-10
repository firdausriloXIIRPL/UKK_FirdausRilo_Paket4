<?php
class LoanController {
    private $db;
    private $loanModel;
    private $bookModel;
    private $userModel;

    public function __construct($database) {
        $this->db = $database->connect();
        $this->loanModel = new Loan($this->db);
        $this->bookModel = new Book($this->db);
        $this->userModel = new User($this->db);
    }

    public function index() {
        requireAdmin();
        
        // Update overdue status first
        $this->loanModel->updateOverdueStatus();
        
        $status = $_GET['status'] ?? null;
        $loans = $this->loanModel->getAll($status);
        
        require_once VIEW_PATH . 'admin/loans.php';
    }

    public function create() {
        requireAdmin();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = cleanInput($_POST['user_id']);
            $book_id = cleanInput($_POST['book_id']);
            $keterangan = cleanInput($_POST['keterangan'] ?? '');
            
            // Validate inputs
            if (empty($user_id) || empty($book_id)) {
                setFlashMessage('danger', 'Semua field harus diisi!');
                redirect('index.php?page=create-loan');
            }
            
            // Check book availability
            $book = $this->bookModel->getById($book_id);
            if (!$book) {
                setFlashMessage('danger', 'Buku tidak ditemukan!');
                redirect('index.php?page=create-loan');
            }
            
            if ($book['stok_tersedia'] <= 0) {
                setFlashMessage('danger', 'Stok buku tidak tersedia!');
                redirect('index.php?page=create-loan');
            }
            
            // Check user's active loans
            $activeLoans = $this->loanModel->countActiveByUser($user_id);
            
            // Get max loan limit from settings
            $stmt = $this->db->query("SELECT nilai FROM settings WHERE nama_setting = 'max_buku_pinjam' LIMIT 1");
            $setting = $stmt->fetch();
            $maxLoans = $setting ? (int)$setting['nilai'] : 3; // Default 3
            
            if ($activeLoans >= $maxLoans) {
                setFlashMessage('danger', "Anggota sudah mencapai batas maksimal peminjaman ($maxLoans buku)!");
                redirect('index.php?page=create-loan');
            }
            
            try {
                $this->db->beginTransaction();
                
                // Get loan period from settings
                $stmt = $this->db->query("SELECT nilai FROM settings WHERE nama_setting = 'lama_pinjam' LIMIT 1");
                $setting = $stmt->fetch();
                $lamaPinjam = $setting ? (int)$setting['nilai'] : 7; // Default 7 days
                
                // Calculate dates
                $tanggal_pinjam = date('Y-m-d');
                $tanggal_kembali = date('Y-m-d', strtotime("+$lamaPinjam days"));
                
                // Prepare loan data
                $data = [
                    'user_id' => $user_id,
                    'book_id' => $book_id,
                    'tanggal_pinjam' => $tanggal_pinjam,
                    'tanggal_kembali' => $tanggal_kembali,
                    'status' => 'dipinjam',
                    'keterangan' => $keterangan
                ];
                
                // Create loan
                if (!$this->loanModel->create($data)) {
                    throw new Exception('Gagal membuat peminjaman');
                }
                
                // Decrease book stock
                if (!$this->bookModel->decrementStock($book_id, 1)) {
                    throw new Exception('Gagal mengupdate stok buku');
                }
                
                $this->db->commit();
                
                setFlashMessage('success', 'Peminjaman berhasil ditambahkan!');
                redirect('index.php?page=admin-loans');
                
            } catch (Exception $e) {
                $this->db->rollBack();
                setFlashMessage('danger', 'Gagal membuat peminjaman: ' . $e->getMessage());
                redirect('index.php?page=create-loan');
            }
        }
        
        // Get data for form
        $users = $this->userModel->getByRole('user');
        $books = $this->bookModel->getAll();
        
        require_once VIEW_PATH . 'admin/create-loan.php';
    }

    public function returnBook() {
        requireAdmin();
        
        $loan_id = $_GET['id'] ?? null;
        
        if (!$loan_id) {
            setFlashMessage('danger', 'ID Peminjaman tidak valid!');
            redirect('index.php?page=admin-loans');
        }
        
        $loan = $this->loanModel->getById($loan_id);
        
        if (!$loan) {
            setFlashMessage('danger', 'Data peminjaman tidak ditemukan!');
            redirect('index.php?page=admin-loans');
        }
        
        if ($loan['status'] == 'dikembalikan') {
            setFlashMessage('warning', 'Buku sudah dikembalikan sebelumnya!');
            redirect('index.php?page=admin-loans');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->db->beginTransaction();
                
                // Calculate fine
                $denda = $this->loanModel->calculateDenda($loan_id);
                
                // Process return
                if (!$this->loanModel->returnBook($loan_id, $denda)) {
                    throw new Exception('Gagal memproses pengembalian');
                }
                
                // Increase book stock
                if (!$this->bookModel->incrementStock($loan['book_id'], 1)) {
                    throw new Exception('Gagal mengupdate stok buku');
                }
                
                $this->db->commit();
                
                $message = 'Buku berhasil dikembalikan!';
                if ($denda > 0) {
                    $message .= ' Denda: Rp ' . number_format($denda, 0, ',', '.');
                }
                
                setFlashMessage('success', $message);
                redirect('index.php?page=admin-loans');
                
            } catch (Exception $e) {
                $this->db->rollBack();
                setFlashMessage('danger', 'Gagal memproses pengembalian: ' . $e->getMessage());
                redirect('index.php?page=return-book&id=' . $loan_id);
            }
        }
        
        // Calculate fine for display
        $denda = $this->loanModel->calculateDenda($loan_id);
        
        require_once VIEW_PATH . 'admin/return-book.php';
    }

    public function history() {
        requireAdmin();
        
        $limit = $_GET['limit'] ?? null;
        $loans = $this->loanModel->getHistory($limit);
        
        require_once VIEW_PATH . 'admin/loan-history.php';
    }

    public function delete() {
        requireAdmin();
        
        $loan_id = $_GET['id'] ?? null;
        
        if (!$loan_id) {
            setFlashMessage('danger', 'ID Peminjaman tidak valid!');
            redirect('index.php?page=admin-loans');
        }
        
        $loan = $this->loanModel->getById($loan_id);
        
        if (!$loan) {
            setFlashMessage('danger', 'Data peminjaman tidak ditemukan!');
            redirect('index.php?page=admin-loans');
        }
        
        // Only allow delete if not returned yet (to restore stock)
        if ($loan['status'] != 'dikembalikan') {
            try {
                $this->db->beginTransaction();
                
                // Restore book stock first
                if (!$this->bookModel->incrementStock($loan['book_id'], 1)) {
                    throw new Exception('Gagal mengupdate stok buku');
                }
                
                // Delete loan record
                if (!$this->loanModel->delete($loan_id)) {
                    throw new Exception('Gagal menghapus data peminjaman');
                }
                
                $this->db->commit();
                
                setFlashMessage('success', 'Data peminjaman berhasil dihapus!');
                
            } catch (Exception $e) {
                $this->db->rollBack();
                setFlashMessage('danger', 'Gagal menghapus: ' . $e->getMessage());
            }
        } else {
            // For returned books, just delete the record
            if ($this->loanModel->delete($loan_id)) {
                setFlashMessage('success', 'Data peminjaman berhasil dihapus!');
            } else {
                setFlashMessage('danger', 'Gagal menghapus data peminjaman!');
            }
        }
        
        redirect('index.php?page=admin-loans');
    }
}
?>
