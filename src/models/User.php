<?php
class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($username, $password) {
        $query = "SELECT * FROM {$this->table} WHERE username = :username AND status = 'aktif' LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch();
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }

    public function getAll($role = null) {
        $query = "SELECT * FROM {$this->table}";
        if ($role) {
            $query .= " WHERE role = :role";
        }
        $query .= " ORDER BY tanggal_daftar DESC";
        
        $stmt = $this->conn->prepare($query);
        if ($role) {
            $stmt->bindParam(':role', $role);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $query = "SELECT * FROM {$this->table} WHERE user_id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($data) {
        $query = "INSERT INTO {$this->table} 
                  (username, password, nama_lengkap, email, no_telepon, alamat, role) 
                  VALUES (:username, :password, :nama_lengkap, :email, :no_telepon, :alamat, :role)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $data['username']);
        $stmt->bindParam(':password', $data['password']);
        $stmt->bindParam(':nama_lengkap', $data['nama_lengkap']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':no_telepon', $data['no_telepon']);
        $stmt->bindParam(':alamat', $data['alamat']);
        $stmt->bindParam(':role', $data['role']);
        
        return $stmt->execute();
    }

    public function update($id, $data) {
        $query = "UPDATE {$this->table} SET 
                  nama_lengkap = :nama_lengkap,
                  email = :email,
                  no_telepon = :no_telepon,
                  alamat = :alamat,
                  status = :status";
        
        if (!empty($data['password'])) {
            $query .= ", password = :password";
        }
        
        $query .= " WHERE user_id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':nama_lengkap', $data['nama_lengkap']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':no_telepon', $data['no_telepon']);
        $stmt->bindParam(':alamat', $data['alamat']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':id', $id);
        
        if (!empty($data['password'])) {
            $stmt->bindParam(':password', $data['password']);
        }
        
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE user_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function countByRole($role = null) {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        if ($role) {
            $query .= " WHERE role = :role";
        }
        
        $stmt = $this->conn->prepare($query);
        if ($role) {
            $stmt->bindParam(':role', $role);
        }
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'];
    }
}
?>
