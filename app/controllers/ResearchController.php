<?php
// app/controllers/ResearchController.php

require_once ROOT_PATH . 'app/models/ResearchOutputModel.php'; 
require_once ROOT_PATH . 'app/models/PublicationModel.php'; 

class ResearchController {
    
    private $ResearchOutputModel; 
    private $PublicationModel;    
    private $items_per_page = 5; // Item per halaman
    
    public function __construct() {
        $this->ResearchOutputModel = new ResearchOutputModel();
        $this->PublicationModel = new PublicationModel();
    }

    /**
     * Menampilkan daftar gabungan Riset dan Publikasi dengan Pagination.
     */
    public function index() {
        $data = [];
        $items_per_page = $this->items_per_page;
        $current_page = isset($_GET['page']) && is_numeric($_GET['page']) 
                         ? (int)$_GET['page'] 
                         : 1;
        
        try {
            // 1. Hitung Total Item dari kedua tabel
            $total_research_outputs = $this->ResearchOutputModel->countTotalOutputs(); 
            $total_publications = $this->PublicationModel->countTotalPublications(); 
            $total_items = $total_research_outputs + $total_publications; 
            
            $total_pages = ceil($total_items / $items_per_page);
            $current_page = max(1, min($current_page, $total_pages > 0 ? $total_pages : 1));
            
            $offset = ($current_page - 1) * $items_per_page;
            // 2. Ambil Data Gabungan (Potong array di PHP)
            $data['research_list'] = $this->getCombinedPaginatedData($items_per_page, $offset); 

            // 3. Kirim Variabel Pagination ke View
            $data['current_page'] = $current_page;
            $data['total_items'] = $total_items;
            $data['total_pages'] = $total_pages;
            
        } catch (\Exception $e) {
            $data['research_list'] = [];
            $data['current_page'] = 1;
            $data['total_pages'] = 1;
            error_log("Error loading combined research list: " . $e->getMessage());
        }

        // Muat View
        $page_title = 'Riset & Publikasi'; 
        extract($data); 
        include ROOT_PATH . 'views/layouts/header.php';
        include ROOT_PATH . 'views/layouts/navbar.php';
        include ROOT_PATH . 'views/public/research/index.php'; 
        include ROOT_PATH . 'views/layouts/footer.php';
    }

    /**
     * Mengambil, menggabungkan, menandai, mengurutkan, dan memotong (slice) data.
     */
    public function getCombinedPaginatedData($limit, $offset) {
        
        $outputs = $this->ResearchOutputModel->getAllOutputs();
        $publications = $this->PublicationModel->getAllPublications();
        
        $all_items = array_merge($outputs, $publications);

        // Tandai jenisnya dan buat key 'excerpt' yang konsisten
        foreach ($all_items as $key => &$item) {
            $is_publication = isset($item['doi']);
            $item['item_type'] = $is_publication ? 'publication' : 'research_output';
            
            // Membuat 'excerpt' yang konsisten (maks 200 karakter)
            if ($is_publication) {
                $item['excerpt'] = substr($item['abstract'] ?? $item['title'], 0, 200) . '...';
            } else {
                $item['excerpt'] = substr($item['description'] ?? $item['title'], 0, 200) . '...';
            }

            // Pastikan member_name selalu ada
            if (!isset($item['member_name'])) {
                $item['member_name'] = $item['name'] ?? 'Unknown';
            }
        }
        
        // Urutkan berdasarkan tahun terbaru, kemudian created_at jika ada
        usort($all_items, function($a, $b) {
            $yearA = $a['year'] ?? 0;
            $yearB = $b['year'] ?? 0;
            if ($yearB !== $yearA) return $yearB <=> $yearA;

            $dateA = strtotime($a['created_at'] ?? '1970-01-01');
            $dateB = strtotime($b['created_at'] ?? '1970-01-01');
            return $dateB <=> $dateA;
        });
        
        // Potong array sesuai pagination
        return array_slice($all_items, $offset, $limit);
    }
    
    /**
     * Metode untuk menampilkan detail Riset atau Publikasi (/research/detail/type/ID).
     */
    public function detail($item_type, $item_id) { 
        $data = [];
        $item = null;
        
        if ($item_type === 'research_output') {
            $item = $this->ResearchOutputModel->getOutputById($item_id);
        } elseif ($item_type === 'publication') {
            $item = $this->PublicationModel->getPublicationById($item_id);
        } 
        
        if (!$item) {
            http_response_code(404);
            die("404 Item ID $item_id Tipe $item_type Tidak Ditemukan."); 
        }

        // Pastikan member_name selalu ada
        if (!isset($item['member_name'])) {
            $item['member_name'] = $item['name'] ?? 'Unknown';
        }

        $data['item'] = $item; 
        $data['item_type'] = $item_type; 
        extract($data); 
        
        include ROOT_PATH . 'views/layouts/header.php';
        include ROOT_PATH . 'views/layouts/navbar.php';
        include ROOT_PATH . 'views/public/research/detail.php'; 
        include ROOT_PATH . 'views/layouts/footer.php';
    }
}
