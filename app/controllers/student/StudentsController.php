<?php
// app/controllers/admin/StudentsController.php

require_once ROOT_PATH . 'app/models/StudentModel.php';

class StudentsController
{
    private StudentModel $model;

    public function __construct()
    {
        $this->model = new StudentModel();
    }

    // ======================================================
    // UTILITIES
    // ======================================================
    private function redirectWithStatus(string $status): void
    {
        header('Location: ' . BASE_URL . 'admin/students?status=' . $status);
        exit;
    }

    private function getStatusMessage(?string $status): string
    {
        return match ($status) {
            'success_create'   => '<div class="alert alert-success">Mahasiswa berhasil ditambahkan.</div>',
            'success_update'   => '<div class="alert alert-success">Data mahasiswa berhasil diperbarui.</div>',
            'success_delete'   => '<div class="alert alert-success">Mahasiswa dihapus.</div>',
            'success_deactive' => '<div class="alert alert-success">Mahasiswa dinonaktifkan.</div>',
            'fail_action'      => '<div class="alert alert-danger">Aksi gagal dilakukan.</div>',
            default => ''
        };
    }

    // ======================================================
    // INDEX
    // ======================================================
    public function index()
    {
        $list = $this->model->getAll();

        $data = [
            'list' => $list,
            'current_page' => 1,
            'items_per_page' => count($list),
            'total_items' => count($list),
            'total_pages' => 1,
            'status_message_html' => $this->getStatusMessage($_GET['status'] ?? null),
        ];

        $page_title = 'Students';
        $current_page_nav = 'students';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/students/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // CREATE
    // ======================================================
    public function create()
    {
        $data = [
            'error' => null,
            'old_data' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $record = [
                'name' => trim($_POST['name'] ?? ''),
                'nim' => trim($_POST['nim'] ?? ''),
                'type' => $_POST['type'] ?? '',
                'program' => $_POST['program'] ?? '',
                'institution' => $_POST['institution'] ?? '',
                'start_date' => $_POST['start_date'] ?? null,
                'end_date' => null,
                'is_active' => true,
                'supervisor_id' => $_POST['supervisor_id'] ?? null
            ];

            $data['old_data'] = $record;

            if (
                empty($record['name']) ||
                empty($record['nim']) ||
                empty($record['type']) ||
                empty($record['program']) ||
                empty($record['institution']) ||
                empty($record['start_date'])
            ) {
                $data['error'] = 'Data mahasiswa tidak lengkap.';
            }
            elseif ($this->model->findByNim($record['nim'])) {
                $data['error'] = 'NIM sudah terdaftar.';
            }
            elseif ($this->model->create($record)) {
                $this->redirectWithStatus('success_create');
            } else {
                $data['error'] = 'Gagal menyimpan data mahasiswa.';
            }
        }

        $page_title = 'Tambah Mahasiswa';
        $current_page_nav = 'students';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/students/create.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // EDIT / UPDATE
    // ======================================================
    public function edit($id)
    {
        $student = $this->model->findById((int)$id);

        if (!$student) {
            $this->redirectWithStatus('fail_action');
        }

        $data = [
            'error' => null,
            'old_data' => $student
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $updateData = [
                'name' => trim($_POST['name'] ?? ''),
                'type' => $_POST['type'] ?? '',
                'program' => $_POST['program'] ?? '',
                'institution' => $_POST['institution'] ?? '',
                'supervisor_id' => $_POST['supervisor_id'] ?? null
            ];

            if (empty($updateData['name'])) {
                $data['error'] = 'Nama mahasiswa wajib diisi.';
            }
            elseif ($this->model->update((int)$id, $updateData)) {
                $this->redirectWithStatus('success_update');
            } else {
                $data['error'] = 'Gagal memperbarui data.';
            }
        }

        $page_title = 'Edit Mahasiswa';
        $current_page_nav = 'students';

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        extract($data);
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/students/edit.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // DEACTIVATE (SOFT DELETE)
    // ======================================================
    public function deactivate($id)
    {
        if ($this->model->deactivate((int)$id)) {
            $this->redirectWithStatus('success_deactive');
        }

        $this->redirectWithStatus('fail_action');
    }

    // ======================================================
    // DELETE (HARD DELETE – OPTIONAL)
    // ======================================================
    public function delete($id)
    {
        if ($this->model->delete((int)$id)) {
            $this->redirectWithStatus('success_delete');
        }

        $this->redirectWithStatus('fail_action');
    }
}
