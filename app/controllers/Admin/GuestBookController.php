<?php
// app/controllers/admin/GuestbookController.php (Revisi Lengkap - Tanpa is_approved)

require_once ROOT_PATH . 'app/models/GuestbookModel.php';

class GuestbookController {

    private GuestbookModel $guestbookModel;

    public function __construct() {
        $this->guestbookModel = new GuestbookModel();
    }

    // ======================================================
    // HELPER
    // ======================================================
    
    private function redirectWithStatus(string $path, string $status): void {
        header('Location: ' . BASE_URL . $path . '?status=' . urlencode($status));
        exit;
    }

    private function nullIfEmpty(?string $value): ?string {
        $trimmed = trim($value ?? '');
        return $trimmed === '' ? null : $trimmed;
    }

    private function sanitize(string $value): string {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    // ======================================================
    // 1. INDEX - Daftar Entri Buku Tamu
    // ======================================================
    
    public function index(): void {
        $current_page = max(1, (int)($_GET['page'] ?? 1));
        $items_per_page = 10;
        
        $total_items = $this->guestbookModel->getTotalEntryCount();
        $total_pages = max(1, ceil($total_items / $items_per_page));
        $current_page = min($current_page, $total_pages);
        $offset = ($current_page - 1) * $items_per_page;

        $entry_list = $this->guestbookModel->getEntriesPaginated($items_per_page, $offset);
        $status = $_GET['status'] ?? null;

        $page_title = 'Kelola Entri Buku Tamu';
        $current_page_nav = 'guestbook';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/guestbook/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // 2. CREATE - Tambah Entri Baru
    // ======================================================

    public function create(): void {
        $error = null;
        $input = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input['name']        = $this->sanitize($_POST['name'] ?? '');
            $input['institution'] = $this->nullIfEmpty($_POST['institution'] ?? '');
            $input['email']       = $this->nullIfEmpty($_POST['email'] ?? '');
            $input['phone']       = $this->nullIfEmpty($_POST['phone'] ?? '');
            $input['category']    = $this->sanitize($_POST['category'] ?? '');
            $input['message']     = $_POST['message'] ?? '';

            if (empty($input['name']) || empty($input['category']) || empty($input['message'])) {
                $error = "Nama, Kategori, dan Pesan harus diisi.";
            } else {
                if ($this->guestbookModel->createGuestbookEntry($input)) {
                    $this->redirectWithStatus('admin/guestbook', 'success_insert');
                } else {
                    $error = "Gagal menyimpan entri buku tamu ke database.";
                }
            }
        }

        $page_title = 'Tambah Entri Buku Tamu Baru';
        $current_page_nav = 'guestbook';
        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/guestbook/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // 3. EDIT - Edit Entri
    // ======================================================

    // app/controllers/admin/GuestbookController.php

public function edit($id) {
    $id = (int)$id;
    $current_entry = $this->guestbookModel->getEntryById($id);

    if (!$current_entry) {
        http_response_code(404);
        echo "404 Entri Buku Tamu ID $id Tidak Ditemukan";
        exit;
    }

    // Jika POST, proses update
    $error = null;
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name        = trim($_POST['name'] ?? '');
        $institution = trim($_POST['institution'] ?? '');
        $email       = trim($_POST['email'] ?? '');
        $phone       = trim($_POST['phone'] ?? '');
        $category    = trim($_POST['category'] ?? '');
        $message     = trim($_POST['message'] ?? '');

        if (empty($name) || empty($category) || empty($message)) {
            $error = "Nama, Kategori, dan Pesan harus diisi.";
        } else {
            $entry_data = [
                'name' => $name,
                'institution' => $institution ?: null,
                'email' => $email ?: null,
                'phone' => $phone ?: null,
                'category' => $category,
                'message' => $message
            ];

            if ($this->guestbookModel->updateGuestbookEntry($id, $entry_data)) {
                header('Location: ' . BASE_URL . 'admin/guestbook?status=success_update');
                exit;
            } else {
                $error = "Gagal memperbarui data di database.";
            }
        }

        // Pastikan data lama tetap ada di form jika gagal
        $current_entry = array_merge($current_entry, $_POST);
    }

    $entry = $current_entry; // variabel untuk view
    include ROOT_PATH . 'views/layouts/header_admin.php';
    include ROOT_PATH . 'views/layouts/sidebar.php';
    include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
    include ROOT_PATH . 'views/admin/guestbook/edit.php';
    include ROOT_PATH . 'views/layouts/admin_footer.php';
}


    // ======================================================
    // 4. DELETE - Hapus Entri
    // ======================================================

    public function delete(int $id): void {
        $id = (int)$id;
        if ($this->guestbookModel->deleteEntry($id)) {
            $this->redirectWithStatus('admin/guestbook', 'success_delete');
        } else {
            $this->redirectWithStatus('admin/guestbook', 'fail_delete');
        }
    }
}
