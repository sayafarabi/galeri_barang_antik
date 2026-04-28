<?php
session_start();
require_once '../includes/config.php';

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: index.php');
    exit;
}

// Kredensial admin — bisa diubah sesuai kebutuhan
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', 'maroon2026'); // Ganti password sesuai keinginan

$error = '';
$attempts_key = 'login_attempts';
$lockout_key  = 'login_lockout';
$max_attempts = 5;
$lockout_time = 300; // 5 menit

// Cek apakah sedang dikunci
if (isset($_SESSION[$lockout_key]) && time() < $_SESSION[$lockout_key]) {
    $remaining = ceil(($_SESSION[$lockout_key] - time()) / 60);
    $error = "Terlalu banyak percobaan. Coba lagi dalam $remaining menit.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        // Login berhasil
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user']      = $username;
        $_SESSION['admin_login_time']= time();
        $_SESSION[$attempts_key]     = 0;
        header('Location: index.php');
        exit;
    } else {
        $_SESSION[$attempts_key] = ($_SESSION[$attempts_key] ?? 0) + 1;
        $sisa = $max_attempts - $_SESSION[$attempts_key];
        if ($_SESSION[$attempts_key] >= $max_attempts) {
            $_SESSION[$lockout_key] = time() + $lockout_time;
            $error = "Terlalu banyak percobaan. Akun dikunci 5 menit.";
        } else {
            $error = "Username atau password salah. Sisa percobaan: $sisa";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — <?= SITE_NAME ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        maroon: { 800:'#5a111d', 900:'#4a0e18', 700:'#6b1624' },
                        gold:   { 400:'#f8cc61', 500:'#c9a84c' },
                        cream:  { 200:'#f4e8d0', 300:'#ead5b0' }
                    },
                    fontFamily: { serif:['Playfair Display','serif'], sans:['Lato','sans-serif'] }
                }
            }
        }
    </script>
    <style>
        body { font-family:'Lato',sans-serif; }
        h1,h2 { font-family:'Playfair Display',serif; }
        .login-bg {
            background: linear-gradient(135deg, #1a0a0e 0%, #4a0e18 50%, #3e2c0b 100%);
            min-height: 100vh;
        }
        .pattern {
            background-image: repeating-linear-gradient(45deg, #c9a84c 0, #c9a84c 1px, transparent 0, transparent 50%);
            background-size: 20px 20px;
            opacity: 0.06;
        }
        .input-field {
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .input-field:focus {
            border-color: #c9a84c;
            box-shadow: 0 0 0 3px rgba(201,168,76,0.2);
            outline: none;
        }
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(20px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .fade-up { animation: fadeUp 0.5s ease forwards; }
        .shake { animation: shake 0.4s ease; }
        @keyframes shake {
            0%,100% { transform:translateX(0); }
            20%,60%  { transform:translateX(-8px); }
            40%,80%  { transform:translateX(8px); }
        }
    </style>
</head>
<body class="login-bg flex items-center justify-center p-4 relative overflow-hidden">
    <div class="pattern absolute inset-0 pointer-events-none"></div>

    <!-- Decorative circles -->
    <div class="absolute top-10 left-10 w-64 h-64 rounded-full opacity-10" style="background:radial-gradient(circle,#c9a84c,transparent);"></div>
    <div class="absolute bottom-10 right-10 w-96 h-96 rounded-full opacity-10" style="background:radial-gradient(circle,#9b2335,transparent);"></div>

    <div class="relative z-10 w-full max-w-md fade-up">

        <!-- Card -->
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden">

            <!-- Header -->
            <div class="p-8 text-center" style="background:linear-gradient(135deg,#5a111d,#3e2c0b);">
                <div class="w-16 h-16 bg-gold-500 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                    <i class="fas fa-gem text-maroon-900 text-2xl"></i>
                </div>
                <h1 class="text-white font-serif text-2xl font-bold">Maroon Admin</h1>
                <p class="text-cream-300 text-sm mt-1 tracking-widest uppercase">Panel Manajemen</p>
            </div>

            <!-- Form -->
            <div class="p-8">
                <p class="text-center text-gray-500 text-sm mb-6">Masukkan kredensial untuk melanjutkan</p>

                <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-3 mb-5 flex items-center space-x-2 shake" id="error-box">
                    <i class="fas fa-exclamation-circle shrink-0"></i>
                    <span class="text-sm"><?= htmlspecialchars($error) ?></span>
                </div>
                <?php endif; ?>

                <form method="POST" id="login-form">
                    <!-- Username -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            <i class="fas fa-user text-gold-500 mr-1"></i> Username
                        </label>
                        <input type="text" name="username" required autocomplete="username"
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                               placeholder="Masukkan username"
                               class="input-field w-full border border-gray-200 rounded-xl px-4 py-3 text-sm">
                    </div>

                    <!-- Password -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            <i class="fas fa-lock text-gold-500 mr-1"></i> Password
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password-input" required autocomplete="current-password"
                                   placeholder="Masukkan password"
                                   class="input-field w-full border border-gray-200 rounded-xl px-4 py-3 pr-11 text-sm">
                            <button type="button" onclick="togglePassword()"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-eye text-sm" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" id="login-btn"
                            class="w-full text-white font-bold py-3.5 rounded-xl text-sm flex items-center justify-center space-x-2 transition-all hover:opacity-90 hover:scale-[1.01]"
                            style="background:linear-gradient(135deg,#9b2335,#7f1d2b);">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Masuk ke Dashboard</span>
                    </button>
                </form>

                <div class="mt-6 p-3 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-700 text-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Default: <strong>admin</strong> / <strong>admin</strong>
                    <br><span class="text-amber-500">(Ganti di file admin/login.php)</span>
                </div>
            </div>
        </div>

        <p class="text-center text-cream-300 text-xs mt-6 opacity-60">
            © <?= date('Y') ?> Maroon Antique Gallery
        </p>
    </div>

    <script>
        function togglePassword() {
            const input = document.getElementById('password-input');
            const icon  = document.getElementById('eye-icon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'fas fa-eye-slash text-sm';
            } else {
                input.type = 'password';
                icon.className = 'fas fa-eye text-sm';
            }
        }

        // Loading state on submit
        document.getElementById('login-form').addEventListener('submit', function() {
            const btn = document.getElementById('login-btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Memverifikasi...</span>';
            btn.disabled = true;
        });
    </script>
</body>
</html>
