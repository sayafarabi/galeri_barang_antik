<!-- ===== FOOTER ===== -->
<footer class="bg-maroon-900 text-cream-200 mt-16">
    <div class="h-1 bg-gradient-to-r from-maroon-800 via-gold-500 to-maroon-800"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">

            <!-- Brand -->
            <div>
                <div class="flex items-center space-x-2 mb-4">
                    <div class="w-10 h-10 bg-gold-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-gem text-maroon-800"></i>
                    </div>
                    <div>
                        <span class="font-serif text-gold-400 font-bold text-xl block leading-none">Maroon</span>
                        <span class="text-cream-300 text-xs tracking-widest uppercase">Antique Gallery</span>
                    </div>
                </div>
                <p class="text-sm text-cream-300 leading-relaxed mb-4">Kurator barang antik pilihan dari seluruh penjuru dunia. Kami menghadirkan sejarah ke tangan Anda.</p>
                <div class="flex space-x-2">
                    <a href="#" class="w-9 h-9 bg-maroon-700 hover:bg-gold-500 rounded-full flex items-center justify-center transition-all hover:scale-110" title="Instagram">
                        <i class="fab fa-instagram text-sm"></i>
                    </a>
                    <a href="#" class="w-9 h-9 bg-maroon-700 hover:bg-gold-500 rounded-full flex items-center justify-center transition-all hover:scale-110" title="Facebook">
                        <i class="fab fa-facebook text-sm"></i>
                    </a>
                    <a href="https://wa.me/628123456789" target="_blank" class="w-9 h-9 bg-maroon-700 hover:bg-green-600 rounded-full flex items-center justify-center transition-all hover:scale-110" title="WhatsApp">
                        <i class="fab fa-whatsapp text-sm"></i>
                    </a>
                </div>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="font-serif text-gold-400 font-semibold text-lg mb-4">Navigasi</h4>
                <ul class="space-y-2 text-sm">
                    <?php
                    $links = [
                        ['index.php',   'Beranda'],
                        ['catalog.php', 'Katalog Produk'],
                        ['about.php',   'Tentang Kami'],
                        ['contact.php', 'Hubungi Kami'],
                    ];
                    foreach ($links as $l): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/<?= $l[0] ?>"
                           class="text-cream-300 hover:text-gold-400 transition-colors flex items-center space-x-1.5 group">
                            <i class="fas fa-chevron-right text-xs text-gold-600 group-hover:translate-x-1 transition-transform"></i>
                            <span><?= $l[1] ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Kategori -->
            <div>
                <h4 class="font-serif text-gold-400 font-semibold text-lg mb-4">Kategori</h4>
                <ul class="space-y-2 text-sm">
                    <?php
                    $cats_footer = mysqli_query($conn, "SELECT name, slug FROM categories ORDER BY name LIMIT 5");
                    while ($cat = mysqli_fetch_assoc($cats_footer)): ?>
                    <li>
                        <a href="<?= BASE_URL ?>/catalog.php?category=<?= urlencode($cat['slug']) ?>"
                           class="text-cream-300 hover:text-gold-400 transition-colors flex items-center space-x-1.5 group">
                            <i class="fas fa-chevron-right text-xs text-gold-600 group-hover:translate-x-1 transition-transform"></i>
                            <span><?= htmlspecialchars($cat['name']) ?></span>
                        </a>
                    </li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <!-- Kontak -->
            <div>
                <h4 class="font-serif text-gold-400 font-semibold text-lg mb-4">Informasi</h4>
                <ul class="space-y-3 text-sm text-cream-300">
                    <li class="flex items-start space-x-2">
                        <i class="fas fa-map-marker-alt text-gold-500 mt-1 w-4 shrink-0"></i>
                        <span>Jl. Antique Heritage No. 88, Jakarta Pusat 10110</span>
                    </li>
                    <li class="flex items-center space-x-2">
                        <i class="fas fa-phone text-gold-500 w-4 shrink-0"></i>
                        <a href="tel:+622112345678" class="hover:text-gold-400 transition-colors">+62 21 1234 5678</a>
                    </li>
                    <li class="flex items-center space-x-2">
                        <i class="fas fa-envelope text-gold-500 w-4 shrink-0"></i>
                        <a href="mailto:info@maroonantique.com" class="hover:text-gold-400 transition-colors">info@maroonantique.com</a>
                    </li>
                    <li class="flex items-center space-x-2">
                        <i class="fas fa-clock text-gold-500 w-4 shrink-0"></i>
                        <span>Sen–Sab: 09.00 – 18.00 WIB</span>
                    </li>
                </ul>

                <!-- Newsletter mini -->
                <div class="mt-5">
                    <p class="text-xs text-gold-400 font-bold uppercase tracking-wider mb-2">Info Koleksi Terbaru</p>
                    <div class="flex">
                        <input type="email" id="footer-email" placeholder="Email kamu"
                               class="flex-1 bg-maroon-800 border border-maroon-600 text-cream-200 text-xs px-3 py-2 rounded-l-lg focus:outline-none focus:border-gold-500 placeholder-maroon-400">
                        <button onclick="subscribeNewsletter()"
                                class="bg-gold-500 hover:bg-gold-400 text-maroon-900 font-bold text-xs px-3 py-2 rounded-r-lg transition-colors">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                    <p class="text-xs text-maroon-400 mt-1" id="sub-msg"></p>
                </div>
            </div>
        </div>

        <!-- Copyright -->
        <div class="border-t border-maroon-700 mt-10 pt-6 flex flex-col md:flex-row justify-between items-center text-xs text-cream-300">
            <p>© <?= date('Y') ?> <span class="text-gold-400 font-semibold">Maroon Antique Gallery</span>. All rights reserved.</p>
            <p class="mt-2 md:mt-0">Maroon Company &mdash; <span class="text-gold-500">Bringing History to Life</span></p>
        </div>
    </div>
