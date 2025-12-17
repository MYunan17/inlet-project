<?php
// setup_admin.php (SIMPAN DI ROOT PROYEK ANDA)
define('ROOT_PATH', dirname(__DIR__) . '/'); 

// Jika Anda menggunakan autoloader/setup, pastikan Database dan UserModel bisa diakses
require_once ROOT_PATH . 'app/models/User.php';
require_once ROOT_PATH . 'app/core/Database.php'; // Ganti path jika berbeda

// --- DATA ADMIN YANG INGIN ANDA BUAT ---
$username = 'fauzi';
$email = 'fauzi@gmail.com';
$password_raw = 'admin1234'; // Ganti dengan password kuat Anda

try {
    $userModel = new UserModel();
    
    // Cek apakah user sudah ada
    if ($userModel->findUserByCredentials($username)) {
        echo "⚠️ User '$username' sudah ada. Tidak perlu dibuat lagi.\n";
        exit;
    }
    
    // Buat user baru dengan password yang otomatis di-hash
    if ($userModel->createInitialAdmin($username, $email, $password_raw, 'staff')) {
        echo "✅ User Admin berhasil dibuat!\n";
        echo "Username: $username\n";
        echo "Password: $password_raw\n";
    } else {
        echo "❌ Gagal memasukkan data ke database. Cek koneksi dan izin.";
    }
    
} catch (Exception $e) {
    echo "Fatal Error: " . $e->getMessage();
}

?>