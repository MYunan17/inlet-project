<?php
require_once ROOT_PATH . 'app/models/StudentModel.php';

class StudentsController {

    private StudentModel $studentModel;
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
        $this->studentModel = new StudentModel();
    }

    // ===============================================================
    // INDEX (LIST MAHASISWA)
    // ===============================================================
    public function index() {
        $current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $items_per_page = 10;

        $search_query = trim($_GET['search'] ?? '');
        $offset = ($current_page - 1) * $items_per_page;

        $result = $this->studentModel->getStudentsPaginatedFiltered(
            limit: $items_per_page,
            offset: $offset,
            search: $search_query,
            onlyActive: true,
            supervisor_id: $this->member_id
        );


        $list = $result['data'];
        $total_items = $result['total'];
        $total_pages = max(1, ceil($total_items / $items_per_page));

        $current_menu = 'students';
        $page_title = 'Daftar Mahasiswa';
        $status_message_html = $this->getStatusMessage($_GET['status'] ?? null);

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/member/students/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ===============================================================
    // CREATE
    // ===============================================================
    public function create() {
        $error = null;
        $old_data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $old_data = $_POST;

            if (empty($_POST['name']) || empty($_POST['nim'])) {
                $error = 'Nama dan NIM wajib diisi.';
            } else {
                $data = [
                    'name' => trim($_POST['name']),
                    'nim' => trim($_POST['nim']),
                    'type' => trim($_POST['type'] ?? ''),
                    'program' => trim($_POST['program'] ?? ''),
                    'institution' => trim($_POST['institution'] ?? ''),
                    'start_date' => $_POST['start_date'] ?: null,
                    'end_date' => $_POST['end_date'] ?: null,
                    'is_active' => TRUE,
                    'supervisor_id' => $this->member_id // pastikan supervisor_id = member yang login
                ];

                if ($this->studentModel->createStudent($data)) {
                    $this->redirectWithStatus('member/students', 'success_insert');
                }

                $error = 'Gagal menyimpan data mahasiswa.';
            }
        }

        $current_menu = 'students';
        $page_title = 'Tambah Mahasiswa';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/member/students/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ===============================================================
    // EDIT
    // ===============================================================
    public function edit($id) {
        $id = (int)$id;

        // Ownership check: pastikan mahasiswa dibimbing oleh member yang login
        $student = $this->studentModel->getStudentByIdAndSupervisor($id, $this->member_id);
        if (!$student) {
            http_response_code(403);
            exit('Akses ditolak.');
        }

        $error = null;
        $old_data = $student;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => trim($_POST['name']),
                'nim' => trim($_POST['nim']),
                'type' => trim($_POST['type'] ?? ''),
                'program' => trim($_POST['program'] ?? ''),
                'institution' => trim($_POST['institution'] ?? ''),
                'start_date' => $_POST['start_date'] ?: null,
                'end_date' => $_POST['end_date'] ?: null,
                'is_active' => isset($_POST['is_active']) ? TRUE : FALSE,
                'supervisor_id' => $this->member_id // jangan ganti supervisor_id
            ];

            if ($this->studentModel->updateStudent($id, $data)) {
                $this->redirectWithStatus('member/students', 'success_update');
            }

            $error = 'Gagal memperbarui data mahasiswa.';
        }

        $current_menu = 'students';
        $page_title = 'Edit Mahasiswa';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/member/students/edit.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ===============================================================
    // DELETE
    // ===============================================================
    public function delete($id) {
        // Ownership check
        $student = $this->studentModel->getStudentByIdAndSupervisor($id, $this->member_id);
        if (!$student) {
            http_response_code(403);
            exit('Akses ditolak.');
        }

        if ($this->studentModel->deleteStudent($id)) {
            $this->redirectWithStatus('member/students', 'success_delete');
        }

        $this->redirectWithStatus('member/students', 'fail_delete');
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
            'success_insert' => '<div class="alert alert-success">Mahasiswa berhasil ditambahkan.</div>',
            'success_update' => '<div class="alert alert-success">Mahasiswa berhasil diperbarui.</div>',
            'success_delete' => '<div class="alert alert-danger">Mahasiswa berhasil dihapus.</div>',
            default => ''
        };
    }
}
