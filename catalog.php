<?php
require_once 'includes/config.php';
$page_title = 'Katalog Produk';

// ---- Filter & Pencarian ----
$where_clauses = [];
$params = [];
$types = '';

// Filter kategori
$cat_filter = '';
if (!empty($_GET['category'])) {
    $cat_slug = sanitize($conn, $_GET['category']);
    $cat_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id, name FROM categories WHERE slug='$cat_slug' LIMIT 1"));
    if ($cat_data) {
        $where_clauses[] = "p.category_id = " . (int)$cat_data['id'];
        $cat_filter = $cat_data['name'];
    }
}

// Pencarian
$search = '';
if (!empty($_GET['search'])) {
    $search = sanitize($conn, $_GET['search']);
    $where_clauses[] = "(p.name LIKE '%$search%' OR p.description LIKE '%$search%' OR p.era LIKE '%$search%' OR p.origin LIKE '%$search%')";
}

// Filter harga
$min_price = (int)($_GET['min_price'] ?? 0);
$max_price = (int)($_GET['max_price'] ?? 0);
if ($min_price > 0) $where_clauses[] = "p.price >= $min_price";
if ($max_price > 0) $where_clauses[] = "p.price <= $max_price";

// Filter kondisi
if (!empty($_GET['condition'])) {
    $cond = sanitize($conn, $_GET['condition']);
    $where_clauses[] = "p.condition_status = '$cond'";
}

// Sort
$sort = $_GET['sort'] ?? 'newest';
$order_by = match($sort) {
    'price_asc'  => 'p.price ASC',
    'price_desc' => 'p.price DESC',
    'name_asc'   => 'p.name ASC',
    default      => 'p.created_at DESC',
};

$where_sql = count($where_clauses) ? 'WHERE ' . implode(' AND ', $where_clauses) : '';

// Pagination
$per_page = 9;
$current_pg = max(1, (int)($_GET['pg'] ?? 1));
$offset = ($current_pg - 1) * $per_page;

$count_result = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM products p LEFT JOIN categories c ON p.category_id = c.id $where_sql"));
$total_items = $count_result['t'];
$total_pages = ceil($total_items / $per_page);

