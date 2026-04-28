<?php
require_once 'includes/config.php';
$page_title = 'Beranda';

$featured    = mysqli_query($conn, "SELECT p.*, c.name as cat_name FROM products p LEFT JOIN categories c ON p.category_id=c.id WHERE p.is_featured=1 ORDER BY p.created_at DESC LIMIT 6");
$categories  = mysqli_query($conn, "SELECT c.*, COUNT(p.id) as total FROM categories c LEFT JOIN products p ON c.id=p.category_id GROUP BY c.id ORDER BY c.name");
$total_prod  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM products"))['t'];
$total_cats  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM categories"))['t'];

include 'includes/header.php';
?>

<!-- ===== HERO ===== -->
<section class="relative min-h-screen flex items-center justify-center overflow-hidden"
         style="background:linear-gradient(135deg,#1a0a0e 0%,#4a0e18 40%,#7f1d2b 70%,#3e2c0b 100%);">
    <div class="absolute inset-0 opacity-10"
         style="background-image:repeating-linear-gradient(45deg,#c9a84c 0,#c9a84c 1px,transparent 0,transparent 50%);background-size:20px 20px;"></div>
    <div class="absolute top-20 left-10 w-64 h-64 rounded-full opacity-10" style="background:radial-gradient(circle,#c9a84c,transparent);"></div>
    <div class="absolute bottom-20 right-10 w-96 h-96 rounded-full opacity-10" style="background:radial-gradient(circle,#9b2335,transparent);"></div>

    <div class="relative z-10 max-w-5xl mx-auto px-4 text-center">
        <div class="inline-flex items-center space-x-2 bg-gold-500 bg-opacity-20 border border-gold-500 rounded-full px-5 py-2 mb-6">
            <i class="fas fa-star text-gold-400 text-xs"></i>
            <span class="text-gold-300 text-sm tracking-widest uppercase font-bold">Galeri Barang Antik Premium</span>
            <i class="fas fa-star text-gold-400 text-xs"></i>
        </div>
        <h1 class="font-serif text-5xl md:text-7xl text-white hero-text font-bold leading-tight mb-4">
            Maroon<br><em class="text-gold-400">Antique</em> Gallery
        </h1>
        <div class="gold-divider mx-auto my-6"></div>
        <p class="text-cream-200 text-lg md:text-xl max-w-2xl mx-auto leading-relaxed mb-8">
            Kurator barang antik pilihan dari seluruh dunia. Setiap koleksi membawa cerita, sejarah, dan nilai yang tak ternilai.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= BASE_URL ?>/catalog.php"
               class="btn-gold text-white font-bold px-8 py-4 rounded-full text-base inline-flex items-center justify-center space-x-2 hover:scale-105 transition-transform">
                <i class="fas fa-store"></i><span>Jelajahi Koleksi</span>
            </a>
            <a href="<?= BASE_URL ?>/about.php"
               class="border border-cream-300 text-cream-200 hover:border-gold-400 hover:text-gold-400 font-bold px-8 py-4 rounded-full text-base inline-flex items-center justify-center space-x-2 transition-colors">
                <i class="fas fa-info-circle"></i><span>Tentang Kami</span>
            </a>
        </div>
        <div class="mt-16 grid grid-cols-3 gap-6 max-w-lg mx-auto">
            <div class="text-center">
                <div class="font-serif text-3xl font-bold text-gold-400"><?= $total_prod ?>+</div>
                <div class="text-cream-300 text-xs mt-1 uppercase tracking-wider">Koleksi</div>
            </div>
            <div class="text-center border-x border-maroon-600">
                <div class="font-serif text-3xl font-bold text-gold-400"><?= $total_cats ?>+</div>
                <div class="text-cream-300 text-xs mt-1 uppercase tracking-wider">Kategori</div>
            </div>
            <div class="text-center">
                <div class="font-serif text-3xl font-bold text-gold-400">100%</div>
                <div class="text-cream-300 text-xs mt-1 uppercase tracking-wider">Terjamin</div>
            </div>
        </div>
        <div class="mt-12 animate-bounce">
            <i class="fas fa-chevron-down text-gold-400 text-2xl"></i>
        </div>
    </div>
