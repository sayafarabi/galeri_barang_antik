<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/config.php';
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ' . BASE_URL . '/admin/login.php'); exit;
}
$page_title         = 'Pesan Masuk';
$current_admin_page = 'messages';

// Tandai semua dibaca
if (isset($_GET['mark_all_read'])) {
    mysqli_query($conn,"UPDATE messages SET is_read=1");
    header('Location: messages.php?msg=read'); exit;
}

// Hapus
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    mysqli_query($conn,"DELETE FROM messages WHERE id=".(int)$_GET['delete']);
    header('Location: messages.php?msg=deleted'); exit;
}

// Baca & view
$view_msg = null;
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $mid = (int)$_GET['view'];
    mysqli_query($conn,"UPDATE messages SET is_read=1 WHERE id=$mid");
    $view_msg = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM messages WHERE id=$mid LIMIT 1"));
}

$alert = null;
if (isset($_GET['msg'])) {
    $alert = match($_GET['msg']) {
        'deleted' => ['success','Pesan berhasil dihapus.'],
        'read'    => ['success','Semua pesan ditandai sudah dibaca.'],
        default   => null,
    };
}

$total   = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM messages"))['t'];
$unread  = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as t FROM messages WHERE is_read=0"))['t'];
$messages = mysqli_query($conn,"SELECT * FROM messages ORDER BY created_at DESC");

include 'admin_header.php';
?>

