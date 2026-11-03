<?php
// app/core/Database.php
// Konfigurasi dibaca dari environment / .env:
//   DATABASE_URL (postgres://user:pass@host/db?sslmode=require)  -- dipakai di hosting (Neon, dsb.)
//   atau DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS               -- dipakai di lokal

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
        [$dsn, $user, $pass] = $this->resolveConfig();

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

    private function resolveConfig(): array {
        $url = getenv('DATABASE_URL_UNPOOLED') ?: getenv('DATABASE_URL') ?: getenv('POSTGRES_URL');

        if ($url) {
            $p = parse_url($url);
            parse_str($p['query'] ?? '', $q);

            $host = $p['host'] ?? 'localhost';
            $dsn  = sprintf(
                'pgsql:host=%s;port=%d;dbname=%s;sslmode=%s',
                $host,
                $p['port'] ?? 5432,
                ltrim($p['path'] ?? '/postgres', '/'),
                $q['sslmode'] ?? 'require'
            );

            // libpq lama tanpa SNI: Neon perlu tahu endpoint-nya lewat opsi koneksi
            if (getenv('DB_ENDPOINT_OPTION') && strpos($host, '.neon.tech') !== false) {
                $endpoint = explode('.', $host)[0];
                $endpoint = preg_replace('/-pooler$/', '', $endpoint);
                $dsn .= ";options='endpoint=" . $endpoint . "'";
            }

            return [$dsn, rawurldecode($p['user'] ?? ''), rawurldecode($p['pass'] ?? '')];
        }

        $host = getenv('DB_HOST') ?: 'localhost';
        $port = getenv('DB_PORT') ?: '5432';
        $name = getenv('DB_NAME') ?: 'db_inlet';
        $user = getenv('DB_USER') ?: 'postgres';
        $pass = getenv('DB_PASS');

        return ["pgsql:host={$host};port={$port};dbname={$name}", $user, $pass === false ? '' : $pass];
    }

    public function getConnection() {
        return $this->pdo;
    }
}
