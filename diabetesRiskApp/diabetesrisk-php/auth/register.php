<?php
/**
 * auth/register.php — Halaman Registrasi DiabetesRisk
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) session_start();

if (isLoggedIn()) {
    header('Location: /diabetesrisk-php/');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm']  ?? '');

    if (!$nama || !$email || !$password || !$confirm) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $pdo = getDB();
        $check = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $check->execute([$email]);
        if ($check->fetch()) {
            $error = 'Email sudah terdaftar.';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $ins    = $pdo->prepare('INSERT INTO users (nama, email, password) VALUES (?, ?, ?)');
            $ins->execute([$nama, $email, $hashed]);
            $success = 'Akun berhasil dibuat! Silakan login.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar | DiabetesRisk</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons@4.29.1/dist/feather.min.js"></script>
    <link rel="stylesheet" href="/diabetesrisk-php/assets/css/style.css">
</head>
<body class="auth-body">

<div class="auth-container">
    <!-- Left Panel -->
    <div class="auth-left">
        <div class="auth-left-content">
            <div class="auth-brand">
                <div class="brand-icon-lg">
                    <i data-feather="heart"></i>
                </div>
                <h1>DiabetesRisk</h1>
                <p>Buat akun untuk mulai memprediksi risiko diabetes Anda</p>
            </div>
            <div class="auth-features">
                <div class="feature-item">
                    <i data-feather="activity"></i>
                    <span>Analisis 8 parameter klinis sekaligus</span>
                </div>
                <div class="feature-item">
                    <i data-feather="bar-chart-2"></i>
                    <span>Riwayat prediksi tersimpan otomatis</span>
                </div>
                <div class="feature-item">
                    <i data-feather="info"></i>
                    <span>Informasi model dan dataset lengkap</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel -->
    <div class="auth-right">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Buat Akun Baru</h2>
                <p>Daftar dan mulai prediksi sekarang</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <i data-feather="alert-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <?php if ($success): ?>
            <div class="alert alert-success">
                <i data-feather="check-circle"></i>
                <span><?= htmlspecialchars($success) ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="" id="registerForm" class="auth-form" novalidate>
                <div class="form-group">
                    <label for="nama" class="form-label">Nama Lengkap</label>
                    <div class="input-wrapper">
                        <i data-feather="user" class="input-icon"></i>
                        <input type="text" id="nama" name="nama" class="form-input"
                               placeholder="Nama lengkap Anda"
                               value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                               required autocomplete="name">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <div class="input-wrapper">
                        <i data-feather="mail" class="input-icon"></i>
                        <input type="email" id="email" name="email" class="form-input"
                               placeholder="nama@email.com"
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               required autocomplete="email">
                    </div>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-wrapper">
                        <i data-feather="lock" class="input-icon"></i>
                        <input type="password" id="password" name="password" class="form-input"
                               placeholder="Minimal 6 karakter" required minlength="6">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i data-feather="eye" id="eye-password"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm" class="form-label">Konfirmasi Password</label>
                    <div class="input-wrapper">
                        <i data-feather="lock" class="input-icon"></i>
                        <input type="password" id="confirm" name="confirm" class="form-input"
                               placeholder="Ulangi password" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('confirm')">
                            <i data-feather="eye" id="eye-confirm"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full">
                    <i data-feather="user-plus"></i>
                    <span>Daftar Sekarang</span>
                </button>
            </form>

            <p class="auth-switch">
                Sudah punya akun?
                <a href="/diabetesrisk-php/auth/login.php">Masuk di sini</a>
            </p>
        </div>
    </div>
</div>

<script>
    feather.replace();
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon  = document.getElementById('eye-' + id);
        if (input.type === 'password') {
            input.type = 'text';
            icon.setAttribute('data-feather', 'eye-off');
        } else {
            input.type = 'password';
            icon.setAttribute('data-feather', 'eye');
        }
        feather.replace();
    }
</script>
</body>
</html>
