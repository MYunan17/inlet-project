<?php
require_once ROOT_PATH . 'app/core/Database.php';

class UsersModel {
    private $db;
    private $table = 'users';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Hitung total user dengan optional search (username, name, email)
     */
    public function countTotalUsersFiltered($search = '') {
        $sql = "SELECT COUNT(id) FROM {$this->table} WHERE TRUE";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (username ILIKE :search OR name ILIKE :search OR email ILIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    /**
     * Ambil user dengan pagination dan optional search
     */
    public function getUsersPaginatedFiltered($limit, $offset, $search = '') {
        $sql = "SELECT id, name, username, email, role, is_active, personal_id, created_at 
                FROM {$this->table} WHERE TRUE";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (username ILIKE :search OR name ILIKE :search OR email ILIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        // Bind parameter search
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        // Bind LIMIT & OFFSET
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil user berdasarkan ID
     */
    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id=:id");
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Tambah user baru
     */
    public function createUser($data) {
        $sql = "INSERT INTO {$this->table} 
                (name, username, email, password, role, is_active, personal_id)
                VALUES (:name, :username, :email, :password, :role, :is_active, :personal_id)";
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':name' => $data['name'],
            ':username' => $data['username'],
            ':email' => $data['email'],
            ':password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':role' => $data['role'] ?? 'user',
            ':is_active' => isset($data['is_active']) ? (int)$data['is_active'] : 1,
            ':personal_id' => $data['personal_id'] ?? null
        ]);
    }

    /**
     * Update user
     */
    public function updateUser($id, $data) {
        $sql = "UPDATE {$this->table} SET
                    name = :name,
                    username = :username,
                    email = :email,
                    role = :role,
                    is_active = :is_active,
                    personal_id = :personal_id";

        // Update password jika diberikan
        if (!empty($data['password'])) {
            $sql .= ", password = :password";
        }

        $sql .= " WHERE id = :id";
        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':username', $data['username']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':role', $data['role'] ?? 'user');
        $stmt->bindValue(':is_active', isset($data['is_active']) ? (int)$data['is_active'] : 1, PDO::PARAM_INT);
        $stmt->bindValue(':personal_id', $data['personal_id'] ?? null);

        if (!empty($data['password'])) {
            $stmt->bindValue(':password', password_hash($data['password'], PASSWORD_DEFAULT));
        }

        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Hapus user
     */
    public function deleteUser($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id=:id");
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function findUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil user berdasarkan username/email (digunakan login)
     */
    public function findUserByCredentials($credential) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} 
                                    WHERE username = :cred OR email = :cred LIMIT 1");
        $stmt->bindValue(':cred', $credential);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update password user berdasarkan user ID
     */
    public function updatePassword($userId, $hashedPassword) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password = :password WHERE id = :id");
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->bindValue(':id', (int)$userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

}
