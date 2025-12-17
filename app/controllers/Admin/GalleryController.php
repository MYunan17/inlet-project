<?php
// app/controllers/admin/GalleryController.php

require_once ROOT_PATH . 'app/models/GalleryModel.php';

class GalleryController {

    private $galleryModel;
    private $allowed_file_types = ['jpg','jpeg','png','gif','mp4','webm','ogg'];
    private $allowed_thumbnail_types = ['jpg','jpeg','png','gif'];

    public function __construct() {
        $this->galleryModel = new GalleryModel();
    }

    private function redirectWithStatus($path, $status) {
        header('Location: ' . BASE_URL . $path . '?status=' . $status);
        exit;
    }

    // ----------------------------------------------------------------------
    // Helper: Upload file (media atau thumbnail)
    // ----------------------------------------------------------------------
    private function handleFileUpload($file, $prefix, $allowed_types, $upload_dir) {
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['error'=>null,'filename'=>null];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_types)) {
            return ['error'=>"File type .$ext tidak diizinkan.", 'filename'=>null];
        }

        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $safe_name = preg_replace('~[^\pL\d]+~u','-',$prefix);
        $safe_name = strtolower(trim($safe_name,'-'));
        $filename = $safe_name.'-'.time().'.'.$ext;
        $target = $upload_dir.$filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return ['error'=>"Gagal mengunggah file.", 'filename'=>null];
        }

        return ['error'=>null,'filename'=>$filename];
    }

    // ----------------------------------------------------------------------
    // 1. index() - Daftar media galeri (Admin)
    // ----------------------------------------------------------------------
    public function index() {
        $items_per_page = 10;
        $current_page = (int)($_GET['page'] ?? 1);

        $total_items = $this->galleryModel->countTotalMedia();
        $total_pages = max(1, ceil($total_items / $items_per_page));
        $current_page = max(1, min($current_page, $total_pages));
        $offset = ($current_page - 1) * $items_per_page;

        $gallery_list = $this->galleryModel->getMediaPaginated($items_per_page, $offset);

        $page_title = 'Kelola Galeri';
        $current_page_nav = 'gallery';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/gallery/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 2. create() - Tambah media baru
    // ----------------------------------------------------------------------
    public function create() {
        $data = ['error'=>null, 'input'=>[]];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = $_POST['description'] ?? '';
            $type = $_POST['type'] ?? 'image';
            $is_public = isset($_POST['is_public']) ? 1 : 0;
            $data['input'] = $_POST;

            if (empty($title) || empty($type)) {
                $data['error'] = "Title dan type harus diisi.";
            } else {
                // Upload media
                $media_upload = $this->handleFileUpload($_FILES['file_url'], $title, $this->allowed_file_types, ROOT_PATH.'public/uploads/media/');
                if ($media_upload['error']) $data['error'] = $media_upload['error'];
                $file_url = $media_upload['filename'];

                // Upload thumbnail
                $thumb_upload = $this->handleFileUpload($_FILES['thumbnail_url'], $title.'-thumb', $this->allowed_thumbnail_types, ROOT_PATH.'public/uploads/media/thumbs/');
                if ($thumb_upload['error']) $data['error'] = $thumb_upload['error'];
                $thumbnail_url = $thumb_upload['filename'];

                if (!isset($data['error'])) {
                    $insert_data = [
                        'type' => $type,
                        'title' => $title,
                        'description' => $description,
                        'file_url' => $file_url,
                        'thumbnail_url' => $thumbnail_url,
                        'is_public' => $is_public,
                        'event_id' => null
                    ];

                    if ($this->galleryModel->createMedia($insert_data)) {
                        $this->redirectWithStatus('admin/gallery', 'success_insert');
                    } else {
                        $data['error'] = "Gagal menyimpan data ke database.";
                    }
                }
            }
        }

        $page_title = 'Tambah Media Galeri';
        $current_page_nav = 'gallery';
        extract($data);
        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/gallery/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 3. edit($id) - Edit media galeri
    // ----------------------------------------------------------------------
    public function edit($id) {
        $id = (int)$id;
        if ($id <= 0) {
            $this->redirectWithStatus('admin/gallery','fail_edit');
        }

        $current_media = $this->galleryModel->getMediaByIdAdmin($id);
        if (!$current_media) {
            $this->redirectWithStatus('admin/gallery','fail_edit');
        }

        $data = ['error'=>null, 'media'=>$current_media];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = $_POST['description'] ?? '';
            $type = $_POST['type'] ?? $current_media['type'];
            $is_public = isset($_POST['is_public']) ? 1 : 0;

            $file_url = $current_media['file_url'];
            $thumbnail_url = $current_media['thumbnail_url'];

            // Upload media baru
            if (isset($_FILES['file_url']) && $_FILES['file_url']['error']===UPLOAD_ERR_OK) {
                $upload = $this->handleFileUpload($_FILES['file_url'], $title, $this->allowed_file_types, ROOT_PATH.'public/uploads/media/');
                if ($upload['error']) $data['error']=$upload['error'];
                else {
                    if ($file_url && file_exists(ROOT_PATH.'public/uploads/media/'.$file_url)) unlink(ROOT_PATH.'public/uploads/media/'.$file_url);
                    $file_url = $upload['filename'];
                }
            }

            // Upload thumbnail baru
            if (isset($_FILES['thumbnail_url']) && $_FILES['thumbnail_url']['error']===UPLOAD_ERR_OK) {
                $upload = $this->handleFileUpload($_FILES['thumbnail_url'], $title.'-thumb', $this->allowed_thumbnail_types, ROOT_PATH.'public/uploads/media/thumbs/');
                if ($upload['error']) $data['error']=$upload['error'];
                else {
                    if ($thumbnail_url && file_exists(ROOT_PATH.'public/uploads/media/thumbs/'.$thumbnail_url)) unlink(ROOT_PATH.'public/uploads/media/thumbs/'.$thumbnail_url);
                    $thumbnail_url = $upload['filename'];
                }
            }

            if (!isset($data['error'])) {
                $update_data = [
                    'type' => $type,
                    'title' => $title,
                    'description' => $description,
                    'file_url' => $file_url,
                    'thumbnail_url' => $thumbnail_url,
                    'is_public' => $is_public,
                    'event_id' => null
                ];

                if ($this->galleryModel->updateMedia($id, $update_data)) {
                    $this->redirectWithStatus('admin/gallery','success_update');
                } else {
                    $data['error'] = "Gagal memperbarui data.";
                }
                $data['media'] = array_merge($update_data, ['id'=>$id]);
            }
        }

        $page_title = 'Edit Media: '.htmlspecialchars($data['media']['title']);
        $current_page_nav = 'gallery';
        extract($data);
        include ROOT_PATH.'views/layouts/header_admin.php';
        include ROOT_PATH.'views/layouts/sidebar.php';
        include ROOT_PATH.'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH.'views/admin/gallery/edit.php';
        include ROOT_PATH.'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 4. delete($id) - Hapus media
    // ----------------------------------------------------------------------
    public function delete($id) {
        $id = (int)$id;
        if ($id <= 0) $this->redirectWithStatus('admin/gallery','fail_delete');

        $media = $this->galleryModel->getMediaByIdAdmin($id);
        if (!$media) $this->redirectWithStatus('admin/gallery','fail_delete');

        if ($this->galleryModel->deleteMedia($id)) {
            if ($media['file_url'] && file_exists(ROOT_PATH.'public/uploads/media/'.$media['file_url'])) unlink(ROOT_PATH.'public/uploads/media/'.$media['file_url']);
            if ($media['thumbnail_url'] && file_exists(ROOT_PATH.'public/uploads/media/thumbs/'.$media['thumbnail_url'])) unlink(ROOT_PATH.'public/uploads/media/thumbs/'.$media['thumbnail_url']);
            $this->redirectWithStatus('admin/gallery','success_delete');
        } else {
            $this->redirectWithStatus('admin/gallery','fail_delete');
        }
    }

    // ----------------------------------------------------------------------
    // 5. togglePublic($id) - Admin toggle public/private
    // ----------------------------------------------------------------------
    public function togglePublic($id) {
        $id = (int)$id;
        if ($id <= 0) $this->redirectWithStatus('admin/gallery','fail_toggle');

        $media = $this->galleryModel->getMediaByIdAdmin($id);
        if (!$media) $this->redirectWithStatus('admin/gallery','fail_toggle');

        $new_status = !$media['is_public'];
        if ($this->galleryModel->setPublicStatus($id, $new_status)) {
            $this->redirectWithStatus('admin/gallery','success_toggle');
        } else {
            $this->redirectWithStatus('admin/gallery','fail_toggle');
        }
    }
}