</footer>

<!-- ===== BACK TO TOP ===== -->
<button id="back-to-top"
        onclick="window.scrollTo({top:0,behavior:'smooth'})"
        class="fixed bottom-6 right-6 w-11 h-11 btn-maroon text-white rounded-full shadow-xl flex items-center justify-center z-50 opacity-0 transition-all duration-300 hover:scale-110"
        style="pointer-events:none;" title="Kembali ke atas">
    <i class="fas fa-arrow-up text-sm"></i>
</button>

<!-- ===== WHATSAPP FLOATING BUTTON ===== -->
<a href="https://wa.me/628123456789?text=Halo, saya ingin bertanya tentang koleksi antik Maroon Gallery"
   target="_blank"
   class="fixed bottom-6 left-6 w-12 h-12 bg-green-500 hover:bg-green-600 text-white rounded-full shadow-xl flex items-center justify-center z-50 transition-all hover:scale-110 bounce-soft"
   title="Chat WhatsApp">
    <i class="fab fa-whatsapp text-xl"></i>
</a>

<script>
    // Back to top
    window.addEventListener('scroll', function() {
        const btn = document.getElementById('back-to-top');
        if (window.scrollY > 300) {
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
        } else {
            btn.style.opacity = '0';
            btn.style.pointerEvents = 'none';
        }
    });

    // Newsletter mock
    function subscribeNewsletter() {
        const email = document.getElementById('footer-email').value.trim();
        const msg   = document.getElementById('sub-msg');
        if (!email || !email.includes('@')) {
            msg.textContent = '⚠️ Masukkan email yang valid.';
            msg.style.color = '#f8b4b4';
            return;
        }
        msg.textContent = '✅ Terima kasih! Kamu akan mendapat info koleksi terbaru.';
        msg.style.color = '#c9a84c';
        document.getElementById('footer-email').value = '';
    }

    // Lazy load images
    if ('IntersectionObserver' in window) {
        const imgs = document.querySelectorAll('img[data-src]');
        const obs  = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.src = e.target.dataset.src;
                    e.target.removeAttribute('data-src');
                    obs.unobserve(e.target);
                }
            });
        });
        imgs.forEach(img => obs.observe(img));
    }

    // Animate elements on scroll
    const animEls = document.querySelectorAll('.product-card, .fade-in-up');
    if ('IntersectionObserver' in window) {
        const fadeObs = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.style.opacity  = '1';
                    e.target.style.transform = 'translateY(0)';
                    fadeObs.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });
        animEls.forEach(el => {
            el.style.opacity  = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            fadeObs.observe(el);
        });
    }
</script>
</body>
</html>