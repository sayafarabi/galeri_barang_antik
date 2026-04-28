<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . BASE_URL . '/admin/login.php'); exit;
}
$page_title         = 'Kelola Kategori';
$current_admin_page = 'categories';

$errors  = [];
$success = '';

// Hapus kategori
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $count  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM products WHERE category_id=$del_id"))['t'];
    if ($count > 0) {
        $errors[] = "Kategori tidak bisa dihapus karena masih memiliki $count produk.";
    } else {
        mysqli_query($conn,"DELETE FROM categories WHERE id=$del_id");
        header('Location: categories.php?msg=deleted'); exit;
    }
}

// Tambah / Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id = (int)($_POST['edit_id'] ?? 0);
    $name    = sanitize($conn, $_POST['name']        ?? '');
    $desc    = sanitize($conn, $_POST['description'] ?? '');
    $slug    = createSlug($name);

    if (empty($name)) {
        $errors[] = 'Nama kategori wajib diisi.';
    } else {
        if ($edit_id > 0) {
            $dup = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM categories WHERE slug='$slug' AND id!=$edit_id"))['t'];
            if ($dup > 0) $slug .= '-'.time();
            mysqli_query($conn,"UPDATE categories SET name='$name',slug='$slug',description='$desc' WHERE id=$edit_id");
            header('Location: categories.php?msg=updated'); exit;
        } else {
            $dup = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM categories WHERE slug='$slug'"))['t'];
            if ($dup > 0) $slug .= '-'.time();
            mysqli_query($conn,"INSERT INTO categories (name,slug,description) VALUES ('$name','$slug','$desc')");
            header('Location: categories.php?msg=added'); exit;
        }
    }
}

// Edit mode
$edit_cat = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_cat = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM categories WHERE id=".(int)$_GET['edit']." LIMIT 1"));
}

$alert = null;
if (isset($_GET['msg'])) {
    $alert = match($_GET['msg']) {
        'added'   => ['success','Kategori berhasil ditambahkan!'],
        'updated' => ['success','Kategori berhasil diperbarui!'],
        'deleted' => ['success','Kategori berhasil dihapus.'],
        default   => null,
    };
}

$categories = mysqli_query($conn,"SELECT c.*, COUNT(p.id) as total FROM categories c LEFT JOIN products p ON c.id=p.category_id GROUP BY c.id ORDER BY c.name");

include 'admin_header.php';
?>

<div class="p-6">
    <div class="mb-6">
        <h1 class="font-serif text-2xl font-bold text-maroon-800">Kelola Kategori</h1>
        <p class="text-gray-500 text-sm">Tambah dan kelola kategori koleksi antik</p>
    </div>

    <?php if ($alert): ?>
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-4 mb-5 flex items-center space-x-2">
        <i class="fas fa-check-circle"></i><span><?= $alert[1] ?></span>
    </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
    <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-4 mb-5">
        <?php foreach($errors as $e): ?>
        <p class="flex items-center space-x-2"><i class="fas fa-exclamation-circle"></i><span><?= htmlspecialchars($e) ?></span></p>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sticky top-24">
                <h2 class="font-serif text-lg font-bold text-maroon-800 mb-4">
                    <?= $edit_cat ? '✏️ Edit Kategori' : '➕ Tambah Kategori' ?>
                </h2>
                <form method="POST">
                    <?php if ($edit_cat): ?>
                    <input type="hidden" name="edit_id" value="<?= $edit_cat['id'] ?>">
                    <?php endif; ?>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                Nama Kategori <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" required
                                   value="<?= htmlspecialchars($edit_cat['name'] ?? '') ?>"
                                   placeholder="Contoh: Furnitur Antik"
                                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-gold-400 focus:ring-1 focus:ring-gold-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                            <textarea name="description" rows="3"
                                      placeholder="Deskripsi singkat kategori..."
                                      class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm resize-none focus:outline-none focus:border-gold-400"><?= htmlspecialchars($edit_cat['description'] ?? '') ?></textarea>
                        </div>
                        <button type="submit"
                                class="w-full bg-maroon-800 hover:bg-maroon-700 text-white font-bold py-3 rounded-xl transition-colors flex items-center justify-center space-x-2">
                            <i class="fas <?= $edit_cat ? 'fa-save' : 'fa-plus' ?>"></i>
                            <span><?= $edit_cat ? 'Simpan Perubahan' : 'Tambah Kategori' ?></span>
                        </button>
                        <?php if ($edit_cat): ?>
                        <a href="<?= BASE_URL ?>/admin/categories.php"
                           class="w-full block text-center text-sm text-gray-500 hover:text-maroon-700 py-2 transition-colors">
                            Batal
                        </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="font-serif text-lg font-bold text-maroon-800">Daftar Kategori</h2>
                    <span class="text-xs text-gray-400"><?= mysqli_num_rows($categories) ?> kategori</span>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left border-b border-gray-100">
                            <th class="px-5 py-3 font-semibold text-gray-600">Nama</th>
                            <th class="px-5 py-3 font-semibold text-gray-600 hidden md:table-cell">Slug</th>
                            <th class="px-5 py-3 font-semibold text-gray-600 text-center">Produk</th>
                            <th class="px-5 py-3 font-semibold text-gray-600 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                        <tr class="hover:bg-gray-50 transition-colors <?= ($edit_cat && $edit_cat['id']==$cat['id'])?'bg-gold-50':'' ?>">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-maroon-800"><?= htmlspecialchars($cat['name']) ?></p>
                                <p class="text-xs text-gray-400 mt-0.5 line-clamp-1"><?= htmlspecialchars($cat['description']) ?></p>
                            </td>
                            <td class="px-5 py-4 hidden md:table-cell">
                                <code class="text-xs bg-gray-100 px-2 py-1 rounded text-gray-600"><?= htmlspecialchars($cat['slug']) ?></code>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <a href="<?= BASE_URL ?>/catalog.php?category=<?= urlencode($cat['slug']) ?>" target="_blank"
                                   class="inline-flex items-center space-x-1 bg-maroon-100 hover:bg-maroon-200 text-maroon-700 text-xs font-bold px-2.5 py-1 rounded-full transition-colors">
                                    <span><?= $cat['total'] ?></span>
                                    <i class="fas fa-external-link-alt text-xs"></i>
                                </a>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="<?= BASE_URL ?>/admin/categories.php?edit=<?= $cat['id'] ?>"
                                       title="Edit"
                                       class="w-8 h-8 bg-blue-50 hover:bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center transition-colors">
                                        <i class="fas fa-pencil text-xs"></i>
                                    </a>
                                    <a href="<?= BASE_URL ?>/admin/categories.php?delete=<?= $cat['id'] ?>"
                                       title="Hapus"
                                       onclick="return confirm('Hapus kategori \'<?= addslashes(htmlspecialchars($cat['name'])) ?>\'?\nKategori yang memiliki produk tidak bisa dihapus.')"
                                       class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg flex items-center justify-center transition-colors">
                                        <i class="fas fa-trash text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'admin_footer.php'; ?>