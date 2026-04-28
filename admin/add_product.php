<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . BASE_URL . '/admin/login.php'); exit;
}
$page_title         = 'Tambah Produk';
$current_admin_page = 'add_product';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = sanitize($conn, $_POST['name']             ?? '');
    $cat_id    = (int)($_POST['category_id']                ?? 0);
    $desc      = sanitize($conn, $_POST['description']      ?? '');
    $price     = (float)str_replace(['.','Rp ',' '],['','',''], $_POST['price'] ?? '0');
    $stock     = (int)($_POST['stock']                      ?? 0);
    $era       = sanitize($conn, $_POST['era']              ?? '');
    $origin    = sanitize($conn, $_POST['origin']           ?? '');
    $condition = sanitize($conn, $_POST['condition_status'] ?? 'Good');
    $featured  = isset($_POST['is_featured']) ? 1 : 0;

    if (empty($name))  $errors[] = 'Nama produk wajib diisi.';
    if ($cat_id === 0) $errors[] = 'Kategori wajib dipilih.';
    if ($price <= 0)   $errors[] = 'Harga harus lebih dari 0.';

    $image_name = '';
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === 0) {
        $allowed = ['jpg','jpeg','png','webp'];
        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            $errors[] = 'Format gambar: JPG, PNG, atau WebP.';
        } elseif ($_FILES['image']['size'] > 3*1024*1024) {
            $errors[] = 'Ukuran gambar maksimal 3MB.';
        } else {
            $image_name = uniqid('prod_').'.'.$ext;
            if (!move_uploaded_file($_FILES['image']['tmp_name'], __DIR__.'/../uploads/'.$image_name)) {
                $errors[] = 'Gagal menyimpan gambar. Pastikan folder uploads/ bisa ditulis.';
                $image_name = '';
            }
        }
    }

    if (empty($errors)) {
        $slug = createSlug($name);
        $dup  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM products WHERE slug='$slug'"))['t'];
        if ($dup > 0) $slug .= '-'.time();

        $sql = "INSERT INTO products (category_id,name,slug,description,price,stock,era,origin,condition_status,image,is_featured)
                VALUES ($cat_id,'$name','$slug','$desc',$price,$stock,'$era','$origin','$condition','$image_name',$featured)";
        if (mysqli_query($conn, $sql)) {
            header('Location: products.php?msg=added'); exit;
        } else {
            $errors[] = 'Gagal menyimpan: '.mysqli_error($conn);
        }
    }
}

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");
include 'admin_header.php';
?>

