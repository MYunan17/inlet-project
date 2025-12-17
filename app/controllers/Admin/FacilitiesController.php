<?php
// app/controllers/admin/FacilitiesController.php

require_once ROOT_PATH . 'app/models/LabFacilitiesModel.php'; 

class FacilitiesController { 
    
    private $facilitiesModel; 
    private $upload_dir = ROOT_PATH . 'public/assets/images/lab_facilities/';
    
    public function __construct() {
        $this->facilitiesModel = new LabFacilitiesModel(); 
        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }
    }

    private function redirectWithStatus($path, $status) {
        header('Location: ' . BASE_URL . $path . '?status=' . $status);
        exit;
    }

    private function getStatusMessage($status) {
        switch ($status) {
            case 'success_insert':
                return '<div class="alert alert-success">Item berhasil ditambahkan!</div>';
            case 'success_update':
                return '<div class="alert alert-success">Item berhasil diperbarui!</div>';
            case 'success_delete':
                return '<div class="alert alert-danger">Item berhasil dihapus!</div>';
            case 'fail_delete':
                return '<div class="alert alert-danger">Gagal menghapus item.</div>';
            default:
                return '';
        }
    }

    public function index() { 
        $data = [];
        $items_per_page = 10; 
        $current_page = (int)($_GET['page'] ?? 1); 
        
        // Ambil parameter filter & search
        $filter_type = $_GET['filter_type'] ?? '';
        $search_query = trim($_GET['search'] ?? '');
        if (!in_array($filter_type, ['facility', 'equipment'])) {
            $filter_type = '';
        }

        // Pagination
        $total_items = $this->facilitiesModel->getTotalCountFiltered($search_query, $filter_type); 
        $total_pages = max(1, ceil($total_items / $items_per_page));
        $current_page = max(1, min($current_page, $total_pages)); 
        $offset = ($current_page - 1) * $items_per_page;

        // Ambil data
        $data['list'] = $this->facilitiesModel->getPaginated($items_per_page, $offset, $search_query, $filter_type);

        // Variabel untuk view
        $data['current_page'] = $current_page;
        $data['total_items'] = $total_items;
        $data['total_pages'] = $total_pages;
        $data['items_per_page'] = $items_per_page;
        $data['status_message_html'] = $this->getStatusMessage($_GET['status'] ?? null); 
        $data['filter_type'] = $filter_type; 
        $data['search_query'] = $search_query;
        
        $page_title = 'Kelola Semua Aset Lab (Fasilitas & Peralatan)';
        $current_page_nav = 'facility'; 

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data); 
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php'; 
        include ROOT_PATH . 'views/admin/lab_facilities/index.php'; 
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    public function create() {
        $data = ['error' => null, 'old_data' => []];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim(htmlspecialchars($_POST['name'] ?? ''));
            $description = $_POST['description'] ?? ''; 
            $type = $_POST['type'] ?? ''; 
            $data['old_data'] = $_POST;
            
            if (empty($name)) {
                $data['error'] = "Nama aset tidak boleh kosong.";
            } elseif (!in_array($type, ['facility', 'equipment'])) {
                 $data['error'] = "Tipe aset tidak valid.";
            } else {
                $photo_url = null;
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['image']['tmp_name'];
                    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $slug = $this->generateSlug($name);
                    $photo_url = $type . '-' . $slug . '-' . time() . '.' . $file_ext;
                    $target_file = $this->upload_dir . $photo_url;
                    if (!move_uploaded_file($file_tmp, $target_file)) {
                        $data['error'] = "Gagal memindahkan file gambar.";
                        $photo_url = null; 
                    }
                }
                
                if (!isset($data['error'])) {
                    $facility_data = [
                        'name' => $name, 
                        'description' => $description, 
                        'photo_url' => $photo_url, 
                        'type' => $type 
                    ];

                    if ($this->facilitiesModel->create($facility_data)) {
                        $this->redirectWithStatus('admin/facilities', 'success_insert');
                    } else {
                        if (!empty($photo_url)) $this->deletePhotoFile($photo_url);
                        $data['error'] = "Gagal menyimpan data ke database.";
                    }
                }
            }
        } 
        
        $page_title = 'Tambah Aset Lab Baru'; 
        $current_page_nav = 'facility'; 
        
        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data); 
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php'; 
        include ROOT_PATH . 'views/admin/lab_facilities/create.php'; 
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    public function edit($id) {
        $data = ['error' => null];
        $id = (int)$id; 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim(htmlspecialchars($_POST['name'] ?? ''));
            $description = $_POST['description'] ?? ''; 
            $current_photo = $_POST['current_photo'] ?? null; 
            $type = $_POST['type'] ?? 'facility'; 
            
            if (empty($name)) {
                $data['error'] = "Nama item tidak boleh kosong.";
            } elseif (!in_array($type, ['facility', 'equipment'])) {
                $data['error'] = "Tipe aset tidak valid.";
            } else {
                $photo_url = $current_photo; 
                
                if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                    $file_tmp = $_FILES['image']['tmp_name'];
                    $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                    $slug = $this->generateSlug($name);
                    $new_photo_url = $type . '-' . $slug . '-' . time() . '.' . $file_ext; 
                    $target_file = $this->upload_dir . $new_photo_url;
                    
                    if (move_uploaded_file($file_tmp, $target_file)) {
                        if (!empty($current_photo)) {
                            $this->deletePhotoFile($current_photo);
                        }
                        $photo_url = $new_photo_url;
                    } else {
                        $data['error'] = "Gagal memindahkan file gambar baru.";
                    }
                }

                if (!isset($data['error'])) {
                    $facility_data = [
                        'name' => $name, 
                        'description' => $description, 
                        'photo_url' => $photo_url, 
                        'type' => $type
                    ];

                    if ($this->facilitiesModel->update($id, $facility_data)) {
                        $this->redirectWithStatus('admin/facilities', 'success_update');
                    } else {
                        $data['error'] = "Gagal memperbarui data di database.";
                    }
                }
                
                $data['facility'] = array_merge($facility_data ?? [], ['id' => $id]);
            }
        } 
        
        if (empty($data['facility'])) {
            $data['facility'] = $this->facilitiesModel->getById($id);
        }

        if (!$data['facility']) {
            http_response_code(404);
            echo "404 Item ID $id Tidak Ditemukan untuk di-Edit.";
            exit;
        }
        
        $page_title = 'Edit ' . ucfirst($data['facility']['type']) . ': ' . htmlspecialchars($data['facility']['name']); 
        $current_page_nav = 'facility'; 

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data); 
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php'; 
        include ROOT_PATH . 'views/admin/lab_facilities/edit.php'; 
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    public function delete($id) {
        $id = (int)$id;
        $item = $this->facilitiesModel->getById($id);
        if (!$item) {
             $this->redirectWithStatus('admin/facilities', 'fail_delete');
        }
        
        $image_to_delete = $item['photo_url'];

        if ($this->facilitiesModel->delete($id)) {
            if (!empty($image_to_delete)) {
                $this->deletePhotoFile($image_to_delete);
            }
            $this->redirectWithStatus('admin/facilities', 'success_delete');
        } else {
            $this->redirectWithStatus('admin/facilities', 'fail_delete');
        }
    }

    private function deletePhotoFile($filename) {
        if (empty($filename)) return;
        $file_path = $this->upload_dir . $filename;
        if (file_exists($file_path)) {
            // unlink($file_path); // Aktifkan saat live
        }
    }

    private function generateSlug($text) {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        return empty($text) ? 'n-a' : $text;
    }

    public function ajaxIndex() {
        $items_per_page = 10; 
        $current_page = (int)($_GET['page'] ?? 1); 
        $filter_type = $_GET['filter_type'] ?? '';
        $search_query = trim($_GET['search'] ?? '');
        
        if (!in_array($filter_type, ['facility', 'equipment'])) {
            $filter_type = '';
        }

        $total_items = $this->facilitiesModel->getTotalCountFiltered($search_query, $filter_type); 
        $total_pages = max(1, ceil($total_items / $items_per_page));
        $current_page = max(1, min($current_page, $total_pages)); 
        $offset = ($current_page - 1) * $items_per_page;

        $data['list'] = $this->facilitiesModel->getPaginated($items_per_page, $offset, $search_query, $filter_type);
        
        $data['current_page'] = $current_page;
        $data['total_items'] = $total_items;
        $data['total_pages'] = $total_pages;
        $data['items_per_page'] = $items_per_page;
        $data['filter_type'] = $filter_type; 
        $data['search_query'] = $search_query;
        $data['status'] = $_GET['status'] ?? null; 

        extract($data);
        include ROOT_PATH . 'views/admin/lab_facilities/table_body.php';
        exit;
    }
}
