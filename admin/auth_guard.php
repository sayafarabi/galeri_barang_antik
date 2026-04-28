<?php
// Guard: wajib dipanggil di setiap halaman admin
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Auto-logout setelah 2 jam tidak aktif
if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time']) > 7200) {
    session_destroy();
    header('Location: login.php?msg=timeout');
    exit;
}
// Perbarui waktu aktif
$_SESSION['admin_login_time'] = time();