<div class="p-6 max-w-3xl">
    <div class="flex items-center space-x-3 mb-6">
        <a href="<?= BASE_URL ?>/admin/products.php"
           class="w-9 h-9 bg-gray-100 hover:bg-gray-200 rounded-xl flex items-center justify-center transition-colors">
            <i class="fas fa-arrow-left text-gray-600 text-sm"></i>
        </a>
        <div>
            <h1 class="font-serif text-2xl font-bold text-maroon-800">Tambah Produk Baru</h1>
            <p class="text-gray-500 text-sm">Isi detail koleksi antik yang akan ditambahkan</p>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 mb-6">
        <p class="font-bold mb-1"><i class="fas fa-exclamation-triangle mr-2"></i>Terdapat kesalahan:</p>
        <ul class="list-disc list-inside text-sm space-y-0.5">
            <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="space-y-5">

        <!-- Informasi Dasar -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-serif text-lg font-bold text-maroon-800 mb-4 flex items-center space-x-2">
                <i class="fas fa-info-circle text-gold-500 text-base"></i><span>Informasi Dasar</span>
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Produk <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                           placeholder="Contoh: Kursi Rotan Victorian 1890"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-gold-400 focus:ring-1 focus:ring-gold-200">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori <span class="text-red-500">*</span></label>
                    <select name="category_id" required
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-gold-400">
                        <option value="">— Pilih Kategori —</option>
                        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($_POST['category_id']??0)==$cat['id']?'selected':'' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kondisi</label>
                    <select name="condition_status"
                            class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm bg-white focus:outline-none focus:border-gold-400">
                        <?php foreach (['Excellent','Very Good','Good','Fair'] as $c): ?>
                        <option value="<?= $c ?>" <?= ($_POST['condition_status']??'Good')===$c?'selected':'' ?>><?= $c ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="4"
                              placeholder="Ceritakan sejarah dan keistimewaan koleksi ini..."
                              class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:border-gold-400"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Harga & Stok -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-serif text-lg font-bold text-maroon-800 mb-4 flex items-center space-x-2">
                <i class="fas fa-tag text-gold-500 text-base"></i><span>Harga & Stok</span>
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Harga (Rp) <span class="text-red-500">*</span></label>
                    <input type="number" name="price" required min="0"
                           value="<?= htmlspecialchars($_POST['price'] ?? '') ?>"
                           placeholder="Contoh: 5000000"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-gold-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jumlah Stok</label>
                    <input type="number" name="stock" min="0"
                           value="<?= htmlspecialchars($_POST['stock'] ?? '1') ?>"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-gold-400">
                </div>
            </div>
        </div>

        <!-- Detail Sejarah -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-serif text-lg font-bold text-maroon-800 mb-4 flex items-center space-x-2">
                <i class="fas fa-history text-gold-500 text-base"></i><span>Detail Sejarah</span>
            </h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Era / Periode</label>
                    <input type="text" name="era"
                           value="<?= htmlspecialchars($_POST['era'] ?? '') ?>"
                           placeholder="Contoh: 1880-1910"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-gold-400">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Asal Negara/Daerah</label>
                    <input type="text" name="origin"
                           value="<?= htmlspecialchars($_POST['origin'] ?? '') ?>"
                           placeholder="Contoh: Belanda, Jepara"
                           class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-gold-400">
                </div>
            </div>
        </div>

        <!-- Foto -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <h2 class="font-serif text-lg font-bold text-maroon-800 mb-4 flex items-center space-x-2">
                <i class="fas fa-image text-gold-500 text-base"></i><span>Foto Produk</span>
            </h2>
            <label for="image-input" class="block border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-gold-400 cursor-pointer transition-colors group">
                <div id="preview-wrap" class="hidden mb-4">
                    <img id="preview-img" class="max-h-48 mx-auto rounded-xl object-contain shadow">
                </div>
                <div id="upload-hint" class="group-hover:opacity-70 transition-opacity">
                    <i class="fas fa-cloud-upload-alt text-4xl text-gray-300 mb-3 block"></i>
                    <p class="text-sm font-semibold text-gray-500">Klik untuk pilih foto</p>
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP · Maks 3MB</p>
                </div>
                <input type="file" id="image-input" name="image" accept=".jpg,.jpeg,.png,.webp" class="hidden"
                       onchange="previewImg(this)">
            </label>
        </div>

        <!-- Featured -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <label class="flex items-center space-x-3 cursor-pointer">
                <input type="checkbox" name="is_featured" value="1"
                       <?= isset($_POST['is_featured'])?'checked':'' ?>
                       class="w-5 h-5 rounded accent-maroon-700">
                <div>
                    <p class="font-semibold text-gray-800 text-sm">Jadikan Produk Unggulan</p>
                    <p class="text-xs text-gray-400">Akan tampil di halaman Beranda</p>
                </div>
            </label>
        </div>

        <!-- Buttons -->
        <div class="flex gap-3">
            <button type="submit"
                    class="flex-1 bg-maroon-800 hover:bg-maroon-700 text-white font-bold py-3.5 rounded-xl flex items-center justify-center space-x-2 transition-colors">
                <i class="fas fa-save"></i><span>Simpan Produk</span>
            </button>
            <a href="<?= BASE_URL ?>/admin/products.php"
               class="px-6 py-3.5 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl font-semibold transition-colors text-sm flex items-center">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-wrap').classList.remove('hidden');
            document.getElementById('upload-hint').classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'admin_footer.php'; ?>