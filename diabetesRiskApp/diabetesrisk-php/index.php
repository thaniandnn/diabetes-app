<?php
/**
 * index.php — Front controller / router utama
 */
require_once __DIR__ . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// Halaman yang tidak perlu login
$publicPages = ['login', 'register'];

$page = trim($_GET['page'] ?? 'dashboard');

// Validasi whitelist halaman
$allowedPages = ['dashboard', 'predict', 'history', 'dataset-info', 'about-model'];

if (!in_array($page, $allowedPages, true)) {
    $page = 'dashboard';
}

// Guard: redirect ke login jika belum login
if (!isLoggedIn()) {
    header('Location: /diabetesrisk-php/auth/login.php');
    exit;
}

// Map halaman ke file
$pageFile = __DIR__ . '/pages/' . $page . '.php';

if (!file_exists($pageFile)) {
    http_response_code(404);
    die('<h1>404 — Halaman tidak ditemukan</h1>');
}

include $pageFile;
