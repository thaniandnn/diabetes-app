<?php
/**
 * config/database.php — Koneksi PDO ke MySQL
 * Ganti DB_HOST, DB_USER, DB_PASS sesuai konfigurasi XAMPP kamu.
 */

define('DB_HOST',    'localhost');
define('DB_PORT',    '3306');
define('DB_NAME',    'diabetesrisk_db');
define('DB_USER',    'root');
define('DB_PASS',    '');          // Default XAMPP: kosong
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo !== null) return $pdo;

    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=%s',
        DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
    );

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        die(json_encode([
            'error' => 'Koneksi database gagal: ' . $e->getMessage()
        ]));
    }

    return $pdo;
}
