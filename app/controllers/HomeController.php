<?php
// app/controllers/HomeController.php

// [DEPENDENCY] Memuat Model Inti (untuk Statistik)
require_once ROOT_PATH . '/app/models/HomeModel.php'; 
// [DEPENDENCY] Memuat Model Output Riset (untuk Cuplikan Terbaru)
require_once ROOT_PATH . '/app/models/ResearchOutputModel.php'; 

class HomeController {
    
    private $HomeModel; 
    private $ResearchOutputModel; // ✅ TAMBAH: Model untuk Research Outputs
    
    public function __construct() {
        // Instansiasi Model HomeModel (untuk Statistik)
        $this->HomeModel = new HomeModel(); 
        // Instansiasi Model Output Riset
        $this->ResearchOutputModel = new ResearchOutputModel(); // ✅ BARU
    }

    /**
     * Menampilkan halaman beranda publik dan statistik dinamis.
     */
    public function index() {
        $data = [];
        
        // ----------------------------------------------------------------------
        // 1. AMBIL DATA DARI MODEL
        // ----------------------------------------------------------------------
        try {
            // Ambil Data Statistik (TETAP DARI HomeModel)
            $data['research_count'] = $this->HomeModel->getTotalResearch();
            $data['members_count'] = $this->HomeModel->getActiveMembers();
            $data['visits_count']  = $this->HomeModel->getTotalLabVisits();
            
            // ✅ PERUBAHAN: Ambil Latest Research OUTPUTS (bukan Publications)
            // Asumsi: ResearchOutputModel memiliki method getLatestOutputs()
            $data['latest_research'] = $this->ResearchOutputModel->getLatestOutputs(3);
            // var_dump($data);die;
            
        } catch (\Exception $e) {
            // Fallback jika terjadi error koneksi/query
            $data['research_count'] = 0;
            $data['members_count'] = 0;
            $data['visits_count'] = 0;
            $data['latest_research'] = []; // Fallback untuk data riset
            error_log("Error loading Home data: " . $e->getMessage()); 
        }

        // ----------------------------------------------------------------------
        // 2. LOAD VIEW DAN LAYOUT
        // ----------------------------------------------------------------------

        // Siapkan variabel umum untuk layout publik
        $page_title = 'Beranda - Lab INLET'; 
        $current_page_nav = 'home'; // ✅ KOREKSI: Ganti 'homeModel' menjadi 'home'

        // Ekstrak data array
        extract($data); 
        
        // Memuat Layout Publik
        include ROOT_PATH . '/views/layouts/header.php';
        include ROOT_PATH . '/views/layouts/navbar.php';
        
        // Memuat View Utama
        include ROOT_PATH . '/views/public/home.php'; 
        
        include ROOT_PATH . '/views/layouts/footer.php';
    }
}