<?php
// ================================================
// TOOL DIAGNOSA UPLOAD - Hapus file ini setelah selesai
// Akses: http://localhost/antique-gallery/check_upload.php
// ================================================

$upload_dir = __DIR__ . '/uploads/';
$results = [];

// 1. Cek folder uploads ada atau tidak
$results['folder_exists'] = is_dir($upload_dir);

// 2. Cek bisa ditulis atau tidak
$results['folder_writable'] = is_writable($upload_dir);

// 3. Cek php.ini settings
$results['upload_max_filesize'] = ini_get('upload_max_filesize');
$results['post_max_size']       = ini_get('post_max_size');
$results['file_uploads']        = ini_get('file_uploads');
$results['max_file_uploads']    = ini_get('max_file_uploads');

// 4. Cek ekstensi GD (untuk image processing)
$results['gd_enabled'] = extension_loaded('gd');

// 5. Coba buat file test di folder uploads
$test_file = $upload_dir . 'test_write_' . time() . '.txt';
$write_test = @file_put_contents($test_file, 'test');
$results['write_test'] = $write_test !== false;
if ($write_test !== false) @unlink($test_file); // hapus file test

// 6. Handle test upload via form
$upload_result = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_image'])) {
    $file = $_FILES['test_image'];
    if ($file['error'] === 0) {
        $ext  = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $dest = $upload_dir . 'test_upload_' . time() . '.' . $ext;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            $upload_result = ['success', '✅ Upload BERHASIL! File disimpan: uploads/' . basename($dest)];
            // Tampilkan sebentar lalu hapus
        } else {
            $upload_result = ['error', '❌ move_uploaded_file() GAGAL. Folder uploads tidak bisa ditulis.'];
        }
    } else {
        $err_msg = [
            0 => 'Tidak ada error',
            1 => 'File terlalu besar (melebihi upload_max_filesize di php.ini)',
            2 => 'File terlalu besar (melebihi MAX_FILE_SIZE di form)',
            3 => 'Upload hanya sebagian',
            4 => 'Tidak ada file yang diupload',
            6 => 'Folder temp tidak ditemukan',
            7 => 'Gagal menulis ke disk',
        ];
        $upload_result = ['error', '❌ Error kode ' . $file['error'] . ': ' . ($err_msg[$file['error']] ?? 'Unknown error')];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Diagnosa Upload - Maroon Antique</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-gray-50 p-6">
<div class="max-w-2xl mx-auto">

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="bg-maroon-800 p-5" style="background:#5a111d;">
            <h1 class="text-white font-bold text-xl">🔍 Diagnosa Fitur Upload Foto</h1>
            <p class="text-gray-300 text-sm mt-1">Maroon Antique Gallery</p>
        </div>

        <div class="p-6 space-y-3">
            <h2 class="font-bold text-gray-700 mb-3">Status Sistem:</h2>

            <?php
            $checks = [
                ['folder_exists',    '📁 Folder uploads/ ada'],
                ['folder_writable',  '✍️ Folder uploads/ bisa ditulis'],
                ['file_uploads',     '📤 Upload file diizinkan (php.ini)'],
                ['gd_enabled',       '🖼️ Ekstensi GD aktif'],
                ['write_test',       '💾 Test tulis file berhasil'],
            ];
            foreach ($checks as $c):
                $ok = (bool)$results[$c[0]];
            ?>
            <div class="flex items-center justify-between p-3 rounded-xl <?= $ok ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' ?>">
                <span class="text-sm font-medium <?= $ok ? 'text-green-800' : 'text-red-800' ?>"><?= $c[1] ?></span>
                <span class="text-sm font-bold <?= $ok ? 'text-green-600' : 'text-red-600' ?>">
                    <?= $ok ? '✅ OK' : '❌ MASALAH' ?>
                </span>
            </div>
            <?php endforeach; ?>

            <div class="p-3 rounded-xl bg-blue-50 border border-blue-200">
                <p class="text-sm text-blue-800">
                    📏 <strong>upload_max_filesize:</strong> <?= $results['upload_max_filesize'] ?> &nbsp;|&nbsp;
                    <strong>post_max_size:</strong> <?= $results['post_max_size'] ?>
                </p>
            </div>
        </div>
    </div>

    <!-- Solusi jika ada masalah -->
    <?php if (!$results['folder_exists'] || !$results['folder_writable'] || !$results['write_test']): ?>
    <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5 mb-6">
        <h2 class="font-bold text-yellow-800 mb-3">⚠️ Cara Memperbaiki:</h2>
        <div class="space-y-3 text-sm text-yellow-900">

            <div class="bg-white rounded-xl p-4 border border-yellow-200">
                <p class="font-bold mb-2">Opsi 1 — Buat folder uploads manual:</p>
                <ol class="list-decimal list-inside space-y-1">
                    <li>Buka Windows Explorer</li>
                    <li>Masuk ke folder <code class="bg-yellow-100 px-1 rounded">C:\laragon\www\antique-gallery\</code></li>
                    <li>Buat folder baru bernama <code class="bg-yellow-100 px-1 rounded">uploads</code></li>
                </ol>
            </div>

            <div class="bg-white rounded-xl p-4 border border-yellow-200">
                <p class="font-bold mb-2">Opsi 2 — Klik tombol di bawah (otomatis):</p>
                <form method="POST">
                    <input type="hidden" name="create_folder" value="1">
                    <button type="submit" name="action" value="create"
                            class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold px-4 py-2 rounded-lg text-sm">
                        🗂️ Buat Folder uploads/ Otomatis
                    </button>
                </form>
                <?php
                if (isset($_POST['action']) && $_POST['action'] === 'create') {
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0775, true);
                        echo '<p class="text-green-700 font-bold mt-2">✅ Folder berhasil dibuat! Refresh halaman ini.</p>';
                    } else {
                        echo '<p class="text-blue-700 mt-2">Folder sudah ada.</p>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Test Upload Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
        <h2 class="font-bold text-gray-700 mb-4">🧪 Test Upload Gambar:</h2>

        <?php if ($upload_result): ?>
        <div class="p-4 rounded-xl mb-4 <?= $upload_result[0] === 'success' ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-red-50 border border-red-200 text-red-800' ?>">
            <?= $upload_result[1] ?>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
            <div class="border-2 border-dashed border-gray-200 rounded-xl p-6 text-center">
                <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 mb-3 block"></i>
                <p class="text-sm text-gray-500 mb-3">Pilih gambar untuk ditest (JPG/PNG/WebP, maks 3MB)</p>
                <input type="file" name="test_image" accept=".jpg,.jpeg,.png,.webp" required
                       class="text-sm text-gray-600">
            </div>
            <button type="submit"
                    class="w-full text-white font-bold py-3 rounded-xl text-sm"
                    style="background:#5a111d;">
                🚀 Test Upload Sekarang
            </button>
        </form>
    </div>

    <!-- Panduan lengkap upload di admin -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-bold text-gray-700 mb-4">📸 Cara Upload Foto Produk di Admin:</h2>
        <ol class="space-y-3 text-sm text-gray-600">
            <li class="flex items-start space-x-3">
                <span class="w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs font-bold shrink-0">1</span>
                <span>Buka <a href="/antique-gallery/admin/add_product.php" class="text-blue-600 underline">Panel Admin → Tambah Produk</a></span>
            </li>
            <li class="flex items-start space-x-3">
                <span class="w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs font-bold shrink-0">2</span>
                <span>Isi semua field yang diperlukan (Nama, Kategori, Harga)</span>
            </li>
            <li class="flex items-start space-x-3">
                <span class="w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs font-bold shrink-0">3</span>
                <span>Di bagian <strong>"Foto Produk"</strong>, klik area kotak bergaris putus-putus</span>
            </li>
            <li class="flex items-start space-x-3">
                <span class="w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs font-bold shrink-0">4</span>
                <span>Pilih foto dari komputer kamu (JPG, PNG, atau WebP, maksimal 3MB)</span>
            </li>
            <li class="flex items-start space-x-3">
                <span class="w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs font-bold shrink-0">5</span>
                <span>Preview foto akan muncul di kotak upload</span>
            </li>
            <li class="flex items-start space-x-3">
                <span class="w-6 h-6 bg-gray-800 text-white rounded-full flex items-center justify-center text-xs font-bold shrink-0">6</span>
                <span>Klik <strong>"Simpan Produk"</strong> — foto otomatis tersimpan di folder <code class="bg-gray-100 px-1 rounded">uploads/</code></span>
            </li>
        </ol>

        <div class="mt-5 p-4 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-800">
            <strong>💡 Tips:</strong> Gunakan foto dengan rasio 1:1 (persegi) agar tampil lebih rapi di website. Ukuran ideal 800×800 pixel ke atas.
        </div>
    </div>

    <p class="text-center text-xs text-gray-400 mt-6">
        ⚠️ Hapus file <code>check_upload.php</code> setelah selesai diagnosa
    </p>
</div>
</body>
</html>
