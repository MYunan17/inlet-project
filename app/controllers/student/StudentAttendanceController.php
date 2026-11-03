<?php
// app/controllers/StudentAttendanceController.php

require_once ROOT_PATH . 'app/models/StudentAttendanceModel.php';
require_once ROOT_PATH . 'app/models/StudentModel.php';

class StudentAttendanceController
{
    private StudentAttendanceModel $attendanceModel;
    private StudentModel $studentModel;
    private string $uploadDir;
    private int $student_id;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Pastikan role student
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
            header('Location: ' . BASE_URL . 'auth/login');
            exit;
        }

        $this->student_id = (int) $_SESSION['personal_id'];
        // var_dump($_SESSION);die;
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
        $query = http_build_query($params);
        $url = BASE_URL . 'student/studentattendance' . ($path ? "/$path" : '');
        if ($query) $url .= '?' . $query;
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
        if (file_exists($path)) unlink($path);
    }

    // ======================================================
    // LIST ABSENSI
    // ======================================================

    public function index(): void
    {
        $attendances = $this->attendanceModel->getByStudent($this->student_id);

        include ROOT_PATH . 'views/layouts/header_admin.php';
        include ROOT_PATH . 'views/layouts/sidebar.php';
        include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
        include ROOT_PATH . 'views/student/attendance/index.php';
        include ROOT_PATH . 'views/layouts/admin_footer.php';
    }

    // ======================================================
    // CHECK-IN
    // ======================================================

    // ======================================================
// CHECK-IN ABSENSI - STUDENT
// ======================================================
// ======================================================
// CHECK-IN ABSENSI - STUDENT
// ======================================================
// ======================================================
// CHECK-IN ABSENSI - STUDENT
// ======================================================
public function checkin(): void
{
    $error = null;

    // Cek apakah hari ini sudah ada record check-in untuk siswa ini
    $todayRecord = $this->attendanceModel->getUnfinishedTodayByStudentId($this->student_id, date('Y-m-d'));

    if ($todayRecord) {
        $error = 'Anda sudah check-in hari ini';
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
        $note = $this->nullIfEmpty($_POST['note'] ?? null);
        $photoBase64 = $this->nullIfEmpty($_POST['checkin_photo'] ?? null);
        $status = $_POST['status'] ?? 'present'; // present, sick, permission

        if (!$photoBase64) {
            $error = 'Foto check-in wajib';
        } else {
            if (preg_match('/^data:image\/(\w+);base64,/', $photoBase64, $type)) {
                $data = substr($photoBase64, strpos($photoBase64, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);

                if ($data === false) {
                    $error = 'Data foto tidak valid';
                } else {
                    $filename = 'checkin_' . $this->student_id . '_' . time() . '.' . $type;
                    $filePath = $this->uploadDir . $filename;

                    if (file_put_contents($filePath, $data) === false) {
                        $error = 'Gagal menyimpan foto';
                    } else {
                        // Jika sakit / izin, langsung checkout juga
                        $checkoutPhoto = ($status === 'sick' || $status === 'permission') ? $filename : null;

                        // Status awal
                        $dbStatus = ($status === 'present') ? 'checkin' : $status;

                        // Simpan ke DB
                        $success = $this->attendanceModel->recordCheckin([
                            'student_id'     => $this->student_id,
                            'checkin_photo'  => $filename,
                            'checkout_photo' => $checkoutPhoto,
                            'note'           => $note,
                            'status'         => $dbStatus
                        ]);

                        if ($success) {
                            $this->redirect('', ['status' => 'checkin_success']);
                        } else {
                            unlink($filePath);
                            $error = 'Gagal menyimpan absensi';
                        }
                    }
                }
            } else {
                $error = 'Format foto tidak valid';
            }
        }
    }

    include ROOT_PATH . 'views/layouts/header_admin.php';
    include ROOT_PATH . 'views/layouts/sidebar.php';
    include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
    include ROOT_PATH . 'views/student/attendance/checkin.php';
    include ROOT_PATH . 'views/layouts/admin_footer.php';
}





    // ======================================================
    // CHECK-OUT
    // ======================================================

   public function checkout(int $id): void
{
    $attendance = $this->attendanceModel->findById($id);

    if (!$attendance || $attendance['status'] !== 'checkin') {
        http_response_code(404);
        echo 'Record tidak valid atau sudah checkout';
        return;
    }

    $error = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $photoBase64 = $this->nullIfEmpty($_POST['checkout_photo'] ?? null);
        $note = $this->nullIfEmpty($_POST['note'] ?? null);

        if (!$photoBase64) {
            $error = 'Foto checkout wajib';
        } else {
            if (preg_match('/^data:image\/(\w+);base64,/', $photoBase64, $type)) {
                $data = substr($photoBase64, strpos($photoBase64, ',') + 1);
                $type = strtolower($type[1]);
                $data = base64_decode($data);

                if ($data === false) {
                    $error = 'Data foto tidak valid';
                } else {
                    $filename = 'checkout_' . $attendance['student_id'] . '_' . time() . '.' . $type;
                    $filePath = $this->uploadDir . $filename;

                    if (file_put_contents($filePath, $data) === false) {
                        $error = 'Gagal menyimpan foto';
                    } else {
                        // Checkout, status jadi present
                        $success = $this->attendanceModel->recordCheckout($id, [
                            'checkout_photo' => $filename,
                            'note'           => $note,
                            'status'         => 'present'
                        ]);

                        if ($success) {
                            $this->redirect('', ['status' => 'checkout_success']);
                        } else {
                            unlink($filePath);
                            $error = 'Gagal checkout';
                        }
                    }
                }
            } else {
                $error = 'Format foto tidak valid';
            }
        }
    }

    include ROOT_PATH . 'views/layouts/header_admin.php';
    include ROOT_PATH . 'views/layouts/sidebar.php';
    include ROOT_PATH . 'views/layouts/admin_wrapper_start.php';
    include ROOT_PATH . 'views/student/attendance/checkout.php';
    include ROOT_PATH . 'views/layouts/admin_footer.php';
}




    // ======================================================
    // DELETE ABSENSI (optional)
    // ======================================================

    public function delete(int $id): void
    {
        $attendance = $this->attendanceModel->findById($id);

        if (!$attendance || $attendance['student_id'] !== $this->student_id) {
            http_response_code(403);
            echo 'Tidak bisa menghapus absensi orang lain';
            return;
        }

        $this->deletePhoto($attendance['checkin_photo']);
        $this->deletePhoto($attendance['checkout_photo']);
        $this->attendanceModel->delete($id);

        $this->redirect('', ['status' => 'deleted']);
    }
}
