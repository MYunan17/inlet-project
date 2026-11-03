<?php
// app/models/EventModel.php

require_once ROOT_PATH . 'app/core/Database.php';

class EventModel {
    private $db;
    private $table = 'events'; 
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection(); 
    }

    // ====================================================================
    // A. FUNGSI UNTUK PUBLIC SITE (Events yang akan datang/berlangsung)
    // ====================================================================

    public function countTotalEvents() {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM events");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    public function getAllEvents() {
        $stmt = $this->db->query("SELECT * FROM events ORDER BY start_date DESC, id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllUpcomingEvents() { 
        $sql = "SELECT id, title, slug, body, start_date, end_date, location, image 
                FROM {$this->table} 
                WHERE is_published = TRUE
                AND (end_date >= NOW() OR start_date >= NOW()) 
                ORDER BY start_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEventBySlug($slug) {
        $sql = "SELECT * FROM {$this->table} WHERE slug = :slug AND is_published = TRUE"; 
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':slug' => $slug]); 
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ====================================================================
    // B. FUNGSI UNTUK ADMIN CMS (CRUD & Pagination)
    // ====================================================================

    public function getTotalEventCount() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function getEventsPaginated($limit, $offset) {
        $sql = "SELECT id, title, slug, is_published, start_date, end_date, location, created_at
                FROM {$this->table} 
                ORDER BY created_at DESC
                LIMIT :limit OFFSET :offset"; 
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    public function getEventByIdAdmin($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createEvent($data) {
        $sql = "INSERT INTO {$this->table} (title, slug, body, image, is_published, start_date, end_date, location)
                VALUES (:title, :slug, :body, :image, :is_published, :start_date, :end_date, :location)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':slug', $data['slug'], PDO::PARAM_STR);
        $stmt->bindValue(':body', $data['body'], PDO::PARAM_STR);
        $stmt->bindValue(':image', $data['image'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':is_published', (int)$data['is_published'], PDO::PARAM_INT); 
        $stmt->bindValue(':start_date', $data['start_date'], PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $data['end_date'], PDO::PARAM_STR);
        $stmt->bindValue(':location', $data['location'], PDO::PARAM_STR);

        return $stmt->execute(); 
    }

    public function updateEvent($id, $data) {
        $sql = "UPDATE {$this->table} SET 
                    title = :title, slug = :slug, body = :body, image = :image, 
                    is_published = :is_published, start_date = :start_date, 
                    end_date = :end_date, location = :location, updated_at = NOW() 
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':slug', $data['slug'], PDO::PARAM_STR);
        $stmt->bindValue(':body', $data['body'], PDO::PARAM_STR);
        $stmt->bindValue(':image', $data['image'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':is_published', (int)$data['is_published'], PDO::PARAM_INT); 
        $stmt->bindValue(':start_date', $data['start_date'], PDO::PARAM_STR);
        $stmt->bindValue(':end_date', $data['end_date'], PDO::PARAM_STR);
        $stmt->bindValue(':location', $data['location'], PDO::PARAM_STR);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function deleteEvent($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getCurrentImageName($id) {
        $sql = "SELECT image FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    // ====================================================================
    // C. TAMBAHAN: Method getEventById() untuk ActivitiesController
    // ====================================================================
    /**
     * Ambil detail event berdasarkan ID (untuk public/detail di ActivitiesController)
     */
    public function getEventById($id) {
        $sql = "SELECT id, title, slug, body, start_date, end_date, location, image, created_at
                FROM {$this->table} 
                WHERE id = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
