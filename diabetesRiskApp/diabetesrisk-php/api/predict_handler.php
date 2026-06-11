<?php
/**
 * api/predict_handler.php — PHP Proxy ke Flask API
 * Menerima JSON POST dari JavaScript, forward ke Flask,
 * simpan hasil ke MySQL, kembalikan JSON ke browser.
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/api.php';

if (session_status() === PHP_SESSION_NONE) session_start();

// ── Auth check ─────────────────────────────────────────────
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Anda harus login untuk melakukan prediksi.']);
    exit;
}

// ── Method check ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method tidak diizinkan.']);
    exit;
}

// ── Parse body ────────────────────────────────────────────
$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Request body tidak valid atau bukan JSON.']);
    exit;
}

// ── Validasi field ────────────────────────────────────────
$required = [
    'pregnancies', 'glucose', 'blood_pressure', 'skin_thickness',
    'insulin', 'bmi', 'diabetes_pedigree_function', 'age',
];
foreach ($required as $field) {
    if (!isset($data[$field]) || $data[$field] === '') {
        http_response_code(400);
        echo json_encode(['error' => "Field '$field' wajib diisi."]);
        exit;
    }
}

// ── Forward ke Flask API ───────────────────────────────────
$allowedModels = ['knn', 'dt', 'svm'];
$modelKey = isset($data['model_key']) && in_array($data['model_key'], $allowedModels)
    ? $data['model_key']
    : 'knn';

$payload = json_encode([
    'pregnancies'                => (float) $data['pregnancies'],
    'glucose'                    => (float) $data['glucose'],
    'blood_pressure'             => (float) $data['blood_pressure'],
    'skin_thickness'             => (float) $data['skin_thickness'],
    'insulin'                    => (float) $data['insulin'],
    'bmi'                        => (float) $data['bmi'],
    'diabetes_pedigree_function' => (float) $data['diabetes_pedigree_function'],
    'age'                        => (float) $data['age'],
    'model_key'                  => $modelKey,
]);

$ch = curl_init(FLASK_API_URL . '/predict');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
    CURLOPT_TIMEOUT        => FLASK_TIMEOUT,
]);
$raw      = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($curlErr) {
    http_response_code(503);
    echo json_encode([
        'error' => 'Tidak dapat terhubung ke Flask API. Pastikan Flask server berjalan di port 5000.',
        'detail' => $curlErr,
    ]);
    exit;
}

$result = json_decode($raw, true);

if ($httpCode !== 200 || isset($result['error'])) {
    http_response_code($httpCode ?: 500);
    echo json_encode(['error' => $result['error'] ?? 'Prediksi gagal.']);
    exit;
}

// ── Simpan ke database ────────────────────────────────────
$userId = currentUser()['id'];
$pdo    = getDB();

$stmt = $pdo->prepare(
    'INSERT INTO predictions
     (user_id, pregnancies, glucose, blood_pressure, skin_thickness,
      insulin, bmi, diabetes_pedigree_function, age, prediction_result, risk_level)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
);

$stmt->execute([
    $userId,
    (int)   $data['pregnancies'],
    (int)   $data['glucose'],
    (int)   $data['blood_pressure'],
    (int)   $data['skin_thickness'],
    (int)   $data['insulin'],
    (float) $data['bmi'],
    (float) $data['diabetes_pedigree_function'],
    (int)   $data['age'],
    $result['prediction'],
    $result['risk_level'],
]);

$result['saved'] = true;
$result['id']    = (int) $pdo->lastInsertId();

echo json_encode($result);
