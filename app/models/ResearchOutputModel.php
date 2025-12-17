<?php
// app/models/ResearchOutputModel.php

require_once ROOT_PATH . 'app/core/Database.php';

class ResearchOutputModel {
    private $db;
    private $table = 'research_outputs'; 
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection(); 
    }

    /**
     * Mengambil semua data research output, termasuk nama member.
     * @return array Semua data output riset.
     */
    public function getAllOutputs() {
        $sql = "SELECT ro.id, ro.title, ro.description, ro.year, ro.pdf_url, m.name AS member_name
                FROM {$this->table} ro
                LEFT JOIN members m ON ro.member_id = m.id
                ORDER BY ro.year DESC, ro.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mengambil detail satu research output berdasarkan ID, termasuk nama member.
     */
    public function getOutputById($id) {
        $sql = "SELECT ro.id, ro.title, ro.description, ro.year, ro.pdf_url, ro.thumbnail_url,
                       m.id AS member_id, m.name AS member_name
                FROM {$this->table} ro
                LEFT JOIN members m ON ro.member_id = m.id
                WHERE ro.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Menghitung total research outputs.
     */
    public function countTotalOutputs() {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    /**
     * Mengambil research outputs terbaru dengan limit tertentu, termasuk nama member.
     */
    public function getLatestOutputs($limit = 3) {
        $sql = "SELECT ro.id, ro.title, ro.description, ro.year, ro.pdf_url, m.name AS member_name
                FROM {$this->table} ro
                LEFT JOIN members m ON ro.member_id = m.id
                ORDER BY ro.created_at DESC
                LIMIT :limit";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
