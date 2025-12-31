<?php
// index.php - ROUTER YANG BENAR UNTUK STRUKTUR ANDA

session_start();

define('BASE_URL', '/lab13_14_vivi');
define('ASSETS_URL', BASE_URL . '/assets');

// Ambil request
$request = $_SERVER['REQUEST_URI'];

// Hapus BASE_URL
$request = str_replace(BASE_URL, '', $request);

// Parse URL
$url_parts = parse_url($request);
$path = trim($url_parts['path'], '/');

// Query parameters
$query = $url_parts['query'] ?? '';
parse_str($query, $params);

// 🔥 PERBAIKAN 1: Gunakan parameter GET jika ada (module & action)
if (isset($params['module']) || isset($params['action'])) {
    $module = $params['module'] ?? 'dashboard';
    $action = $params['action'] ?? 'index';
    
    // 🔥 Tentukan file yang akan di-load
    if ($module == 'dashboard') {
        // Dashboard ada di views/dashboard.php
        $file = __DIR__ . "/views/dashboard.php";
    } elseif ($module == 'views' && $action == 'dashboard') {
        // Handle views/dashboard
        $file = __DIR__ . "/views/dashboard.php";
        $module = 'dashboard';
        $action = 'index';
    } else {
        // Module lain ada di modules/
        $file = __DIR__ . "/modules/{$module}/{$action}.php";
    }
    
    // Cek file
    if (file_exists($file)) {
        // Check login untuk halaman protected
        $public_pages = ['auth/login', 'auth/logout', 'auth/test'];
        
        if (!isset($_SESSION['user']) && !in_array("{$module}/{$action}", $public_pages)) {
            header('Location: ' . BASE_URL . '/auth/login');
            exit;
        }
        
        // Include header untuk protected pages
        if (!in_array("{$module}/{$action}", $public_pages)) {
            if (file_exists(__DIR__ . '/views/header.php')) {
                include __DIR__ . '/views/header.php';
            }
        }
        
        // Include content
        include $file;
        
        // Include footer untuk protected pages
        if (!in_array("{$module}/{$action}", $public_pages)) {
            if (file_exists(__DIR__ . '/views/footer.php')) {
                include __DIR__ . '/views/footer.php';
            }
        }
        exit;
    }
}

// 🔥 PERBAIKAN 2: Handle URL rewriting (/user/index, /views/dashboard, dll)
if (!empty($path)) {
    $parts = explode('/', $path);
    $module = $parts[0] ?? 'dashboard';
    $action = $parts[1] ?? 'index';
    
    // 🔥 PERBAIKAN: Handle URL yang mengandung 'views/'
    if ($module == 'views') {
        if ($action == 'dashboard') {
            // views/dashboard -> load dashboard.php
            $module = 'dashboard';
            $action = 'index';
            $file = __DIR__ . "/views/dashboard.php";
        } else {
            // views/something -> load modules/views/something.php
            $file = __DIR__ . "/modules/views/{$action}.php";
        }
    } elseif ($module == 'dashboard') {
        // /dashboard -> load views/dashboard.php
        $action = 'index';
        $file = __DIR__ . "/views/dashboard.php";
    } else {
        // /module/action -> load modules/module/action.php
        $file = __DIR__ . "/modules/{$module}/{$action}.php";
    }
} else {
    // Empty path -> dashboard
    $module = 'dashboard';
    $action = 'index';
    $file = __DIR__ . "/views/dashboard.php";
}

// Cek file
if (file_exists($file)) {
    // Public pages yang tidak butuh login
    $public_pages = ['auth/login', 'auth/logout', 'auth/test'];
    
    // Check login
    if (!isset($_SESSION['user']) && !in_array("{$module}/{$action}", $public_pages)) {
        header('Location: ' . BASE_URL . '/auth/login');
        exit;
    }
    
    // Include header untuk protected pages
    if (!in_array("{$module}/{$action}", $public_pages)) {
        if (file_exists(__DIR__ . '/views/header.php')) {
            include __DIR__ . '/views/header.php';
        }
    }
    
    // Include content
    include $file;
    
    // Include footer untuk protected pages
    if (!in_array("{$module}/{$action}", $public_pages)) {
        if (file_exists(__DIR__ . '/views/footer.php')) {
            include __DIR__ . '/views/footer.php';
        }
    }
    
} else {
    // 404 - File not found
    http_response_code(404);
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>404 - File Tidak Ditemukan</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 40px; text-align: center; }
            .error-container { max-width: 600px; margin: 0 auto; }
            h1 { color: #d32f2f; }
            .error-details { background: #f5f5f5; padding: 20px; border-radius: 5px; margin: 20px 0; text-align: left; }
            .btn { display: inline-block; padding: 10px 20px; background: #7b4bff; color: white; text-decoration: none; border-radius: 5px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="error-container">
            <h1>404 - File Tidak Ditemukan</h1>
            <div class="error-details">
                <p><strong>File:</strong> ' . htmlspecialchars(str_replace(__DIR__, '', $file)) . '</p>
                <p><strong>Path:</strong> ' . htmlspecialchars($path) . '</p>
                <p><strong>Module:</strong> ' . htmlspecialchars($module) . ' | <strong>Action:</strong> ' . htmlspecialchars($action) . '</p>
                <p><strong>Request:</strong> ' . htmlspecialchars($request) . '</p>
            </div>
            <a href="' . BASE_URL . '" class="btn">Kembali ke Dashboard</a>
        </div>
    </body>
    </html>';
}