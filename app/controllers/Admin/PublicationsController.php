<?php
// app/controllers/admin/PublicationController.php

require_once ROOT_PATH . 'app/models/PublicationModel.php';

class PublicationsController {

    private $model;

    public function __construct() {
        $this->model = new PublicationModel();
    }

    private function redirectWithStatus($path, $status) {
        header('Location: ' . BASE_URL . $path . '?status=' . $status);
        exit;
    }

    private function getStatusMessage($status) {
        return match ($status) {
            'success_insert' => '<div class="alert alert-success">Publication berhasil ditambahkan!</div>',
            'success_update' => '<div class="alert alert-success">Publication berhasil diperbarui!</div>',
            'success_delete' => '<div class="alert alert-danger">Publication berhasil dihapus!</div>',
            'fail_delete'    => '<div class="alert alert-danger">Gagal menghapus Publication.</div>',
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

    // Hitung total item
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

    $page_title = 'Kelola Publications';
    $current_page_nav = 'publications';

    include ROOT_PATH . 'views/layouts/header_admin.php';
    include ROOT_PATH . 'views/layouts/sidebar.php';
    extract($data);
    include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
    include ROOT_PATH . 'views/admin/publications/index.php';
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
                'abstract' => $_POST['abstract'] ?? '',
                'publication_type' => trim($_POST['publication_type'] ?? ''),
                'authors' => trim($_POST['authors'] ?? ''),
                'year' => trim($_POST['year'] ?? ''),
                'doi' => trim($_POST['doi'] ?? ''),
                'file_url' => trim($_POST['file_url'] ?? '')
            ];

            $data['old_data'] = $record;

            if (
                empty($record['title']) ||
                empty($record['publication_type']) ||
                empty($record['authors']) ||
                empty($record['year'])
            ) {
                $data['error'] = 'Title, Publication Type, Authors, dan Year wajib diisi.';
            } elseif ($this->model->create($record)) {
                $this->redirectWithStatus('admin/publications', 'success_insert');
            } else {
                $data['error'] = 'Gagal menyimpan data Publication.';
            }
        }

        $page_title = 'Tambah Publication';
        $current_page_nav = 'publications';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/publications/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // EDIT
    // ======================================================
    public function edit($id) {
        $id = (int)$id;
        $data = ['error' => null];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $record = [
                'title' => trim($_POST['title'] ?? ''),
                'abstract' => $_POST['abstract'] ?? '',
                'publication_type' => trim($_POST['publication_type'] ?? ''),
                'authors' => trim($_POST['authors'] ?? ''),
                'year' => trim($_POST['year'] ?? ''),
                'doi' => trim($_POST['doi'] ?? ''),
                'file_url' => trim($_POST['file_url'] ?? '')
            ];

            if (
                empty($record['title']) ||
                empty($record['publication_type']) ||
                empty($record['authors']) ||
                empty($record['year'])
            ) {
                $data['error'] = 'Title, Publication Type, Authors, dan Year wajib diisi.';
            } elseif ($this->model->update($id, $record)) {
                $this->redirectWithStatus('admin/publications', 'success_update');
            } else {
                $data['error'] = 'Gagal memperbarui data Publication.';
            }

            $data['publication'] = array_merge($record, ['id' => $id]);
        }

        if (empty($data['publication'])) {
            $data['publication'] = $this->model->getById($id);
        }

        if (!$data['publication']) {
            http_response_code(404);
            echo "404 Publication tidak ditemukan";
            exit;
        }

        $page_title = 'Edit Publication';
        $current_page_nav = 'publications';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/publications/edit.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // DELETE
    // ======================================================
    public function delete($id) {
        $id = (int)$id;
        $this->redirectWithStatus(
            'admin/publications',
            $this->model->delete($id) ? 'success_delete' : 'fail_delete'
        );
    }
}
