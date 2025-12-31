<?php
// 🔥 TAMBAHKAN DI AWAL: Start session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔥 PERBAIKAN PATH: Pastikan BASE_URL selalu didefinisikan
$base_url = '/lab13_14_vivi';
if (!defined('BASE_URL')) {
    define('BASE_URL', $base_url);
}
if (!defined('ASSETS_URL')) {
    define('ASSETS_URL', BASE_URL . '/assets');
}

// 🔥 PERBAIKAN: Ambil path dengan benar
$current_uri = $_SERVER['REQUEST_URI'];
$current_path = parse_url($current_uri, PHP_URL_PATH);
$current_path = trim(str_replace(BASE_URL, '', $current_path), '/');

// Debug info (bisa dihapus setelah fix)
// echo "<!-- DEBUG: URI = $current_uri -->";
// echo "<!-- DEBUG: Path = $current_path -->";
// echo "<!-- DEBUG: BASE_URL = " . BASE_URL . " -->";

// Tentukan active page
$active_page = 'dashboard';
if (strpos($current_path, 'user/index') !== false) {
    $active_page = 'data_barang';
} elseif (strpos($current_path, 'user/tambah') !== false) {
    $active_page = 'tambah_barang';
} elseif (strpos($current_path, 'user/edit') !== false) {
    $active_page = 'edit_barang';
} elseif(strpos($current_path, 'auth/profile') !== false) {
    $active_page = 'profile';
} elseif ($current_path == '' || $current_path == 'dashboard' || strpos($current_path, 'views/dashboard') !== false) {
    $active_page = 'dashboard';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Inventaris Barang</title>
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="stylesheet" href="/lab13_14_vivi/assets/css/style.css">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* Ubah style yang sudah ada untuk main-content */
        .main-content {
            width: 85%; /* Sama dengan header-wrapper */
            max-width: 1100px; /* Sama dengan header-wrapper */
            margin: 30px auto;
            min-height: 60vh;
            padding: 0;
        }
        
        /* Atau jika ingin mengikuti header-container yang ada di header-wrapper */
        .main-content {
            width: 85%; /* Sama dengan header-container */
            max-width: auto; /* Tanpa batas maksimum seperti header-container */
            margin: 30px auto;
            min-height: 60vh;
            padding: 0;
        }
        
        /* Atau jika ingin mengikuti inline version */
        .main-content {
            width: 90%; /* Sama dengan header-container-inline */
            max-width: 1200px; /* Sama dengan header-container-inline */
            margin: 30px auto;
            min-height: 60vh;
            padding: 0;
        }
</style>
</head>
<body>
    <!-- Header -->
    <div class="header-wrapper">
        <div class="header-container">
            <!-- Top Bar -->
            <div class="header-top">
                <!-- Brand & Logo -->
                <div class="brand">
                    <div class="logo">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <div class="brand-text">
                        <h1>Sistem Inventaris Barang</h1>
                        <!-- 🔥 PERBAIKAN: Typo "Tentriegrasi" jadi "Terintegrasi" -->
                        <p>Manajemen Stok & Inventaris Terintegrasi</p>
                    </div>
                </div>
                
                <!-- User Info -->
                <?php if (isset($_SESSION['user'])): ?>
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="user-details">
                        <span class="user-name"><?= htmlspecialchars($_SESSION['user']['username'] ?? 'User') ?></span>
                        <span class="user-role">
                            <?= isset($_SESSION['user']['role']) && $_SESSION['user']['role'] == 'admin' ? 'Administrator' : 'User' ?>
                        </span>
                    </div>
                    <a href="<?= BASE_URL ?>/auth/logout" class="logout-btn" onclick="return confirm('Yakin ingin logout?')">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
                <?php else: ?>
                <div class="user-info">
                    <a href="<?= BASE_URL ?>/auth/login" class="logout-btn">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </a>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Current Page Indicator -->
            <div class="current-page">
                <i class="fas fa-map-marker-alt"></i>
                <span>Anda berada di: 
                    <?php 
                    if (strpos($current_path, 'user/index') !== false) {
                        echo 'Data Barang';
                    } elseif (strpos($current_path, 'user/tambah') !== false) {
                        echo 'Tambah Barang';
                    } elseif (strpos($current_path, 'user/edit') !== false) {
                        echo 'Edit Barang';
                    } elseif (strpos($current_path, 'auth/profile') !== false) {
                        echo 'Profile';
                    } elseif ($current_path == '' || $current_path == 'dashboard' || strpos($current_path, 'views/dashboard') !== false) {
                        echo 'Dashboard';
                    } else {
                        echo 'Halaman';
                    }
                    ?>
                </span>
            </div>

            
            <!-- Navigation -->
            <?php if (isset($_SESSION['user'])): ?>
            <nav class="main-nav">
                <a href="<?= BASE_URL ?>/dashboard" 
                   class="nav-link <?= ($active_page == 'dashboard') ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="<?= BASE_URL ?>/user/index" 
                   class="nav-link <?= ($active_page == 'data_barang') ? 'active' : '' ?>">
                    <i class="fas fa-boxes"></i>
                    <span>Data Barang</span>
                </a>
                
                <a href="<?= BASE_URL ?>/user/tambah" 
                   class="nav-link <?= ($active_page == 'tambah_barang') ? 'active' : '' ?>">
                    <i class="fas fa-plus-circle"></i>
                    <span>Tambah Barang</span>
                </a>

                <a href="<?= BASE_URL ?>/auth/profile"
                   class="nav-link <?= (strpos($current_path, 'auth/profile') !== false) ? 'active' : '' ?>">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </nav>
            <?php endif; ?>
        </div>
    </div>

    <!-- Notification Area -->
    <div class="notification-area">
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?= $_SESSION['success'] ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i> <?= $_SESSION['error'] ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div class="main-content">