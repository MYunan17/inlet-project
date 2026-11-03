<?php
// bin/create_admin.php - buat akun pengguna dari terminal (hanya CLI)
// Pakai:  php bin/create_admin.php <username> <email> <password> [role=admin] ["Nama Lengkap"]
// role: admin | member | student

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

define('ROOT_PATH', dirname(__DIR__) . '/');
require_once ROOT_PATH . 'app/core/Env.php';
require_once ROOT_PATH . 'app/core/Database.php';
loadEnv(ROOT_PATH . '.env');

[, $username, $email, $password] = $argv + [null, null, null, null];
$role = $argv[4] ?? 'admin';
$name = $argv[5] ?? $username;

if (!$username || !$email || !$password || !in_array($role, ['admin', 'member', 'student'], true)) {
    fwrite(STDERR, "Pakai: php bin/create_admin.php <username> <email> <password> [admin|member|student] [\"Nama\"]\n");
    exit(1);
}
if (strlen($password) < 6) {
    fwrite(STDERR, "Password minimal 6 karakter.\n");
    exit(1);
}

$db = Database::getInstance()->getConnection();

$exists = $db->prepare('SELECT 1 FROM users WHERE username = :u OR email = :e');
$exists->execute([':u' => $username, ':e' => $email]);
if ($exists->fetchColumn()) {
    fwrite(STDERR, "Username atau email sudah dipakai.\n");
    exit(1);
}

$stmt = $db->prepare(
    'INSERT INTO users (name, username, email, password, role, is_active)
     VALUES (:name, :username, :email, :password, :role, TRUE)'
);
$stmt->execute([
    ':name'     => $name,
    ':username' => $username,
    ':email'    => $email,
    ':password' => password_hash($password, PASSWORD_DEFAULT),
    ':role'     => $role,
]);

echo "Akun '$username' ($role) berhasil dibuat. Login di /auth/login\n";
