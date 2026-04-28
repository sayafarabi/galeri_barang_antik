<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; }
$page_title = 'Kelola Produk';
$current_admin_page = 'products';

// Handle delete
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    // Hapus gambar jika ada
    $prod = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM products WHERE id=$del_id LIMIT 1"));
    if ($prod && $prod['image'] && file_exists('../uploads/' . $prod['image'])) {
        unlink('../uploads/' . $prod['image']);
    }
    mysqli_query($conn, "DELETE FROM products WHERE id=$del_id");
    header('Location: products.php?msg=deleted');
    exit;
}

// Toggle featured
if (isset($_GET['toggle_featured']) && is_numeric($_GET['toggle_featured'])) {
    $tf_id = (int)$_GET['toggle_featured'];
    mysqli_query($conn, "UPDATE products SET is_featured = NOT is_featured WHERE id=$tf_id");
    header('Location: products.php?msg=updated');
    exit;
}

// Alert message
$alert = '';
if (isset($_GET['msg'])) {
    $alert = match($_GET['msg']) {
        'added'   => ['success', 'Produk berhasil ditambahkan!'],
        'updated' => ['success', 'Produk berhasil diperbarui!'],
        'deleted' => ['success', 'Produk berhasil dihapus.'],
        default   => ['', ''],
    };
}

// Search
$search = sanitize($conn, $_GET['search'] ?? '');
$where = $search ? "WHERE p.name LIKE '%$search%' OR p.era LIKE '%$search%' OR c.name LIKE '%$search%'" : '';

// Pagination
$per_page = 10;
$pg = max(1, (int)($_GET['pg'] ?? 1));
$offset = ($pg - 1) * $per_page;
$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM products p LEFT JOIN categories c ON p.category_id = c.id $where"))['t'];
$total_pages = ceil($total / $per_page);

