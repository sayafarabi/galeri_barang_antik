<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . BASE_URL . '/admin/login.php'); exit;
}
$admin_user      = $_SESSION['admin_user'] ?? 'Admin';
$unread_messages = (int)mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM messages WHERE is_read=0"))['t'];
$cur             = $current_admin_page ?? '';

// Ambil logo jika ada
$site_logo = '';
$logo_check = mysqli_query($conn,"SHOW TABLES LIKE 'site_settings'");
if (mysqli_num_rows($logo_check) > 0) {
    $logo_row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT value FROM site_settings WHERE `key`='site_logo' LIMIT 1"));
    if ($logo_row && $logo_row['value'] && file_exists(__DIR__.'/../uploads/'.$logo_row['value'])) {
        $site_logo = $logo_row['value'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title).' — Admin | '.SITE_NAME : 'Admin | '.SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon:{ 50:'#fdf2f2',100:'#fde8e8',200:'#fbd5d5',300:'#f8b4b4',400:'#f17878',500:'#9b2335',600:'#7f1d2b',700:'#6b1624',800:'#5a111d',900:'#4a0e18' },
                        gold:  { 100:'#fef9e7',200:'#fdefc3',300:'#fbe09a',400:'#f8cc61',500:'#c9a84c',600:'#a07c30',700:'#7a5a1e' },
                        cream: { 50:'#fdfaf5',100:'#faf4e8',200:'#f4e8d0',300:'#ead5b0' }
                    },
                    fontFamily:{ serif:['Playfair Display','serif'], sans:['Lato','sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family:'Lato',sans-serif; }
        h1,h2,h3,h4 { font-family:'Playfair Display',serif; }
        .sidebar-link { border-left:3px solid transparent; transition:all 0.2s; }
        .sidebar-link:hover  { background:rgba(201,168,76,0.12); color:#c9a84c; border-left-color:#c9a84c; }
        .sidebar-link.active { background:rgba(201,168,76,0.18); color:#c9a84c; border-left-color:#c9a84c; font-weight:600; }
        .submenu { max-height:0; overflow:hidden; transition:max-height 0.3s ease; }
        .submenu.open { max-height:200px; }
        .chevron { transition:transform 0.3s; display:inline-block; }
        .chevron.open { transform:rotate(180deg); }
        .submenu-link { border-left:2px solid transparent; transition:all 0.15s; }
        .submenu-link:hover  { background:rgba(201,168,76,0.08); color:#c9a84c; border-left-color:#c9a84c; }
        .submenu-link.active { color:#c9a84c; font-weight:600; border-left-color:#c9a84c; }
        input:focus,textarea:focus,select:focus { border-color:#c9a84c!important; box-shadow:0 0 0 2px rgba(201,168,76,0.2)!important; outline:none!important; }
        @keyframes pulse-dot { 0%,100%{opacity:1}50%{opacity:0.4} }
        .pulse { animation:pulse-dot 1.5s infinite; }
        .line-clamp-1 { display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden; }
    </style>
</head>
<body class="bg-gray-50">

<div class="flex min-h-screen">

    <!-- ===== SIDEBAR ===== -->
    <aside id="sidebar"
           class="w-64 bg-maroon-900 flex-shrink-0 flex flex-col shadow-2xl z-30
                  fixed lg:static h-full -translate-x-full lg:translate-x-0 transition-transform duration-300">

        <!-- Logo -->
        <div class="p-5 border-b border-maroon-700">
            <a href="<?= BASE_URL ?>/admin/index.php" class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-full flex items-center justify-center overflow-hidden bg-gold-500 shrink-0 shadow">
                    <?php if ($site_logo): ?>
                        <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($site_logo) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <i class="fas fa-gem text-maroon-900 text-lg"></i>
                    <?php endif; ?>
                </div>
                <div>
                    <p class="font-serif text-gold-400 font-bold leading-none text-sm">Maroon Admin</p>
                    <p class="text-maroon-300 text-xs mt-0.5">Panel Manajemen</p>
                </div>
            </a>
        </div>

        <!-- Nav -->
        <nav class="flex-1 p-3 overflow-y-auto space-y-0.5">

            <!-- Dashboard -->
            <a href="<?= BASE_URL ?>/admin/index.php"
               class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-r-xl text-cream-200 text-sm <?= $cur==='dashboard'?'active':'' ?>">
                <i class="fas fa-tachometer-alt w-5 text-center text-sm shrink-0"></i>
                <span>Dashboard</span>
            </a>

            <!-- Kelola Produk (sub-menu) -->
            <div>
                <button type="button"
                        onclick="toggleSub('sub-produk','chev-produk')"
                        class="sidebar-link w-full flex items-center justify-between px-3 py-2.5 rounded-r-xl text-cream-200 text-sm
                               <?= in_array($cur,['products','add_product','edit_product'])?'active':'' ?>">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-boxes w-5 text-center text-sm shrink-0"></i>
                        <span>Kelola Produk</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs chevron <?= in_array($cur,['products','add_product','edit_product'])?'open':'' ?>"
                       id="chev-produk"></i>
                </button>
                <div class="submenu pl-11 pr-2 <?= in_array($cur,['products','add_product','edit_product'])?'open':'' ?>"
                     id="sub-produk">
                    <a href="<?= BASE_URL ?>/admin/products.php"
                       class="submenu-link flex items-center space-x-2 px-3 py-2 rounded-lg text-cream-300 text-xs my-0.5
                              <?= $cur==='products'?'active':'' ?>">
                        <i class="fas fa-list text-xs shrink-0"></i><span>Semua Produk</span>
                    </a>
                    <a href="<?= BASE_URL ?>/admin/add_product.php"
                       class="submenu-link flex items-center space-x-2 px-3 py-2 rounded-lg text-cream-300 text-xs my-0.5
                              <?= $cur==='add_product'?'active':'' ?>">
                        <i class="fas fa-plus text-xs shrink-0"></i><span>Tambah Produk</span>
                    </a>
                </div>
            </div>

            <!-- Kategori -->
            <a href="<?= BASE_URL ?>/admin/categories.php"
               class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-r-xl text-cream-200 text-sm <?= $cur==='categories'?'active':'' ?>">
                <i class="fas fa-tags w-5 text-center text-sm shrink-0"></i>
                <span>Kategori</span>
            </a>

            <!-- Pesan -->
            <a href="<?= BASE_URL ?>/admin/messages.php"
               class="sidebar-link flex items-center justify-between px-3 py-2.5 rounded-r-xl text-cream-200 text-sm <?= $cur==='messages'?'active':'' ?>">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-envelope w-5 text-center text-sm shrink-0"></i>
                    <span>Pesan Masuk</span>
                </div>
                <?php if ($unread_messages > 0): ?>
                <span class="bg-red-500 pulse text-white text-xs px-1.5 py-0.5 rounded-full font-bold min-w-[20px] text-center">
                    <?= $unread_messages ?>
                </span>
                <?php endif; ?>
            </a>

            <div class="border-t border-maroon-700 my-2"></div>

            <!-- Pengaturan -->
            <a href="<?= BASE_URL ?>/admin/settings.php"
               class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-r-xl text-cream-200 text-sm <?= $cur==='settings'?'active':'' ?>">
                <i class="fas fa-cog w-5 text-center text-sm shrink-0"></i>
                <span>Pengaturan</span>
            </a>

            <!-- Lihat Website -->
            <a href="<?= BASE_URL ?>/index.php" target="_blank"
               class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-r-xl text-cream-200 text-sm">
                <i class="fas fa-external-link-alt w-5 text-center text-sm shrink-0"></i>
                <span>Lihat Website</span>
            </a>

            <!-- Logout -->
            <a href="<?= BASE_URL ?>/admin/logout.php"
               onclick="return confirm('Yakin ingin keluar?')"
               class="sidebar-link flex items-center space-x-3 px-3 py-2.5 rounded-r-xl text-red-300 hover:text-red-200 hover:bg-red-900 hover:bg-opacity-30 text-sm mt-1">
                <i class="fas fa-sign-out-alt w-5 text-center text-sm shrink-0"></i>
                <span>Keluar</span>
            </a>
        </nav>

        <!-- User info -->
        <div class="p-4 border-t border-maroon-700">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-gold-500 rounded-full flex items-center justify-center shrink-0">
                    <i class="fas fa-user-shield text-maroon-900 text-xs"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-cream-200 text-sm font-semibold truncate"><?= htmlspecialchars($admin_user) ?></p>
                    <p class="text-maroon-400 text-xs">Administrator</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Overlay mobile -->
    <div id="sidebar-overlay"
         class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden lg:hidden"
         onclick="toggleSidebar()"></div>

    <!-- ===== MAIN ===== -->
    <div class="flex-1 flex flex-col min-w-0">

        <!-- Topbar -->
        <header class="bg-white border-b border-gray-100 px-5 py-3.5 flex items-center justify-between shadow-sm sticky top-0 z-10">
            <div class="flex items-center space-x-3">
                <button onclick="toggleSidebar()"
                        class="lg:hidden w-9 h-9 flex items-center justify-center text-gray-500 hover:text-maroon-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-bars"></i>
                </button>
                <!-- Breadcrumb -->
                <div class="flex items-center space-x-2 text-sm text-gray-500">
                    <a href="<?= BASE_URL ?>/admin/index.php" class="hover:text-maroon-700 transition-colors flex items-center space-x-1">
                        <i class="fas fa-home text-xs"></i><span class="hidden sm:inline">Dashboard</span>
                    </a>
                    <?php if ($cur !== 'dashboard'): ?>
                    <i class="fas fa-chevron-right text-xs text-gray-300"></i>
                    <span class="text-maroon-800 font-semibold"><?= htmlspecialchars($page_title ?? '') ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right topbar -->
            <div class="flex items-center space-x-1">
                <!-- Bell notif -->
                <?php if ($unread_messages > 0): ?>
                <a href="<?= BASE_URL ?>/admin/messages.php"
                   class="relative w-9 h-9 flex items-center justify-center text-gray-500 hover:text-maroon-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-bell text-sm"></i>
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full pulse"></span>
                </a>
                <?php endif; ?>

                <!-- Shortcut tambah produk -->
                <a href="<?= BASE_URL ?>/admin/add_product.php"
                   title="Tambah produk baru"
                   class="hidden sm:flex w-9 h-9 items-center justify-center text-gray-500 hover:text-maroon-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-plus text-sm"></i>
                </a>

                <!-- User dropdown -->
                <div class="relative" id="user-wrap">
                    <button onclick="toggleUserMenu()"
                            class="flex items-center space-x-2 pl-2 pr-3 py-1.5 hover:bg-gray-100 rounded-xl transition-colors ml-1">
                        <div class="w-8 h-8 bg-maroon-800 rounded-full flex items-center justify-center shrink-0">
                            <i class="fas fa-user-shield text-gold-400 text-xs"></i>
                        </div>
                        <span class="text-sm font-semibold text-gray-700 hidden sm:block"><?= htmlspecialchars($admin_user) ?></span>
                        <i class="fas fa-chevron-down text-xs text-gray-400 hidden sm:block"></i>
                    </button>
                    <div id="user-dropdown"
                         class="hidden absolute right-0 top-full mt-1 w-48 bg-white rounded-xl shadow-xl border border-gray-100 py-1 z-50">
                        <div class="px-4 py-2.5 border-b border-gray-100">
                            <p class="text-xs text-gray-400">Login sebagai</p>
                            <p class="text-sm font-bold text-maroon-800"><?= htmlspecialchars($admin_user) ?></p>
                        </div>
                        <a href="<?= BASE_URL ?>/admin/settings.php"
                           class="flex items-center space-x-2 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-cog w-4 text-center text-gray-400"></i><span>Pengaturan</span>
                        </a>
                        <a href="<?= BASE_URL ?>/index.php" target="_blank"
                           class="flex items-center space-x-2 px-4 py-2.5 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-globe w-4 text-center text-gray-400"></i><span>Lihat Website</span>
                        </a>
                        <div class="border-t border-gray-100 mt-1">
                            <a href="<?= BASE_URL ?>/admin/logout.php"
                               onclick="return confirm('Yakin ingin keluar?')"
                               class="flex items-center space-x-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors">
                                <i class="fas fa-sign-out-alt w-4 text-center"></i><span>Keluar</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page content -->
        <main class="flex-1 overflow-y-auto">

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
    document.getElementById('sidebar-overlay').classList.toggle('hidden');
}
function toggleSub(menuId, chevId) {
    document.getElementById(menuId).classList.toggle('open');
    document.getElementById(chevId).classList.toggle('open');
}
function toggleUserMenu() {
    document.getElementById('user-dropdown').classList.toggle('hidden');
}
document.addEventListener('click', function(e) {
    const wrap = document.getElementById('user-wrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('user-dropdown').classList.add('hidden');
    }
});
</script>