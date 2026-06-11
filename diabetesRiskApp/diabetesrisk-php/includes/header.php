<?php
/**
 * includes/header.php — Global Navigation Header
 * Requires: $pageTitle variable set before including
 */
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../includes/auth.php';

$user        = currentUser();
$currentPage = $_GET['page'] ?? 'dashboard';

$navItems = [
    'dashboard'    => ['label' => 'Dashboard',         'icon' => 'grid'],
    'predict'      => ['label' => 'Prediksi Baru',     'icon' => 'activity'],
    'history'      => ['label' => 'Riwayat Prediksi',  'icon' => 'clock'],
    'dataset-info' => ['label' => 'Informasi Dataset', 'icon' => 'database'],

];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="DiabetesRisk — Prediksi risiko diabetes menggunakan Machine Learning KNN berbasis data klinis.">
    <title><?= htmlspecialchars($pageTitle ?? 'DiabetesRisk') ?> | DiabetesRisk</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons@4.29.1/dist/feather.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <!-- App CSS -->
    <link rel="stylesheet" href="/diabetesrisk-php/assets/css/style.css?v=<?= time() ?>">
</head>
<body>

<!-- ── Sidebar ──────────────────────────────────────────── -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon">
            <i data-feather="heart"></i>
        </div>
        <div class="brand-text">
            <span class="brand-name">DiabetesRisk</span>
            <span class="brand-tagline">AI Prediction</span>
        </div>
    </div>

    <nav class="sidebar-nav">
        <?php foreach ($navItems as $key => $item): ?>
            <a href="/diabetesrisk-php/?page=<?= $key ?>"
               class="nav-item <?= $currentPage === $key ? 'active' : '' ?>"
               id="nav-<?= $key ?>">
                <span class="nav-icon"><i data-feather="<?= $item['icon'] ?>"></i></span>
                <span class="nav-label"><?= $item['label'] ?></span>
                <?php if ($currentPage === $key): ?>
                    <span class="nav-indicator"></span>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">
                <?= strtoupper(substr($user['nama'], 0, 1)) ?>
            </div>
            <div class="user-info">
                <span class="user-name"><?= htmlspecialchars($user['nama']) ?></span>
                <span class="user-email"><?= htmlspecialchars($user['email']) ?></span>
            </div>
        </div>
        <a href="/diabetesrisk-php/auth/logout.php" class="btn-logout" title="Logout">
            <i data-feather="log-out"></i>
        </a>
    </div>
</aside>

<!-- ── Main Content Wrapper ─────────────────────────────── -->
<div class="main-wrapper">

    <!-- Top Bar -->
    <header class="topbar">
        <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
            <i data-feather="menu"></i>
        </button>
        <div class="topbar-title">
            <span><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></span>
        </div>
        <div class="topbar-right">
            <div class="topbar-time" id="currentTime"></div>
            <div class="notification-dot"></div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="page-content">
