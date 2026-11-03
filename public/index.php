<?php
// public/index.php - Front controller

// Server bawaan PHP (php -S): biarkan file statis (css, js, gambar) dilayani langsung
if (PHP_SAPI === 'cli-server') {
    $staticPath = __DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (is_file($staticPath) && realpath($staticPath) !== __FILE__) {
        return false;
    }
}

define('ROOT_PATH', dirname(__DIR__) . '/');
require_once ROOT_PATH . 'app/core/Env.php';
loadEnv(ROOT_PATH . '.env');

// ---------------------------------------------------------------------
// Session (cookie hanya via HTTP, tidak bisa dibaca JavaScript)
// ---------------------------------------------------------------------
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

if (session_status() === PHP_SESSION_NONE) {
    // Hosting serverless: simpan sesi di database agar dibagi antar instance
    if (getenv('SESSION_DRIVER') === 'database') {
        require_once ROOT_PATH . 'app/core/Database.php';
        require_once ROOT_PATH . 'app/core/DbSession.php';
        session_set_save_handler(new DbSession(Database::getInstance()->getConnection()), true);
    }

    session_set_cookie_params([
        'httponly' => true,
        'secure'   => $isHttps,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ---------------------------------------------------------------------
// BASE_URL: ambil dari APP_URL (.env) jika ada, jika tidak deteksi otomatis
// ---------------------------------------------------------------------
$scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/index.php')), '/');

$appUrl = getenv('APP_URL');
if ($appUrl) {
    define('BASE_URL', rtrim($appUrl, '/') . '/');
} else {
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    define('BASE_URL', ($isHttps ? 'https' : 'http') . '://' . $host . $scriptDir . '/');
}

// ---------------------------------------------------------------------
// Helper
// ---------------------------------------------------------------------
function inletAbort(int $code, string $message): void {
    http_response_code($code);
    echo '<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>' . $code . '</title></head>'
       . '<body style="font-family:sans-serif;text-align:center;padding:4rem">'
       . '<h1>' . $code . '</h1><p>' . htmlspecialchars($message) . '</p>'
       . '<p><a href="' . htmlspecialchars(BASE_URL) . '">Kembali ke beranda</a></p></body></html>';
    exit;
}

/** Cari file/folder di $dir tanpa membedakan huruf besar/kecil (aman di Linux). */
function inletFind(string $dir, string $name, bool $wantDir): ?string {
    if (!is_dir($dir)) return null;
    foreach (scandir($dir) as $entry) {
        if ($entry === '.' || $entry === '..') continue;
        if (strcasecmp($entry, $name) === 0 && is_dir($dir . $entry) === $wantDir) {
            return $entry;
        }
    }
    return null;
}

/** "item-loans" / "item_loans" -> "ItemLoans" */
function inletStudly(string $segment): string {
    return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $segment)));
}

/** "submit-item" -> "submitItem" */
function inletCamel(string $segment): string {
    return lcfirst(inletStudly($segment));
}

/** Cari file controller; coba nama apa adanya lalu bentuk tunggalnya (borrowers -> Borrower). */
function inletFindController(string $dir, string $segment): ?array {
    $studly = inletStudly($segment);
    $candidates = [$studly];
    if (strlen($studly) > 1 && substr($studly, -1) === 's') {
        $candidates[] = substr($studly, 0, -1);
    }
    foreach ($candidates as $name) {
        $file = inletFind($dir, $name . 'Controller.php', false);
        if ($file !== null) {
            return [$dir . $file, substr($file, 0, -4)];
        }
    }
    return null;
}

// ---------------------------------------------------------------------
// 1. Ambil path request, buang prefix folder instalasi
// ---------------------------------------------------------------------
$path = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
$path = '/' . trim($path, '/');

$prefixes = array_unique([$scriptDir, rtrim(str_replace('\\', '/', dirname($scriptDir)), '/')]);
usort($prefixes, fn($a, $b) => strlen($b) <=> strlen($a));
foreach ($prefixes as $prefix) {
    if ($prefix !== '' && $prefix !== '/' && stripos($path, $prefix . '/') === 0) {
        $path = substr($path, strlen($prefix));
        break;
    }
}

$segments = array_values(array_filter(explode('/', trim($path, '/')), 'strlen'));

// Alias URL lama
if (($segments[0] ?? '') === 'login') {
    $segments = array_merge(['auth', 'login'], array_slice($segments, 1));
}

