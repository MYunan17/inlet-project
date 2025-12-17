<?php
// app/controllers/FacilityController.php

require_once ROOT_PATH . '/app/models/LabFacilitiesModel.php';

class FacilityController {
    private $model;
    private $per_page = 4;

    public function __construct() {
        $this->model = new LabFacilitiesModel();
    }

    public function index() {
        $search_query = $_GET['search'] ?? '';
        $filter_type = $_GET['type'] ?? '';

        $current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

        $total_items = $this->model->getTotalCountFiltered($search_query, $filter_type);
        $total_pages = max(1, ceil($total_items / $this->per_page));
        $current_page = max(1, min($current_page, $total_pages));

        $offset = ($current_page - 1) * $this->per_page;

        $all_items = $this->model->getPaginated($this->per_page, $offset, $search_query, $filter_type);

        // Pisahkan berdasarkan type
        $facilities = array_filter($all_items, fn($item)=>$item['type']==='facility');
        $equipment = array_filter($all_items, fn($item)=>$item['type']==='equipment');

        // Load view
        $data = [
            'facilities'=>$facilities,
            'equipment'=>$equipment,
            'current_page'=>$current_page,
            'total_pages'=>$total_pages,
            'search_query'=>$search_query,
            'filter_type'=>$filter_type
        ];
        extract($data);

        $page_title = 'Fasilitas & Peralatan Lab INLET';
        include ROOT_PATH.'/views/layouts/header.php';
        include ROOT_PATH.'/views/layouts/navbar.php';
        include ROOT_PATH.'/views/public/facility.php';
        include ROOT_PATH.'/views/layouts/footer.php';
    }
}
