<?php
class Loan {
    private $conn;
    private $table = 'loans';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll($status = null, $user_id = null) {
        $query = "SELECT l.*, 
                         u.nama_lengkap as nama_anggota,
                         u.email,
                         u.no_telepon,
                         b.judul as judul_buku,
                         b.kode_buku,
                         b.cover_image,
                         a.nama_penulis
                  FROM {$this->table} l
                  JOIN users u ON l.user_id = u.user_id
                  JOIN books b ON l.book_id = b.book_id
                  LEFT JOIN authors a ON b.author_id = a.author_id";
        
        $conditions = [];
        
        if ($status) {
            $conditions[] = "l.status = :status";
        }
        
        if ($user_id) {
            $conditions[] = "l.user_id = :user_id";
        }
        
        if (!empty($conditions)) {
            $query .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $query .= " ORDER BY l.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT l.*, 
                         u.nama_lengkap as nama_anggota, 
                         u.no_telepon, 
                         u.email,
                         u.alamat,
                         b.judul as judul_buku, 
                         b.kode_buku,
                         b.cover_image,
                         a.nama_penulis,
                         c.nama_kategori,
                         p.nama_penerbit
                  FROM {$this->table} l
                  JOIN users u ON l.user_id = u.user_id
                  JOIN books b ON l.book_id = b.book_id
                  LEFT JOIN authors a ON b.author_id = a.author_id
                  LEFT JOIN categories c ON b.category_id = c.category_id
                  LEFT JOIN publishers p ON b.publisher_id = p.publisher_id
                  WHERE l.loan_id = :id 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (user_id, book_id, tanggal_pinjam, tanggal_kembali, status, keterangan) 
                  VALUES 
                  (:user_id, :book_id, :tanggal_pinjam, :tanggal_kembali, :status, :keterangan)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':book_id', $data['book_id']);
        $stmt->bindParam(':tanggal_pinjam', $data['tanggal_pinjam']);
        $stmt->bindParam(':tanggal_kembali', $data['tanggal_kembali']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':keterangan', $data['keterangan']);
        
        return $stmt->execute();
    }

    public function returnBook($id, $denda = 0) {
        $query = "UPDATE {$this->table} 
                  SET status = 'dikembalikan',
                      tanggal_pengembalian = CURDATE(),
                      denda = :denda
                  WHERE loan_id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':denda', $denda);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    public function countByStatus($status) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} WHERE status = :status";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'];
    }

    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        
        $stmt = $this->conn->query($query);
        $result = $stmt->fetch();
        
        return $result['total'];
    }

    public function countOverdue() {
        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE status IN ('dipinjam', 'terlambat')
                  AND tanggal_kembali < CURDATE()";
        
        $stmt = $this->conn->query($query);
        $result = $stmt->fetch();
        
        return $result['total'];
    }

    public function countActiveByUser($user_id) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE user_id = :user_id 
                  AND status IN ('dipinjam', 'terlambat')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'];
    }

    public function getOverdueLoans() {
        $query = "SELECT l.*, 
                         u.nama_lengkap as nama_anggota,
                         u.email,
                         u.no_telepon,
                         b.judul as judul_buku,
                         b.kode_buku,
                         DATEDIFF(CURDATE(), l.tanggal_kembali) as hari_terlambat
                  FROM {$this->table} l
                  JOIN users u ON l.user_id = u.user_id
                  JOIN books b ON l.book_id = b.book_id
                  WHERE l.status IN ('dipinjam', 'terlambat')
                  AND l.tanggal_kembali < CURDATE()
                  ORDER BY l.tanggal_kembali ASC";
        
        $stmt = $this->conn->query($query);
        return $stmt->fetchAll();
    }

    public function updateOverdueStatus() {
        $query = "UPDATE {$this->table} 
                  SET status = 'terlambat'
                  WHERE status = 'dipinjam' 
                  AND tanggal_kembali < CURDATE()";
        
        return $this->conn->exec($query);
    }

    public function calculateDenda($loan_id) {
        $loan = $this->getById($loan_id);
        
        if (!$loan || $loan['status'] == 'dikembalikan') {
            return 0;
        }
        
        $tanggal_kembali = new DateTime($loan['tanggal_kembali']);
        $tanggal_sekarang = new DateTime();
        
        // Jika belum lewat deadline, tidak ada denda
        if ($tanggal_sekarang <= $tanggal_kembali) {
            return 0;
        }
        
        $selisih = $tanggal_sekarang->diff($tanggal_kembali);
        $hari_terlambat = $selisih->days;
        
        // Get denda per hari from settings
        $stmt = $this->conn->query("SELECT nilai FROM settings WHERE nama_setting = 'denda_perhari' LIMIT 1");
        $setting = $stmt->fetch();
        $denda_perhari = $setting ? (int)$setting['nilai'] : 1000; // Default 1000
        
        return $hari_terlambat * $denda_perhari;
    }

    public function getHistory($limit = null, $user_id = null) {
        $query = "SELECT l.*, 
                         u.nama_lengkap as nama_anggota,
                         u.email,
                         b.judul as judul_buku,
                         b.kode_buku,
                         a.nama_penulis
                  FROM {$this->table} l
                  JOIN users u ON l.user_id = u.user_id
                  JOIN books b ON l.book_id = b.book_id
                  LEFT JOIN authors a ON b.author_id = a.author_id
                  WHERE l.status = 'dikembalikan'";
        
        if ($user_id) {
            $query .= " AND l.user_id = :user_id";
        }
        
        $query .= " ORDER BY l.tanggal_pengembalian DESC";
        
        if ($limit) {
            $query .= " LIMIT :limit";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        }
        
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByUserId($user_id, $status = null) {
        $query = "SELECT l.*, 
                         b.judul as judul_buku,
                         b.kode_buku,
                         b.cover_image,
                         a.nama_penulis,
                         CASE 
                             WHEN l.status IN ('dipinjam', 'terlambat') AND l.tanggal_kembali < CURDATE() 
                             THEN DATEDIFF(CURDATE(), l.tanggal_kembali)
                             ELSE 0
                         END as hari_terlambat
                  FROM {$this->table} l
                  JOIN books b ON l.book_id = b.book_id
                  LEFT JOIN authors a ON b.author_id = a.author_id
                  WHERE l.user_id = :user_id";
        
        if ($status) {
            $query .= " AND l.status = :status";
        }
        
        $query .= " ORDER BY l.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getRecentLoans($limit = 5) {
        $query = "SELECT l.*, 
                         u.nama_lengkap as nama_anggota,
                         b.judul as judul_buku,
                         b.kode_buku
                  FROM {$this->table} l
                  JOIN users u ON l.user_id = u.user_id
                  JOIN books b ON l.book_id = b.book_id
                  WHERE l.status IN ('dipinjam', 'terlambat')
                  ORDER BY l.created_at DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getTotalDenda() {
        $query = "SELECT SUM(denda) as total FROM {$this->table} WHERE denda > 0";
        
        $stmt = $this->conn->query($query);
        $result = $stmt->fetch();
        
        return $result['total'] ?? 0;
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE loan_id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }
}
?>
