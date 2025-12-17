<?php
// public/index.php

// WAJIB: Define Project Root Global dan load .env
define('ROOT_PATH', dirname(__DIR__) . '/'); 
define('BASE_URL', 'https://inlet-project.test/'); // Sesuaikan
require_once ROOT_PATH . 'app/core/Env.php';
loadEnv(ROOT_PATH . '.env'); 

// 1. Ambil dan Bersihkan URL Path
$url = $_SERVER['REQUEST_URI'];
// Hapus BASE_URL path jika ada, dan trim '/'
$url = trim(parse_url($url, PHP_URL_PATH), '/'); 

$segments = explode('/', $url);

// --- INISIALISASI VARIABEL ROUTING ---
$defaultControllerName = 'Home';
$defaultSubfolderController = 'Index'; // Nama controller default di subfolder (misalnya Admin/IndexController)
$controllerFile = '';
$controllerClass = ''; // Nama kelas tanpa namespace
$methodName = 'index';
$params = [];

// =========================================================================
// LOGIKA ROUTING YANG DIPERBARUI
// =========================================================================

// Kasus 1: URL kosong (/) -> HomeController
if (empty($segments[0])) {
    $controllerName = $defaultControllerName;
    $controllerFile = ROOT_PATH . 'app/controllers/' . $controllerName . 'Controller.php';
    $controllerClass = $controllerName . 'Controller';
    $methodName = 'index';
    $params = [];
    
} else {
    // URL memiliki setidaknya 1 segmen (e.g., /admin atau /admin/dashboard)
    
    $folderName = ucfirst($segments[0]); // e.g., 'Admin'
    
    // ---------------------------------------------------------------------
    // PATH 1: Cek di Subfolder (e.g., app/controllers/Admin/DashboardController.php)
    // ---------------------------------------------------------------------
    
    // Tentukan nama controller dari segmen kedua (jika ada)
    $controllerNameFromUrl = isset($segments[1]) && !empty($segments[1]) 
        ? ucfirst($segments[1]) // e.g., 'Dashboard' dari /admin/dashboard
        : $defaultSubfolderController; // e.g., 'Index' dari /admin
        
    // Cek Path A: Folder + Controller spesifik/default (e.g., Admin/DashboardController.php atau Admin/IndexController.php)
    $potentialSubfolderControllerPath = ROOT_PATH . 'app/controllers/' . $folderName . '/' . $controllerNameFromUrl . 'Controller.php';
    
    if (file_exists($potentialSubfolderControllerPath)) {
        
        $controllerName = $controllerNameFromUrl;
        $controllerFile = $potentialSubfolderControllerPath;
        $controllerClass = $controllerName . 'Controller';
        
        // Tentukan method dan params
        // Jika URL: /admin/dashboard -> method: $segments[2] (index)
        // Jika URL: /admin -> method: $segments[1] (index)
        $methodIndex = isset($segments[1]) && !empty($segments[1]) ? 2 : 1;
        $methodName = isset($segments[$methodIndex]) && !empty($segments[$methodIndex]) 
            ? $segments[$methodIndex] 
            : 'index';
        $params = array_slice($segments, $methodIndex + 1);
        
    } else {
        
        // ---------------------------------------------------------------------
        // PATH 2: Fallback ke Controller Utama (e.g., app/controllers/RisetController.php)
        // ---------------------------------------------------------------------
        
        // Asumsi segmen pertama adalah nama Controller utama (e.g., /riset)
        $controllerName = $folderName; // e.g., 'Riset' dari /riset
        $controllerFile = ROOT_PATH . 'app/controllers/' . $controllerName . 'Controller.php';
        $controllerClass = $controllerName . 'Controller';

        // Tentukan method dan params
        $methodName = isset($segments[1]) && !empty($segments[1]) ? $segments[1] : 'index';
        $params = array_slice($segments, 2);
    }
}

// 2. EKSEKUSI ROUTING (TIDAK BERUBAH)
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    
    // Perhatikan: Karena tidak menggunakan namespace, ini harus tetap sederhana
    if (!class_exists($controllerClass)) { 
        http_response_code(500);
        echo "<h1>500 Internal Error: Kelas Controller '$controllerClass' Tidak Ditemukan di dalam File.</h1>"; 
        exit;
    }

    $controller = new $controllerClass();
    
    if (method_exists($controller, $methodName)) {
        // Sanitasi parameter sebelum dieksekusi
        $sanitizedParams = array_map('htmlspecialchars', $params);
        call_user_func_array([$controller, $methodName], $sanitizedParams);
    } else {
        http_response_code(404);
        echo "<h1>404 Not Found: Method '$methodName' Tidak Ditemukan di Controller '$controllerName'.</h1>";
    }
    
} else {
    // Controller tidak ditemukan (File tidak ada)
    http_response_code(404);
    echo "<h1>404 Not Found: Halaman Tidak Ditemukan. File Controller '$controllerName' tidak ada.</h1>";
}