<?php

class DashboardController {

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }
    }

    public function index() {
        $current_page = 'dashboard';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/member/dashboard.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }
}
