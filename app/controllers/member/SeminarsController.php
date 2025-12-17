<?php
require_once ROOT_PATH . 'app/models/SeminarsModel.php';

class SeminarsController {

    private SeminarsModel $seminarsModel;
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
        $this->seminarsModel = new SeminarsModel();

        $this->upload_dir = ROOT_PATH . 'public/assets/images/seminars/';
        if (!is_dir($this->upload_dir)) {
            mkdir($this->upload_dir, 0777, true);
        }
    }

    // ===============================================================
    // INDEX
    // ===============================================================
    // ===============================================================
// INDEX (SEARCH + FILTER + PAGINATION FIX)
// ===============================================================
public function index() {

    // =============================
    // PARAMETER
    // =============================
    $current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $items_per_page = 10;

    $search_query = trim($_GET['search'] ?? '');
    $filter_date  = $_GET['date'] ?? '';

    $offset = ($current_page - 1) * $items_per_page;

    // =============================
    // AMBIL DATA DARI MODEL
    // =============================
    $result = $this->seminarsModel->getSeminarsByMemberFiltered(
        member_id: $this->member_id,
        search: $search_query,
        date: $filter_date,
        limit: $items_per_page,
        offset: $offset
    );

    $list = $result['data'];
    $total_items = $result['total'];

    $total_pages = max(1, ceil($total_items / $items_per_page));

    // =============================
    // VIEW DATA
    // =============================
    $current_menu = 'seminars';
    $page_title = 'Seminar Saya';
    $status_message_html = $this->getStatusMessage($_GET['status'] ?? null);

    // =============================
    // LOAD VIEW
    // =============================
    include ROOT_PATH . 'views/layouts/header_admin.php';
    include ROOT_PATH . 'views/layouts/sidebar.php';
    include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
    include ROOT_PATH . 'views/member/seminars/index.php';
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

            $old_data = $_POST;

            if (!$title || !$date || !$location) {
                $error = 'Judul, tanggal, dan lokasi wajib diisi.';
            } else {
                $poster_url = null;

                if (!empty($_FILES['poster']['name'])) {
                    $ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
                    $poster_url = $this->generateSlug($title) . '-' . time() . '.' . $ext;

                    if (!move_uploaded_file(
                        $_FILES['poster']['tmp_name'],
                        $this->upload_dir . $poster_url
                    )) {
                        $error = 'Gagal upload poster.';
                        $poster_url = null;
                    }
                }

                if (!$error) {
                    if ($this->seminarsModel->createSeminar([
                        'title' => $title,
                        'description' => $description,
                        'date' => $date,
                        'location' => $location,
                        'poster_url' => $poster_url,
                        'member_id' => $this->member_id
                    ])) {
                        $this->redirectWithStatus('member/seminars', 'success_insert');
                    }

                    $error = 'Gagal menyimpan data.';
                }
            }
        }

        $current_page = 1;
        $current_menu = 'seminars';
        $page_title = 'Tambah Seminar';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/member/seminars/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ===============================================================
    // EDIT
    // ===============================================================
    public function edit($id) {
        $id = (int) $id;

        $seminar = $this->seminarsModel
            ->getSeminarByIdAndMember($id, $this->member_id);

        if (!$seminar) {
            http_response_code(403);
            exit('Akses ditolak.');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $poster_url = $seminar['poster_url'];

            if (!empty($_FILES['poster']['name'])) {
                $ext = strtolower(pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION));
                $poster_url = $this->generateSlug($_POST['title']) . '-' . time() . '.' . $ext;

                move_uploaded_file(
                    $_FILES['poster']['tmp_name'],
                    $this->upload_dir . $poster_url
                );
            }

            $this->seminarsModel->updateSeminar($id, [
                'title' => trim($_POST['title']),
                'description' => $_POST['description'] ?? '',
                'date' => $_POST['date'],
                'location' => trim($_POST['location']),
                'poster_url' => $poster_url
            ]);

            $this->redirectWithStatus('member/seminars', 'success_update');
        }

        $current_page = 1;
        $current_menu = 'seminars';
        $page_title = 'Edit Seminar';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/member/seminars/edit.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ===============================================================
    // DELETE
    // ===============================================================
    public function delete($id) {
        if ($this->seminarsModel->deleteSeminarByMember((int)$id, $this->member_id)) {
            $this->redirectWithStatus('member/seminars', 'success_delete');
        }

        $this->redirectWithStatus('member/seminars', 'fail_delete');
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
            'success_insert' => '<div class="alert alert-success">Seminar berhasil ditambahkan.</div>',
            'success_update' => '<div class="alert alert-success">Seminar berhasil diperbarui.</div>',
            'success_delete' => '<div class="alert alert-danger">Seminar berhasil dihapus.</div>',
            default => ''
        };
    }

    private function generateSlug(string $text): string {
        return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
    }
}
