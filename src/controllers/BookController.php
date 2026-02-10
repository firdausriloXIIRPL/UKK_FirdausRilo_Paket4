<?php
class BookController {
    private $db;
    private $bookModel;
    private $categoryModel;

    public function __construct($database) {
        $this->db = $database->connect();
        $this->bookModel = new Book($this->db);
        $this->categoryModel = new Category($this->db);
    }

    public function index() {
        requireAdmin();
        
        $search = $_GET['search'] ?? null;
        $books = $this->bookModel->getAll($search);
        
        require_once VIEW_PATH . 'admin/books.php';
    }

    public function form() {
        requireAdmin();
        
        $book = null;
        $isEdit = false;
        $categories = $this->categoryModel->getAll();
        
        // Get authors and publishers
        $stmt = $this->db->query("SELECT * FROM authors ORDER BY nama_penulis ASC");
        $authors = $stmt->fetchAll();
        
        $stmt = $this->db->query("SELECT * FROM publishers ORDER BY nama_penerbit ASC");
        $publishers = $stmt->fetchAll();
        
        if (isset($_GET['id'])) {
            $isEdit = true;
            $book = $this->bookModel->getById($_GET['id']);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'kode_buku' => cleanInput($_POST['kode_buku']),
                'judul' => cleanInput($_POST['judul']),
                'author_id' => $_POST['author_id'] ?: null,
                'publisher_id' => $_POST['publisher_id'] ?: null,
                'category_id' => $_POST['category_id'] ?: null,
                'tahun_terbit' => $_POST['tahun_terbit'] ?: null,
                'isbn' => cleanInput($_POST['isbn']),
                'jumlah_halaman' => $_POST['jumlah_halaman'] ?: null,
                'stok_tersedia' => $_POST['stok_tersedia'],
                'stok_total' => $_POST['stok_total'],
                'rak_lokasi' => cleanInput($_POST['rak_lokasi']),
                'deskripsi' => cleanInput($_POST['deskripsi']),
                'cover_image' => ''
            ];
            
            // Handle file upload
            if (!empty($_FILES['cover_image']['name'])) {
                $upload = uploadImage($_FILES['cover_image'], 'covers');
                if ($upload['success']) {
                    $data['cover_image'] = $upload['filename'];
                }
            }
            
            if ($isEdit) {
                if (empty($data['cover_image'])) {
                    unset($data['cover_image']);
                }
                $this->bookModel->update($_GET['id'], $data);
                setFlashMessage('success', 'Data buku berhasil diupdate!');
            } else {
                $this->bookModel->create($data);
                setFlashMessage('success', 'Buku baru berhasil ditambahkan!');
            }
            
            redirect('index.php?page=admin-books');
        }
        
        require_once VIEW_PATH . 'admin/book-form.php';
    }

    public function delete() {
        requireAdmin();
        
        if (isset($_GET['id'])) {
            $this->bookModel->delete($_GET['id']);
            setFlashMessage('success', 'Data buku berhasil dihapus!');
        }
        
        redirect('index.php?page=admin-books');
    }
}
?>
