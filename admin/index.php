<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; }
$page_title = 'Dashboard Admin';

// Statistik
$total_products  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM products"))['t'];
$total_categories = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM categories"))['t'];
$total_messages  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM messages"))['t'];
$unread_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM messages WHERE is_read=0"))['t'];
$total_featured  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM products WHERE is_featured=1"))['t'];

// Produk terbaru
$latest_products = mysqli_query($conn, "SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC LIMIT 5");

// Pesan terbaru
$latest_messages = mysqli_query($conn, "SELECT * FROM messages ORDER BY created_at DESC LIMIT 5");

$current_admin_page = 'dashboard';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - Admin | <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: { 50:'#fdf2f2',100:'#fde8e8',200:'#fbd5d5',300:'#f8b4b4',400:'#f17878',500:'#9b2335',600:'#7f1d2b',700:'#6b1624',800:'#5a111d',900:'#4a0e18' },
                        gold:   { 100:'#fef9e7',200:'#fdefc3',300:'#fbe09a',400:'#f8cc61',500:'#c9a84c',600:'#a07c30',700:'#7a5a1e' },
                        cream:  { 50:'#fdfaf5',100:'#faf4e8',200:'#f4e8d0',300:'#ead5b0' }
                    },
                    fontFamily: { serif:['Playfair Display','serif'], sans:['Lato','sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family:'Lato',sans-serif; }
        h1,h2,h3 { font-family:'Playfair Display',serif; }
        .sidebar-link { transition: all 0.2s; }
        .sidebar-link:hover, .sidebar-link.active { background: rgba(201,168,76,0.1); color: #c9a84c; border-left: 3px solid #c9a84c; }
        .sidebar-link { border-left: 3px solid transparent; }
        #mobile-sidebar { transition: transform 0.3s ease; }
    </style>
</head>
<body class="bg-gray-50">

<div class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside id="sidebar" class="w-64 bg-maroon-900 flex-shrink-0 flex flex-col shadow-xl z-30 fixed lg:static h-full -translate-x-full lg:translate-x-0 transition-transform duration-300">
        <!-- Logo -->
        <div class="p-6 border-b border-maroon-700">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-gold-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-gem text-maroon-900"></i>
                </div>
                <div>
                    <p class="font-serif text-gold-400 font-bold leading-none">Maroon Admin</p>
                    <p class="text-cream-300 text-xs">Panel Manajemen</p>
                </div>
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 p-4 overflow-y-auto">
            <p class="text-maroon-400 text-xs uppercase tracking-widest font-bold mb-3 px-3">Menu Utama</p>
            <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; }
            $admin_nav = [
                ['index.php',      'fa-tachometer-alt', 'Dashboard',        'dashboard'],
                ['products.php',   'fa-boxes',          'Kelola Produk',    'products'],
                ['add_product.php','fa-plus-circle',    'Tambah Produk',    'add_product'],
                ['categories.php', 'fa-tags',           'Kategori',         'categories'],
                ['messages.php',   'fa-envelope',       'Pesan Masuk',      'messages'],
            ];
            foreach($admin_nav as $n): ?>
            <a href="<?= BASE_URL ?>/admin/<?= $n[0] ?>"
               class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-r-xl text-cream-200 mb-1 text-sm
                      <?= $current_admin_page === $n[3] ? 'active' : '' ?>">
                <i class="fas <?= $n[1] ?> w-5 text-center"></i>
                <span><?= $n[2] ?></span>
                <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } if($n[3]==='messages' && $unread_messages>0): ?>
                    <span class="ml-auto bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full"><?= $unread_messages ?></span>
                <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } endif; ?>
            </a>
            <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } endforeach; ?>

            <div class="border-t border-maroon-700 mt-4 pt-4">
                <a href="<?= BASE_URL ?>/index.php" target="_blank"
                   class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-r-xl text-cream-200 text-sm">
                    <i class="fas fa-external-link-alt w-5 text-center"></i>
                    <span>Lihat Website</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- Overlay mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden lg:hidden" onclick="toggleSidebar()"></div>

    <!-- Main -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Topbar -->
        <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between shadow-sm">
            <div class="flex items-center space-x-4">
                <button onclick="toggleSidebar()" class="lg:hidden text-gray-500 hover:text-maroon-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div>
                    <h1 class="font-serif text-xl font-bold text-maroon-800">Dashboard</h1>
                    <p class="text-gray-500 text-xs">Selamat datang di Panel Admin</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                <a href="<?= BASE_URL ?>/index.php" target="_blank"
                   class="text-sm text-gray-500 hover:text-maroon-700 flex items-center space-x-1">
                    <i class="fas fa-globe text-xs"></i>
                    <span class="hidden sm:inline">Website</span>
                </a>
                <div class="w-9 h-9 bg-maroon-800 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-shield text-gold-400 text-sm"></i>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 overflow-y-auto p-6">

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; }
                $cards = [
                    ['fa-boxes',       'Total Produk',   $total_products,   'bg-maroon-800', 'products.php'],
                    ['fa-tags',        'Kategori',       $total_categories, 'bg-amber-700',  'categories.php'],
                    ['fa-star',        'Produk Unggulan',$total_featured,   'bg-emerald-700','products.php'],
                    ['fa-envelope',    'Pesan Masuk',    $total_messages,   'bg-blue-700',   'messages.php'],
                ];
                foreach($cards as $c): ?>
                <a href="<?= BASE_URL ?>/admin/<?= $c[4] ?>"
                   class="<?= $c[3] ?> text-white rounded-2xl p-5 hover:opacity-90 transition-opacity">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-white text-opacity-80 text-xs uppercase tracking-wider mb-1"><?= $c[1] ?></p>
                            <p class="font-serif text-3xl font-bold"><?= $c[2] ?></p>
                        </div>
                        <div class="w-10 h-10 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <i class="fas <?= $c[0] ?> text-white text-lg"></i>
                        </div>
                    </div>
                    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } if($c[0]==='fa-envelope' && $unread_messages>0): ?>
                        <p class="text-white text-opacity-80 text-xs mt-2"><?= $unread_messages ?> belum dibaca</p>
                    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } endif; ?>
                </a>
                <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } endforeach; ?>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Latest Products -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center p-5 border-b border-gray-100">
                        <h2 class="font-serif text-lg font-bold text-maroon-800">Produk Terbaru</h2>
                        <a href="products.php" class="text-xs text-gold-600 hover:text-maroon-700 font-semibold">Lihat Semua →</a>
                    </div>
                    <div class="p-5 space-y-4">
                        <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } while($p = mysqli_fetch_assoc($latest_products)): ?>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-cream-200 rounded-xl flex items-center justify-center shrink-0">
                                    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } if($p['image'] && file_exists('../uploads/'.$p['image'])): ?>
                                        <img src="<?= BASE_URL ?>/uploads/<?= $p['image'] ?>" class="w-full h-full object-cover rounded-xl">
                                    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } endif; ?>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800 line-clamp-1"><?= htmlspecialchars($p['name']) ?></p>
                                    <p class="text-xs text-gray-400"><?= htmlspecialchars($p['cat_name']) ?></p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-sm font-bold text-maroon-700"><?= formatRupiah($p['price']) ?></p>
                                <a href="edit_product.php?id=<?= $p['id'] ?>" class="text-xs text-gold-600 hover:text-maroon-700">Edit</a>
                            </div>
                        </div>
                        <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } endwhile; ?>
                    </div>
                </div>

                <!-- Latest Messages -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
                    <div class="flex justify-between items-center p-5 border-b border-gray-100">
                        <h2 class="font-serif text-lg font-bold text-maroon-800">Pesan Terbaru</h2>
                        <a href="messages.php" class="text-xs text-gold-600 hover:text-maroon-700 font-semibold">Lihat Semua →</a>
                    </div>
                    <div class="p-5 space-y-4">
                        <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } while($m = mysqli_fetch_assoc($latest_messages)): ?>
                        <div class="flex items-start space-x-3 <?= !$m['is_read'] ? 'opacity-100' : 'opacity-60' ?>">
                            <div class="w-9 h-9 bg-maroon-100 rounded-full flex items-center justify-center shrink-0 mt-0.5">
                                <i class="fas fa-user text-maroon-500 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-semibold text-gray-800"><?= htmlspecialchars($m['name']) ?></p>
                                    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } if(!$m['is_read']): ?>
                                        <span class="w-2 h-2 bg-red-500 rounded-full shrink-0"></span>
                                    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } endif; ?>
                                </div>
                                <p class="text-xs text-gray-500"><?= htmlspecialchars($m['email']) ?></p>
                                <p class="text-xs text-gray-600 mt-1 line-clamp-1"><?= htmlspecialchars($m['message']) ?></p>
                            </div>
                        </div>
                        <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-4">
                <a href="add_product.php" class="bg-maroon-800 hover:bg-maroon-700 text-white rounded-2xl p-5 flex items-center space-x-3 transition-colors">
                    <i class="fas fa-plus-circle text-2xl text-gold-400"></i>
                    <div><p class="font-bold">Tambah Produk Baru</p><p class="text-cream-300 text-xs">Upload koleksi antik baru</p></div>
                </a>
                <a href="categories.php" class="bg-amber-700 hover:bg-amber-600 text-white rounded-2xl p-5 flex items-center space-x-3 transition-colors">
                    <i class="fas fa-tags text-2xl"></i>
                    <div><p class="font-bold">Kelola Kategori</p><p class="text-white text-opacity-80 text-xs">Tambah atau edit kategori</p></div>
                </a>
                <a href="messages.php" class="bg-blue-700 hover:bg-blue-600 text-white rounded-2xl p-5 flex items-center space-x-3 transition-colors">
                    <i class="fas fa-envelope text-2xl"></i>
                    <div>
                        <p class="font-bold">Lihat Pesan</p>
                        <p class="text-white text-opacity-80 text-xs"><?= $unread_messages ?> pesan belum dibaca</p>
                    </div>
                </a>
            </div>
        </main>
    </div>
</div>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebar-overlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}
</script>
</body>
</html>
