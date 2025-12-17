<?php
// app/controllers/admin/PracticumsController.php

require_once ROOT_PATH . 'app/models/PracticumsModel.php';

class PracticumsController {

    private $practicumsModel;

    public function __construct() {
        $this->practicumsModel = new PracticumsModel();
    }

    private function redirectWithStatus($path, $status) {
        header('Location: ' . BASE_URL . $path . '?status=' . $status);
        exit;
    }

    private function getStatusMessage($status) {
        switch ($status) {
            case 'success_insert':
                return '<div class="alert alert-success">Practicum berhasil ditambahkan!</div>';
            case 'success_update':
                return '<div class="alert alert-success">Practicum berhasil diperbarui!</div>';
            case 'success_delete':
                return '<div class="alert alert-danger">Practicum berhasil dihapus!</div>';
            case 'fail_delete':
                return '<div class="alert alert-danger">Gagal menghapus practicum.</div>';
            default:
                return '';
        }
    }

    // ----------------------------------------------------------------------
    // 1. index() - Daftar Practicums + Pagination + Search
    // ----------------------------------------------------------------------
    public function index() {
        $data = [];
        $items_per_page = 10;
        $current_page = (int)($_GET['page'] ?? 1);
        $search_query = trim($_GET['search'] ?? '');

        $total_items = $this->practicumsModel->countTotalPracticumsFiltered($search_query);
        $total_pages = ceil($total_items / $items_per_page);
        $current_page = max(1, min($current_page, $total_pages));
        $offset = ($current_page - 1) * $items_per_page;

        $data['list'] = $this->practicumsModel->getPracticumsPaginatedFiltered(
            $items_per_page,
            $offset,
            $search_query
        );

        $data['current_page'] = $current_page;
        $data['total_items'] = $total_items;
        $data['total_pages'] = $total_pages;
        $data['items_per_page'] = $items_per_page;
        $data['status_message_html'] = $this->getStatusMessage($_GET['status'] ?? null);
        $data['search_query'] = $search_query;

        $page_title = 'Kelola Practicums';
        $current_page_nav = 'practicums';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/practicums/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 2. create() - Tambah Practicum Baru
    // ----------------------------------------------------------------------
    public function create() {
        $data = ['error' => null, 'old_data' => []];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course_name = trim($_POST['course_name'] ?? '');
            $academic_year = trim($_POST['academic_year'] ?? '');
            $semester = trim($_POST['semester'] ?? '');
            $description = $_POST['description'] ?? '';
            $instructor = trim($_POST['instructor'] ?? '');
            $assistant = trim($_POST['assistant'] ?? '');
            $documentation_url = trim($_POST['documentation_url'] ?? '');
            $data['old_data'] = $_POST;

            if (empty($course_name) || empty($academic_year) || empty($semester) || empty($instructor)) {
                $data['error'] = "Nama mata kuliah, tahun akademik, semester, dan instruktur harus diisi.";
            } else {
                $practicum_data = [
                    'course_name' => $course_name,
                    'academic_year' => $academic_year,
                    'semester' => $semester,
                    'description' => $description,
                    'instructor' => $instructor,
                    'assistant' => $assistant,
                    'documentation_url' => $documentation_url
                ];

                if ($this->practicumsModel->createPracticum($practicum_data)) {
                    $this->redirectWithStatus('admin/practicums', 'success_insert');
                } else {
                    $data['error'] = "Gagal menyimpan practicum ke database.";
                }
            }
        }

        $page_title = 'Tambah Practicum Baru';
        $current_page_nav = 'practicums';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/practicums/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 3. edit($id) - Edit Practicum
    // ----------------------------------------------------------------------
    public function edit($id) {
        $data = ['error' => null];
        $id = (int)$id;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $course_name = trim($_POST['course_name'] ?? '');
            $academic_year = trim($_POST['academic_year'] ?? '');
            $semester = trim($_POST['semester'] ?? '');
            $description = $_POST['description'] ?? '';
            $instructor = trim($_POST['instructor'] ?? '');
            $assistant = trim($_POST['assistant'] ?? '');
            $documentation_url = trim($_POST['documentation_url'] ?? '');

            if (empty($course_name) || empty($academic_year) || empty($semester) || empty($instructor)) {
                $data['error'] = "Nama mata kuliah, tahun akademik, semester, dan instruktur harus diisi.";
            } else {
                $practicum_data = [
                    'course_name' => $course_name,
                    'academic_year' => $academic_year,
                    'semester' => $semester,
                    'description' => $description,
                    'instructor' => $instructor,
                    'assistant' => $assistant,
                    'documentation_url' => $documentation_url
                ];

                if ($this->practicumsModel->updatePracticum($id, $practicum_data)) {
                    $this->redirectWithStatus('admin/practicums', 'success_update');
                } else {
                    $data['error'] = "Gagal memperbarui practicum di database.";
                }
            }

            $data['practicum'] = array_merge($practicum_data, ['id' => $id]);
        }

        if (empty($data['practicum'])) {
            $data['practicum'] = $this->practicumsModel->getPracticumById($id);
        }

        if (!$data['practicum']) {
            http_response_code(404);
            echo "404 Practicum ID $id tidak ditemukan.";
            exit;
        }

        $page_title = 'Edit Practicum: ' . htmlspecialchars($data['practicum']['course_name']);
        $current_page_nav = 'practicums';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/practicums/edit.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 4. delete($id) - Hapus Practicum
    // ----------------------------------------------------------------------
    public function delete($id) {
        $id = (int)$id;
        if ($this->practicumsModel->deletePracticum($id)) {
            $this->redirectWithStatus('admin/practicums', 'success_delete');
        } else {
            $this->redirectWithStatus('admin/practicums', 'fail_delete');
        }
    }
}
