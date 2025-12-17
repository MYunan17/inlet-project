<?php
// app/controllers/admin/ResearchOutputsController.php

require_once ROOT_PATH . 'app/models/ResearchOutputsModel.php';

class ResearchController {

    private $model;

    public function __construct() {
        $this->model = new ResearchOutputsModel();
    }

    private function redirectWithStatus($path, $status) {
        header('Location: ' . BASE_URL . $path . '?status=' . $status);
        exit;
    }

    private function getStatusMessage($status) {
        return match ($status) {
            'success_insert' => '<div class="alert alert-success">Research Output berhasil ditambahkan!</div>',
            'success_update' => '<div class="alert alert-success">Research Output berhasil diperbarui!</div>',
            'success_delete' => '<div class="alert alert-danger">Research Output berhasil dihapus!</div>',
            'fail_delete'    => '<div class="alert alert-danger">Gagal menghapus Research Output.</div>',
            default => ''
        };
    }

    // ======================================================
    // INDEX
    // ======================================================
    public function index() {
        $items_per_page = 10;
        $current_page = (int)($_GET['page'] ?? 1);
        $search_query = trim($_GET['search'] ?? '');

        $total_items = $this->model->countTotalFiltered($search_query);
        $total_pages = max(1, ceil($total_items / $items_per_page));
        $current_page = max(1, min($current_page, $total_pages));
        $offset = ($current_page - 1) * $items_per_page;

        $data = [
            'list' => $this->model->getPaginatedFiltered($items_per_page, $offset, $search_query),
            'current_page' => $current_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'items_per_page' => $items_per_page,
            'search_query' => $search_query,
            'status_message_html' => $this->getStatusMessage($_GET['status'] ?? null),
        ];

        $page_title = 'Kelola Research Outputs';
        $current_page_nav = 'research_outputs';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/research_outputs/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // CREATE
    // ======================================================
    public function create() {
        $data = ['error' => null, 'old_data' => []];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $record = [
                'title' => trim($_POST['title'] ?? ''),
                'authors' => trim($_POST['authors'] ?? ''),
                'year' => trim($_POST['year'] ?? ''),
                'description' => $_POST['description'] ?? '',
                'pdf_url' => trim($_POST['pdf_url'] ?? ''),
                'thumbnail_url' => trim($_POST['thumbnail_url'] ?? '')
            ];

            $data['old_data'] = $record;

            if (empty($record['title']) || empty($record['authors']) || empty($record['year'])) {
                $data['error'] = 'Judul, Authors, dan Tahun wajib diisi.';
            } elseif ($this->model->create($record)) {
                $this->redirectWithStatus('admin/research', 'success_insert');
            } else {
                $data['error'] = 'Gagal menyimpan data.';
            }
        }

        $page_title = 'Tambah Research Output';
        $current_page_nav = 'research_outputs';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/research_outputs/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // EDIT (INI YANG PALING PENTING)
    // ======================================================
    public function edit($id) {
        $id = (int)$id;
        $data = ['error' => null];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $record = [
                'title' => trim($_POST['title'] ?? ''),
                'authors' => trim($_POST['authors'] ?? ''),
                'year' => trim($_POST['year'] ?? ''),
                'description' => $_POST['description'] ?? '',
                'pdf_url' => trim($_POST['pdf_url'] ?? ''),
                'thumbnail_url' => trim($_POST['thumbnail_url'] ?? '')
            ];

            if (empty($record['title']) || empty($record['authors']) || empty($record['year'])) {
                $data['error'] = 'Judul, Authors, dan Tahun wajib diisi.';
            } elseif ($this->model->update($id, $record)) {
                $this->redirectWithStatus('admin/research', 'success_update');
            } else {
                $data['error'] = 'Gagal memperbarui data.';
            }

            $data['research_output'] = array_merge($record, ['id' => $id]);
        }

        if (empty($data['research_output'])) {
            $data['research_output'] = $this->model->getById($id);
        }

        if (!$data['research_output']) {
            http_response_code(404);
            echo "404 Research Output tidak ditemukan";
            exit;
        }

        $page_title = 'Edit Research Output';
        $current_page_nav = 'research_outputs';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/research_outputs/edit.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // DELETE
    // ======================================================
    public function delete($id) {
        $id = (int)$id;
        $this->redirectWithStatus(
            'admin/research',
            $this->model->delete($id) ? 'success_delete' : 'fail_delete'
        );
    }
}
