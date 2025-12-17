<?php
// app/controllers/admin/ItemsController.php

require_once ROOT_PATH . 'app/models/ItemModel.php';

class ItemsController
{
    private ItemModel $model;

    public function __construct()
    {
        $this->model = new ItemModel();
    }

    // ======================================================
    // UTILITIES
    // ======================================================

    private function redirectWithStatus(string $path, string $status): void
    {
        header('Location: ' . BASE_URL . $path . '?status=' . $status);
        exit;
    }

    private function getStatusMessage(?string $status): string
    {
        return match ($status) {
            'success_insert' => '<div class="alert alert-success">Item berhasil ditambahkan!</div>',
            'success_update' => '<div class="alert alert-success">Item berhasil diperbarui!</div>',
            'success_delete' => '<div class="alert alert-danger">Item berhasil dihapus!</div>',
            'fail_delete'    => '<div class="alert alert-danger">Gagal menghapus item.</div>',
            default => ''
        };
    }

    // ======================================================
    // INDEX
    // ======================================================
    public function index()
    {
        $items_per_page = 10;
        $current_page = (int)($_GET['page'] ?? 1);
        $search_query = trim($_GET['search'] ?? '');

        $total_items = $this->model->countTotalFiltered($search_query);
        $total_pages = max(1, ceil($total_items / $items_per_page));
        $current_page = max(1, min($current_page, $total_pages));
        $offset = ($current_page - 1) * $items_per_page;

        $data = [
            'list' => $this->model->getPaginatedFiltered(
                $items_per_page,
                $offset,
                $search_query
            ),
            'current_page' => $current_page,
            'total_items' => $total_items,
            'total_pages' => $total_pages,
            'items_per_page' => $items_per_page,
            'search_query' => $search_query,
            'status_message_html' => $this->getStatusMessage($_GET['status'] ?? null),
        ];

        $page_title = 'Kelola Items';
        $current_page_nav = 'items';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/items/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // CREATE
    // ======================================================
    public function create()
    {
        $data = ['error' => null, 'old_data' => []];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $record = [
                'name' => trim($_POST['name'] ?? ''),
                'code' => trim($_POST['code'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'total_stock' => (int)($_POST['total_stock'] ?? 0),
            ];

            $data['old_data'] = $record;

            if (
                empty($record['name']) ||
                empty($record['code']) ||
                $record['total_stock'] <= 0
            ) {
                $data['error'] = 'Name, Code, dan Total Stock wajib diisi.';
            } elseif ($this->model->createItem($record)) {
                $this->redirectWithStatus('admin/items', 'success_insert');
            } else {
                $data['error'] = 'Gagal menyimpan data item.';
            }
        }

        $page_title = 'Tambah Item';
        $current_page_nav = 'items';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/items/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // EDIT
    // ======================================================
    public function edit($id)
    {
        $id = (int)$id;
        $data = ['error' => null];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $record = [
                'name' => trim($_POST['name'] ?? ''),
                'code' => trim($_POST['code'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'total_stock' => (int)($_POST['total_stock'] ?? 0),
            ];

            if (
                empty($record['name']) ||
                empty($record['code']) ||
                $record['total_stock'] <= 0
            ) {
                $data['error'] = 'Name, Code, dan Total Stock wajib diisi.';
            } elseif ($this->model->updateItem($id, $record)) {
                $this->redirectWithStatus('admin/items', 'success_update');
            } else {
                $data['error'] = 'Gagal memperbarui item (cek stok tersedia).';
            }

            $data['item'] = array_merge($record, ['id' => $id]);
        }

        if (empty($data['item'])) {
            $data['item'] = $this->model->getItemById($id);
        }

        if (!$data['item']) {
            http_response_code(404);
            echo '404 Item tidak ditemukan';
            exit;
        }

        $page_title = 'Edit Item';
        $current_page_nav = 'items';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/items/edit.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // DELETE
    // ======================================================
    public function delete($id)
    {
        $id = (int)$id;

        $this->redirectWithStatus(
            'admin/items',
            $this->model->delete($id)
                ? 'success_delete'
                : 'fail_delete'
        );
    }
}