</section>

<!-- ===== KATEGORI ===== -->
<section class="py-16 bg-cream-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <p class="text-gold-600 text-sm uppercase tracking-widest font-bold mb-2">Jelajahi Berdasarkan</p>
            <h2 class="font-serif text-4xl text-maroon-800 font-bold">Kategori Koleksi</h2>
            <div class="gold-divider mt-4"></div>
        </div>
        <?php
        $icons  = ['fa-couch','fa-bowl-food','fa-clock','fa-palette','fa-ring'];
        $colors = ['bg-amber-50 hover:bg-amber-100','bg-blue-50 hover:bg-blue-100','bg-emerald-50 hover:bg-emerald-100','bg-rose-50 hover:bg-rose-100','bg-purple-50 hover:bg-purple-100'];
        $ci = 0;
        ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
            <a href="<?= BASE_URL ?>/catalog.php?category=<?= urlencode($cat['slug']) ?>"
               class="<?= $colors[$ci % 5] ?> rounded-2xl p-6 text-center group border border-gray-100 hover:border-gold-300 transition-all duration-300 hover:-translate-y-1">
                <div class="w-14 h-14 mx-auto bg-maroon-800 group-hover:bg-gold-500 rounded-full flex items-center justify-center mb-4 transition-colors duration-300">
                    <i class="fas <?= $icons[$ci % 5] ?> text-white text-xl"></i>
                </div>
                <h3 class="font-serif text-sm font-bold text-maroon-800 group-hover:text-gold-700 transition-colors leading-tight"><?= htmlspecialchars($cat['name']) ?></h3>
                <p class="text-gray-500 text-xs mt-1"><?= $cat['total'] ?> item</p>
            </a>
            <?php $ci++; endwhile; ?>
        </div>
    </div>
</section>

<!-- ===== PRODUK UNGGULAN ===== -->
<section class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-center mb-12">
            <div>
                <p class="text-gold-600 text-sm uppercase tracking-widest font-bold mb-2">Pilihan Terbaik</p>
                <h2 class="font-serif text-4xl text-maroon-800 font-bold">Koleksi Unggulan</h2>
                <div class="gold-divider mt-4 mx-0"></div>
            </div>
            <a href="<?= BASE_URL ?>/catalog.php" class="mt-6 md:mt-0 btn-maroon text-white font-bold px-6 py-3 rounded-full inline-flex items-center space-x-2">
                <span>Lihat Semua</span><i class="fas fa-arrow-right"></i>
            </a>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php while ($p = mysqli_fetch_assoc($featured)): ?>
            <div class="product-card bg-white rounded-2xl overflow-hidden shadow-md hover:shadow-xl border border-gray-100">
                <div class="overflow-hidden bg-cream-100 h-56 relative">
                    <?php if ($p['image'] && file_exists('uploads/'.$p['image'])): ?>
                        <img src="<?= BASE_URL ?>/uploads/<?= htmlspecialchars($p['image']) ?>"
                             alt="<?= htmlspecialchars($p['name']) ?>"
                             class="card-img w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-cream-200 to-cream-300">
                            <i class="fas fa-gem text-5xl text-maroon-300 mb-2"></i>
                            <span class="text-maroon-400 text-xs">Tidak ada gambar</span>
                        </div>
                    <?php endif; ?>
                    <span class="absolute top-3 right-3 text-xs font-bold px-2 py-1 rounded-full
                                 badge-condition-<?= strtolower(str_replace(' ','-',$p['condition_status'])) ?>">
                        <?= htmlspecialchars($p['condition_status']) ?>
                    </span>
                </div>
                <div class="p-5">
                    <p class="text-gold-600 text-xs uppercase tracking-widest font-bold mb-1"><?= htmlspecialchars($p['cat_name']) ?></p>
                    <h3 class="font-serif text-lg font-bold text-maroon-800 leading-tight mb-2"><?= htmlspecialchars($p['name']) ?></h3>
                    <div class="flex items-center space-x-3 text-xs text-gray-500 mb-3">
                        <span><i class="fas fa-history text-gold-500 mr-1"></i><?= htmlspecialchars($p['era']) ?></span>
                        <span><i class="fas fa-globe text-gold-500 mr-1"></i><?= htmlspecialchars($p['origin']) ?></span>
                    </div>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2 leading-relaxed"><?= htmlspecialchars($p['description']) ?></p>
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-maroon-700 text-lg"><?= formatRupiah($p['price']) ?></span>
                        <a href="<?= BASE_URL ?>/product.php?id=<?= $p['id'] ?>"
                           class="btn-maroon text-white text-xs font-bold px-4 py-2 rounded-full">
                            Detail <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- ===== KEUNGGULAN ===== -->