<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h1 class="font-serif text-2xl font-bold text-maroon-800">Pesan Masuk</h1>
            <p class="text-gray-500 text-sm">
                <?= $total ?> total &nbsp;·&nbsp;
                <span class="<?= $unread>0?'text-red-500 font-semibold':'text-gray-400' ?>"><?= $unread ?> belum dibaca</span>
            </p>
        </div>
        <?php if ($unread > 0): ?>
        <a href="?mark_all_read=1"
           onclick="return confirm('Tandai semua pesan sebagai sudah dibaca?')"
           class="flex items-center space-x-2 border border-gray-200 hover:bg-gray-50 text-gray-600 text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors">
            <i class="fas fa-check-double text-green-500"></i>
            <span>Tandai Semua Dibaca</span>
        </a>
        <?php endif; ?>
    </div>

    <?php if ($alert): ?>
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-4 mb-5 flex items-center space-x-2">
        <i class="fas fa-check-circle"></i><span><?= $alert[1] ?></span>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

        <!-- Daftar Pesan -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <?php
                $msg_count = 0;
                mysqli_data_seek($messages, 0);
                while ($m = mysqli_fetch_assoc($messages)):
                    $msg_count++;
                    $is_active = $view_msg && $view_msg['id'] == $m['id'];
                ?>
                <a href="<?= BASE_URL ?>/admin/messages.php?view=<?= $m['id'] ?>"
                   class="flex items-start p-4 border-b border-gray-50 hover:bg-gray-50 transition-colors
                          <?= $is_active ? 'bg-cream-100 border-l-4 !border-l-maroon-500' : '' ?>
                          <?= !$m['is_read'] && !$is_active ? 'bg-blue-50' : '' ?>">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 mr-3
                                <?= !$m['is_read'] ? 'bg-maroon-800' : 'bg-gray-100' ?>">
                        <i class="fas fa-user text-sm <?= !$m['is_read'] ? 'text-gold-400' : 'text-gray-400' ?>"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-center mb-0.5">
                            <p class="text-sm font-bold text-gray-800 truncate"><?= htmlspecialchars($m['name']) ?></p>
                            <?php if (!$m['is_read']): ?>
                            <span class="w-2 h-2 bg-red-500 rounded-full shrink-0 ml-2"></span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-gray-500 truncate"><?= htmlspecialchars($m['subject'] ?: 'Pesan dari website') ?></p>
                        <p class="text-xs text-gray-400 mt-0.5"><?= date('d M Y, H:i', strtotime($m['created_at'])) ?></p>
                    </div>
                </a>
                <?php endwhile; ?>
                <?php if ($msg_count === 0): ?>
                <div class="p-10 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-200 mb-3 block"></i>
                    <p class="text-gray-400 text-sm">Belum ada pesan masuk</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Detail Pesan -->
        <div class="lg:col-span-3">
            <?php if ($view_msg): ?>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
                <div class="p-5 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <h2 class="font-serif text-xl font-bold text-maroon-800">
                            <?= htmlspecialchars($view_msg['subject'] ?: 'Pesan Baru') ?>
                        </h2>
                        <p class="text-gray-400 text-sm mt-1">
                            <?= date('d F Y, H:i', strtotime($view_msg['created_at'])) ?> WIB
                        </p>
                    </div>
                    <a href="<?= BASE_URL ?>/admin/messages.php?delete=<?= $view_msg['id'] ?>"
                       onclick="return confirm('Hapus pesan ini?')"
                       class="w-8 h-8 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg flex items-center justify-center transition-colors">
                        <i class="fas fa-trash text-xs"></i>
                    </a>
                </div>

                <div class="p-5">
                    <!-- Pengirim -->
                    <div class="flex items-center space-x-4 p-4 bg-cream-50 border border-cream-200 rounded-xl mb-5">
                        <div class="w-12 h-12 bg-maroon-800 rounded-full flex items-center justify-center shrink-0">
                            <i class="fas fa-user text-gold-400"></i>
                        </div>
                        <div>
                            <p class="font-bold text-maroon-800"><?= htmlspecialchars($view_msg['name']) ?></p>
                            <div class="flex flex-wrap gap-3 mt-1">
                                <a href="mailto:<?= htmlspecialchars($view_msg['email']) ?>"
                                   class="text-sm text-blue-600 hover:underline flex items-center space-x-1">
                                    <i class="fas fa-envelope text-xs"></i>
                                    <span><?= htmlspecialchars($view_msg['email']) ?></span>
                                </a>
                                <?php if ($view_msg['phone']): ?>
                                <a href="tel:<?= htmlspecialchars($view_msg['phone']) ?>"
                                   class="text-sm text-green-600 hover:underline flex items-center space-x-1">
                                    <i class="fas fa-phone text-xs"></i>
                                    <span><?= htmlspecialchars($view_msg['phone']) ?></span>
                                </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Isi pesan -->
                    <div class="bg-gray-50 rounded-xl p-5 mb-5">
                        <p class="text-gray-800 leading-relaxed whitespace-pre-wrap text-sm">
                            <?= htmlspecialchars($view_msg['message']) ?>
                        </p>
                    </div>

                    <!-- Tombol balas -->
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="mailto:<?= htmlspecialchars($view_msg['email']) ?>?subject=Re: <?= urlencode($view_msg['subject'] ?? 'Pesan Anda') ?>&body=Halo <?= urlencode($view_msg['name']) ?>,"
                           class="flex-1 bg-maroon-800 hover:bg-maroon-700 text-white font-bold py-3 rounded-xl flex items-center justify-center space-x-2 transition-colors text-sm">
                            <i class="fas fa-reply"></i><span>Balas via Email</span>
                        </a>
                        <?php if ($view_msg['phone']): ?>
                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/','',$view_msg['phone']) ?>?text=Halo+<?= urlencode($view_msg['name']) ?>%2C+terima+kasih+sudah+menghubungi+Maroon+Antique+Gallery."
                           target="_blank"
                           class="flex-1 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl flex items-center justify-center space-x-2 transition-colors text-sm">
                            <i class="fab fa-whatsapp text-base"></i><span>Balas WhatsApp</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <?php else: ?>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm flex flex-col items-center justify-center p-16 text-center h-full min-h-64">
                <div class="w-20 h-20 bg-cream-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-envelope-open text-3xl text-maroon-300"></i>
                </div>
                <h3 class="font-serif text-xl text-maroon-800 font-bold mb-2">Pilih Pesan</h3>
                <p class="text-gray-400 text-sm">Klik salah satu pesan di sebelah kiri untuk membacanya</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'admin_footer.php'; ?>