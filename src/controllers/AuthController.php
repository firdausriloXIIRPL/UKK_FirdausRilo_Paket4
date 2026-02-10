<?php
class AuthController {
    private $db;
    private $userModel;

    public function __construct($database) {
        $this->db = $database->connect();
        $this->userModel = new User($this->db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = cleanInput($_POST['username']);
            $password = $_POST['password'];

            $user = $this->userModel->login($username, $password);

            if ($user) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    redirect('index.php?page=admin-dashboard');
                } else {
                    redirect('index.php?page=user-dashboard');
                }
            } else {
                setFlashMessage('danger', 'Username atau password salah!');
                redirect('index.php?page=login');
            }
        }

        require_once VIEW_PATH . 'auth/login.php';
    }

    public function logout() {
        session_destroy();
        redirect('index.php?page=login');
    }
}
?>
