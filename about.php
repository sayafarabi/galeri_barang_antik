<?php
require_once 'includes/config.php';
$page_title = 'Tentang Kami';
include 'includes/header.php';
?>

<!-- Page Hero -->
<div class="relative py-20 overflow-hidden" style="background: linear-gradient(135deg, #1a0a0e 0%, #4a0e18 50%, #3e2c0b 100%);">
    <div class="absolute inset-0 opacity-10" style="background-image: repeating-linear-gradient(45deg, #c9a84c 0, #c9a84c 1px, transparent 0, transparent 50%); background-size: 20px 20px;"></div>
    <div class="relative max-w-4xl mx-auto px-4 text-center">
        <i class="fas fa-building text-4xl text-gold-400 mb-4"></i>
        <h1 class="font-serif text-5xl text-white font-bold mb-4">Tentang Kami</h1>
        <div class="gold-divider my-4"></div>
        <p class="text-cream-200 text-lg leading-relaxed">Menghubungkan masa lalu dengan masa kini melalui koleksi barang antik yang autentik dan bernilai tinggi.</p>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">

    <!-- Who We Are -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-16">
        <div>
            <p class="text-gold-600 text-sm uppercase tracking-widest font-bold mb-3">Siapa Kami</p>
            <h2 class="font-serif text-4xl text-maroon-800 font-bold mb-5">Maroon Antique Gallery</h2>
            <div class="gold-divider mx-0 mb-6"></div>
            <p class="text-gray-600 leading-relaxed mb-4">
                Maroon Antique Gallery adalah bagian dari <strong>Maroon Company</strong>, sebuah tech-startup yang melakukan ekspansi ke berbagai lini bisnis. Gallery ini hadir sebagai platform kurator barang antik premium yang menghubungkan pecinta sejarah, kolektor, dan investor dengan koleksi-koleksi bersejarah terpilih.
            </p>
            <p class="text-gray-600 leading-relaxed mb-4">
                Didirikan dengan visi untuk melestarikan warisan budaya dunia, kami telah mengkurasi lebih dari ratusan item antik dari berbagai penjuru dunia — mulai dari Eropa, Asia, hingga Nusantara.
            </p>
            <p class="text-gray-600 leading-relaxed">
                Setiap koleksi kami telah melalui proses verifikasi ketat oleh tim ahli bersertifikat, memastikan keaslian dan nilai investasi jangka panjang bagi para pelanggan kami.
            </p>
        </div>
        <div class="bg-gradient-to-br from-maroon-800 to-maroon-900 rounded-3xl p-10 text-center">
            <div class="grid grid-cols-2 gap-6">
                <?php
                $stats = [
                    ['10+', 'Tahun Pengalaman'],
                    ['500+', 'Koleksi Tersedia'],
                    ['1000+', 'Pelanggan Puas'],
                    ['50+', 'Negara Asal'],
                ];
                foreach($stats as $s): ?>
                <div class="text-center">
                    <div class="font-serif text-4xl font-bold text-gold-400"><?= $s[0] ?></div>
                    <div class="text-cream-300 text-sm mt-1"><?= $s[1] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Visi Misi -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-16">
        <div class="bg-cream-100 rounded-3xl p-8 border border-cream-200">
            <div class="w-12 h-12 bg-maroon-800 rounded-full flex items-center justify-center mb-5">
                <i class="fas fa-eye text-gold-400"></i>
            </div>
            <h3 class="font-serif text-2xl text-maroon-800 font-bold mb-4">Visi Kami</h3>
            <p class="text-gray-600 leading-relaxed">
                Menjadi galeri barang antik digital terkemuka di Asia Tenggara yang menghubungkan warisan budaya dunia dengan generasi modern, menjadikan sejarah sebagai investasi yang berharga dan aksesibel.
            </p>
        </div>
        <div class="bg-maroon-800 rounded-3xl p-8">
            <div class="w-12 h-12 bg-gold-500 rounded-full flex items-center justify-center mb-5">
                <i class="fas fa-bullseye text-maroon-800"></i>
            </div>
            <h3 class="font-serif text-2xl text-gold-400 font-bold mb-4">Misi Kami</h3>
            <ul class="space-y-3 text-cream-300 text-sm">
                <li class="flex items-start space-x-2"><i class="fas fa-check text-gold-500 mt-0.5 shrink-0"></i><span>Mengkurasi koleksi antik autentik dengan standar verifikasi internasional</span></li>
                <li class="flex items-start space-x-2"><i class="fas fa-check text-gold-500 mt-0.5 shrink-0"></i><span>Menyediakan platform digital yang mudah dan transparan bagi kolektor</span></li>
                <li class="flex items-start space-x-2"><i class="fas fa-check text-gold-500 mt-0.5 shrink-0"></i><span>Memberikan edukasi tentang nilai historis dan estetika setiap koleksi</span></li>
                <li class="flex items-start space-x-2"><i class="fas fa-check text-gold-500 mt-0.5 shrink-0"></i><span>Membangun komunitas pecinta barang antik yang aktif dan berpengetahuan</span></li>
            </ul>
        </div>
    </div>

    <!-- Tim -->
    <div class="mb-16">
        <div class="text-center mb-10">
            <p class="text-gold-600 text-sm uppercase tracking-widest font-bold mb-2">Ahli Kami</p>
            <h2 class="font-serif text-4xl text-maroon-800 font-bold">Tim Kurator</h2>
            <div class="gold-divider mt-4"></div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $team = [
                ['fa-user-tie','Dr. Rafi Achmad Farabi','Direktur & Kurator Utama','20+ tahun pengalaman dalam dunia antik Asia Tenggara'],
                ['fa-user-graduate','Siti Rahayu','Spesialis Keramik & Porselen','Bersertifikat dari Christie\'s Education London'],
                ['fa-user','James van Berg','Kurator Furnitur Eropa','Mantan kurator Rijksmuseum Amsterdam'],
                ['fa-user-check','Priya Sharma','Appraisal & Valuation','Anggota RICS (Royal Institution of Chartered Surveyors)'],
            ];
            foreach($team as $t): ?>
            <div class="text-center bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:border-gold-300 hover:shadow-md transition-all">
                <div class="w-20 h-20 mx-auto bg-gradient-to-br from-maroon-700 to-maroon-900 rounded-full flex items-center justify-center mb-4 shadow-md">
                    <i class="fas <?= $t[0] ?> text-gold-400 text-2xl"></i>
                </div>
                <h3 class="font-serif text-lg font-bold text-maroon-800 mb-1"><?= $t[1] ?></h3>
                <p class="text-gold-600 text-xs font-bold uppercase tracking-wide mb-2"><?= $t[2] ?></p>
                <p class="text-gray-500 text-sm"><?= $t[3] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Proses Verifikasi -->
    <div class="bg-gradient-to-br from-cream-100 to-cream-200 rounded-3xl p-10">
        <div class="text-center mb-10">
            <h2 class="font-serif text-4xl text-maroon-800 font-bold">Proses Verifikasi Kami</h2>
            <p class="text-gray-600 mt-3">Setiap produk melewati 4 tahap verifikasi sebelum tersedia di galeri</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $steps = [
                ['1','fa-magnifying-glass','Inspeksi Awal','Tim kurator melakukan inspeksi fisik menyeluruh terhadap kondisi, material, dan karakteristik'],
                ['2','fa-microscope','Uji Autentifikasi','Pengujian laboratorium untuk memverifikasi usia, material, dan keaslian barang'],
                ['3','fa-file-contract','Dokumentasi','Pembuatan sertifikat keaslian, provenance, dan laporan kondisi lengkap'],
                ['4','fa-tag','Penetapan Harga','Appraisal nilai pasar berdasarkan kondisi, kelangkaan, dan permintaan kolektor'],
            ];
            foreach($steps as $s): ?>
            <div class="text-center">
                <div class="w-14 h-14 mx-auto bg-maroon-800 rounded-full flex items-center justify-center mb-4 shadow-md relative">
                    <i class="fas <?= $s[1] ?> text-gold-400 text-lg"></i>
                    <span class="absolute -top-1 -right-1 w-5 h-5 bg-gold-500 text-maroon-900 text-xs font-bold rounded-full flex items-center justify-center"><?= $s[0] ?></span>
                </div>
                <h3 class="font-serif text-base font-bold text-maroon-800 mb-2"><?= $s[2] ?></h3>
                <p class="text-gray-600 text-sm"><?= $s[3] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
