<?php
class LoanRequest {
    private $conn;
    private $table = 'loan_requests';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} (user_id, book_id, keterangan) 
                  VALUES (:user_id, :book_id, :keterangan)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $data['user_id']);
        $stmt->bindParam(':book_id', $data['book_id']);
        $stmt->bindParam(':keterangan', $data['keterangan']);
        
        return $stmt->execute();
    }

    public function getByUserId($user_id) {
        $query = "SELECT lr.*, 
                         b.judul, b.kode_buku, b.cover_image, 
                         a.nama_penulis, c.nama_kategori
                  FROM {$this->table} lr
                  JOIN books b ON lr.book_id = b.book_id
                  LEFT JOIN authors a ON b.author_id = a.author_id
                  LEFT JOIN categories c ON b.category_id = c.category_id
                  WHERE lr.user_id = :user_id
                  ORDER BY 
                    CASE lr.status
                        WHEN 'pending' THEN 1
                        WHEN 'approved' THEN 2
                        WHEN 'rejected' THEN 3
                    END,
                    lr.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getAll($status = null) {
        $query = "SELECT lr.*, 
                         b.judul, b.kode_buku, b.cover_image, b.stok_tersedia,
                         u.nama_lengkap, u.email,
                         a.nama_penulis,
                         admin.nama_lengkap as admin_name
                  FROM {$this->table} lr
                  JOIN books b ON lr.book_id = b.book_id
                  JOIN users u ON lr.user_id = u.user_id
                  LEFT JOIN authors a ON b.author_id = a.author_id
                  LEFT JOIN users admin ON lr.admin_response = admin.user_id";
        
        if ($status) {
            $query .= " WHERE lr.status = :status";
        }
        
        $query .= " ORDER BY 
                    CASE lr.status
                        WHEN 'pending' THEN 1
                        WHEN 'approved' THEN 2
                        WHEN 'rejected' THEN 3
                    END,
                    lr.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT lr.*, 
                         b.judul, b.kode_buku, b.stok_tersedia,
                         u.nama_lengkap, u.email,
                         a.nama_penulis
                  FROM {$this->table} lr
                  JOIN books b ON lr.book_id = b.book_id
                  JOIN users u ON lr.user_id = u.user_id
                  LEFT JOIN authors a ON b.author_id = a.author_id
                  WHERE lr.request_id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->fetch();
    }

    public function approve($request_id, $admin_id) {
        $query = "UPDATE {$this->table} 
                  SET status = 'approved', 
                      admin_response = :admin_id,
                      approved_date = NOW()
                  WHERE request_id = :id AND status = 'pending'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $request_id);
        $stmt->bindParam(':admin_id', $admin_id);
        
        return $stmt->execute();
    }

    public function reject($request_id, $admin_id, $note) {
        $query = "UPDATE {$this->table} 
                  SET status = 'rejected', 
                      admin_response = :admin_id,
                      response_note = :note
                  WHERE request_id = :id AND status = 'pending'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $request_id);
        $stmt->bindParam(':admin_id', $admin_id);
        $stmt->bindParam(':note', $note);
        
        return $stmt->execute();
    }

    public function delete($id, $user_id) {
        $query = "DELETE FROM {$this->table} 
                  WHERE request_id = :id AND user_id = :user_id AND status = 'pending'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }

    public function countByStatus($status = null) {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        
        if ($status) {
            $query .= " WHERE status = :status";
        }
        
        $stmt = $this->conn->prepare($query);
        
        if ($status) {
            $stmt->bindParam(':status', $status);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'];
    }

    public function countPendingByUser($user_id) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE user_id = :user_id AND status = 'pending'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'];
    }

    public function checkDuplicateRequest($user_id, $book_id) {
        $query = "SELECT COUNT(*) as total FROM {$this->table} 
                  WHERE user_id = :user_id AND book_id = :book_id AND status = 'pending'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':book_id', $book_id);
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result['total'] > 0;
    }

    public function getRecentRequests($limit = 5) {
        $query = "SELECT lr.*, 
                         b.judul, u.nama_lengkap
                  FROM {$this->table} lr
                  JOIN books b ON lr.book_id = b.book_id
                  JOIN users u ON lr.user_id = u.user_id
                  WHERE lr.status = 'pending'
                  ORDER BY lr.created_at DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
}
?>
