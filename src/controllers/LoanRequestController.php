<?php
class LoanRequestController {
    private $db;
    private $loanRequestModel;
    private $bookModel;

    public function __construct($database) {
        $this->db = $database->connect();
        $this->loanRequestModel = new LoanRequest($this->db);
        $this->bookModel = new Book($this->db);
    }

    // User request peminjaman
    public function requestLoan() {
        requireLogin();
        
        $book_id = $_GET['book_id'] ?? null;
        
        if (!$book_id) {
            setFlashMessage('danger', 'ID Buku tidak valid!');
            redirect('index.php?page=catalog');
        }
        
        // Get book info
        $book = $this->bookModel->getById($book_id);
        
        if (!$book) {
            setFlashMessage('danger', 'Buku tidak ditemukan!');
            redirect('index.php?page=catalog');
        }
        
        // Check if stock available
        if ($book['stok_tersedia'] <= 0) {
            setFlashMessage('danger', 'Maaf, stok buku sedang tidak tersedia!');
            redirect('index.php?page=catalog');
        }
        
        $user_id = $_SESSION['user_id'];
        
        // Check active loans
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM loans WHERE user_id = :user_id AND status = 'dipinjam'");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $active = $stmt->fetch();
        
        // Get max loan setting
        $stmt = $this->db->query("SELECT nilai FROM settings WHERE nama_setting = 'max_buku_pinjam' LIMIT 1");
        $setting = $stmt->fetch();
        $max = $setting ? $setting['nilai'] : 3; // Default 3 buku
        
        if ($active['total'] >= $max) {
            setFlashMessage('danger', "Anda sudah mencapai batas maksimal peminjaman ($max buku)!");
            redirect('index.php?page=catalog');
        }
        
        // Check pending requests
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM loan_requests 
                                     WHERE user_id = :user_id AND status = 'pending'");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $pending = $stmt->fetch();
        
        if ($pending['total'] >= 3) {
            setFlashMessage('danger', 'Anda masih memiliki 3 request yang belum diproses!');
            redirect('index.php?page=my-requests');
        }
        
        // Check if already requested this book
        if ($this->loanRequestModel->checkDuplicateRequest($user_id, $book_id)) {
            setFlashMessage('warning', 'Anda sudah pernah request buku ini. Tunggu persetujuan admin!');
            redirect('index.php?page=my-requests');
        }
        
        // Process form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $keterangan = cleanInput($_POST['keterangan'] ?? '');
            
            $data = [
                'user_id' => $user_id,
                'book_id' => $book_id,
                'keterangan' => $keterangan
            ];
            
            if ($this->loanRequestModel->create($data)) {
                setFlashMessage('success', 'Request peminjaman berhasil dikirim! Tunggu persetujuan admin.');
                redirect('index.php?page=my-requests');
            } else {
                setFlashMessage('danger', 'Gagal mengirim request! Silakan coba lagi.');
            }
        }
        