// ---------------------------------------------------------------------
// 2. Proteksi area terkunci berdasarkan role (berlaku untuk SEMUA controller di area itu)
// ---------------------------------------------------------------------
$protectedAreas = [
    'admin'   => ['admin'],
    'member'  => ['member'],
    'student' => ['student'],
];
$area = strtolower($segments[0] ?? '');
if (isset($protectedAreas[$area])) {
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . BASE_URL . 'auth/login');
        exit;
    }
    if (!in_array($_SESSION['role'] ?? '', $protectedAreas[$area], true)) {
        inletAbort(403, 'Anda tidak memiliki akses ke halaman ini.');
    }
}

// ---------------------------------------------------------------------
// 3. Routing
// ---------------------------------------------------------------------
$controllerFile  = null;
$controllerClass = null;
$methodSegment   = null;
$params          = [];

if (empty($segments)) {
    $controllerFile  = ROOT_PATH . 'app/controllers/HomeController.php';
    $controllerClass = 'HomeController';
} else {
    $controllersDir = ROOT_PATH . 'app/controllers/';
    $subfolder = inletFind($controllersDir, $segments[0], true);

    if ($subfolder !== null) {
        // /admin, /admin/dashboard, /admin/news/edit/5
        $hasController = isset($segments[1]);
        $found = $hasController
            ? inletFindController($controllersDir . $subfolder . '/', $segments[1])
            : null;

        if ($found === null) {
            $found = inletFindController($controllersDir . $subfolder . '/', 'Index');
            $rest  = array_slice($segments, 1);
        } else {
            $rest  = array_slice($segments, 2);
        }

        if ($found !== null) {
            [$controllerFile, $controllerClass] = $found;
            $methodSegment = $rest[0] ?? null;
            $params        = array_slice($rest, 1);
        }
    } else {
        // /research, /research/detail/slug
        $found = inletFindController($controllersDir, $segments[0]);
        if ($found !== null) {
            [$controllerFile, $controllerClass] = $found;
            $methodSegment = $segments[1] ?? null;
            $params        = array_slice($segments, 2);
        }
    }
}

if ($controllerFile === null || !is_file($controllerFile)) {
    inletAbort(404, 'Halaman tidak ditemukan.');
}

// ---------------------------------------------------------------------
// 4. Eksekusi
// ---------------------------------------------------------------------
try {
    require_once $controllerFile;

    if (!class_exists($controllerClass, false)) {
        // Nama kelas bisa berbeda huruf besar/kecil dari nama file
        $matched = null;
        foreach (get_declared_classes() as $declared) {
            if (strcasecmp($declared, $controllerClass) === 0) { $matched = $declared; break; }
        }
        if ($matched === null) {
            error_log("Kelas controller '$controllerClass' tidak ada di $controllerFile");
            inletAbort(500, 'Terjadi kesalahan pada server.');
        }
        $controllerClass = $matched;
    }

    $controller = new $controllerClass();
    $methodName = $methodSegment !== null ? inletCamel($methodSegment) : 'index';

    if ($methodName === '' || $methodName[0] === '_' || !is_callable([$controller, $methodName])) {
        inletAbort(404, 'Halaman tidak ditemukan.');
    }

    // Akun demo hanya boleh melihat: tolak semua aksi yang mengubah data
    $mutatingMethods = ['approve', 'checkin', 'checkout', 'complete', 'deactivate', 'delete', 'togglepublic'];
    if (!empty($_SESSION['is_demo']) && $controllerClass !== 'AuthController'
        && ($_SERVER['REQUEST_METHOD'] !== 'GET' || in_array(strtolower($methodName), $mutatingMethods, true))) {
        inletAbort(403, 'Akun demo hanya bisa melihat-lihat. Perubahan data dinonaktifkan.');
    }

    $reflection = new ReflectionMethod($controller, $methodName);
    if ($reflection->isConstructor() || $reflection->isStatic() || count($params) > $reflection->getNumberOfParameters() && !$reflection->isVariadic()) {
        inletAbort(404, 'Halaman tidak ditemukan.');
    }
    if (count($params) < $reflection->getNumberOfRequiredParameters()) {
        inletAbort(404, 'Halaman tidak ditemukan.');
    }

    call_user_func_array([$controller, $methodName], $params);
} catch (TypeError $e) {
    // Parameter URL tidak sesuai tipe (misal id bukan angka)
    error_log($e->getMessage());
    inletAbort(404, 'Halaman tidak ditemukan.');
} catch (Throwable $e) {
    error_log($e);
    inletAbort(500, 'Terjadi kesalahan pada server.');
}
