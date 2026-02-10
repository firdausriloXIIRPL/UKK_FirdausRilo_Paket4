<?php
class AdminController {
    private $db;
    private $userModel;
    private $bookModel;
    private $loanModel;

    public function __construct($database) {
        $this->db = $database->connect();
        $this->userModel = new User($this->db);
        $this->bookModel = new Book($this->db);
        $this->loanModel = new Loan($this->db);
    }

    public function dashboard() {
        requireAdmin();
        
        $totalBooks = $this->bookModel->count();
        $totalMembers = $this->userModel->countByRole('anggota');
        $totalLoans = $this->loanModel->countByStatus('dipinjam');
        $totalOverdue = $this->loanModel->countOverdue();
        
        $recentLoans = $this->loanModel->getAll('dipinjam');
        
        require_once VIEW_PATH . 'admin/dashboard.php';
    }

    public function users() {
        requireAdmin();
        
        $users = $this->userModel->getAll('anggota');
        
        require_once VIEW_PATH . 'admin/users.php';
    }

    public function userForm() {
        requireAdmin();
        
        $user = null;
        $isEdit = false;
        
        if (isset($_GET['id'])) {
            $isEdit = true;
            $user = $this->userModel->getById($_GET['id']);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => cleanInput($_POST['username']),
                'nama_lengkap' => cleanInput($_POST['nama_lengkap']),
                'email' => cleanInput($_POST['email']),
                'no_telepon' => cleanInput($_POST['no_telepon']),
                'alamat' => cleanInput($_POST['alamat']),
                'status' => cleanInput($_POST['status'] ?? 'aktif'),
                'role' => 'anggota'
            ];
            
            if (!empty($_POST['password'])) {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
            }
            
            if ($isEdit) {
                $this->userModel->update($_GET['id'], $data);
                setFlashMessage('success', 'Data anggota berhasil diupdate!');
            } else {
                $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $this->userModel->create($data);
                setFlashMessage('success', 'Anggota baru berhasil ditambahkan!');
            }
            
            redirect('index.php?page=admin-users');
        }
        
        require_once VIEW_PATH . 'admin/user-form.php';
    }

    public function deleteUser() {
        requireAdmin();
        
        if (isset($_GET['id'])) {
            $this->userModel->delete($_GET['id']);
            setFlashMessage('success', 'Data anggota berhasil dihapus!');
        }
        
        redirect('index.php?page=admin-users');
    }
}
?>
