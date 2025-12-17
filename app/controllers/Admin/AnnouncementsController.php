<?php
// app/controllers/admin/AnnouncementsController.php

require_once ROOT_PATH . 'app/models/AnnouncementModel.php';

class AnnouncementsController {

    private $announcementModel;

    public function __construct() {
        $this->announcementModel = new AnnouncementModel();
    }

    private function redirectWithStatus($path, $status) {
        header('Location: ' . BASE_URL . $path . '?status=' . $status);
        exit;
    }

    // ----------------------------------------------------------------------
    // Helper: Upload file attachment (PDF, DOCX, ZIP)
    // ----------------------------------------------------------------------
    private function handleAttachmentUpload($file, $title) {
        $filename = null;

        if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
            $upload_dir = ROOT_PATH . 'public/uploads/attachments/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $safe_title = $this->generateSlug($title);
            $filename = $safe_title . '-' . time() . '.' . $file_ext;
            $target_file = $upload_dir . $filename;

            if (!move_uploaded_file($file['tmp_name'], $target_file)) {
                return ['error' => "Gagal mengunggah file lampiran.", 'filename' => null];
            }
        }

        return ['error' => null, 'filename' => $filename];
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

    // ----------------------------------------------------------------------
    // 1. index() - Daftar pengumuman (Admin)
    // ----------------------------------------------------------------------
    public function index() {
        $current_page = (int)($_GET['page'] ?? 1);
        $items_per_page = 10;

        $total_items = $this->announcementModel->getTotalAnnouncementCount();
        $total_pages = max(1, ceil($total_items / $items_per_page));
        $current_page = max(1, min($current_page, $total_pages));
        $offset = ($current_page - 1) * $items_per_page;

        $announcement_list = $this->announcementModel->getAnnouncementsPaginated($items_per_page, $offset);
        $status = $_GET['status'] ?? null;

        $page_title = 'Kelola Pengumuman';
        $current_page_nav = 'announcements';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/announcements/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 2. create() - Tambah pengumuman baru
    // ----------------------------------------------------------------------
    public function create() {
        $data = ['error' => null, 'input' => []];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $body = $_POST['body'] ?? '';
            $expires_at = trim($_POST['expires_at'] ?? null);
            $data['input'] = $_POST;

            if (empty($title) || empty($body)) {
                $data['error'] = "Judul dan isi pengumuman harus diisi.";
            } else {
                $upload_result = $this->handleAttachmentUpload($_FILES['attachment'], $title);
                if ($upload_result['error']) {
                    $data['error'] = $upload_result['error'];
                }
                $attachment_filename = $upload_result['filename'];

                if (!isset($data['error'])) {
                    $announcement_data = [
                        'title' => $title,
                        'body' => $body,
                        'attachment' => $attachment_filename,
                        'expires_at' => empty($expires_at) ? null : $expires_at
                    ];

                    if ($this->announcementModel->createAnnouncement($announcement_data)) {
                        $this->redirectWithStatus('admin/announcements', 'success_insert');
                    } else {
                        $data['error'] = "Gagal menyimpan pengumuman ke database.";
                    }
                }
            }
        }

        $page_title = 'Tambah Pengumuman Baru';
        $current_page_nav = 'announcements';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/announcements/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 3. edit($id) - Edit pengumuman
    // ----------------------------------------------------------------------
    public function edit($id) {
        $id = (int)$id;
        $data = ['error' => null];

        $current_announcement = $this->announcementModel->getAnnouncementById($id);
        if (!$current_announcement) {
            http_response_code(404);
            exit("Pengumuman ID $id tidak ditemukan.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $body = $_POST['body'] ?? '';
            $expires_at = trim($_POST['expires_at'] ?? null);
            $current_attachment = $_POST['current_attachment'] ?? null;
            $attachment_filename = $current_attachment;

            if (empty($title) || empty($body)) {
                $data['error'] = "Judul dan isi pengumuman harus diisi.";
            } else {
                if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
                    $upload_result = $this->handleAttachmentUpload($_FILES['attachment'], $title);
                    if ($upload_result['error']) {
                        $data['error'] = $upload_result['error'];
                    } else {
                        // Hapus file lama
                        $upload_dir = ROOT_PATH . 'public/uploads/attachments/';
                        if (!empty($current_attachment) && file_exists($upload_dir . $current_attachment)) {
                            unlink($upload_dir . $current_attachment);
                        }
                        $attachment_filename = $upload_result['filename'];
                    }
                }

                if (!isset($data['error'])) {
                    $announcement_data = [
                        'title' => $title,
                        'body' => $body,
                        'attachment' => $attachment_filename,
                        'expires_at' => empty($expires_at) ? null : $expires_at
                    ];

                    if ($this->announcementModel->updateAnnouncement($id, $announcement_data)) {
                        $this->redirectWithStatus('admin/announcements', 'success_update');
                    } else {
                        $data['error'] = "Gagal memperbarui data di database.";
                    }
                }
            }

            $data['announcement'] = array_merge($announcement_data ?? [], ['id' => $id]);
        }

        if (empty($data['announcement'])) {
            $data['announcement'] = $current_announcement;
        }

        $page_title = 'Edit Pengumuman: ' . htmlspecialchars($data['announcement']['title']);
        $current_page_nav = 'announcements';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/announcements/edit.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ----------------------------------------------------------------------
    // 4. delete($id) - Hapus pengumuman
    // ----------------------------------------------------------------------
    public function delete($id) {
        $id = (int)$id;
        $file_to_delete = $this->announcementModel->getCurrentAttachmentName($id);

        if ($this->announcementModel->deleteAnnouncement($id)) {
            if (!empty($file_to_delete)) {
                $file_path = ROOT_PATH . 'public/uploads/attachments/' . $file_to_delete;
                if (file_exists($file_path)) unlink($file_path);
            }
            $this->redirectWithStatus('admin/announcements', 'success_delete');
        } else {
            $this->redirectWithStatus('admin/announcements', 'fail_delete');
        }
    }
}
