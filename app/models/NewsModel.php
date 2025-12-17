<?php
// app/models/NewsModel.php

require_once ROOT_PATH . 'app/core/Database.php';

class NewsModel {
    private $db;
    private $table = 'news'; 
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection(); 
    }

    // ====================================================================
    // A. PUBLIC SITE
    // ====================================================================

    /**
     * Ambil berita terbaru untuk public site
     * @param int $limit
     * @return array
     */
    public function getLatestNews(int $limit = 5): array {
        $sql = "SELECT id, title, slug, image, created_at, SUBSTRING(body, 1, 150) AS body_excerpt
                FROM {$this->table} 
                WHERE is_published = TRUE 
                ORDER BY created_at DESC 
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil berita berdasarkan slug (Public)
     * @param string $slug
     * @return array|false
     */
    public function getNewsBySlug(string $slug) {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug AND is_published = TRUE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':slug' => $slug]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil semua berita yang diterbitkan (Public)
     */
    public function getAllNewsForPublic(): array {
        $sql = "SELECT id, title, slug, body, image, created_at
                FROM {$this->table} 
                WHERE is_published = TRUE
                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ====================================================================
    // B. ADMIN CMS (CRUD & Pagination)
    // ====================================================================

    /**
     * Ambil semua berita untuk admin (semua status)
     */
    public function getAllNewsAdmin(): array {
        $sql = "SELECT id, title, slug, is_published, created_at, updated_at 
                FROM {$this->table} 
                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil berita per halaman (Admin pagination)
     */
    public function getNewsPaginated(int $limit, int $offset): array {
        $sql = "SELECT id, title, slug, is_published, created_at
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
     * Ambil total jumlah berita (untuk pagination)
     */
    public function getTotalNewsCount(): int {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Ambil berita berdasarkan ID (Admin)
     */
    public function getNewsByIdAdmin(int $id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Ambil berita berdasarkan ID (Public)
     */
    public function getNewsById(int $id) {
        return $this->getNewsByIdAdmin($id); // Sama saja, bisa filter di controller jika perlu
    }

    /**
     * Tambah berita baru (Admin)
     */
    public function createNews(array $data): bool {
        $sql = "INSERT INTO {$this->table} (title, slug, body, image, is_published)
                VALUES (:title, :slug, :body, :image, :is_published)";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':slug', $data['slug'], PDO::PARAM_STR);
        $stmt->bindValue(':body', $data['body'], PDO::PARAM_STR);
        $stmt->bindValue(':image', $data['image'], PDO::PARAM_STR);
        $stmt->bindValue(':is_published', (bool)$data['is_published'], PDO::PARAM_BOOL);

        return $stmt->execute();
    }

    /**
     * Update berita (Admin)
     */
    public function updateNews(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} SET 
                    title = :title, 
                    slug = :slug, 
                    body = :body, 
                    image = :image, 
                    is_published = :is_published, 
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':slug', $data['slug'], PDO::PARAM_STR);
        $stmt->bindValue(':body', $data['body'], PDO::PARAM_STR);
        $stmt->bindValue(':image', $data['image'], PDO::PARAM_STR);
        $stmt->bindValue(':is_published', (bool)$data['is_published'], PDO::PARAM_BOOL);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Ambil nama gambar saat ini (untuk hapus file lama)
     */
    public function getCurrentImageName(int $id): ?string {
        $sql = "SELECT image FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() ?: null;
    }

    /**
     * Hapus berita (Admin)
     */
    public function deleteNews(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
