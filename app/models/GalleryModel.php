<?php
// app/models/GalleryModel.php

require_once ROOT_PATH . 'app/core/Database.php';

class GalleryModel {
    private $db;
    private $table = 'media_gallery'; 
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection(); 
    }

    // ====================================================================
    // 1. FUNGSI PUBLIC (Frontend)
    // ====================================================================

    /**
     * Hitung total media publik
     * @return int
     */
    public function countTotalPublicMedia(): int {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE is_public = true"; // MySQL boolean = 1
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Ambil media publik untuk halaman index/front
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getPublicMedia(int $limit, int $offset): array {
        $sql = "SELECT id, type, title, description, thumbnail_url, file_url, event_id, created_at, updated_at, is_public
                FROM {$this->table} 
                WHERE is_public = true
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as &$item) {
            $item['is_public'] = (bool)$item['is_public'];
            $item['thumbnail_url'] = $item['thumbnail_url'] ?? 'default_thumb.jpg';
            $item['file_url'] = $item['file_url'] ?? '#';
            $item['title'] = $item['title'] ?? 'Media Tanpa Judul';
            $item['description'] = $item['description'] ?? '';
            $item['type'] = $item['type'] ?? 'image';
        }

        return $items;
    }

    /**
     * Ambil detail media publik berdasarkan ID
     * @param int $id
     * @return array|null
     */
    public function getPublicMediaById(int $id): ?array {
        $sql = "SELECT id, type, title, description, thumbnail_url, file_url, event_id, created_at, updated_at, is_public
                FROM {$this->table} 
                WHERE id = :id AND is_public = true";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $item['is_public'] = (bool)$item['is_public'];
            $item['thumbnail_url'] = $item['thumbnail_url'] ?? 'default_thumb.jpg';
            $item['file_url'] = $item['file_url'] ?? '#';
            $item['title'] = $item['title'] ?? 'Media Tanpa Judul';
            $item['description'] = $item['description'] ?? '';
            $item['type'] = $item['type'] ?? 'image';
        }

        return $item ?: null;
    }

    // ====================================================================
    // 2. FUNGSI ADMIN (CRUD + Pagination)
    // ====================================================================

    public function countTotalMedia(): int {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function getMediaPaginated(int $limit, int $offset): array {
        $sql = "SELECT id, type, title, description, thumbnail_url, file_url, event_id, created_at, updated_at, is_public
                FROM {$this->table}
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($items as &$item) {
            $item['is_public'] = (bool)$item['is_public'];
        }

        return $items;
    }

    public function getMediaByIdAdmin(int $id): ?array {
        $sql = "SELECT id, type, title, description, thumbnail_url, file_url, event_id, created_at, updated_at, is_public
                FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) $item['is_public'] = (bool)$item['is_public'];
        return $item ?: null;
    }

    public function createMedia(array $data): bool {
        $sql = "INSERT INTO {$this->table} 
                (type, title, description, file_url, thumbnail_url, event_id, is_public, created_at, updated_at)
                VALUES (:type, :title, :description, :file_url, :thumbnail_url, :event_id, :is_public, NOW(), NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':type', $data['type'], PDO::PARAM_STR);
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $data['description'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':file_url', $data['file_url'], PDO::PARAM_STR);
        $stmt->bindValue(':thumbnail_url', $data['thumbnail_url'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':event_id', $data['event_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':is_public', !empty($data['is_public']) ? 1 : 0, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updateMedia(int $id, array $data): bool {
        $sql = "UPDATE {$this->table} SET 
                    type = :type,
                    title = :title,
                    description = :description,
                    file_url = :file_url,
                    thumbnail_url = :thumbnail_url,
                    event_id = :event_id,
                    is_public = :is_public,
                    updated_at = NOW()
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':type', $data['type'], PDO::PARAM_STR);
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $data['description'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':file_url', $data['file_url'], PDO::PARAM_STR);
        $stmt->bindValue(':thumbnail_url', $data['thumbnail_url'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':event_id', $data['event_id'] ?? null, PDO::PARAM_INT);
        $stmt->bindValue(':is_public', !empty($data['is_public']) ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteMedia(int $id): bool {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ====================================================================
    // 3. Toggle Public/Private
    // ====================================================================

    public function setPublicStatus(int $id, bool $is_public): bool {
        $sql = "UPDATE {$this->table} SET is_public = :is_public, updated_at = NOW() WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':is_public', $is_public ? 1 : 0, PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
