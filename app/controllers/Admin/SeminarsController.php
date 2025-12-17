<?php
// app/controllers/admin/SeminarsController.php

require_once ROOT_PATH . 'app/models/SeminarsModel.php';

class SeminarsController {

    private $seminarsModel;
    private $upload_dir = ROOT_PATH . 'public/assets/images/seminars/';

    public function __construct() {
        $this->seminarsModel = new SeminarsModel();
        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }
    }

    private function redirectWithStatus($path, $status) {
        header('Location: ' . BASE_URL . $path . '?status=' . $status);
        exit;
    }

    private function getStatusMessage($status) {
        switch ($status) {
            case 'success_insert':
                return '<div class="alert alert-success">Seminar berhasil ditambahkan!</div>';
            case 'success_update':
                return '<div class="alert alert-success">Seminar berhasil diperbarui!</div>';
            case 'success_delete':
                return '<div class="alert alert-danger">Seminar berhasil dihapus!</div>';
            case 'fail_delete':
                return '<div class="alert alert-danger">Gagal menghapus seminar.</div>';
            default:
                return '';
        }
    }

    // ----------------------------------------------------------------------
    // 1. index() - Daftar Seminar + Pagination + Search + Filter Date
    // ----------------------------------------------------------------------
    public function index() {
        $data = [];
        $items_per_page = 10;
        $current_page = (int)($_GET['page'] ?? 1);

        $search_query = trim($_GET['search'] ?? '');
        $filter_date = $_GET['date'] ?? '';

        $total_items = $this->seminarsModel->countTotalSeminarsFiltered($search_query, $filter_date);
        $total_pages = ceil($total_items / $items_per_page);
        $current_page = max(1, min($current_page, $total_pages));
        $offset = ($current_page - 1) * $items_per_page;

        $data['list'] = $this->seminarsModel->getSeminarsPaginatedFiltered(
            $items_per_page,
            $offset,
            $search_query,
            $filter_date
        );

        $data['current_page'] = $current_page;
        $data['total_items'] = $total_items;
        $data['total_pages'] = $total_pages;
        $data['items_per_page'] = $items_per_page;
        $data['status_message_html'] = $this->getStatusMessage($_GET['status'] ?? null);
        $data['search_query'] = $search_query;
        $data['filter_date'] = $filter_date;

        $page_title = 'Kelola Seminar';
        $current_page_nav = 'seminar';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/seminars/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 2. create() - Tambah Seminar Baru
    // ----------------------------------------------------------------------
    public function create() {
        $data = ['error' => null, 'old_data' => []];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = $_POST['description'] ?? '';
            $speaker = trim($_POST['speaker'] ?? '');
            $date = $_POST['date'] ?? '';
            $location = trim($_POST['location'] ?? '');
            $data['old_data'] = $_POST;

            if (empty($title) || empty($speaker) || empty($date) || empty($location)) {
                $data['error'] = "Judul, pembicara, tanggal, dan lokasi harus diisi.";
            } else {
                $photo_url = null;
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['photo']['tmp_name'];
                    $file_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                    $slug = $this->generateSlug($title);
                    $photo_url = $slug . '-' . time() . '.' . $file_ext;
                    $target_file = $this->upload_dir . $photo_url;
                    if (!move_uploaded_file($file_tmp, $target_file)) {
                        $data['error'] = "Gagal mengunggah foto seminar.";
                        $photo_url = null;
                    }
                }

                if (!isset($data['error'])) {
                    $seminar_data = [
                        'title' => $title,
                        'description' => $description,
                        'speaker' => $speaker,
                        'date' => $date,
                        'location' => $location,
                        'poster_url' => $photo_url
                    ];

                    if ($this->seminarsModel->createSeminar($seminar_data)) {
                        $this->redirectWithStatus('admin/seminars', 'success_insert');
                    } else {
                        if (!empty($photo_url)) $this->deletePhotoFile($photo_url);
                        $data['error'] = "Gagal menyimpan seminar ke database.";
                    }
                }
            }
        }

        $page_title = 'Tambah Seminar Baru';
        $current_page_nav = 'seminar';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/seminars/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 3. edit($id) - Edit Seminar
    // ----------------------------------------------------------------------
    public function edit($id) {
        $data = ['error' => null];
        $id = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = $_POST['description'] ?? '';
            $speaker = trim($_POST['speaker'] ?? '');
            $date = $_POST['date'] ?? '';
            $location = trim($_POST['location'] ?? '');
            $current_photo = $_POST['current_photo'] ?? null;

            if (empty($title) || empty($speaker) || empty($date) || empty($location)) {
                $data['error'] = "Judul, pembicara, tanggal, dan lokasi harus diisi.";
            } else {
                $photo_url = $current_photo;
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['photo']['tmp_name'];
                    $file_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
                    $slug = $this->generateSlug($title);
                    $new_photo_url = $slug . '-' . time() . '.' . $file_ext;
                    $target_file = $this->upload_dir . $new_photo_url;

                    if (move_uploaded_file($file_tmp, $target_file)) {
                        if (!empty($current_photo)) $this->deletePhotoFile($current_photo);
                        $photo_url = $new_photo_url;
                    } else {
                        $data['error'] = "Gagal mengunggah foto baru.";
                    }
                }

                if (!isset($data['error'])) {
                    $seminar_data = [
                        'title' => $title,
                        'description' => $description,
                        'speaker' => $speaker,
                        'date' => $date,
                        'location' => $location,
                        'poster_url' => $photo_url
                    ];

                    if ($this->seminarsModel->updateSeminar($id, $seminar_data)) {
                        $this->redirectWithStatus('admin/seminars', 'success_update');
                    } else {
                        $data['error'] = "Gagal memperbarui seminar di database.";
                    }
                }

                $data['seminar'] = array_merge($seminar_data ?? [], ['id' => $id]);
            }
        }

        if (empty($data['seminar'])) {
            $data['seminar'] = $this->seminarsModel->getSeminarById($id);
        }

        if (!$data['seminar']) {
            http_response_code(404);
            echo "404 Seminar ID $id tidak ditemukan.";
            exit;
        }

        $page_title = 'Edit Seminar: ' . htmlspecialchars($data['seminar']['title']);
        $current_page_nav = 'seminar';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/seminars/edit.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 4. delete($id) - Hapus Seminar
    // ----------------------------------------------------------------------
    public function delete($id) {
        $id = (int)$id;
        $seminar = $this->seminarsModel->getSeminarById($id);
        if (!$seminar) {
            $this->redirectWithStatus('admin/seminars', 'fail_delete');
        }

        $photo_to_delete = $seminar['poster_url'];

        if ($this->seminarsModel->deleteSeminar($id)) {
            if (!empty($photo_to_delete)) $this->deletePhotoFile($photo_to_delete);
            $this->redirectWithStatus('admin/seminars', 'success_delete');
        } else {
            $this->redirectWithStatus('admin/seminars', 'fail_delete');
        }
    }

    // ----------------------------------------------------------------------
    // 5. HELPER: deletePhotoFile() & generateSlug()
    // ----------------------------------------------------------------------
    private function deletePhotoFile($filename) {
        if (empty($filename)) return;
        $file_path = $this->upload_dir . $filename;
        if (file_exists($file_path)) {
            // unlink($file_path); // Aktifkan saat live
        }
    }

    private function generateSlug($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'n-a' : $text;
    }
}
