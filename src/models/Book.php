<?php
class Book {
    private $conn;
    private $table = 'books';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($search = null) {
        $query = "SELECT b.*, 
                  c.nama_kategori,
                  a.nama_penulis,
                  p.nama_penerbit
                  FROM {$this->table} b
                  LEFT JOIN categories c ON b.category_id = c.category_id
                  LEFT JOIN authors a ON b.author_id = a.author_id
                  LEFT JOIN publishers p ON b.publisher_id = p.publisher_id";
        
        if ($search) {
            $query .= " WHERE b.judul LIKE :search OR b.kode_buku LIKE :search";
        }
        
        $query .= " ORDER BY b.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($search) {
            $searchParam = "%{$search}%";
            $stmt->bindParam(':search', $searchParam);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT b.*, 
                  c.nama_kategori,
                  a.nama_penulis,
                  p.nama_penerbit
                  FROM {$this->table} b
                  LEFT JOIN categories c ON b.category_id = c.category_id
                  LEFT JOIN authors a ON b.author_id = a.author_id
                  LEFT JOIN publishers p ON b.publisher_id = p.publisher_id
                  WHERE b.book_id = :id LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (kode_buku, judul, author_id, publisher_id, category_id, 
                   tahun_terbit, isbn, jumlah_halaman, stok_tersedia, stok_total, 
                   rak_lokasi, deskripsi, cover_image) 
                  VALUES 
                  (:kode_buku, :judul, :author_id, :publisher_id, :category_id,
                   :tahun_terbit, :isbn, :jumlah_halaman, :stok_tersedia, :stok_total,
                   :rak_lokasi, :deskripsi, :cover_image)";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind parameters explicitly
        $stmt->bindParam(':kode_buku', $data['kode_buku']);
        $stmt->bindParam(':judul', $data['judul']);
        $stmt->bindParam(':author_id', $data['author_id']);
        $stmt->bindParam(':publisher_id', $data['publisher_id']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':tahun_terbit', $data['tahun_terbit']);
        $stmt->bindParam(':isbn', $data['isbn']);
        $stmt->bindParam(':jumlah_halaman', $data['jumlah_halaman']);
        $stmt->bindParam(':stok_tersedia', $data['stok_tersedia']);
        $stmt->bindParam(':stok_total', $data['stok_total']);
        $stmt->bindParam(':rak_lokasi', $data['rak_lokasi']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        $stmt->bindParam(':cover_image', $data['cover_image']);
        
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET 
                  kode_buku = :kode_buku,
                  judul = :judul,
                  author_id = :author_id,
                  publisher_id = :publisher_id,
                  category_id = :category_id,
                  tahun_terbit = :tahun_terbit,
                  isbn = :isbn,
                  jumlah_halaman = :jumlah_halaman,
                  stok_tersedia = :stok_tersedia,
                  stok_total = :stok_total,
                  rak_lokasi = :rak_lokasi,
                  deskripsi = :deskripsi";
        
        // Only add cover_image if provided
        if (!empty($data['cover_image'])) {
            $query .= ", cover_image = :cover_image";
        }
        
        $query .= " WHERE book_id = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind all parameters explicitly
        $stmt->bindParam(':kode_buku', $data['kode_buku']);
        $stmt->bindParam(':judul', $data['judul']);
        $stmt->bindParam(':author_id', $data['author_id']);
        $stmt->bindParam(':publisher_id', $data['publisher_id']);
        $stmt->bindParam(':category_id', $data['category_id']);
        $stmt->bindParam(':tahun_terbit', $data['tahun_terbit']);
        $stmt->bindParam(':isbn', $data['isbn']);
        $stmt->bindParam(':jumlah_halaman', $data['jumlah_halaman']);
        $stmt->bindParam(':stok_tersedia', $data['stok_tersedia']);
        $stmt->bindParam(':stok_total', $data['stok_total']);
        $stmt->bindParam(':rak_lokasi', $data['rak_lokasi']);
        $stmt->bindParam(':deskripsi', $data['deskripsi']);
        
        // Only bind cover_image if provided
        if (!empty($data['cover_image'])) {
            $stmt->bindParam(':cover_image', $data['cover_image']);
        }
        
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function delete($id) {
        // Get book data to delete cover image file
        $book = $this->getById($id);
        
        // Delete cover image file if exists
        if ($book && !empty($book['cover_image'])) {
            $coverPath = UPLOAD_PATH . 'covers/' . $book['cover_image'];
            if (file_exists($coverPath)) {
                @unlink($coverPath);
            }
        }
        
        $query = "DELETE FROM {$this->table} WHERE book_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function updateStock($id, $stok_tersedia) {
        $query = "UPDATE {$this->table} 
                  SET stok_tersedia = :stok 
                  WHERE book_id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':stok', $stok_tersedia);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function incrementStock($id, $amount = 1) {
        $query = "UPDATE {$this->table} 
                  SET stok_tersedia = stok_tersedia + :amount 
                  WHERE book_id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function decrementStock($id, $amount = 1) {
        $query = "UPDATE {$this->table} 
                  SET stok_tersedia = stok_tersedia - :amount 
                  WHERE book_id = :id AND stok_tersedia >= :amount";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function count() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function countAvailable() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE stok_tersedia > 0";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }

    public function getTotalStock() {
        $query = "SELECT SUM(stok_total) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function getAvailableStock() {
        $query = "SELECT SUM(stok_tersedia) as total FROM {$this->table}";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }

    public function checkStock($id) {
        $query = "SELECT stok_tersedia FROM {$this->table} WHERE book_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['stok_tersedia'] ?? 0;
    }
}
?>
