<?php
// app/models/FacilityModel.php

// Memuat core Database.php (asumsi path sudah benar)
require_once ROOT_PATH . 'app/core/Database.php';

class FacilityModel {
    private $db;
    
    // --- Tabel yang Digunakan ---
    private $table_facilities = 'lab_facilities'; 
    
    public function __construct() {
        // Menggunakan Singleton Pattern untuk koneksi DB
        $this->db = Database::getInstance()->getConnection(); 
    }
    
    /**
     * Mengambil semua fasilitas dan peralatan.
     * Dapat digunakan untuk halaman daftar fasilitas.
     */
    public function getAllFacilities() {
        $sql = "SELECT id, name, description, photo_url, type 
                FROM {$this->table_facilities} 
                ORDER BY type ASC, name ASC"; // Urutkan berdasarkan tipe (facility/equipment)
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC); 
    }
    
    /**
     * Mengambil daftar item berdasarkan jenis (type): 'facility' atau 'equipment'.
     * @param string $type Jenis item ('facility' atau 'equipment').
     */
    public function getFacilitiesByType($type) {
        // Memastikan tipe adalah nilai yang valid
        if (!in_array($type, ['facility', 'equipment'])) {
            return [];
        }
        
        $sql = "SELECT id, name, description, photo_url
                FROM {$this->table_facilities} 
                WHERE type = :type
                ORDER BY name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Menghitung total Fasilitas dan Peralatan yang tersedia.
     */
    public function countTotalItems() {
        $sql = "SELECT COUNT(id) FROM {$this->table_facilities}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return (int) $stmt->fetchColumn(); 
    }

    public function getAllFacilitiesPaginated($limit, $offset) {
    $sql = "SELECT id, name, description, photo_url, type 
            FROM {$this->table_facilities} 
            ORDER BY id ASC  -- Diurutkan berdasarkan ID untuk distribusi merata per halaman
            LIMIT :limit OFFSET :offset";
    
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}

public function getAvailableEquipment() {
        // NOTE: Stok (stock) diasumsikan sebagai kolom di tabel items atau dihitung terpisah
        $sql = "SELECT id, name, description, photo_url
                FROM {$this->table_facilities} 
                WHERE type = 'equipment'
                ORDER BY name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Mengambil daftar ruangan yang tersedia (type = facility).
     */
    public function getAvailableFacilities() {
        $sql = "SELECT id, name, description, photo_url
                FROM {$this->table_facilities} 
                WHERE type = 'facility'
                ORDER BY name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Helper: Mendapatkan detail aset berdasarkan ID (untuk validasi).
     */
    public function getFacilityById($id) {
        $sql = "SELECT id, name, type FROM {$this->table_facilities} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}