$products = mysqli_query($conn, "
    SELECT p.*, c.name as cat_name
    FROM products p LEFT JOIN categories c ON p.category_id = c.id
    $where
    ORDER BY p.created_at DESC
    LIMIT $per_page OFFSET $offset
");

// Include admin layout header
include 'admin_header.php';
?>

<div class="p-6">
    <!-- Page header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="font-serif text-2xl font-bold text-maroon-800">Kelola Produk</h1>
            <p class="text-gray-500 text-sm">Total <?= $total ?> produk</p>
        </div>
        <a href="add_product.php" class="bg-maroon-800 hover:bg-maroon-700 text-white font-bold px-5 py-2.5 rounded-xl flex items-center space-x-2 transition-colors">
            <i class="fas fa-plus"></i>
            <span>Tambah Produk</span>
        </a>
    </div>

    <!-- Alert -->
    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } if ($alert && $alert[0]): ?>
    <div class="<?= $alert[0]==='success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800' ?> border rounded-xl p-4 mb-5 flex items-center space-x-2">
        <i class="fas fa-check-circle"></i>
        <span><?= $alert[1] ?></span>
    </div>
    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } endif; ?>

    <!-- Search -->
    <form method="GET" class="mb-5">
        <div class="relative max-w-md">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                   placeholder="Cari produk..."
                   class="w-full border border-gray-200 rounded-xl pl-4 pr-10 py-2.5 text-sm focus:outline-none focus:border-gold-400">
            <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-maroon-700">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </form>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-maroon-800 text-white text-left">
                        <th class="px-4 py-3 font-semibold">Produk</th>
                        <th class="px-4 py-3 font-semibold hidden md:table-cell">Kategori</th>
                        <th class="px-4 py-3 font-semibold hidden lg:table-cell">Harga</th>
                        <th class="px-4 py-3 font-semibold hidden lg:table-cell">Kondisi</th>
                        <th class="px-4 py-3 font-semibold hidden sm:table-cell">Stok</th>
                        <th class="px-4 py-3 font-semibold text-center">Featured</th>
                        <th class="px-4 py-3 font-semibold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } if(mysqli_num_rows($products) === 0): ?>
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-400">
                            <i class="fas fa-box-open text-4xl mb-3 block text-gray-300"></i>
                            Tidak ada produk ditemukan
                        </td>
                    </tr>
                    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } endif; ?>
                    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } while($p = mysqli_fetch_assoc($products)): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-12 h-12 bg-cream-200 rounded-xl overflow-hidden shrink-0 flex items-center justify-center">
                                    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } if($p['image'] && file_exists('../uploads/'.$p['image'])): ?>
                                        <img src="<?= BASE_URL ?>/uploads/<?= $p['image'] ?>" class="w-full h-full object-cover">
                                    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } endif; ?>
                                </div>
                                <div>
                                    <p class="font-semibold text-maroon-800 line-clamp-1"><?= htmlspecialchars($p['name']) ?></p>
                                    <p class="text-xs text-gray-400"><?= htmlspecialchars($p['era']) ?> • <?= htmlspecialchars($p['origin']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <span class="bg-gold-100 text-gold-700 text-xs px-2 py-1 rounded-full font-semibold"><?= htmlspecialchars($p['cat_name']) ?></span>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell font-bold text-maroon-700"><?= formatRupiah($p['price']) ?></td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <span class="text-xs px-2 py-0.5 rounded-full font-bold
                                <?= match($p['condition_status']) {
                                    'Excellent'  => 'bg-green-100 text-green-700',
                                    'Very Good'  => 'bg-blue-100 text-blue-700',
                                    'Good'       => 'bg-yellow-100 text-yellow-700',
                                    default      => 'bg-red-100 text-red-700'
                                } ?>">
                                <?= $p['condition_status'] ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden sm:table-cell text-center">
                            <span class="<?= $p['stock'] > 0 ? 'text-green-600' : 'text-red-500' ?> font-bold"><?= $p['stock'] ?></span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="?toggle_featured=<?= $p['id'] ?><?= $search ? '&search='.urlencode($search) : '' ?>"
                               class="text-xl <?= $p['is_featured'] ? 'text-gold-500 hover:text-gray-400' : 'text-gray-300 hover:text-gold-500' ?> transition-colors">
                                <i class="fas fa-star"></i>
                            </a>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-center space-x-2">
                                <a href="<?= BASE_URL ?>/product.php?id=<?= $p['id'] ?>" target="_blank"
                                   class="w-8 h-8 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg flex items-center justify-center transition-colors" title="Lihat">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <a href="edit_product.php?id=<?= $p['id'] ?>"
                                   class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center transition-colors" title="Edit">
                                    <i class="fas fa-pencil text-xs"></i>
                                </a>
                                <a href="?delete=<?= $p['id'] ?><?= $search ? '&search='.urlencode($search) : '' ?>"
                                   onclick="return confirm('Yakin ingin menghapus produk ini?')"
                                   class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg flex items-center justify-center transition-colors" title="Hapus">
                                    <i class="fas fa-trash text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } if($total_pages > 1): ?>
        <div class="flex justify-between items-center px-5 py-4 border-t border-gray-100">
            <p class="text-sm text-gray-500">Hal <?= $pg ?> dari <?= $total_pages ?></p>
            <div class="flex space-x-1">
                <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } for($i=1; $i<=$total_pages; $i++): ?>
                <a href="?pg=<?= $i ?><?= $search ? '&search='.urlencode($search) : '' ?>"
                   class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold transition-colors
                          <?= $i===$pg ? 'bg-maroon-800 text-white' : 'bg-gray-100 text-gray-600 hover:bg-maroon-100 hover:text-maroon-700' ?>">
                    <?= $i ?>
                </a>
                <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } endfor; ?>
            </div>
        </div>
        <?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } endif; ?>
    </div>
</div>

<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header('Location: ' . (defined('BASE_URL') ? BASE_URL : '') . '/admin/login.php'); exit; } include 'admin_footer.php'; ?>
