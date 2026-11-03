<?php
// app/core/Database.php
// Konfigurasi dibaca dari .env (lihat .env.example): DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS

class Database {
    private static $instance = null;
    private $pdo;

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    private function __construct() {
        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: '5432';
        $name = getenv('DB_NAME') ?: 'db_inlet';
        $user = getenv('DB_USER') ?: 'postgres';
        $pass = getenv('DB_PASS');
        $pass = $pass === false ? '' : $pass;

        $dsn = "pgsql:host={$host};port={$port};dbname={$name}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            // Detail error hanya ke log, jangan ditampilkan ke pengunjung
            error_log('Koneksi database gagal: ' . $e->getMessage());
            http_response_code(500);
            die('Koneksi database gagal. Silakan coba lagi nanti.');
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
