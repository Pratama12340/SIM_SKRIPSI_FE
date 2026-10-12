<?php
// Konfigurasi session cookie 7 hari
ini_set('session.gc_maxlifetime', 604800);
session_set_cookie_params([
    'lifetime' => 604800,
    'path'     => '/',
    'httponly' => true,
    'samesite' => 'Lax'
]);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Helper untuk membangun absolute URL internal secara adaptif (Windows & Linux compatible)
function url($path = '') {
    $proto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    if ($scriptDir === '/' || $scriptDir === '.') {
        $scriptDir = '';
    }
    $base = rtrim($proto . $host . $scriptDir, '/');
    return $base . '/' . ltrim($path, '/');
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
$script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
if ($script_dir === '/' || $script_dir === '.') {
    $script_dir = '';
}

if (!empty($script_dir) && strpos($request_uri, $script_dir) === 0) {
    $path_info = substr($request_uri, strlen($script_dir));
} else {
    $path_info = $request_uri;
}

$path_info = '/' . ltrim($path_info, '/');
if (empty($path_info)) $path_info = '/';

// Panggil Helper & Definisi Rute
require_once __DIR__ . '/../app/Helpers/ApiClient.php';
require_once __DIR__ . '/../app/Controllers/Controller.php';
$routes = require_once __DIR__ . '/../routes/web.php';

// Pencocokan Rute (Matching)
$method = $_SERVER['REQUEST_METHOD'];
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
        
        require_once __DIR__ . '/../app/Controllers/' . $controllerName . '.php';
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
        
        require_once __DIR__ . '/../app/Controllers/' . $controllerName . '.php';
        $controller = new $controllerName();
        
        array_shift($matches);
        call_user_func_array([$controller, $action], $matches);
        break;
    }
}

if (!$matched) {
    http_response_code(404);
    echo "<!DOCTYPE html><html lang='id'><head><meta charset='utf-8'><title>404 Not Found</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-slate-50 flex items-center justify-center min-h-screen'><div class='text-center p-8 bg-white rounded-xl shadow-sm border border-slate-200 max-w-md'><h1 class='text-6xl font-black text-blue-600 mb-2'>404</h1><p class='text-lg font-bold text-gray-800 mb-1'>Halaman Tidak Ditemukan</p><p class='text-sm text-gray-500 mb-6'>Rute '{$path_info}' tidak terdaftar pada sistem SIM Skripsi.</p><a href='" . url('/') . "' class='inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 text-white text-sm font-semibold hover:bg-blue-700 transition'>Kembali ke Dashboard</a></div></body></html>";
}
