<?php
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) . ' — ' . SITE_NAME : SITE_NAME ?></title>
    <meta name="description" content="Maroon Antique Gallery — Kurator barang antik pilihan dari seluruh dunia.">

    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: { 50:'#fdf2f2',100:'#fde8e8',200:'#fbd5d5',300:'#f8b4b4',400:'#f17878',500:'#9b2335',600:'#7f1d2b',700:'#6b1624',800:'#5a111d',900:'#4a0e18' },
                        gold:   { 100:'#fef9e7',200:'#fdefc3',300:'#fbe09a',400:'#f8cc61',500:'#c9a84c',600:'#a07c30',700:'#7a5a1e',800:'#5c4214',900:'#3e2c0b' },
                        cream:  { 50:'#fdfaf5',100:'#faf4e8',200:'#f4e8d0',300:'#ead5b0' }
                    },
                    fontFamily: {
                        serif: ['Playfair Display','Georgia','serif'],
                        sans:  ['Lato','sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        body { font-family:'Lato',sans-serif; background-color:#fdfaf5; }
        h1,h2,h3,h4 { font-family:'Playfair Display',serif; }
        ::-webkit-scrollbar { width:6px; }
        ::-webkit-scrollbar-track { background:#faf4e8; }
        ::-webkit-scrollbar-thumb { background:#9b2335; border-radius:3px; }
        .gold-divider { width:60px; height:3px; background:linear-gradient(90deg,#c9a84c,#f8cc61,#c9a84c); margin:0 auto; }
        .product-card { transition:all .3s ease; }
        .product-card:hover { transform:translateY(-6px); box-shadow:0 20px 40px rgba(0,0,0,0.12); }
        .product-card:hover .card-img { transform:scale(1.07); }
        .card-img { transition:transform .4s ease; }
        .nav-active { color:#c9a84c !important; border-bottom:2px solid #c9a84c; }
        .hero-text { text-shadow:2px 2px 8px rgba(0,0,0,.5); }
        .btn-maroon { background:linear-gradient(135deg,#9b2335,#7f1d2b); transition:all .3s ease; }
        .btn-maroon:hover { background:linear-gradient(135deg,#7f1d2b,#5a111d); box-shadow:0 4px 20px rgba(155,35,53,.4); transform:translateY(-1px); }
        .btn-gold { background:linear-gradient(135deg,#c9a84c,#a07c30); transition:all .3s ease; }
        .btn-gold:hover { background:linear-gradient(135deg,#a07c30,#7a5a1e); box-shadow:0 4px 20px rgba(201,168,76,.4); transform:translateY(-1px); }
        .badge-condition-excellent  { background:#1a5c38; color:#fff; }
        .badge-condition-very-good  { background:#1a4d6b; color:#fff; }
        .badge-condition-good       { background:#5c4a1a; color:#fff; }
        .badge-condition-fair       { background:#5c1a1a; color:#fff; }
        .alert-success { background:#d4edda; border:1px solid #c3e6cb; color:#155724; }
        .alert-error   { background:#f8d7da; border:1px solid #f5c6cb; color:#721c24; }
        .page-active   { background:#9b2335; color:#fff; }
        #mobile-menu   { transition:max-height .35s ease,opacity .35s ease; overflow:hidden; max-height:0; opacity:0; }
        #mobile-menu.open { max-height:500px; opacity:1; }
        .line-clamp-2 { display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .line-clamp-1 { display:-webkit-box; -webkit-line-clamp:1; -webkit-box-orient:vertical; overflow:hidden; }

        /* Search overlay */
        #search-overlay { transition:opacity 0.25s ease; }
        #search-overlay.hidden { pointer-events:none; }

        /* Scroll progress bar */
        #progress-bar { transition:width 0.1s linear; }

        /* Tooltip */
        .tooltip { position:relative; }
        .tooltip::after { content:attr(data-tip); position:absolute; bottom:110%; left:50%; transform:translateX(-50%); background:#1a0a0e; color:#f8cc61; font-size:11px; white-space:nowrap; padding:4px 8px; border-radius:6px; opacity:0; pointer-events:none; transition:opacity 0.2s; }
        .tooltip:hover::after { opacity:1; }

        /* Floating cart badge — reserved for future */
        @keyframes bounce-soft { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-4px)} }
        .bounce-soft { animation:bounce-soft 2s infinite; }

        /* Image skeleton */
        .skeleton { background:linear-gradient(90deg,#f0e8d8 25%,#e8dcc8 50%,#f0e8d8 75%); background-size:200% 100%; animation:shimmer 1.5s infinite; }
        @keyframes shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
    </style>
</head>
<body class="text-gray-800">

<!-- ===== SCROLL PROGRESS BAR ===== -->
<div class="fixed top-0 left-0 z-[100] h-0.5 bg-gold-500" id="progress-bar" style="width:0%"></div>

<!-- ===== NAVBAR ===== -->
<nav class="bg-maroon-800 shadow-lg sticky top-0 z-50" id="navbar">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">

            <!-- Logo -->
            <a href="<?= BASE_URL ?>/index.php" class="flex items-center space-x-2 shrink-0 group">
                <div class="w-9 h-9 bg-gold-500 group-hover:bg-gold-400 rounded-full flex items-center justify-center transition-colors">
                    <i class="fas fa-gem text-maroon-800 text-sm"></i>
                </div>
                <div>
                    <span class="font-serif text-gold-400 font-bold text-lg leading-none block">Maroon</span>
                    <span class="text-cream-200 text-xs tracking-widest uppercase">Antique Gallery</span>
                </div>
            </a>

            <!-- Desktop Nav -->
            <div class="hidden md:flex items-center space-x-1">
                <?php
                $nav_items = [
                    'index'   => ['Beranda',     'fa-home'],
                    'catalog' => ['Katalog',     'fa-store'],
                    'about'   => ['Tentang Kami','fa-building'],
                    'contact' => ['Kontak',      'fa-envelope'],
                ];
                foreach ($nav_items as $page => $info): ?>
                    <a href="<?= BASE_URL ?>/<?= $page ?>.php"
                       class="flex items-center space-x-1.5 px-3 py-2 text-sm text-cream-200 hover:text-gold-400 transition-colors duration-200
                              <?= $current_page === $page ? 'nav-active' : '' ?>">
                        <i class="fas <?= $info[1] ?> text-xs"></i>
                        <span><?= $info[0] ?></span>
                    </a>
                <?php endforeach; ?>

                <!-- Search icon -->
                <button onclick="openSearch()"
                        class="ml-1 w-9 h-9 flex items-center justify-center text-cream-200 hover:text-gold-400 hover:bg-maroon-700 rounded-full transition-colors tooltip"
                        data-tip="Cari produk">
                    <i class="fas fa-search text-sm"></i>
                </button>
            </div>

            <!-- Mobile right -->
            <div class="flex items-center space-x-2 md:hidden">
                <button onclick="openSearch()" class="w-9 h-9 flex items-center justify-center text-cream-200 hover:text-gold-400">
                    <i class="fas fa-search"></i>
                </button>
                <button id="hamburger" class="w-9 h-9 flex items-center justify-center text-cream-200 hover:text-gold-400 focus:outline-none">
                    <i class="fas fa-bars text-xl" id="ham-icon"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden bg-maroon-900">
        <div class="px-4 py-3 space-y-1">
            <?php foreach ($nav_items as $page => $info): ?>
                <a href="<?= BASE_URL ?>/<?= $page ?>.php"
                   class="flex items-center space-x-2 px-3 py-2.5 rounded-lg text-cream-200 hover:text-gold-400 hover:bg-maroon-800 transition-colors
                          <?= $current_page === $page ? 'text-gold-400 bg-maroon-800' : '' ?>">
                    <i class="fas <?= $info[1] ?> text-sm w-5 text-center"></i>
                    <span><?= $info[0] ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</nav>

<!-- ===== SEARCH OVERLAY ===== -->
<div id="search-overlay"
     class="hidden fixed inset-0 bg-black bg-opacity-70 z-[60] flex items-start justify-center pt-24 px-4"
     onclick="closeSearchIfOutside(event)">
    <div class="w-full max-w-xl">
        <form action="<?= BASE_URL ?>/catalog.php" method="GET" id="search-form"
              class="bg-white rounded-2xl shadow-2xl overflow-hidden">
            <div class="flex items-center px-5 py-4 border-b border-gray-100">
                <i class="fas fa-search text-gold-500 text-lg mr-3 shrink-0"></i>
                <input type="text" name="search" id="search-input"
                       placeholder="Cari koleksi antik... (era, nama, asal)"
                       class="flex-1 text-base text-gray-700 outline-none bg-transparent"
                       autocomplete="off">
                <button type="button" onclick="closeSearch()"
                        class="ml-2 w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <!-- Quick suggestions -->
            <div class="px-5 py-3">
                <p class="text-xs text-gray-400 mb-2 uppercase tracking-wider">Pencarian populer</p>
                <div class="flex flex-wrap gap-2" id="suggestions">
                    <?php
                    $suggestions = mysqli_query($conn, "SELECT DISTINCT era FROM products WHERE era != '' ORDER BY RAND() LIMIT 5");
                    while ($s = mysqli_fetch_assoc($suggestions)): ?>
                    <button type="button"
                            onclick="document.getElementById('search-input').value='<?= htmlspecialchars($s['era']) ?>';document.getElementById('search-form').submit();"
                            class="text-xs bg-cream-100 hover:bg-gold-100 text-maroon-700 px-3 py-1.5 rounded-full border border-cream-200 hover:border-gold-300 transition-colors">
                        <?= htmlspecialchars($s['era']) ?>
                    </button>
                    <?php endwhile; ?>
                    <?php
                    $cat_sugg = mysqli_query($conn, "SELECT name, slug FROM categories ORDER BY RAND() LIMIT 3");
                    while ($cs = mysqli_fetch_assoc($cat_sugg)): ?>
                    <button type="button"
                            onclick="window.location='<?= BASE_URL ?>/catalog.php?category=<?= urlencode($cs['slug']) ?>'"
                            class="text-xs bg-maroon-100 hover:bg-maroon-200 text-maroon-700 px-3 py-1.5 rounded-full border border-maroon-200 transition-colors">
                        <?= htmlspecialchars($cs['name']) ?>
                    </button>
                    <?php endwhile; ?>
                </div>
            </div>
        </form>
        <p class="text-center text-white text-opacity-50 text-xs mt-3">Tekan <kbd class="bg-white bg-opacity-20 px-1.5 py-0.5 rounded text-white">ESC</kbd> untuk menutup</p>
    </div>
</div>

<script>
    // ── Hamburger ──
    const hamburger = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobile-menu');
    const hamIcon = document.getElementById('ham-icon');
    hamburger.addEventListener('click', function() {
        mobileMenu.classList.toggle('open');
        hamIcon.className = mobileMenu.classList.contains('open') ? 'fas fa-times text-xl' : 'fas fa-bars text-xl';
    });

    // ── Search overlay ──
    function openSearch() {
        document.getElementById('search-overlay').classList.remove('hidden');
        setTimeout(() => document.getElementById('search-input').focus(), 50);
        document.body.style.overflow = 'hidden';
    }
    function closeSearch() {
        document.getElementById('search-overlay').classList.add('hidden');
        document.body.style.overflow = '';
    }
    function closeSearchIfOutside(e) {
        if (e.target === document.getElementById('search-overlay')) closeSearch();
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSearch();
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') { e.preventDefault(); openSearch(); }
    });

    // ── Scroll progress bar ──
    window.addEventListener('scroll', function() {
        const scrollTop  = document.documentElement.scrollTop;
        const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const pct = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
        document.getElementById('progress-bar').style.width = pct + '%';
    });

    // ── Navbar shadow on scroll ──
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        navbar.style.boxShadow = window.scrollY > 10 ? '0 4px 20px rgba(0,0,0,0.3)' : '';
    });
</script>