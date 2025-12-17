<?php
require_once ROOT_PATH . 'app/models/WorkshopsModel.php';

class WorkshopsController {

    private WorkshopsModel $workshopsModel;
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
        $this->workshopsModel = new WorkshopsModel();

        $this->upload_dir = ROOT_PATH . 'public/assets/images/workshops/';
        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }
    }

    // ===============================================================
    // INDEX
    // ===============================================================
    public function index() {
        $current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $items_per_page = 10;
        $search_query = trim($_GET['search'] ?? '');
        $filter_date = $_GET['date'] ?? '';
        $offset = ($current_page - 1) * $items_per_page;

        // Ambil data workshop milik member dengan filter
        $result = $this->workshopsModel->getWorkshopsByMemberFiltered(
            member_id: $this->member_id,
            search: $search_query,
            date: $filter_date,
            limit: $items_per_page,
            offset: $offset
        );

        $list = $result['data'];
        $total_items = $result['total'];
        $total_pages = max(1, ceil($total_items / $items_per_page));

        // ===========================================================
        $current_menu = 'workshops';
        $page_title = 'Workshop Saya';
        $status_message_html = $this->getStatusMessage($_GET['status'] ?? null);

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/member/workshops/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ===============================================================
    // CREATE
    // ===============================================================
    public function create() {
        $error = null;
        $old_data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $description = $_POST['description'] ?? '';
            $date = $_POST['date'] ?? '';
            $location = trim($_POST['location'] ?? '');
            $materials_url = trim($_POST['materials_url'] ?? '');
            $old_data = $_POST;

            if (!$title || !$date || !$location) {
                $error = 'Judul, tanggal, dan lokasi wajib diisi.';
            } else {
                $poster_url = null;
                if (!empty($_FILES['poster']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
                    $poster_url = $this->generateSlug($title) . '-' . time() . '.' . $ext;

                    if (!move_uploaded_file($_FILES['poster']['tmp_name'], $this->upload_dir . $poster_url)) {
                        $error = 'Gagal upload poster.';
                        $poster_url = null;
                    }
                }

                if (!$error) {
                    if ($this->workshopsModel->createWorkshop([
                        'title' => $title,
                        'description' => $description,
                        'date' => $date,
                        'location' => $location,
                        'materials_url' => $materials_url,
                        'poster_url' => $poster_url,
                        'member_id' => $this->member_id
                    ])) {
                        $this->redirectWithStatus('member/workshops', 'success_insert');
                    }

                    $error = 'Gagal menyimpan data.';
                }
            }
        }

        $current_menu = 'workshops';
        $page_title = 'Tambah Workshop';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/member/workshops/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ===============================================================
    // EDIT
    // ===============================================================
    public function edit($id) {
        $id = (int)$id;

        $workshop = $this->workshopsModel->getWorkshopByIdAndMember($id, $this->member_id);

        if (!$workshop) {
            http_response_code(403);
            exit('Akses ditolak.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $poster_url = $workshop['poster_url'];

            if (!empty($_FILES['poster']['name'])) {
                $ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
                $poster_url = $this->generateSlug($_POST['title']) . '-' . time() . '.' . $ext;

                move_uploaded_file($_FILES['poster']['tmp_name'], $this->upload_dir . $poster_url);
            }

            $this->workshopsModel->updateWorkshop($id, [
                'title' => trim($_POST['title']),
                'description' => $_POST['description'] ?? '',
                'date' => $_POST['date'],
                'location' => trim($_POST['location']),
                'materials_url' => trim($_POST['materials_url'] ?? ''),
                'poster_url' => $poster_url,
                'member_id' => $this->member_id
            ]);

            $this->redirectWithStatus('member/workshops', 'success_update');
        }

        $current_menu = 'workshops';
        $page_title = 'Edit Workshop';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/member/workshops/edit.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ===============================================================
    // DELETE
    // ===============================================================
    public function delete($id) {
        if ($this->workshopsModel->deleteWorkshopByMember((int)$id, $this->member_id)) {
            $this->redirectWithStatus('member/workshops', 'success_delete');
        }

        $this->redirectWithStatus('member/workshops', 'fail_delete');
    }

    // ===============================================================
    // HELPER
    // ===============================================================
    private function redirectWithStatus(string $path, string $status): void {
        header('Location: ' . BASE_URL . $path . '?status=' . urlencode($status));
        exit;
    }

    private function getStatusMessage(?string $status): string {
        return match ($status) {
            'success_insert' => '<div class="alert alert-success">Workshop berhasil ditambahkan.</div>',
            'success_update' => '<div class="alert alert-success">Workshop berhasil diperbarui.</div>',
            'success_delete' => '<div class="alert alert-danger">Workshop berhasil dihapus.</div>',
            default => ''
        };
    }

    private function generateSlug(string $text): string {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
    }
}
