<?php
// app/models/GuestbookModel.php
// Revisi Lengkap & Konsisten

require_once ROOT_PATH . 'app/core/Database.php';

class GuestbookModel {
    private $db;
    private $table = 'guestbook'; 

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    // ====================================================================
    // A. PUBLIC SITE
    // ====================================================================

    /**
     * Ambil semua entri untuk public site
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function getAllEntries($limit = null, $offset = 0) {
        $sql = "SELECT id, name, institution, message, created_at, category 
                FROM {$this->table} 
                ORDER BY created_at DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);
        if ($limit !== null) {
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Tambah entri baru (CREATE)
     * @param array $data
     * @return bool
     */
    public function createEntry(array $data) {
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$fields}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => $value) {
            $type = PDO::PARAM_STR;
            if (is_int($value)) $type = PDO::PARAM_INT;
            if (is_null($value)) $type = PDO::PARAM_NULL;

            $stmt->bindValue(":$key", $value, $type);
        }

        return $stmt->execute();
    }

    /**
     * Alias khusus Guestbook
     */
    public function createGuestbookEntry(array $data) {
        return $this->createEntry($data);
    }

    // ====================================================================
    // B. ADMIN CMS (CRUD & Pagination)
    // ====================================================================

    /**
     * Hitung total entri (untuk pagination)
     * @return int
     */
    public function getTotalEntryCount() {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table}");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Ambil entri dengan pagination untuk admin
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getEntriesPaginated(int $limit, int $offset) {
        $sql = "SELECT id, name, institution, email, phone, category, message, created_at
                FROM {$this->table}
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil detail entri berdasarkan ID
     * @param int $id
     * @return array|false
     */
    public function getEntryById(int $id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Update entri berdasarkan ID
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function updateGuestbookEntry(int $id, array $data) {
        $sql = "UPDATE {$this->table} SET
                    name = :name,
                    institution = :institution,
                    email = :email,
                    phone = :phone,
                    category = :category,
                    message = :message,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':name', $data['name'], PDO::PARAM_STR);
        $stmt->bindValue(':institution', $data['institution'], PDO::PARAM_STR);
        $stmt->bindValue(':email', $data['email'], PDO::PARAM_STR);
        $stmt->bindValue(':phone', $data['phone'], PDO::PARAM_STR);
        $stmt->bindValue(':category', $data['category'], PDO::PARAM_STR);
        $stmt->bindValue(':message', $data['message'], PDO::PARAM_STR);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Hapus entri berdasarkan ID
     * @param int $id
     * @return bool
     */
    public function deleteEntry(int $id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
