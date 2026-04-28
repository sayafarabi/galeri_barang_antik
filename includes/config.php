<?php
// ============================================
// Konfigurasi Database
// Maroon Company - Galeri Barang Antik
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Ganti dengan username MySQL Anda
define('DB_PASS', '');            // Ganti dengan password MySQL Anda
define('DB_NAME', 'maroon_antique');

// ============================================
// Deteksi BASE_URL secara otomatis
// ============================================
$protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host      = $_SERVER['HTTP_HOST'] ?? 'localhost';
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Temukan root folder project
$rootFolder = 'antique-gallery';
$pos = strpos($scriptDir, '/' . $rootFolder);
if ($pos !== false) {
    $basePath = substr($scriptDir, 0, $pos) . '/' . $rootFolder;
} else {
    $basePath = $scriptDir;
}
$basePath = rtrim($basePath, '/');

define('BASE_URL', $protocol . '://' . $host . $basePath);
define('SITE_NAME', 'Maroon Antique Gallery');

// ============================================
// Koneksi Database
// ============================================
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("
    <!DOCTYPE html>
    <html lang='id'>
    <head>
        <meta charset='UTF-8'>
        <title>Koneksi Gagal - Maroon Antique</title>
        <script src='https://cdn.tailwindcss.com'></script>
    </head>
    <body class='bg-gray-50 flex items-center justify-center min-h-screen p-4'>
        <div class='bg-white rounded-2xl shadow-md p-10 max-w-lg w-full text-center'>
            <h2 class='text-2xl font-bold text-red-600 mb-3'>⚠️ Koneksi Database Gagal</h2>
            <p class='text-gray-500 text-sm mb-4'>Error: " . htmlspecialchars($conn->connect_error) . "</p>
            <div class='bg-gray-50 rounded-xl p-4 text-left text-sm text-gray-700 space-y-2'>
                <p class='font-bold'>Cara memperbaiki:</p>
                <p>1. Pastikan MySQL / XAMPP sudah berjalan</p>
                <p>2. Edit file <code class='bg-gray-200 px-1 rounded'>includes/config.php</code> — sesuaikan DB_USER & DB_PASS</p>
                <p>3. Buka phpMyAdmin, buat database <code class='bg-gray-200 px-1 rounded'>maroon_antique</code></p>
                <p>4. Import file <code class='bg-gray-200 px-1 rounded'>database.sql</code></p>
            </div>
        </div>
    </body>
    </html>
    ");
}

$conn->set_charset("utf8mb4");

// ============================================
// Helper Functions
// ============================================
function formatRupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

function createSlug($text) {
    $text = mb_strtolower(trim($text), 'UTF-8');
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

function sanitize($conn, $input) {
    return $conn->real_escape_string(htmlspecialchars(strip_tags(trim($input))));
}

function timeAgo($datetime) {
    $now  = new DateTime();
    $ago  = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->d === 0 && $diff->h === 0) return $diff->i . ' menit lalu';
    if ($diff->d === 0) return $diff->h . ' jam lalu';
    if ($diff->d < 7)   return $diff->d . ' hari lalu';
    return date('d M Y', strtotime($datetime));
}