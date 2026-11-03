<?php

class AbsensiController {
    public function index() {
        include ROOT_PATH . 'views/layouts/header.php';
        include ROOT_PATH . 'views/layouts/navbar.php';
        include ROOT_PATH . 'views/public/absensi.php';
        include ROOT_PATH . 'views/layouts/footer.php';
    }
}