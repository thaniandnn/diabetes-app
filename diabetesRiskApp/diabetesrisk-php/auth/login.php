<?php
/**
 * auth/login.php — Halaman Login DiabetesRisk
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Redirect jika sudah login
if (isLoggedIn()) {
    header('Location: /diabetesrisk-php/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!$email || !$password) {
        $error = 'Email dan password wajib diisi.';
    } else {
        $pdo  = getDB();
        $stmt = $pdo->prepare('SELECT id, nama, email, password FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_nama']  = $user['nama'];
            $_SESSION['user_email'] = $user['email'];
            header('Location: /diabetesrisk-php/');
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | DiabetesRisk</title>
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
                <p>Platform prediksi risiko diabetes berbasis Machine Learning</p>
            </div>
            <div class="auth-features">
                <div class="feature-item">
                    <i data-feather="cpu"></i>
                    <span>Model KNN dengan akurasi 70.78%</span>
                </div>
                <div class="feature-item">
                    <i data-feather="shield"></i>
                    <span>Data tersimpan aman di database lokal</span>
                </div>
                <div class="feature-item">
                    <i data-feather="zap"></i>
                    <span>Prediksi instan berbasis data klinis</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel (Form) -->
    <div class="auth-right">
        <div class="auth-card">
            <div class="auth-header">
                <h2>Selamat Datang</h2>
                <p>Masuk ke akun DiabetesRisk Anda</p>
            </div>

            <?php if ($error): ?>
            <div class="alert alert-error">
                <i data-feather="alert-circle"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
            <?php endif; ?>

            <form method="POST" action="" id="loginForm" class="auth-form" novalidate>
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
                               placeholder="Masukkan password"
                               required autocomplete="current-password">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i data-feather="eye" id="eye-password"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full" id="loginBtn">
                    <i data-feather="log-in"></i>
                    <span>Masuk</span>
                </button>
            </form>

            <p class="auth-switch">
                Belum punya akun?
                <a href="/diabetesrisk-php/auth/register.php">Daftar sekarang</a>
            </p>

            <div class="auth-demo-hint">
                <i data-feather="info"></i>
                <span>Demo: admin@diabetesrisk.local / admin123</span>
            </div>
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
