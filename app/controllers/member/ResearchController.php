<?php
require_once ROOT_PATH . 'app/models/ResearchOutputsModel.php';

class ResearchController {

    private ResearchOutputsModel $model;
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
        $this->model = new ResearchOutputsModel();

        $this->upload_dir = ROOT_PATH . 'public/assets/images/research/';
        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }
    }

    // ===============================================================
    // INDEX MEMBER
    // ===============================================================
    public function index() {
        $current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $items_per_page = 10;

        $search_query = trim($_GET['search'] ?? '');
        $offset = ($current_page - 1) * $items_per_page;

        // Ambil data milik member
        $result = $this->model->getByMemberPaginated(
            $this->member_id,
            $search_query,
            $items_per_page,
            $offset
        );

        $list = $result['data'];
        $total_items = $result['total'];
        $total_pages = max(1, ceil($total_items / $items_per_page));

        $current_menu = 'research';
        $page_title = 'Research Outputs Saya';
        $status_message_html = $this->getStatusMessage($_GET['status'] ?? null);

        include ROOT_PATH . 'views/layouts/header_member.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/member_wrapper_start.php';
        include ROOT_PATH . 'views/member/research_outputs/index.php';
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
            $description = $_POST['description'] ?? '';
            $year = $_POST['year'] ?? '';
            $pdf_url = null;
            $thumbnail_url = null;

            $old_data = $_POST;

            if (!$title || !$year) {
                $error = 'Judul dan tahun wajib diisi.';
            } else {
                // Upload PDF
                if (!empty($_FILES['pdf']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['pdf']['name'], PATHINFO_EXTENSION));
                    $pdf_url = $this->generateSlug($title) . '-' . time() . '.' . $ext;

                    if (!move_uploaded_file($_FILES['pdf']['tmp_name'], $this->upload_dir . $pdf_url)) {
                        $error = 'Gagal upload PDF.';
                        $pdf_url = null;
                    }
                }

                // Upload Thumbnail
                if (!empty($_FILES['thumbnail']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
                    $thumbnail_url = $this->generateSlug($title) . '-thumb-' . time() . '.' . $ext;

                    if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $this->upload_dir . $thumbnail_url)) {
                        $error = 'Gagal upload thumbnail.';
                        $thumbnail_url = null;
                    }
                }

                if (!$error) {
                    if ($this->model->create([
                        'title' => $title,
                        'description' => $description,
                        'year' => $year,
                        'pdf_url' => $pdf_url,
                        'thumbnail_url' => $thumbnail_url,
                        'member_id' => $this->member_id
                    ])) {
                        $this->redirectWithStatus('member/research', 'success_insert');
                    }
                    $error = 'Gagal menyimpan data.';
                }
            }
        }

        $current_menu = 'research';
        $page_title = 'Tambah Research Output';
        include ROOT_PATH . 'views/layouts/header_member.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/member_wrapper_start.php';
        include ROOT_PATH . 'views/member/research_outputs/create.php';
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ambil data dari form
        $title = trim($_POST['title']);
        $description = $_POST['description'] ?? '';
        $year = $_POST['year'];
        $pdf_url = trim($_POST['pdf_url'] ?? ''); // <-- ambil dari input URL
        $thumbnail_url = $item['thumbnail_url'];

        // Upload thumbnail baru jika ada
        if (!empty($_FILES['thumbnail']['name'])) {
            $ext = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
            $thumbnail_url = $this->generateSlug($title) . '-thumb-' . time() . '.' . $ext;
            move_uploaded_file($_FILES['thumbnail']['tmp_name'], $this->upload_dir . $thumbnail_url);
        }

        $this->model->update($id, [
            'title' => $title,
            'description' => $description,
            'year' => $year,
            'pdf_url' => $pdf_url,
            'thumbnail_url' => $thumbnail_url,
            'member_id' => $this->member_id
        ]);

        $this->redirectWithStatus('member/research', 'success_update');
    }

    $current_menu = 'research';
    $page_title = 'Edit Research Output';
    include ROOT_PATH . 'views/layouts/header_member.php';
    include ROOT_PATH . 'views/layouts/sidebar.php';
    include ROOT_PATH . 'views/layouts/member_wrapper_start.php';
    include ROOT_PATH . 'views/member/research_outputs/edit.php';
    include ROOT_PATH . 'views/layouts/member_footer.php';
}


    // ===============================================================
    // DELETE MEMBER
    // ===============================================================
    public function delete($id) {
        if ($this->model->deleteByMember((int)$id, $this->member_id)) {
            $this->redirectWithStatus('member/research', 'success_delete');
        }
        $this->redirectWithStatus('member/research', 'fail_delete');
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
            'success_insert' => '<div class="alert alert-success">Research Output berhasil ditambahkan.</div>',
            'success_update' => '<div class="alert alert-success">Research Output berhasil diperbarui.</div>',
            'success_delete' => '<div class="alert alert-danger">Research Output berhasil dihapus.</div>',
            default => ''
        };
    }

    private function generateSlug(string $text): string {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
    }
}
