<?php
require_once ROOT_PATH . 'app/models/ContactModel.php';

class ContactController {

    private ContactModel $contactModel;

    public function __construct() {
        $this->contactModel = new ContactModel();
    }

    private function redirectWithStatus(string $path, string $status): void {
        header('Location: ' . BASE_URL . $path . '?status=' . urlencode($status));
        exit;
    }

    private function sanitize(string $value): string {
        return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
    }

    public function index(): void {
        $current_page   = max(1, (int)($_GET['page'] ?? 1));
        $items_per_page = 10;

        $total_items = $this->contactModel->getTotalMessages();
        $total_pages = max(1, ceil($total_items / $items_per_page));
        $current_page = min($current_page, $total_pages);
        $offset = ($current_page - 1) * $items_per_page;

        $messages = $this->contactModel->getMessagesPaginated($items_per_page, $offset);
        $status = $_GET['status'] ?? null;

        $page_title = 'Daftar Pesan Kontak';
        $current_page_nav = 'contacts';
        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/contacts/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }
}
