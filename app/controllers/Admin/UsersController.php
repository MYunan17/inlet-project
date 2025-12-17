<?php
// app/controllers/admin/UsersController.php

require_once ROOT_PATH . 'app/models/UsersModel.php';

class UsersController {

    private UsersModel $usersModel;

    public function __construct() {
        $this->usersModel = new UsersModel();
    }

    private function redirectWithStatus(string $path, string $status): void {
        header('Location: ' . BASE_URL . $path . '?status=' . urlencode($status));
        exit;
    }

    private function sanitize(string $value): string {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    private function getStatusMessage($status) {
        switch ($status) {
            case 'success_insert': return '<div class="alert alert-success">User berhasil ditambahkan!</div>';
            case 'success_update': return '<div class="alert alert-success">User berhasil diperbarui!</div>';
            case 'success_delete': return '<div class="alert alert-danger">User berhasil dihapus!</div>';
            case 'fail_delete': return '<div class="alert alert-danger">Gagal menghapus user.</div>';
            default: return '';
        }
    }

    // ----------------------------------------------------------------------
    // 1. index() - Daftar User + Pagination + Search
    // ----------------------------------------------------------------------
    public function index() {
        $items_per_page = 10;
        $current_page = max(1, (int)($_GET['page'] ?? 1));
        $search_query = trim($_GET['search'] ?? '');

        $total_items = $this->usersModel->countTotalUsersFiltered($search_query);
        $total_pages = max(1, ceil($total_items / $items_per_page));
        $current_page = min($current_page, $total_pages);
        $offset = ($current_page - 1) * $items_per_page;

        $users_list = $this->usersModel->getUsersPaginatedFiltered($items_per_page, $offset, $search_query);

        $status_message_html = $this->getStatusMessage($_GET['status'] ?? null);

        $page_title = 'Kelola Users';
        $current_page_nav = 'users';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/users/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 2. create() - Tambah User Baru
    // ----------------------------------------------------------------------
    public function create() {
        $error = null;
        $old_data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old_data['name'] = $this->sanitize($_POST['name'] ?? '');
            $old_data['username'] = $this->sanitize($_POST['username'] ?? '');
            $old_data['email'] = $this->sanitize($_POST['email'] ?? '');
            $old_data['password'] = $_POST['password'] ?? '';
            $old_data['role'] = $_POST['role'] ?? 'user';
            $old_data['is_active'] = isset($_POST['is_active']) ? 1 : 0;
            $old_data['personal_id'] = $this->sanitize($_POST['personal_id'] ?? '');

            if (empty($old_data['name']) || empty($old_data['username']) || empty($old_data['email']) || empty($old_data['password'])) {
                $error = "Nama, username, email, dan password wajib diisi.";
            } else {
                if ($this->usersModel->createUser($old_data)) {
                    $this->redirectWithStatus('admin/users', 'success_insert');
                } else {
                    $error = "Gagal menyimpan user ke database.";
                }
            }
        }

        $page_title = 'Tambah User Baru';
        $current_page_nav = 'users';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/users/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 3. edit($id) - Edit User
    // ----------------------------------------------------------------------
    public function edit($id) {
        $id = (int)$id;
        $user = $this->usersModel->getUserById($id);
        if (!$user) {
            http_response_code(404);
            echo "404 User ID $id tidak ditemukan.";
            exit;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input['name'] = $this->sanitize($_POST['name'] ?? '');
            $input['username'] = $this->sanitize($_POST['username'] ?? '');
            $input['email'] = $this->sanitize($_POST['email'] ?? '');
            $input['password'] = $_POST['password'] ?? '';
            $input['role'] = $_POST['role'] ?? 'user';
            $input['is_active'] = isset($_POST['is_active']) ? 1 : 0;
            $input['personal_id'] = $this->sanitize($_POST['personal_id'] ?? '');

            if (empty($input['name']) || empty($input['username']) || empty($input['email'])) {
                $error = "Nama, username, dan email wajib diisi.";
            } else {
                if ($this->usersModel->updateUser($id, $input)) {
                    $this->redirectWithStatus('admin/users', 'success_update');
                } else {
                    $error = "Gagal memperbarui data user.";
                }
            }

            $user = array_merge($user, $_POST);
        }

        $old_data = $user;
        $page_title = 'Edit User: ' . htmlspecialchars($user['username']);
        $current_page_nav = 'users';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/users/edit.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 4. delete($id) - Hapus User
    // ----------------------------------------------------------------------
    public function delete($id) {
        $id = (int)$id;
        if ($this->usersModel->deleteUser($id)) {
            $this->redirectWithStatus('admin/users', 'success_delete');
        } else {
            $this->redirectWithStatus('admin/users', 'fail_delete');
        }
    }
}
