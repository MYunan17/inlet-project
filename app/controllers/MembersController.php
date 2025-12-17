<?php

require_once ROOT_PATH . 'app/models/MembersModel.php';

class MembersController {

    private $members_per_page = 3;

    public function index() {
        $membersModel = new MembersModel();

        // Ambil semua anggota
        $all_members = $membersModel->getAllTeamMembers();
        $total_members = count($all_members);

        // Pagination
        $total_pages = max(1, ceil($total_members / $this->members_per_page));
        $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
        $current_page = max(1, min($current_page, $total_pages));
        $offset = ($current_page - 1) * $this->members_per_page;

        $members_data_paginated = array_slice($all_members, $offset, $this->members_per_page);

        $data = [
            'anggota_data' => $members_data_paginated,
            'current_page' => $current_page,
            'total_pages' => $total_pages
        ];

        extract($data);

        include '../views/layouts/header.php';
        include '../views/layouts/navbar.php';
        include '../views/public/members.php';
        include '../views/layouts/footer.php';
    }
}
