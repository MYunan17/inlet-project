<?php
// app/controllers/StudentAttendanceController.php

require_once ROOT_PATH . 'app/models/StudentAttendanceModel.php';
require_once ROOT_PATH . 'app/models/StudentModel.php';

class StudentAttendanceController
{
    private StudentAttendanceModel $attendanceModel;
    private StudentModel $studentModel;
    private string $uploadDir;

    public function __construct()
    {
        $this->attendanceModel = new StudentAttendanceModel();
        $this->studentModel = new StudentModel();

        $this->uploadDir = ROOT_PATH . 'public/uploads/attendance/';
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    // ======================================================
    // HELPER
    // ======================================================

    private function redirect(string $path = '', array $params = []): void
    {
        // BASE_URL sudah mengarah ke admin, jadi cukup path relatif
        $query = http_build_query($params);
        $url = BASE_URL . 'admin/studentattendance' . ($path ? "/$path" : '');
        if ($query) {
            $url .= '?' . $query;
        }
        header('Location: ' . $url);
        exit;
    }

    private function nullIfEmpty(?string $value): ?string
    {
        $value = trim($value ?? '');
        return $value === '' ? null : $value;
    }

    private function uploadPhoto(array $file, string $prefix): array
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['error' => 'Foto wajib diupload', 'filename' => null];
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png'], true)) {
            return ['error' => 'Format foto harus JPG / PNG', 'filename' => null];
        }

        $filename = $prefix . '_' . time() . '.' . $ext;
        $target = $this->uploadDir . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return ['error' => 'Gagal menyimpan foto', 'filename' => null];
        }

        return ['error' => null, 'filename' => $filename];
    }

    private function deletePhoto(?string $filename): void
    {
        if (!$filename) return;

        $path = $this->uploadDir . $filename;
        if (file_exists($path)) {
            unlink($path);
        }
    }

    // ======================================================
    // LIST ABSENSI
    // ======================================================

    public function index(): void
    {
        $page = max(1, (int) ($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $attendanceList = $this->attendanceModel->getPaginated($limit, $offset);
        $total = $this->attendanceModel->getTotalCount();
        $totalPages = (int) ceil($total / $limit);

        // variabel untuk view
        $list = $attendanceList;
        $total_items = $total;
        $total_pages = $totalPages;
        $current_page = $page;

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/attendance/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // CHECK-IN
    // ======================================================

    public function checkin(): void
    {
        $error = null;
        $old_data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nim  = $this->nullIfEmpty($_POST['student_nim'] ?? null);
            $note = $this->nullIfEmpty($_POST['note'] ?? null);
            $old_data = ['student_nim' => $nim, 'note' => $note];

            if (!$nim) {
                $error = 'NIM wajib diisi';
            } else {
                $student = $this->studentModel->findByNim($nim);

                if (!$student) {
                    $error = 'Mahasiswa tidak ditemukan';
                } elseif (!$student['is_active']) {
                    $error = 'Mahasiswa tidak aktif';
                } elseif ($this->attendanceModel->getUnfinishedTodayByStudentId((int) $student['id'])) {
                    $error = 'Anda sudah check-in hari ini';
                } elseif (empty($_FILES['checkin_photo']['name'])) {
                    $error = 'Foto check-in wajib';
                } else {
                    $upload = $this->uploadPhoto($_FILES['checkin_photo'], "checkin_{$nim}");

                    if ($upload['error']) {
                        $error = $upload['error'];
                    } else {
                        $success = $this->attendanceModel->recordCheckin([
                            'student_id'    => (int) $student['id'],
                            'checkin_photo' => $upload['filename'],
                            'note'          => $note
                        ]);

                        if ($success) {
                            $this->redirect('', ['status' => 'checkin_success']);
                        } else {
                            $this->deletePhoto($upload['filename']);
                            $error = 'Gagal menyimpan absensi';
                        }
                    }
                }
            }
        }

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/attendance/checkin.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // CHECK-OUT
    // ======================================================

    public function checkout(int $id): void
    {
        $attendance = $this->attendanceModel->findById($id);

        if (!$attendance || $attendance['checkout_time']) {
            http_response_code(404);
            echo 'Record tidak valid';
            return;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($_FILES['checkout_photo']['name'])) {
                $error = 'Foto checkout wajib';
            } else {
                $upload = $this->uploadPhoto($_FILES['checkout_photo'], 'checkout_' . $attendance['student_nim']);

                if ($upload['error']) {
                    $error = $upload['error'];
                } else {
                    $success = $this->attendanceModel->recordCheckout($id, [
                        'checkout_photo' => $upload['filename'],
                        'note' => $this->nullIfEmpty($_POST['note'] ?? null)
                    ]);

                    if ($success) {
                        $this->redirect('', ['status' => 'checkout_success']);
                    } else {
                        $this->deletePhoto($upload['filename']);
                        $error = 'Gagal checkout';
                    }
                }
            }
        }

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/admin/attendance/checkout.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // DELETE
    // ======================================================

    public function delete(int $id): void
    {
        $checkin  = $this->attendanceModel->getPhotoName($id, 'checkin_photo');
        $checkout = $this->attendanceModel->getPhotoName($id, 'checkout_photo');

        if ($this->attendanceModel->delete($id)) {
            $this->deletePhoto($checkin);
            $this->deletePhoto($checkout);
            $this->redirect('', ['status' => 'deleted']);
        } else {
            $this->redirect('', ['status' => 'delete_failed']);
        }
    }
}
