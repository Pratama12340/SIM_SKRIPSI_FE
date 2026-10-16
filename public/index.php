<?php
// Aktifkan error reporting untuk deteksi masalah saat pengembangan
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Konfigurasi session cookie 7 hari
ini_set('session.gc_maxlifetime', 604800);
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 604800,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Muat konfigurasi dari file .env jika ada
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || strpos($line, '#') === 0) continue;
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\n\r\0\x0B\"'");
            putenv("$key=$value");
            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }
}

// Dapatkan base path URL proyek terhadap Document Root web server (Laragon / Apache)
function get_base_path() {
    static $basePath = null;
    if ($basePath !== null) return $basePath;

    $projectRoot = str_replace('\\', '/', realpath(__DIR__ . '/..') ?: '');
    $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '');

    $path = '';
    if (!empty($docRoot) && !empty($projectRoot) && strpos($projectRoot, $docRoot) === 0) {
        $path = substr($projectRoot, strlen($docRoot));
    }
    $basePath = rtrim(str_replace('\\', '/', $path), '/');
    return $basePath;
}

// Helper untuk membangun absolute URL internal aplikasi secara adaptif
function url($path = '') {
    $proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $base = rtrim($proto . $host . get_base_path(), '/');
    return $base . '/' . ltrim($path, '/');
}

// Helper untuk URL berkas upload backend (misal PDF proposal/naskah)
function upload_url($path = '') {
    if (empty($path)) return '';
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        return $path;
    }
    require_once __DIR__ . '/../app/Helpers/ApiClient.php';
    $backendBase = ApiClient::getUploadsUrl();
    return rtrim($backendBase, '/') . '/' . ltrim($path, '/');
}

// Pastikan aset gambar logo tersedia di folder public/assets
$pubAssets = __DIR__ . '/assets';
if (!is_dir($pubAssets)) {
    @mkdir($pubAssets, 0777, true);
}
if (!file_exists($pubAssets . '/logoumb.png') && file_exists(__DIR__ . '/../assets/logoumb.png')) {
    @copy(__DIR__ . '/../assets/logoumb.png', $pubAssets . '/logoumb.png');
}

// Resolusi path URL router yang aman untuk Windows dan Laragon
$request_uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$basePath = get_base_path();

if (!empty($basePath) && strpos($request_uri, $basePath) === 0) {
    $path_info = substr($request_uri, strlen($basePath));
} else {
    $path_info = $request_uri;
}

// Normalisasi jika ada awalan /public atau /index.php
$path_info = preg_replace('#^/public#', '', $path_info);
$path_info = preg_replace('#^/index\.php#', '', $path_info);

$path_info = '/' . ltrim($path_info, '/');
if (empty($path_info)) $path_info = '/';

// Panggil Helper & Definisi Rute
require_once __DIR__ . '/../app/Helpers/ApiClient.php';
require_once __DIR__ . '/../app/Controllers/Controller.php';
$routes = require_once __DIR__ . '/../routes/web.php';

// Pencocokan Rute (Matching)
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$matched = false;

foreach ($routes as $route) {
    if ($route['method'] !== $method) {
        continue;
    }

    $routePath = $route['path'];
    $matches = [];

    // Exact match
    if ($routePath === $path_info) {
        $matched = true;
        $controllerName = $route['controller'];
        $action = $route['action'];
        
        $controllerFile = __DIR__ . '/../app/Controllers/' . $controllerName . '.php';
        if (!file_exists($controllerFile)) {
            http_response_code(500);
            die("Controller file not found: " . htmlspecialchars($controllerFile));
        }
        require_once $controllerFile;
        $controller = new $controllerName();
        $controller->$action();
        break;
    }

    // Regex pattern match (seperti /detail/(\d+) atau /quick-login/([a-zA-Z0-9_-]+))
    $pattern = '@^' . $routePath . '$@';
    if (@preg_match($pattern, $path_info, $matches)) {
        $matched = true;
        $controllerName = $route['controller'];
        $action = $route['action'];
        
        $controllerFile = __DIR__ . '/../app/Controllers/' . $controllerName . '.php';
        if (!file_exists($controllerFile)) {
            http_response_code(500);
            die("Controller file not found: " . htmlspecialchars($controllerFile));
        }
        require_once $controllerFile;
        $controller = new $controllerName();
        
        array_shift($matches);
        call_user_func_array([$controller, $action], $matches);
        break;
    }
}

if (!$matched) {
    http_response_code(404);
    echo "<!DOCTYPE html><html lang='id'><head><meta charset='utf-8'><title>404 Not Found</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-slate-50 flex items-center justify-center min-h-screen'><div class='text-center p-8 bg-white rounded-xl shadow-sm border border-slate-200 max-w-md'><h1 class='text-6xl font-black text-blue-600 mb-2'>404</h1><p class='text-lg font-bold text-gray-800 mb-1'>Halaman Tidak Ditemukan</p><p class='text-sm text-gray-500 mb-6'>Rute '{$path_info}' tidak terdaftar pada sistem SIM Skripsi.</p><a href='" . url('/') . "' class='inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition'>Kembali ke Beranda</a></div></body></html>";
}
