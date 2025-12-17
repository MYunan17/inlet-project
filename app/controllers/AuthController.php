<?php
// app/controllers/AuthController.php

require_once ROOT_PATH . 'app/models/UsersModel.php'; 

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require ROOT_PATH . 'vendor/autoload.php';

class AuthController {

    private UsersModel $usersModel;

    public function __construct() {
        $this->usersModel = new UsersModel();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ======================================================
    // HELPER: redirect berdasarkan role
    // ======================================================
    private function redirectByRole(string $role): void {
        switch ($role) {
            case 'admin':
                header('Location: ' . BASE_URL . 'admin/dashboard');
                break;
            case 'member':
                header('Location: ' . BASE_URL . 'member/dashboard');
                break;
            case 'student':
                header('Location: ' . BASE_URL . 'student/dashboard');
                break;
            default:
                header('Location: ' . BASE_URL . 'auth/login');
        }
        exit;
    }

    // ======================================================
    // DEFAULT
    // ======================================================
    public function index() {
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }

    // ======================================================
    // LOGIN
    // ======================================================
    public function login() {

        // Kalau sudah login → lempar sesuai role
        if (isset($_SESSION['user_id'], $_SESSION['role'])) {
            $this->redirectByRole($_SESSION['role']);
        }

        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $credential = trim($_POST['credential'] ?? '');
            $password   = $_POST['password'] ?? '';

            $user = $this->usersModel->findUserByCredentials($credential);

            if ($user && $user['is_active'] && password_verify($password, $user['password'])) {

                // SET SESSION
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['name']     = $user['name'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];
                $_SESSION['personal_id']     = $user['personal_id'];

                // REDIRECT SESUAI ROLE
                $this->redirectByRole($user['role']);

            } else {
                $data['error'] = "Username/Email atau password salah.";
            }
        }

        extract((array)$data);
        include ROOT_PATH . 'views/auth/login.php';
    }

    // ======================================================
    // LOGOUT
    // ======================================================
    public function logout() {
        session_unset();
        session_destroy();

        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }

    // ======================================================
    // FORGOT PASSWORD
    // ======================================================
    public function forgot() {
        $data = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            if (empty($email)) {
                $data['error'] = "Email tidak boleh kosong.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $data['error'] = "Format email tidak valid.";
            } else {
                $user = $this->usersModel->findUserByEmail($email);

                if ($user) {
                    $verificationCode = random_int(1000, 9999);

                    $_SESSION['reset_user']        = $user['id'];
                    $_SESSION['verification_code'] = $verificationCode;
                    $_SESSION['code_generated_at'] = time();

                    $mail = new PHPMailer(true);

                    try {
                        $mail->isSMTP();
                        $mail->Host       = 'smtp.gmail.com';
                        $mail->SMTPAuth   = true;
                        $mail->Username   = 'chentiro.dev@gmail.com';
                        $mail->Password   = 'aoju xrfg otlz yivd';
                        $mail->SMTPSecure = 'tls';
                        $mail->Port       = 587;

                        $mail->setFrom('no-reply@inlet.com', 'InLET Service');
                        $mail->addAddress($email, $user['name']);

                        $mail->isHTML(true);
                        $mail->Subject = 'Kode Verifikasi Reset Password';
                        $mail->Body    =
                            "Halo <b>{$user['name']}</b>,<br><br>
                             Kode reset password Anda: <b>$verificationCode</b><br>
                             Berlaku selama 10 menit.";

                        $mail->send();

                        header('Location: ' . BASE_URL . 'auth/verify_code');
                        exit;

                    } catch (Exception $e) {
                        $data['error'] = "Gagal mengirim email.";
                    }
                } else {
                    $data['error'] = "Email tidak ditemukan.";
                }
            }
        }

        extract((array)$data);
        include ROOT_PATH . 'views/auth/forgot.php';
    }

    // ======================================================
    // VERIFY CODE
    // ======================================================
    public function verify_code() {
        $data = [];

        if (!isset($_SESSION['verification_code'], $_SESSION['reset_user'])) {
            header('Location: ' . BASE_URL . 'auth/forgot');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = ($_POST['code1'] ?? '')
                  . ($_POST['code2'] ?? '')
                  . ($_POST['code3'] ?? '')
                  . ($_POST['code4'] ?? '');

            if (
                $code == $_SESSION['verification_code'] &&
                (time() - $_SESSION['code_generated_at']) <= 600
            ) {
                $_SESSION['code_verified'] = true;
                header('Location: ' . BASE_URL . 'auth/reset_password');
                exit;
            } else {
                $data['error'] = "Kode salah atau kadaluarsa.";
            }
        }

        extract((array)$data);
        include ROOT_PATH . 'views/auth/verify_code.php';
    }

    // ======================================================
    // RESET PASSWORD
    // ======================================================
    public function reset_password() {
        $data = [];

        if (
            !isset($_SESSION['code_verified'], $_SESSION['reset_user']) ||
            $_SESSION['code_verified'] !== true
        ) {
            header('Location: ' . BASE_URL . 'auth/forgot');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';

            if (empty($password) || empty($confirm)) {
                $data['error'] = "Semua field wajib diisi.";
            } elseif ($password !== $confirm) {
                $data['error'] = "Password tidak sama.";
            } elseif (strlen($password) < 6) {
                $data['error'] = "Password minimal 6 karakter.";
            } else {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $this->usersModel->updatePassword($_SESSION['reset_user'], $hashed);

                unset(
                    $_SESSION['reset_user'],
                    $_SESSION['verification_code'],
                    $_SESSION['code_verified'],
                    $_SESSION['code_generated_at']
                );

                $_SESSION['success_message'] = "Password berhasil diubah.";
                header('Location: ' . BASE_URL . 'auth/login');
                exit;
            }
        }

        extract((array)$data);
        include ROOT_PATH . 'views/auth/reset_password.php';
    }
}
