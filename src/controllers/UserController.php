<?php
class UserController {
    private $db;
    private $bookModel;
    private $loanModel;

    public function __construct($database) {
        $this->db = $database->connect();
        $this->bookModel = new Book($this->db);
        $this->loanModel = new Loan($this->db);
    }

    public function dashboard() {
        requireLogin();
        
        $userId = $_SESSION['user_id'];
        $activeLoans = $this->loanModel->getAll('dipinjam', $userId);
        $loanHistory = $this->loanModel->getAll('dikembalikan', $userId);
        
        require_once VIEW_PATH . 'user/dashboard.php';
    }

    public function catalog() {
        requireLogin();
        
        $search = $_GET['search'] ?? null;
        $books = $this->bookModel->getAll($search);
        
        require_once VIEW_PATH . 'user/catalog.php';
    }

    public function myLoans() {
        requireLogin();
        
        $userId = $_SESSION['user_id'];
        $loans = $this->loanModel->getAll(null, $userId);
        
        require_once VIEW_PATH . 'user/my-loans.php';
    }
}
?>
