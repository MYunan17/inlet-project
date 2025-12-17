<?php
// app/controllers/admin/WorkshopsController.php

require_once ROOT_PATH . 'app/models/WorkshopsModel.php';

class WorkshopsController {

    private $workshopsModel;
    private $upload_dir = ROOT_PATH . 'public/assets/images/workshops/';

    public function __construct() {
        $this->workshopsModel = new WorkshopsModel();
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
                return '<div class="alert alert-success">Workshop berhasil ditambahkan!</div>';
            case 'success_update':
                return '<div class="alert alert-success">Workshop berhasil diperbarui!</div>';
            case 'success_delete':
                return '<div class="alert alert-danger">Workshop berhasil dihapus!</div>';
            case 'fail_delete':
                return '<div class="alert alert-danger">Gagal menghapus workshop.</div>';
            default:
                return '';
        }
    }

    // ----------------------------------------------------------------------
    // 1. index() - Daftar Workshop + Pagination + Search + Filter Date
    // ----------------------------------------------------------------------
    public function index() {
        $data = [];
        $items_per_page = 10;
        $current_page = (int)($_GET['page'] ?? 1);

        $search_query = trim($_GET['search'] ?? '');
        $filter_date = $_GET['date'] ?? '';

        $total_items = $this->workshopsModel->countTotalWorkshopsFiltered($search_query, $filter_date);
        $total_pages = ceil($total_items / $items_per_page);
        $current_page = max(1, min($current_page, $total_pages));
        $offset = ($current_page - 1) * $items_per_page;

        $data['list'] = $this->workshopsModel->getWorkshopsPaginatedFiltered(
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

        $page_title = 'Kelola Workshop';
        $current_page_nav = 'workshop';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/workshops/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 2. create() - Tambah Workshop Baru
    // ----------------------------------------------------------------------
    public function create() {
        $data = ['error' => null, 'old_data' => []];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = $_POST['description'] ?? '';
            $instructor = trim($_POST['instructor'] ?? '');
            $date = $_POST['date'] ?? '';
            $location = trim($_POST['location'] ?? '');
            $materials_url = trim($_POST['materials_url'] ?? '');
            $data['old_data'] = $_POST;

            if (empty($title) || empty($instructor) || empty($date) || empty($location)) {
                $data['error'] = "Judul, instruktur, tanggal, dan lokasi harus diisi.";
            } else {
                $poster_url = null;
                if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['poster']['tmp_name'];
                    $file_ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
                    $slug = $this->generateSlug($title);
                    $poster_url = $slug . '-' . time() . '.' . $file_ext;
                    $target_file = $this->upload_dir . $poster_url;
                    if (!move_uploaded_file($file_tmp, $target_file)) {
                        $data['error'] = "Gagal mengunggah poster workshop.";
                        $poster_url = null;
                    }
                }

                if (!isset($data['error'])) {
                    $workshop_data = [
                        'title' => $title,
                        'description' => $description,
                        'instructor' => $instructor,
                        'date' => $date,
                        'location' => $location,
                        'materials_url' => $materials_url,
                        'poster_url' => $poster_url
                    ];

                    if ($this->workshopsModel->createWorkshop($workshop_data)) {
                        $this->redirectWithStatus('admin/workshops', 'success_insert');
                    } else {
                        if (!empty($poster_url)) $this->deletePosterFile($poster_url);
                        $data['error'] = "Gagal menyimpan workshop ke database.";
                    }
                }
            }
        }

        $page_title = 'Tambah Workshop Baru';
        $current_page_nav = 'workshop';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/workshops/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 3. edit($id) - Edit Workshop
    // ----------------------------------------------------------------------
    public function edit($id) {
        $data = ['error' => null];
        $id = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = $_POST['description'] ?? '';
            $instructor = trim($_POST['instructor'] ?? '');
            $date = $_POST['date'] ?? '';
            $location = trim($_POST['location'] ?? '');
            $materials_url = trim($_POST['materials_url'] ?? '');
            $current_poster = $_POST['current_poster'] ?? null;

            if (empty($title) || empty($instructor) || empty($date) || empty($location)) {
                $data['error'] = "Judul, instruktur, tanggal, dan lokasi harus diisi.";
            } else {
                $poster_url = $current_poster;
                if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['poster']['tmp_name'];
                    $file_ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
                    $slug = $this->generateSlug($title);
                    $new_poster_url = $slug . '-' . time() . '.' . $file_ext;
                    $target_file = $this->upload_dir . $new_poster_url;

                    if (move_uploaded_file($file_tmp, $target_file)) {
                        if (!empty($current_poster)) $this->deletePosterFile($current_poster);
                        $poster_url = $new_poster_url;
                    } else {
                        $data['error'] = "Gagal mengunggah poster baru.";
                    }
                }

                if (!isset($data['error'])) {
                    $workshop_data = [
                        'title' => $title,
                        'description' => $description,
                        'instructor' => $instructor,
                        'date' => $date,
                        'location' => $location,
                        'materials_url' => $materials_url,
                        'poster_url' => $poster_url
                    ];

                    if ($this->workshopsModel->updateWorkshop($id, $workshop_data)) {
                        $this->redirectWithStatus('admin/workshops', 'success_update');
                    } else {
                        $data['error'] = "Gagal memperbarui workshop di database.";
                    }
                }

                $data['workshop'] = array_merge($workshop_data ?? [], ['id' => $id]);
            }
        }

        if (empty($data['workshop'])) {
            $data['workshop'] = $this->workshopsModel->getWorkshopById($id);
        }

        if (!$data['workshop']) {
            http_response_code(404);
            echo "404 Workshop ID $id tidak ditemukan.";
            exit;
        }

        $page_title = 'Edit Workshop: ' . htmlspecialchars($data['workshop']['title']);
        $current_page_nav = 'workshop';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/workshops/edit.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 4. delete($id) - Hapus Workshop
    // ----------------------------------------------------------------------
    public function delete($id) {
        $id = (int)$id;
        $workshop = $this->workshopsModel->getWorkshopById($id);
        if (!$workshop) {
            $this->redirectWithStatus('admin/workshops', 'fail_delete');
        }

        $poster_to_delete = $workshop['poster_url'];

        if ($this->workshopsModel->deleteWorkshop($id)) {
            if (!empty($poster_to_delete)) $this->deletePosterFile($poster_to_delete);
            $this->redirectWithStatus('admin/workshops', 'success_delete');
        } else {
            $this->redirectWithStatus('admin/workshops', 'fail_delete');
        }
    }

    // ----------------------------------------------------------------------
    // 5. HELPER: deletePosterFile() & generateSlug()
    // ----------------------------------------------------------------------
    private function deletePosterFile($filename) {
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
