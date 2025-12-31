<?php
// modules/auth/logout.php
session_start();
session_destroy();
header('Location: ' . BASE_URL . '/auth/login');
exit;
?>