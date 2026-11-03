<?php
// app/controllers/admin/EventsController.php

require_once ROOT_PATH . 'app/models/EventModel.php';

class EventsController {

    private EventModel $eventModel;
    private int $items_per_page = 5;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // PROTEKSI ADMIN
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $this->eventModel = new EventModel();
    }

    // =========================================================
    // INDEX (LIST EVENT)
    // =========================================================
    public function index() {

        $current_page = max(1, (int)($_GET['page'] ?? 1));

        $total_items = (int)$this->eventModel->getTotalEventCount();
        $total_pages = max(1, ceil($total_items / $this->items_per_page));

        $current_page = min($current_page, $total_pages);
        $offset = ($current_page - 1) * $this->items_per_page;

        $data = [
            'event_list'   => $this->eventModel->getEventsPaginated($this->items_per_page, $offset),
            'current_page' => $current_page,
            'total_items'  => $total_items,
            'total_pages'  => $total_pages,
            'start_index'  => $offset,
            'status'       => $_GET['status'] ?? null
        ];

        $page_title = 'Kelola Acara & Kegiatan';
        $current_page_nav = 'events';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/events/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // =========================================================
    // CREATE
    // =========================================================
    public function create() {

        $data = ['error' => null, 'input' => []];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $title       = trim($_POST['title'] ?? '');
            $body        = $_POST['body'] ?? '';
            $start_date  = $_POST['start_date'] ?? null;
            $end_date    = $_POST['end_date'] ?? null;
            $location    = trim($_POST['location'] ?? '');
            $is_published = isset($_POST['is_published']) ? 1 : 0;

            $data['input'] = $_POST;

            if ($title === '' || $start_date === null || $location === '') {
                $data['error'] = 'Judul, tanggal mulai, dan lokasi wajib diisi.';
            } elseif ($end_date && strtotime($start_date) > strtotime($end_date)) {
                $data['error'] = 'Tanggal mulai tidak boleh setelah tanggal selesai.';
            } else {

                $slug = $this->generateSlug($title);
                $image_filename = null;

                // UPLOAD IMAGE
                if (!empty($_FILES['image']['name'])) {

                    $allowed = ['jpg','jpeg','png','webp'];
                    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));

                    if (!in_array($ext, $allowed)) {
                        $data['error'] = 'Format gambar tidak valid.';
                    } else {
                        $upload_dir = ROOT_PATH . 'public/assets/images/events/';
                        $image_filename = $slug . '-' . time() . '.' . $ext;

                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_filename)) {
                            $data['error'] = 'Gagal upload gambar.';
                        }
                    }
                }

                if (!$data['error']) {
                    $event_data = [
                        'title' => $title,
                        'slug' => $slug,
                        'body' => $body,
                        'image' => $image_filename,
                        'is_published' => $is_published,
                        'start_date' => $start_date,
                        'end_date' => $end_date,
                        'location' => $location
                    ];

                    if ($this->eventModel->createEvent($event_data)) {
                        $this->redirectWithStatus('success_insert');
                    }

                    $data['error'] = 'Gagal menyimpan data.';
                }
            }
        }

        $page_title = 'Tambah Acara Baru';
        $current_page_nav = 'events';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/events/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // =========================================================
    // EDIT
    // =========================================================
    public function edit($id) {

        $id = (int)$id;
        $data = ['error' => null];
        $event = $this->eventModel->getEventByIdAdmin($id);

        if (!$event) {
            http_response_code(404);
            exit('Event tidak ditemukan');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $title = trim($_POST['title'] ?? '');
            $body  = $_POST['body'] ?? '';
            $start_date = $_POST['start_date'] ?? null;
            $end_date = $_POST['end_date'] ?? null;
            $location = trim($_POST['location'] ?? '');
            $is_published = isset($_POST['is_published']) ? 1 : 0;

            if ($title === '' || $start_date === null || $location === '') {
                $data['error'] = 'Field wajib tidak boleh kosong.';
            } else {

                $slug = $this->generateSlug($title);
                $image_filename = $event['image'];

                if (!empty($_FILES['image']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $upload_dir = ROOT_PATH . 'public/assets/images/events/';
                    $image_filename = $slug . '-' . time() . '.' . $ext;

                    move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $image_filename);

                    if ($event['image'] && file_exists($upload_dir . $event['image'])) {
                        unlink($upload_dir . $event['image']);
                    }
                }

                $update = [
                    'title' => $title,
                    'slug' => $slug,
                    'body' => $body,
                    'image' => $image_filename,
                    'is_published' => $is_published,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'location' => $location
                ];

                if ($this->eventModel->updateEvent($id, $update)) {
                    $this->redirectWithStatus('success_update');
                }

                $data['error'] = 'Gagal update data.';
            }
        }

        $data['event'] = $event;
        $page_title = 'Edit Event';
        $current_page_nav = 'events';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/events/edit.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // =========================================================
    // DELETE
    // =========================================================
    public function delete($id) {

        $id = (int)$id;
        $image = $this->eventModel->getCurrentImageName($id);

        if ($this->eventModel->deleteEvent($id)) {

            if ($image) {
                $path = ROOT_PATH . 'public/assets/images/events/' . $image;
                if (file_exists($path)) unlink($path);
            }

            $this->redirectWithStatus('success_delete');
        }

        $this->redirectWithStatus('fail_delete');
    }

    // =========================================================
    private function generateSlug($text) {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
    }

    private function redirectWithStatus($status) {
        header('Location: ' . BASE_URL . 'admin/events?status=' . $status);
        exit;
    }
}