$products = mysqli_query($conn, "
    SELECT p.*, c.name as cat_name, c.slug as cat_slug
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    $where_sql
    ORDER BY $order_by
    LIMIT $per_page OFFSET $offset
");

$all_categories = mysqli_query($conn, "SELECT c.*, COUNT(p.id) as total FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.name");

// Build query string tanpa pg untuk pagination
$query_params = $_GET;
unset($query_params['pg']);
$base_query = http_build_query($query_params);

include 'includes/header.php';
?>

<!-- Page Header -->
<div class="bg-gradient-to-r from-maroon-900 to-maroon-700 py-12">
    <div class="max-w-7xl mx-auto px-4 text-center">
        <h1 class="font-serif text-4xl text-white font-bold mb-2">Katalog Koleksi</h1>
        <p class="text-cream-300">
            <?php if($cat_filter): ?>
                Menampilkan kategori: <span class="text-gold-400 font-semibold"><?= $cat_filter ?></span>
            <?php elseif($search): ?>
                Hasil pencarian: <span class="text-gold-400 font-semibold">"<?= htmlspecialchars($search) ?>"</span>
            <?php else: ?>
                Jelajahi koleksi barang antik pilihan kami
            <?php endif; ?>
        </p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex flex-col lg:flex-row gap-8">

        <!-- ===== SIDEBAR FILTER ===== -->
        <aside class="lg:w-72 shrink-0">
            <form method="GET" id="filter-form">
                <!-- Cari -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
                    <h3 class="font-serif text-maroon-800 font-bold text-lg mb-3">Pencarian</h3>
                    <div class="relative">
                        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                               placeholder="Cari koleksi antik..."
                               class="w-full border border-gray-200 rounded-xl pl-4 pr-10 py-2.5 text-sm focus:outline-none focus:border-gold-400">
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-maroon-500 hover:text-gold-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Kategori -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
                    <h3 class="font-serif text-maroon-800 font-bold text-lg mb-3">Kategori</h3>
                    <ul class="space-y-1">
                        <li>
                            <a href="catalog.php" class="flex justify-between items-center py-1.5 px-2 rounded-lg text-sm hover:bg-cream-100 transition-colors <?= empty($_GET['category']) ? 'text-maroon-700 font-bold bg-cream-100' : 'text-gray-600' ?>">
                                <span>Semua Kategori</span>
                                <span class="text-xs bg-gray-100 px-2 py-0.5 rounded-full"><?= $total_items_all = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM products"))['t'] ?></span>
                            </a>
                        </li>
                        <?php
                        mysqli_data_seek($all_categories, 0);
                        while($cat = mysqli_fetch_assoc($all_categories)): ?>
                        <li>
                            <a href="?category=<?= $cat['slug'] ?>"
                               class="flex justify-between items-center py-1.5 px-2 rounded-lg text-sm hover:bg-cream-100 transition-colors
                                      <?= ($_GET['category'] ?? '') === $cat['slug'] ? 'text-maroon-700 font-bold bg-cream-100' : 'text-gray-600' ?>">
                                <span><?= $cat['name'] ?></span>
                                <span class="text-xs bg-gray-100 px-2 py-0.5 rounded-full"><?= $cat['total'] ?></span>
                            </a>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <!-- Kondisi -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
                    <h3 class="font-serif text-maroon-800 font-bold text-lg mb-3">Kondisi</h3>
                    <?php
                    $conditions = ['Excellent','Very Good','Good','Fair'];
                    foreach($conditions as $cond): ?>
                    <label class="flex items-center space-x-2 py-1.5 cursor-pointer">
                        <input type="radio" name="condition" value="<?= $cond ?>"
                               <?= ($_GET['condition'] ?? '') === $cond ? 'checked' : '' ?>
                               class="accent-maroon-700">
                        <span class="text-sm text-gray-700"><?= $cond ?></span>
                    </label>
                    <?php endforeach; ?>
                    <label class="flex items-center space-x-2 py-1.5 cursor-pointer">
                        <input type="radio" name="condition" value="" <?= empty($_GET['condition']) ? 'checked' : '' ?> class="accent-maroon-700">
                        <span class="text-sm text-gray-700">Semua kondisi</span>
                    </label>
                </div>

                <!-- Harga -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-4">
                    <h3 class="font-serif text-maroon-800 font-bold text-lg mb-3">Rentang Harga</h3>
                    <div class="space-y-2">
                        <input type="number" name="min_price" value="<?= $_GET['min_price'] ?? '' ?>"
                               placeholder="Harga minimum" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-gold-400">
                        <input type="number" name="max_price" value="<?= $_GET['max_price'] ?? '' ?>"
                               placeholder="Harga maksimum" class="w-full border border-gray-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:border-gold-400">
                    </div>
                </div>

                <button type="submit" class="w-full btn-maroon text-white font-bold py-3 rounded-xl">
                    <i class="fas fa-filter mr-2"></i>Terapkan Filter
                </button>
                <a href="catalog.php" class="w-full block text-center mt-2 text-sm text-gray-500 hover:text-maroon-700 py-2">Reset Filter</a>
            </form>
        </aside>

        <!-- ===== PRODUCTS GRID ===== -->
        <div class="flex-1">

            <!-- Toolbar -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
                <p class="text-gray-600 text-sm">
                    Menampilkan <strong><?= $total_items ?></strong> produk
                    <?= $current_pg > 1 ? "(Hal $current_pg dari $total_pages)" : '' ?>
                </p>
                <select name="sort" form="filter-form" onchange="document.getElementById('filter-form').submit()"
                        class="border border-gray-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-gold-400 bg-white">
                    <option value="newest" <?= ($sort=='newest'?'selected':'') ?>>Terbaru</option>
                    <option value="price_asc" <?= ($sort=='price_asc'?'selected':'') ?>>Harga: Rendah ke Tinggi</option>
                    <option value="price_desc" <?= ($sort=='price_desc'?'selected':'') ?>>Harga: Tinggi ke Rendah</option>
                    <option value="name_asc" <?= ($sort=='name_asc'?'selected':'') ?>>Nama A-Z</option>
                </select>
            </div>

            <?php if($total_items === 0): ?>
            <!-- Empty state -->
            <div class="text-center py-20">
                <i class="fas fa-search text-5xl text-gray-300 mb-4"></i>
                <h3 class="font-serif text-xl text-gray-500 mb-2">Tidak Ada Produk Ditemukan</h3>
                <p class="text-gray-400 text-sm mb-6">Coba ubah filter atau kata kunci pencarian Anda.</p>
                <a href="catalog.php" class="btn-maroon text-white font-bold px-6 py-3 rounded-full inline-block">Reset Semua Filter</a>
            </div>

            <?php else: ?>
            <!-- Product Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                <?php while($p = mysqli_fetch_assoc($products)): ?>
                <div class="product-card bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg border border-gray-100">
                    <div class="overflow-hidden bg-cream-100 h-48 relative">
                        <?php if($p['image'] && file_exists('uploads/' . $p['image'])): ?>
                            <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($p['image']) ?>"
                                 alt="<?= htmlspecialchars($p['name']) ?>"
                                 class="card-img w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-cream-200 to-cream-300">
                                <i class="fas fa-gem text-4xl text-maroon-300 mb-2"></i>
                            </div>
                        <?php endif; ?>
                        <span class="absolute top-2 right-2 text-xs font-bold px-2 py-0.5 rounded-full
                                     badge-condition-<?= strtolower(str_replace(' ', '-', $p['condition_status'])) ?>">
                            <?= $p['condition_status'] ?>
                        </span>
                        <?php if($p['is_featured']): ?>
                        <span class="absolute top-2 left-2 text-xs font-bold px-2 py-0.5 rounded-full bg-gold-500 text-maroon-900">
                            ✦ Featured
                        </span>
                        <?php endif; ?>
                    </div>
                    <div class="p-4">
                        <p class="text-gold-600 text-xs uppercase tracking-widest font-bold mb-1"><?= htmlspecialchars($p['cat_name']) ?></p>
                        <h3 class="font-serif text-base font-bold text-maroon-800 leading-snug mb-2"><?= htmlspecialchars($p['name']) ?></h3>
                        <div class="flex items-center gap-3 text-xs text-gray-400 mb-2">
                            <span><i class="fas fa-history text-gold-500 mr-1"></i><?= htmlspecialchars($p['era']) ?></span>
                            <span><i class="fas fa-globe text-gold-500 mr-1"></i><?= htmlspecialchars($p['origin']) ?></span>
                        </div>
                        <div class="flex items-center justify-between mt-3">
                            <span class="font-bold text-maroon-700"><?= formatRupiah($p['price']) ?></span>
                            <a href="product.php?id=<?= $p['id'] ?>"
                               class="btn-maroon text-white text-xs font-bold px-3 py-1.5 rounded-full">Detail</a>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <!-- Pagination -->
            <?php if($total_pages > 1): ?>
            <div class="flex justify-center items-center space-x-2 mt-10">
                <?php if($current_pg > 1): ?>
                    <a href="?<?= $base_query ?>&pg=<?= $current_pg-1 ?>" class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center hover:bg-maroon-700 hover:text-white hover:border-maroon-700 transition-colors">
                        <i class="fas fa-chevron-left text-xs"></i>
                    </a>
                <?php endif; ?>

                <?php for($pg_i = 1; $pg_i <= $total_pages; $pg_i++): ?>
                    <a href="?<?= $base_query ?>&pg=<?= $pg_i ?>"
                       class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold transition-colors
                              <?= $pg_i === $current_pg ? 'page-active' : 'border border-gray-200 text-gray-600 hover:bg-maroon-700 hover:text-white hover:border-maroon-700' ?>">
                        <?= $pg_i ?>
                    </a>
                <?php endfor; ?>

                <?php if($current_pg < $total_pages): ?>
                    <a href="?<?= $base_query ?>&pg=<?= $current_pg+1 ?>" class="w-9 h-9 rounded-full border border-gray-200 flex items-center justify-center hover:bg-maroon-700 hover:text-white hover:border-maroon-700 transition-colors">
                        <i class="fas fa-chevron-right text-xs"></i>
                    </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
