<?php
// app/controllers/member/PublicationsController.php

require_once ROOT_PATH . 'app/models/PublicationModel.php';

class PublicationsController {

    private PublicationModel $model;
    private string $upload_dir;
    private int $member_id;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $this->member_id = (int) $_SESSION['user_id'];
        $this->model = new PublicationModel();

        $this->upload_dir = ROOT_PATH . 'public/assets/files/publications/';
        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }
    }

    // ===============================================================
    // INDEX MEMBER
    // ===============================================================
    public function index() {
        $current_page = max(1, (int)($_GET['page'] ?? 1));
        $items_per_page = 10;
        $search_query = trim($_GET['search'] ?? '');
        $offset = ($current_page - 1) * $items_per_page;

        $result = $this->model->getByMemberPaginated(
            $this->member_id,
            $search_query,
            $items_per_page,
            $offset
        );

        $list = $result['data'];
        $total_items = $result['total'];
        $total_pages = max(1, ceil($total_items / $items_per_page));

        $current_menu = 'publications';
        $page_title = 'Publications Saya';
        $status_message_html = $this->getStatusMessage($_GET['status'] ?? null);

        include ROOT_PATH . 'views/layouts/header_member.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/member_wrapper_start.php';
        include ROOT_PATH . 'views/member/publications/index.php';
        include ROOT_PATH . 'views/layouts/member_footer.php';
    }

    // ===============================================================
    // CREATE MEMBER
    // ===============================================================
    public function create() {
        $error = null;
        $old_data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $abstract = trim($_POST['abstract'] ?? '');
            $publication_type = trim($_POST['publication_type'] ?? '');
            $doi = trim($_POST['doi'] ?? '');
            $year = trim($_POST['year'] ?? '');
            $methodology = trim($_POST['methodology'] ?? '');
            $results_conclusion = trim($_POST['results_conclusion'] ?? '');
            $file_url = null;

            $old_data = $_POST;

            // Validasi wajib
            if (!$title || !$publication_type || !$year) {
                $error = 'Title, Publication Type, dan Year wajib diisi.';
            } else {
                // Casting year ke int
                $year_int = (int)$year;
                if ($year_int <= 0) {
                    $error = 'Tahun tidak valid.';
                }

                // Upload file PDF jika ada
                if (!$error && !empty($_FILES['file']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
                    if ($ext !== 'pdf') {
                        $error = 'Hanya file PDF yang diperbolehkan.';
                    } else {
                        $file_url = $this->generateSlug($title) . '-' . time() . '.' . $ext;
                        if (!move_uploaded_file($_FILES['file']['tmp_name'], $this->upload_dir . $file_url)) {
                            $error = 'Gagal upload file PDF.';
                            $file_url = null;
                        }
                    }
                }

                // Simpan ke DB jika tidak ada error
                if (!$error) {
                    $data = [
                        'title' => $title,
                        'abstract' => $abstract,
                        'publication_type' => $publication_type,
                        'year' => $year_int,
                        'doi' => $doi,
                        'file_url' => $file_url,
                        'methodology' => $methodology,
                        'results_conclusion' => $results_conclusion,
                        'member_id' => $this->member_id
                    ];

                    if ($this->model->create($data)) {
                        $this->redirectWithStatus('member/publications', 'success_insert');
                    } else {
                        $error = 'Gagal menyimpan data.';
                    }
                }
            }
        }

        $current_menu = 'publications';
        $page_title = 'Tambah Publication';

        include ROOT_PATH . 'views/layouts/header_member.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/member_wrapper_start.php';
        include ROOT_PATH . 'views/member/publications/create.php';
        include ROOT_PATH . 'views/layouts/member_footer.php';
    }

    // ===============================================================
    // EDIT MEMBER
    // ===============================================================
    public function edit($id) {
        $id = (int)$id;
        $item = $this->model->getByIdAndMember($id, $this->member_id);

        if (!$item) {
            http_response_code(403);
            exit('Akses ditolak.');
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $abstract = trim($_POST['abstract'] ?? '');
            $publication_type = trim($_POST['publication_type'] ?? '');
            $doi = trim($_POST['doi'] ?? '');
            $year = trim($_POST['year'] ?? '');
            $methodology = trim($_POST['methodology'] ?? '');
            $results_conclusion = trim($_POST['results_conclusion'] ?? '');
            $file_url = $item['file_url'];

            // Casting year ke int
            $year_int = (int)$year;
            if (!$title || !$publication_type || $year_int <= 0) {
                $error = 'Title, Publication Type, dan Year wajib diisi.';
            }

            // Upload file baru jika ada
            if (!$error && !empty($_FILES['file']['name'])) {
                $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
                if ($ext !== 'pdf') {
                    $error = 'Hanya file PDF yang diperbolehkan.';
                } else {
                    $file_url = $this->generateSlug($title) . '-' . time() . '.' . $ext;
                    move_uploaded_file($_FILES['file']['tmp_name'], $this->upload_dir . $file_url);
                }
            }

            // Update DB jika tidak ada error
            if (!$error) {
                $data = [
                    'title' => $title,
                    'abstract' => $abstract,
                    'publication_type' => $publication_type,
                    'year' => $year_int,
                    'doi' => $doi,
                    'file_url' => $file_url,
                    'methodology' => $methodology,
                    'results_conclusion' => $results_conclusion,
                    'member_id' => $this->member_id
                ];

                // var_dump($data);die;

                $this->model->update($id, $data);
                $this->redirectWithStatus('member/publications', 'success_update');
            }
        }

        $current_menu = 'publications';
        $page_title = 'Edit Publication';

        include ROOT_PATH . 'views/layouts/header_member.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/member_wrapper_start.php';
        include ROOT_PATH . 'views/member/publications/edit.php';
        include ROOT_PATH . 'views/layouts/member_footer.php';
    }

    // ===============================================================
    // DELETE MEMBER
    // ===============================================================
    public function delete($id) {
        if ($this->model->deleteByMember((int)$id, $this->member_id)) {
            $this->redirectWithStatus('member/publications', 'success_delete');
        } else {
            $this->redirectWithStatus('member/publications', 'fail_delete');
        }
    }

    // ===============================================================
    // HELPERS
    // ===============================================================
    private function redirectWithStatus(string $path, string $status): void {
        header('Location: ' . BASE_URL . $path . '?status=' . urlencode($status));
        exit;
    }

    private function getStatusMessage(?string $status): string {
        return match ($status) {
            'success_insert' => '<div class="alert alert-success">Publication berhasil ditambahkan.</div>',
            'success_update' => '<div class="alert alert-success">Publication berhasil diperbarui.</div>',
            'success_delete' => '<div class="alert alert-danger">Publication berhasil dihapus.</div>',
            default => ''
        };
    }

    private function generateSlug(string $text): string {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
    }
}
