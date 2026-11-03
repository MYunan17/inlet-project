<?php

class KontenController {
    public function index() {
        // --- DATA LAYOUT DINAMIS ---
        $page_title = 'Kelola Konten'; // Diperlukan untuk judul halaman dan breadcrumb
        $current_page = 'konten';      // Diperlukan untuk mengaktifkan menu sidebar
        // ---------------------------
        
        // Panggil Views
        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php'; 
        include ROOT_PATH . 'views/admin/kelola_konten.php'; 
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }
}