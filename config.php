<?php
// config.php - QUICK FIX

$config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'db_name' => 'latihan1'
];

// HANYA define jika belum ada
if (!defined('DB_HOST')) define('DB_HOST', $config['host']);
if (!defined('DB_USER')) define('DB_USER', $config['username']);
if (!defined('DB_PASS')) define('DB_PASS', $config['password']);
if (!defined('DB_NAME')) define('DB_NAME', $config['db_name']);

// Tambahkan juga ini
if (!defined('BASE_URL')) define('BASE_URL', '/lab13_14_vivi');
if (!defined('ASSETS_URL')) define('ASSETS_URL', BASE_URL . '/assets');
?>