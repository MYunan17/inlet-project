<?php
// app/controllers/ActivitiesController.php

// [DEPENDENCY] Memuat Model yang sesuai 
require_once ROOT_PATH . 'app/models/NewsModel.php'; 
require_once ROOT_PATH . 'app/models/AnnouncementModel.php'; 
require_once ROOT_PATH . 'app/models/EventModel.php'; 

class ActivitiesController {
    
    private $NewsModel;
    private $AnnouncementModel;
    private $EventModel;
    private $items_per_page = 10; // Jumlah item per halaman

    public function __construct() {
        $this->NewsModel = new NewsModel();
        $this->AnnouncementModel = new AnnouncementModel();
        $this->EventModel = new EventModel();
    }

    public function index() {
        $data = [];
        $items_per_page = $this->items_per_page;
        $current_page = isset($_GET['page']) && is_numeric($_GET['page']) 
                         ? (int)$_GET['page'] 
                         : 1;
        
        try {
            // 1. Hitung Total Item dari ketiga tabel
            $total_news = $this->NewsModel->getTotalNewsCount(); 
            $total_announcements = $this->AnnouncementModel->countTotalAnnouncements(); 
            $total_events = $this->EventModel->countTotalEvents(); 
            
            $total_items = $total_news + $total_announcements + $total_events; 
            $total_pages = ceil($total_items / $items_per_page);
            $current_page = max(1, min($current_page, $total_pages > 0 ? $total_pages : 1));
            
            $offset = ($current_page - 1) * $items_per_page;
            
            // 2. Ambil Data Gabungan (memanggil metode helper)
            $data['activity_list'] = $this->getCombinedPaginatedData($items_per_page, $offset); 
            
            // 3. Kirim Variabel Pagination ke View
            $data['current_page'] = $current_page;
            $data['total_items'] = $total_items;
            $data['total_pages'] = $total_pages;
            
        } catch (\Exception $e) {
            $data['activity_list'] = [];
            $data['current_page'] = 1;
            $data['total_pages'] = 1;
            error_log("Error loading combined activities list: " . $e->getMessage());
        }

        $page_title = 'Kegiatan & Berita'; 
        extract($data); 
        include ROOT_PATH . 'views/layouts/header.php';
        include ROOT_PATH . 'views/layouts/navbar.php';
        // Menggunakan nama view yang disarankan: activities_news.php
        include ROOT_PATH . 'views/public/activities/index.php'; 
        include ROOT_PATH . 'views/layouts/footer.php';
    }

    /**
     * Helper: Mengambil, menggabungkan, menandai, mengurutkan, dan memotong (slice) data.
     */
    private function getCombinedPaginatedData($limit, $offset) {
    // Ambil data dari semua model
    $news = $this->NewsModel->getAllNewsForPublic() ?? [];
    $announcements = $this->AnnouncementModel->getAllAnnouncements() ?? [];
    $events = $this->EventModel->getAllEvents() ?? [];

    $all_items = [];

    // --- Tambahkan News ---
    foreach ($news as $item) {
        $all_items[] = [
            'id' => $item['id'],
            'title' => $item['title'] ?? '',
            'body' => $item['body'] ?? '',
            'image' => $item['image'] ?? null,
            'created_at' => $item['created_at'] ?? null,
            'sort_date' => $item['created_at'] ?? null,
            'item_type' => 'news',
            'excerpt' => substr($item['body'] ?? $item['title'] ?? '', 0, 150) . '...'
        ];
    }

    // --- Tambahkan Announcements ---
    foreach ($announcements as $item) {
        $all_items[] = [
            'id' => $item['id'],
            'title' => $item['title'] ?? '',
            'body' => $item['body'] ?? '',
            'image' => $item['image'] ?? null,
            'created_at' => $item['created_at'] ?? null,
            'sort_date' => $item['created_at'] ?? null,
            'item_type' => 'announcement',
            'excerpt' => substr($item['body'] ?? $item['title'] ?? '', 0, 150) . '...'
        ];
    }

    // --- Tambahkan Events ---
    foreach ($events as $item) {
        $event_date = $item['start_date'] ?? $item['created_at'] ?? date('Y-m-d H:i:s');
        $all_items[] = [
            'id' => $item['id'],
            'title' => $item['title'] ?? '',
            'body' => $item['body'] ?? '',
            'image' => $item['image'] ?? null,
            'start_date' => $item['start_date'] ?? null,
            'sort_date' => $event_date,
            'item_type' => 'event',
            'excerpt' => substr($item['body'] ?? $item['title'] ?? '', 0, 150) . '...'
        ];
    }

    // --- Sorting DESC berdasarkan sort_date ---
    usort($all_items, function($a, $b) {
        $dateA = strtotime($a['sort_date'] ?? '1970-01-01 00:00:00');
        $dateB = strtotime($b['sort_date'] ?? '1970-01-01 00:00:00');
        return $dateB <=> $dateA; // descending
    });

    // --- Pagination ---
    return array_slice($all_items, $offset, $limit);
}



    ### 2. Perbaikan `detail($id)`

    public function detail($item_type, $item_id) { 
        $data = [];
        $item = null;
        
        // Tentukan Model mana yang akan dipanggil
        if ($item_type === 'news') {
            $item = $this->NewsModel->getNewsById($item_id);
        } elseif ($item_type === 'announcement') {
            $item = $this->AnnouncementModel->getAnnouncementById($item_id);
        } elseif ($item_type === 'event') {
            $item = $this->EventModel->getEventById($item_id);
        }
        
        if (!$item) {
            http_response_code(404);
            die("404 Item ID $item_id Tipe $item_type Tidak Ditemukan."); 
        }

        $data['item'] = $item; 
        $data['item_type'] = $item_type; // Kirim tipe untuk View
        
        $page_title = $item['title'];
        extract($data); 
        
        // Muat Views
        include ROOT_PATH . 'views/layouts/header.php';
        include ROOT_PATH . 'views/layouts/navbar.php';
        // Menggunakan view detail tunggal untuk ketiga tipe
        include ROOT_PATH . 'views/public/activities/detail.php'; 
        include ROOT_PATH . 'views/layouts/footer.php';
    }
}