        require_once VIEW_PATH . 'user/request-form.php';
    }
    
    // User lihat request mereka
    public function myRequests() {
        requireLogin();
        
        $user_id = $_SESSION['user_id'];
        $requests = $this->loanRequestModel->getByUserId($user_id);
        
        require_once VIEW_PATH . 'user/my-requests.php';
    }
    
    // User batalkan request
    public function cancelRequest() {
        requireLogin();
        
        $request_id = $_GET['id'] ?? null;
        $user_id = $_SESSION['user_id'];
        
        if (!$request_id) {
            setFlashMessage('danger', 'Request tidak valid!');
            redirect('index.php?page=my-requests');
        }
        
        if ($this->loanRequestModel->delete($request_id, $user_id)) {
            setFlashMessage('success', 'Request berhasil dibatalkan!');
        } else {
            setFlashMessage('danger', 'Gagal membatalkan request! Request mungkin sudah diproses atau tidak ditemukan.');
        }
        
        redirect('index.php?page=my-requests');
    }

    // Admin lihat semua request
    public function adminRequests() {
        requireAdmin();
        
        $status = $_GET['status'] ?? null;
        $requests = $this->loanRequestModel->getAll($status);
        
        require_once VIEW_PATH . 'admin/loan-requests.php';
    }

    // Admin approve request (otomatis jadi peminjaman)
    public function approveRequest() {
        requireAdmin();
        
        $request_id = $_GET['id'] ?? null;
        
        if (!$request_id) {
            setFlashMessage('danger', 'Request ID tidak valid!');
            redirect('index.php?page=admin-requests');
        }
        
        // Get request details
        $request = $this->loanRequestModel->getById($request_id);
        
        if (!$request) {
            setFlashMessage('danger', 'Request tidak ditemukan!');
            redirect('index.php?page=admin-requests');
        }
        
        if ($request['status'] != 'pending') {
            setFlashMessage('warning', 'Request ini sudah diproses sebelumnya!');
            redirect('index.php?page=admin-requests');
        }
        
        // Check stock availability
        if ($request['stok_tersedia'] <= 0) {
            setFlashMessage('danger', 'Stok buku tidak tersedia! Tidak dapat menyetujui request.');
            redirect('index.php?page=admin-requests');
        }
        
        try {
            $this->db->beginTransaction();
            
            // 1. Update request status to approved
            $admin_id = $_SESSION['user_id'];
            if (!$this->loanRequestModel->approve($request_id, $admin_id)) {
                throw new Exception('Gagal mengupdate status request');
            }
            
            // 2. Get loan period from settings
            $stmt = $this->db->query("SELECT nilai FROM settings WHERE nama_setting = 'lama_pinjam' LIMIT 1");
            $setting = $stmt->fetch();
            $lama_pinjam = $setting ? (int)$setting['nilai'] : 7; // Default 7 hari
            
            // 3. Calculate dates
            $tanggal_pinjam = date('Y-m-d');
            $tanggal_kembali = date('Y-m-d', strtotime("+$lama_pinjam days"));
            
            // 4. Create loan record
            $keterangan = "Disetujui dari request #" . $request_id;
            
            $query = "INSERT INTO loans 
                      (user_id, book_id, tanggal_pinjam, tanggal_kembali, status, keterangan)
                      VALUES 
                      (:user_id, :book_id, :tanggal_pinjam, :tanggal_kembali, 'dipinjam', :keterangan)";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $request['user_id']);
            $stmt->bindParam(':book_id', $request['book_id']);
            $stmt->bindParam(':tanggal_pinjam', $tanggal_pinjam);
            $stmt->bindParam(':tanggal_kembali', $tanggal_kembali);
            $stmt->bindParam(':keterangan', $keterangan);
            
            if (!$stmt->execute()) {
                throw new Exception('Gagal membuat record peminjaman');
            }
            
            // 5. Decrease book stock
            if (!$this->bookModel->decrementStock($request['book_id'], 1)) {
                throw new Exception('Gagal mengupdate stok buku');
            }
            
            $this->db->commit();
            
            setFlashMessage('success', 'Request disetujui! Peminjaman berhasil dibuat. Anggota dapat mengambil buku di perpustakaan.');
            redirect('index.php?page=admin-loans');
            
        } catch (Exception $e) {
            $this->db->rollBack();
            setFlashMessage('danger', 'Gagal memproses request: ' . $e->getMessage());
            redirect('index.php?page=admin-requests');
        }
    }

    // Admin reject request
    public function rejectRequest() {
        requireAdmin();
        
        $request_id = $_GET['id'] ?? null;
        
        if (!$request_id) {
            setFlashMessage('danger', 'Request ID tidak valid!');
            redirect('index.php?page=admin-requests');
        }
        
        // Get request details
        $request = $this->loanRequestModel->getById($request_id);
        
        if (!$request) {
            setFlashMessage('danger', 'Request tidak ditemukan!');
            redirect('index.php?page=admin-requests');
        }
        
        if ($request['status'] != 'pending') {
            setFlashMessage('warning', 'Request ini sudah diproses sebelumnya!');
            redirect('index.php?page=admin-requests');
        }
        
        // Process rejection form
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $response_note = cleanInput($_POST['response_note'] ?? '');
            
            if (empty($response_note)) {
                setFlashMessage('danger', 'Alasan penolakan harus diisi!');
                require_once VIEW_PATH . 'admin/reject-request.php';
                return;
            }
            
            $admin_id = $_SESSION['user_id'];
            
            if ($this->loanRequestModel->reject($request_id, $admin_id, $response_note)) {
                setFlashMessage('success', 'Request berhasil ditolak!');
            } else {
                setFlashMessage('danger', 'Gagal menolak request!');
            }
            
            redirect('index.php?page=admin-requests');
        }
        
        require_once VIEW_PATH . 'admin/reject-request.php';
    }
}
?>
