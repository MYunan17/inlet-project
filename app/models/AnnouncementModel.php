<?php
// app/models/AnnouncementModel.php (REKOMENDASI LENGKAP)

require_once ROOT_PATH . 'app/core/Database.php';

class AnnouncementModel {
    private $db;
    private $table = 'announcements'; 
    
    public function __construct() {
        // 
        $this->db = Database::getInstance()->getConnection(); 
    }

    public function countTotalAnnouncements() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM announcements");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    // Ambil semua pengumuman (opsional)
    public function getAllAnnouncements() {
        $stmt = $this->db->query("SELECT * FROM announcements ORDER BY expires_at DESC, id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ====================================================================
    // A. FUNGSI UNTUK PUBLIC SITE (Hanya pengumuman aktif)
    // ====================================================================

    /**
     * Mengambil semua pengumuman yang aktif (belum kadaluarsa) untuk tampilan publik.
     */
    public function getActiveAnnouncements($limit = null, $offset = 0) {
        $sql = "SELECT id, title, body, created_at, attachment, expires_at 
                FROM {$this->table} 
                WHERE expires_at IS NULL OR expires_at >= NOW()
                ORDER BY created_at DESC";
                
        if ($limit !== null) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        if ($limit !== null) {
             $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
             $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // ====================================================================
    // B. FUNGSI UNTUK ADMIN CMS (CRUD & Pagination)
    // ====================================================================

    /**
     * Menghitung total SEMUA pengumuman (aktif dan kedaluwarsa) untuk Admin Pagination.
     */
    public function getTotalAnnouncementCount() {
        // --- PERBAIKAN: Hapus filter expires_at untuk Admin ---
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Mengambil data pengumuman untuk tampilan Admin dengan pagination.
     */
    public function getAnnouncementsPaginated($limit, $offset) {
        $sql = "SELECT id, title, attachment, expires_at, created_at
                FROM {$this->table} 
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset"; 
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Mengambil detail satu pengumuman berdasarkan ID (digunakan di Admin Edit).
     */
    public function getAnnouncementById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Menyimpan data pengumuman baru (CREATE).
     */
    public function createAnnouncement($data) {
        $sql = "INSERT INTO {$this->table} (title, body, attachment, expires_at)
            VALUES (:title, :body, :attachment, :expires_at)";

        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':body', $data['body'], PDO::PARAM_STR);
        $stmt->bindValue(':attachment', $data['attachment'], PDO::PARAM_STR);
        
        // Handling NULL for expires_at
        $expires_at = $data['expires_at'] ?? null;
        $stmt->bindValue(':expires_at', $expires_at, $expires_at === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        
        return $stmt->execute(); 
    }

    /**
     * Memperbarui data pengumuman (UPDATE).
     */
    public function updateAnnouncement($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                    title = :title, 
                    body = :body, 
                    attachment = :attachment, 
                    expires_at = :expires_at, 
                    updated_at = NOW() 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':body', $data['body'], PDO::PARAM_STR);
        $stmt->bindValue(':attachment', $data['attachment'], PDO::PARAM_STR);
        
        // Handling NULL for expires_at
        $expires_at = $data['expires_at'] ?? null;
        $stmt->bindValue(':expires_at', $expires_at, $expires_at === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Helper untuk mendapatkan nama attachment saat ini (digunakan di Controller untuk menghapus file lama)
     */
    public function getCurrentAttachmentName($id) {
        $sql = "SELECT attachment FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn(); 
    }

    /**
     * Menghapus pengumuman berdasarkan ID (DELETE).
     */
    public function deleteAnnouncement($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}