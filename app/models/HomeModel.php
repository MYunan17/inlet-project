<?php
// app/models/HomeModel.php

require_once ROOT_PATH . 'app/core/Database.php';

class HomeModel {
    private $db;
    
    // --- Tabel yang Digunakan ---
    private $table_publications = 'publications'; 
    private $table_research_outputs = 'research_outputs'; // ✅ TAMBAH: Tabel Output Riset
    private $table_members = 'members';
    private $table_guestbook = 'guestbook';
    private $table_attendance = 'student_attendance';
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection(); 
    }
    
    /**
     * Menghitung total Riset (Output Riset) & Publikasi yang sudah terbit.
     */
    public function getTotalResearch() {
        // Hitung total dari PUBLIKASI
        $sql_pub = "SELECT COUNT(id) FROM {$this->table_publications}";
        $stmt_pub = $this->db->prepare($sql_pub);
        $stmt_pub->execute();
        $total_pub = (int) $stmt_pub->fetchColumn(); 
        
        // Hitung total dari OUTPUT RISET
        $sql_output = "SELECT COUNT(id) FROM {$this->table_research_outputs}"; // Asumsi semua research_outputs dihitung
        $stmt_output = $this->db->prepare($sql_output);
        $stmt_output->execute();
        $total_output = (int) $stmt_output->fetchColumn();

        // ✅ GABUNGKAN TOTAL
        return $total_pub + $total_output; 
    }
    
    /**
     * Menghitung Anggota Tim yang statusnya aktif.
     */
    public function getActiveMembers() {
        $sql = "SELECT COUNT(id) FROM {$this->table_members} WHERE is_active = TRUE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        
        return (int) $stmt->fetchColumn();
    }

    /**
     * Mengambil total Kunjungan Lab (Gabungan dari Buku Tamu dan Absensi Mahasiswa).
     */
    public function getTotalLabVisits() {
        // 1. Total dari Buku Tamu (Pengunjung Umum)
        $sql_guest = "SELECT COUNT(id) FROM {$this->table_guestbook}";
        $stmt_guest = $this->db->prepare($sql_guest);
        $stmt_guest->execute();
        $total_guest = (int)$stmt_guest->fetchColumn(); 

        // 2. Total dari Absensi Mahasiswa
        $sql_attendance = "SELECT COUNT(id) FROM {$this->table_attendance}"; 
        $stmt_attendance = $this->db->prepare($sql_attendance);
        $stmt_attendance->execute();
        $total_attendance = (int)$stmt_attendance->fetchColumn(); 

        // 3. Gabungkan total kumulatif
        return $total_guest + $total_attendance; 
    }

    // ❌ METHOD getLatestPublications($limit = 3) DIHAPUS 
    // Karena logika ini dipindahkan ke HomeController (getLatestCombinedResearch).
}