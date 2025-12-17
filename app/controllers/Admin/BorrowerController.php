<?php
// app/controllers/admin/BorrowerController.php

require_once ROOT_PATH . 'app/models/BorrowerModel.php';

class BorrowerController {

    private BorrowerModel $borrowerModel;

    public function __construct() {
        $this->borrowerModel = new BorrowerModel();
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
    // 1. INDEX - Daftar Borrowers (dengan pagination)
    // ======================================================
    public function index(): void {
        $current_page   = max(1, (int)($_GET['page'] ?? 1));
        $items_per_page = 10;
        $total_items    = $this->borrowerModel->getTotalBorrowerCount();
        $total_pages    = max(1, ceil($total_items / $items_per_page));
        $current_page   = min($current_page, $total_pages);
        $offset         = ($current_page - 1) * $items_per_page;

        $borrowers_list = $this->borrowerModel->getBorrowersPaginated($items_per_page, $offset);
        $status         = $_GET['status'] ?? null;

        $page_title         = 'Daftar Borrowers Identity';
        $current_page_nav   = 'borrowers';
        $base_pagination_url = BASE_URL . "admin/borrowers";

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/borrowers/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // 2. CREATE - Form tambah borrower
    // ======================================================
    public function create(): void {
        $error = null;
        $old_data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old_data['name'] = $this->sanitize($_POST['name'] ?? '');
            $old_data['identification_type'] = $this->sanitize($_POST['identification_type'] ?? 'NIM');
            $old_data['identification_number'] = $this->sanitize($_POST['identification_number'] ?? '');
            $old_data['phone_number'] = $this->sanitize($_POST['phone_number'] ?? '');

            if (empty($old_data['name']) || empty($old_data['identification_number']) || empty($old_data['phone_number'])) {
                $error = "Nama, Nomor Identifikasi, dan Nomor Telepon wajib diisi.";
            } else {
                $result = $this->borrowerModel->createBorrower($old_data);
                if ($result !== false) {
                    $this->redirectWithStatus('admin/borrowers', 'success_insert');
                } else {
                    $error = "Gagal menyimpan borrower ke database.";
                }
            }
        }

        $page_title = 'Tambah Borrower Identity';
        $current_page_nav = 'borrowers';
        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/borrowers/form.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // 3. EDIT - Form edit borrower
    // ======================================================
    public function edit(int $id): void {
        $borrower = $this->borrowerModel->getBorrowerById($id);

        if (!$borrower) {
            http_response_code(404);
            echo "404 Borrower ID $id Tidak Ditemukan";
            exit;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $input['name'] = $this->sanitize($_POST['name'] ?? '');
            $input['identification_type'] = $this->sanitize($_POST['identification_type'] ?? 'NIM');
            $input['identification_number'] = $this->sanitize($_POST['identification_number'] ?? '');
            $input['phone_number'] = $this->sanitize($_POST['phone_number'] ?? '');

            if (empty($input['name']) || empty($input['identification_number']) || empty($input['phone_number'])) {
                $error = "Nama, Nomor Identifikasi, dan Nomor Telepon wajib diisi.";
            } else {
                if ($this->borrowerModel->updateBorrower($id, $input)) {
                    $this->redirectWithStatus('admin/borrowers', 'success_update');
                } else {
                    $error = "Gagal memperbarui data borrower.";
                }
            }

            // agar form tetap menampilkan input user jika gagal
            $borrower = array_merge($borrower, $_POST);
        }

        $old_data = $borrower;
        $page_title = 'Edit Borrower Identity';
        $current_page_nav = 'borrowers';
        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/borrowers/form.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // 4. DELETE - Hapus borrower
    // ======================================================
    public function delete(int $id): void {
        $this->borrowerModel->deleteBorrower($id);
        $this->redirectWithStatus('admin/borrowers', 'success_delete');
    }
}
