<?php

require_once ROOT_PATH . 'app\core\Database.php';

class UserModel {
    private $db;
    private $table = 'users';

    public function __construct() {
        // Asumsi: Class Database sudah di-load dan bisa diakses
        $this->db = Database::getInstance()->getConnection(); 
    }

    /**
     * Mencari user berdasarkan username ATAU email.
     * @param string $credential (Bisa berupa username atau email)
     * @return array|false Data user jika ditemukan.
     */
    // app/models/UserModel.php

// ... (di dalam public function findUserByCredentials($credential)) ...

public function findUserByCredentials($credential) {
    // ASUMSI: Kolom yang ada di database Anda bernama 'password' (bukan 'password_hash')
    $sql = "SELECT id, name, username, email, password AS password_hash, role, is_active 
            FROM {$this->table} 
            WHERE (username = :cred OR email = :cred) AND is_active = TRUE";
            
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':cred', $credential, PDO::PARAM_STR); 
    $stmt->execute();
    
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
    
    /**
     * HANYA UNTUK DEBUG/SETUP: Menyimpan user baru dengan password hash.
     * Metode ini harus dijalankan sekali untuk membuat user admin pertama.
     */
    public function createInitialAdmin($username, $email, $raw_password, $role = 'admin') {
        $hash = password_hash($raw_password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO {$this->table} (username, email, password, name, role, is_active)
                VALUES (:user, :email, :hash, :name, :role, TRUE)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':user', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':hash', $hash);
        $stmt->bindValue(':name', 'Atmint');
        $stmt->bindValue(':role', $role);
        
        return $stmt->execute();
    }
}