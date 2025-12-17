<?php
// app/controllers/GalleryController.php

require_once ROOT_PATH . 'app/models/GalleryModel.php';

class GalleryController {

    private $galleryModel;
    private $initial_limit = 6; // Jumlah item awal yang dimuat

    public function __construct() {
        $this->galleryModel = new GalleryModel();
    }

    /**
     * Menampilkan halaman Galeri
     */
    public function index() {
        $data = [];

        try {
            // Ambil data awal untuk grid
            $offset = 0;
            $data['total_items'] = $this->galleryModel->countTotalPublicMedia();
            $data['gallery_list'] = $this->galleryModel->getPublicMedia($this->initial_limit, $offset);

        } catch (\Exception $e) {
            $data['gallery_list'] = [];
            $data['total_items'] = 0;
            error_log("Error loading gallery data: " . $e->getMessage());
        }

        $page_title = 'Galeri Lab INLET';
        extract($data);

        include ROOT_PATH . 'views/layouts/header.php';
        include ROOT_PATH . 'views/layouts/navbar.php';
        include ROOT_PATH . 'views/public/gallery.php';
        include ROOT_PATH . 'views/layouts/footer.php';
    }

    /**
     * Endpoint AJAX Load More
     */
    public function loadMore() {
        // Pastikan hanya GET yang diterima
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
            exit;
        }

        // Ambil parameter limit & offset
        $limit = filter_input(INPUT_GET, 'limit', FILTER_VALIDATE_INT) ?: 6;
        $offset = filter_input(INPUT_GET, 'offset', FILTER_VALIDATE_INT) ?: 0;

        // Header JSON
        header('Content-Type: application/json');

        try {
            // Ambil data dari model
            $new_items = $this->galleryModel->getPublicMedia($limit, $offset);
            $total_available = $this->galleryModel->countTotalPublicMedia();

            // Cek apakah halaman terakhir
            $is_last_page = ($offset + count($new_items)) >= $total_available;

            echo json_encode([
                'success' => true,
                'items' => $new_items,
                'is_last_page' => $is_last_page
            ]);

        } catch (\Exception $e) {
            error_log("Gallery AJAX error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Gagal memuat data server.'
            ]);
        }

        exit;
    }
}
