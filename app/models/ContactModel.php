<?php
require_once ROOT_PATH . 'app/core/Database.php';

class ContactModel {
    private $db;
    private $table_messages = 'contact_messages'; 

    public function __construct() {
        $this->db = Database::getInstance()->getConnection(); 
    }

    // Ambil total pesan
    public function getTotalMessages(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM {$this->table_messages}");
        return (int) $stmt->fetchColumn();
    }

    // Ambil pesan dengan pagination
    public function getMessagesPaginated(int $limit, int $offset): array {
        $sql = "SELECT * FROM {$this->table_messages} ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Hapus pesan berdasarkan ID
    public function deleteMessage(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM {$this->table_messages} WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Tambah pesan baru (jika diperlukan)
    public function createMessage(array $data): bool {
        $fields = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table_messages} ({$fields}, created_at) VALUES ({$placeholders}, NOW())";
        $stmt = $this->db->prepare($sql);

        foreach ($data as $key => &$value) {
            $stmt->bindParam(":$key", $value);
        }

        return $stmt->execute();
    }
}