<section class="py-16 bg-maroon-800 relative overflow-hidden">
    <div class="absolute inset-0 opacity-5" style="background-image:repeating-linear-gradient(45deg,#c9a84c 0,#c9a84c 1px,transparent 0,transparent 50%);background-size:30px 30px;"></div>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-serif text-4xl text-white font-bold">Mengapa Maroon Antique?</h2>
            <div class="gold-divider mt-4"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $feats = [
                ['fa-certificate','Keaslian Terjamin','Setiap produk diverifikasi oleh kurator dan appraisal bersertifikat internasional.'],
                ['fa-shield-alt','Garansi Keaslian','Kami menjamin keaslian setiap barang antik yang kami jual dengan dokumen resmi.'],
                ['fa-truck','Pengiriman Aman','Pengiriman menggunakan kemasan khusus dengan asuransi penuh untuk menjaga keamanan koleksi.'],
                ['fa-handshake','Layanan Premium','Tim ahli kami siap membantu Anda menemukan koleksi yang tepat untuk investasi dan dekorasi.'],
            ];
            foreach ($feats as $f): ?>
            <div class="text-center p-6 rounded-2xl bg-maroon-700 border border-maroon-600 hover:border-gold-500 transition-colors">
                <div class="w-14 h-14 mx-auto bg-gold-500 rounded-full flex items-center justify-center mb-4">
                    <i class="fas <?= $f[0] ?> text-maroon-800 text-xl"></i>
                </div>
                <h3 class="font-serif text-lg text-gold-400 font-bold mb-2"><?= $f[1] ?></h3>
                <p class="text-cream-300 text-sm leading-relaxed"><?= $f[2] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== CTA ===== -->
<section class="py-16 bg-cream-100 text-center">
    <div class="max-w-2xl mx-auto px-4">
        <i class="fas fa-gem text-5xl text-gold-500 mb-6"></i>
        <h2 class="font-serif text-4xl text-maroon-800 font-bold mb-4">Temukan Koleksi Impian Anda</h2>
        <p class="text-gray-600 mb-8 leading-relaxed">Kami memiliki lebih dari <?= $total_prod ?> koleksi barang antik pilihan yang siap menjadi bagian dari warisan Anda.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="<?= BASE_URL ?>/catalog.php"
               class="btn-maroon text-white font-bold px-8 py-4 rounded-full inline-flex items-center justify-center space-x-2">
                <i class="fas fa-store"></i><span>Lihat Semua Koleksi</span>
            </a>
            <a href="<?= BASE_URL ?>/contact.php"
               class="btn-gold text-white font-bold px-8 py-4 rounded-full inline-flex items-center justify-center space-x-2">
                <i class="fas fa-phone"></i><span>Konsultasi Gratis</span>
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>