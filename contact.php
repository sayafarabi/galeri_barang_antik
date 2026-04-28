<?php
require_once 'includes/config.php';
$page_title = 'Kontak';

$success = '';
$error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = sanitize($conn, $_POST['name'] ?? '');
    $email   = sanitize($conn, $_POST['email'] ?? '');
    $phone   = sanitize($conn, $_POST['phone'] ?? '');
    $subject = sanitize($conn, $_POST['subject'] ?? '');
    $message = sanitize($conn, $_POST['message'] ?? '');

    if (empty($name) || empty($email) || empty($message)) {
        $error = 'Nama, email, dan pesan wajib diisi.';
    } elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } else {
        $sql = "INSERT INTO messages (name, email, phone, subject, message) VALUES ('$name','$email','$phone','$subject','$message')";
        if (mysqli_query($conn, $sql)) {
            $success = 'Pesan Anda berhasil dikirim! Tim kami akan menghubungi Anda dalam 1x24 jam.';
        } else {
            $error = 'Gagal mengirim pesan. Silakan coba lagi.';
        }
    }
}

// Pre-fill product dari query string
$product_name = htmlspecialchars($_GET['product'] ?? '');

include 'includes/header.php';
?>

<!-- Hero -->
<div class="bg-gradient-to-r from-maroon-900 to-maroon-700 py-12">
    <div class="max-w-4xl mx-auto px-4 text-center">
        <i class="fas fa-envelope text-4xl text-gold-400 mb-3"></i>
        <h1 class="font-serif text-5xl text-white font-bold mb-3">Hubungi Kami</h1>
        <div class="gold-divider my-4"></div>
        <p class="text-cream-200">Konsultasikan kebutuhan koleksi antik Anda dengan tim ahli kami</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        <!-- Info Kontak -->
        <div class="lg:col-span-1 space-y-4">
            <div>
                <p class="text-gold-600 text-sm uppercase tracking-widest font-bold mb-2">Informasi</p>
                <h2 class="font-serif text-3xl text-maroon-800 font-bold">Detail Kontak</h2>
                <div class="gold-divider mx-0 mt-4"></div>
            </div>

            <?php
            $contacts = [
                ['fa-map-marker-alt','Alamat','Jl. Raya Cicatih, Bangbayang, Kec. Cicurug, Kabupaten Sukabumi, Jawa Barat'],
                ['fa-phone','Telepon','+62 21 1234 5678 (WA)'],
                ['fa-envelope','Email','info@maroonantique.com'],
                ['fa-clock','Jam Operasional','Senin – Jumat : 09.00 – 18.00 WIB <br> Sabtu : 09.00 – 15.00 WIB'],
            ];
            foreach($contacts as $c): ?>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm flex items-start space-x-4">
                <div class="w-10 h-10 bg-maroon-800 rounded-full flex items-center justify-center shrink-0">
                    <i class="fas <?= $c[0] ?> text-gold-400 text-sm"></i>
                </div>
                <div>
                    <p class="text-gold-600 text-xs font-bold uppercase tracking-wide mb-1"><?= $c[1] ?></p>
                    <p class="text-gray-700 text-sm leading-relaxed"><?= nl2br($c[2]) ?></p>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Social Media -->
            <div class="bg-maroon-800 rounded-2xl p-5">
                <p class="text-gold-400 font-bold text-sm uppercase tracking-wide mb-4">Media Sosial</p>
                <div class="space-y-2">
                    <a href="#" class="flex items-center space-x-3 text-cream-200 hover:text-gold-400 transition-colors">
                        <i class="fab fa-instagram text-lg w-5"></i>
                        <span class="text-sm">@maroonantique</span>
                    </a>
                    <a href="#" class="flex items-center space-x-3 text-cream-200 hover:text-gold-400 transition-colors">
                        <i class="fab fa-facebook text-lg w-5"></i>
                        <span class="text-sm">Maroon Antique Gallery</span>
                    </a>
                    <a href="https://wa.me/628123456789" target="_blank" class="flex items-center space-x-3 text-cream-200 hover:text-gold-400 transition-colors">
                        <i class="fab fa-whatsapp text-lg w-5"></i>
                        <span class="text-sm">+62 812 3456 7890</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h2 class="font-serif text-2xl text-maroon-800 font-bold mb-2">Kirim Pesan</h2>
                <p class="text-gray-500 text-sm mb-6">Isi form di bawah ini dan tim kami akan membalas dalam 1x24 jam kerja.</p>

                <?php if ($success): ?>
                <div class="alert-success rounded-xl p-4 mb-6 flex items-start space-x-3">
                    <i class="fas fa-check-circle text-green-600 mt-0.5"></i>
                    <p><?= $success ?></p>
                </div>
                <?php endif; ?>

                <?php if ($error): ?>
                <div class="alert-error rounded-xl p-4 mb-6 flex items-start space-x-3">
                    <i class="fas fa-exclamation-circle text-red-600 mt-0.5"></i>
                    <p><?= $error ?></p>
                </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" required placeholder="Nama Anda"
                                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold-400 focus:ring-1 focus:ring-gold-400 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Alamat Email <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" required placeholder="email@anda.com"
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold-400 focus:ring-1 focus:ring-gold-400 transition-colors">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nomor Telepon</label>
                            <input type="tel" name="phone" placeholder="+62 8xx xxxx xxxx"
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold-400 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Subjek</label>
                            <select name="subject" class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold-400 transition-colors bg-white">
                                <option value="">Pilih subjek</option>
                                <option value="Tanya produk" <?= ($_POST['subject']??'')==='Tanya produk'?'selected':'' ?>>Tanya Produk</option>
                                <option value="Konsultasi investasi" <?= ($_POST['subject']??'')==='Konsultasi investasi'?'selected':'' ?>>Konsultasi Investasi</option>
                                <option value="Appraisal barang" <?= ($_POST['subject']??'')==='Appraisal barang'?'selected':'' ?>>Appraisal Barang</option>
                                <option value="Kerjasama" <?= ($_POST['subject']??'')==='Kerjasama'?'selected':'' ?>>Kerjasama / Partnership</option>
                                <option value="Lainnya" <?= ($_POST['subject']??'')==='Lainnya'?'selected':'' ?>>Lainnya</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Pesan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="message" required rows="5" placeholder="Ceritakan kebutuhan Anda..."
                                  class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-gold-400 focus:ring-1 focus:ring-gold-400 transition-colors resize-none"><?= htmlspecialchars($_POST['message'] ?? ($product_name ? "Halo, saya tertarik dengan koleksi \"$product_name\". Mohon informasi lebih lanjut." : '')) ?></textarea>
                    </div>

                    <button type="submit" class="w-full btn-maroon text-white font-bold py-4 rounded-2xl flex items-center justify-center space-x-2 hover:scale-[1.01] transition-transform">
                        <i class="fas fa-paper-plane"></i>
                        <span>Kirim Pesan</span>
                    </button>

                    <p class="text-gray-400 text-xs text-center mt-4">
                        <i class="fas fa-lock mr-1"></i>Data Anda aman dan tidak akan dibagikan kepada pihak ketiga.
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
