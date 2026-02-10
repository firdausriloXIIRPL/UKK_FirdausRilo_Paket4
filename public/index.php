<?php
// Include configurations
require_once '../config/config.php';
require_once '../config/database.php';

// Initialize database
$database = new Database();
$db = $database->connect();

// Get page parameter
$page = $_GET['page'] ?? 'login';

// Route handling
switch($page) {
    // ==================== AUTH ROUTES ====================
    case 'login':
        $controller = new AuthController($database);
        $controller->login();
        break;
        
    case 'logout':
        $controller = new AuthController($database);
        $controller->logout();
        break;
    
    // ==================== ADMIN ROUTES ====================
    
    // Dashboard
    case 'admin-dashboard':
        $controller = new AdminController($database);
        $controller->dashboard();
        break;
    
    // User Management
    case 'admin-users':
        $controller = new AdminController($database);
        $controller->users();
        break;
        
    case 'user-form':
        $controller = new AdminController($database);
        $controller->userForm();
        break;
        
    case 'delete-user':
        $controller = new AdminController($database);
        $controller->deleteUser();
        break;
    
    // Book Management
    case 'admin-books':
        $controller = new BookController($database);
        $controller->index();
        break;
        
    case 'book-form':
        $controller = new BookController($database);
        $controller->form();
        break;
        
    case 'delete-book':
        $controller = new BookController($database);
        $controller->delete();
        break;
    
    // Loan Management
    case 'admin-loans':
        $controller = new LoanController($database);
        $controller->index();
        break;
        
    case 'create-loan':
        $controller = new LoanController($database);
        $controller->create();
        break;
        
    case 'return-book':
        $controller = new LoanController($database);
        $controller->returnBook();
        break;
        
    case 'loan-history':
        $controller = new LoanController($database);
        $controller->history();
        break;
    
    // Loan Request Management (ADMIN)
    case 'admin-requests':
        $controller = new LoanRequestController($database);
        $controller->adminRequests();
        break;
        
    case 'approve-request':
        $controller = new LoanRequestController($database);
        $controller->approveRequest();
        break;
        
    case 'reject-request':
        $controller = new LoanRequestController($database);
        $controller->rejectRequest();
        break;
    
    // ==================== USER/MEMBER ROUTES ====================
    
    // Dashboard
    case 'user-dashboard':
        $controller = new UserController($database);
        $controller->dashboard();
        break;
    
    // Catalog
    case 'catalog':
        $controller = new UserController($database);
        $controller->catalog();
        break;
    
    // My Loans
    case 'my-loans':
        $controller = new UserController($database);
        $controller->myLoans();
        break;
    
    // Loan Request (USER)
    case 'request-loan':
        $controller = new LoanRequestController($database);
        $controller->requestLoan();
        break;
        
    case 'my-requests':
        $controller = new LoanRequestController($database);
        $controller->myRequests();
        break;
        
    case 'cancel-request':
        $controller = new LoanRequestController($database);
        $controller->cancelRequest();
        break;
    
    // ==================== DEFAULT ROUTE ====================
    default:
        if (isLoggedIn()) {
            if (isAdmin()) {
                redirect('index.php?page=admin-dashboard');
            } else {
                redirect('index.php?page=user-dashboard');
            }
        } else {
            redirect('index.php?page=login');
        }
        break;
}
?>
