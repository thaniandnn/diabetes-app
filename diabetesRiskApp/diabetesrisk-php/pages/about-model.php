<?php
/**
 * pages/about-model.php — Informasi & Perbandingan Semua Model ML
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/api.php';
requireLogin();

// ── Fetch semua model dari Flask API ───────────────────────
$modelKeys = ['knn', 'dt', 'svm'];
$allModels = [];
$apiError  = null;

foreach ($modelKeys as $key) {
    $ch = curl_init(FLASK_API_URL . '/model-info?model_key=' . $key);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => FLASK_TIMEOUT,
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);
    $raw  = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err || $code !== 200) {
        $apiError = true;
        // Fallback ke file lokal
        $fallback = __DIR__ . '/../../diabetesrisk-api/metrics/' . $key . '_metrics.json';
        if (file_exists($fallback)) {
            $data = json_decode(file_get_contents($fallback), true);
            $data['model_key'] = $key;
            $allModels[$key]   = $data;
        }
    } else {
        $data = json_decode($raw, true);
        $allModels[$key] = $data;
    }
}

$pageTitle = 'Tentang Model';
require_once __DIR__ . '/../includes/header.php';

// ── Helper: model display info ─────────────────────────────
$modelMeta = [
    'knn' => ['label' => 'K-Nearest Neighbor', 'short' => 'KNN',
              'icon'  => 'git-merge',
              'color' => '#3b82f6', 'bg' => 'linear-gradient(135deg,#3b82f6,#1d4ed8)',
              'desc'  => 'Algoritma berbasis kedekatan jarak antar data point. Mengklasifikasikan berdasarkan mayoritas tetangga terdekat.'],
    'dt'  => ['label' => 'Decision Tree',       'short' => 'DT',
              'icon'  => 'git-branch',
              'color' => '#10b981', 'bg' => 'linear-gradient(135deg,#10b981,#065f46)',
              'desc'  => 'Algoritma berbasis pohon keputusan. Membuat serangkaian aturan berbasis fitur untuk mengklasifikasikan data.'],
    'svm' => ['label' => 'Support Vector Machine', 'short' => 'SVM',
              'icon'  => 'maximize-2',
              'color' => '#8b5cf6', 'bg' => 'linear-gradient(135deg,#8b5cf6,#5b21b6)',
              'desc'  => 'Algoritma yang mencari hyperplane optimal untuk memisahkan kelas. Efektif untuk data berdimensi tinggi.'],
];

$modelSteps = [
    'knn' => [
        ['Normalisasi Data',        'Data input dinormalisasi menggunakan <code>StandardScaler</code> agar semua fitur memiliki skala yang sama.'],
        ['Hitung Jarak Euclidean',  'Algoritma menghitung jarak antara data baru dengan <strong>semua data training</strong> menggunakan metrik Euclidean.'],
        ['Pilih K Tetangga Terdekat', 'Dipilih <strong>k=3</strong> data training dengan jarak terkecil sebagai "tetangga" dari data baru.'],
        ['Voting Berbobot',         'Kelas dipilih berdasarkan <strong>weighted voting</strong> (bobot = 1/jarak) dari k tetangga terdekat.'],
    ],
    'dt'  => [
        ['Normalisasi Data',        'Data input dinormalisasi menggunakan <code>StandardScaler</code> sebelum diproses.'],
        ['Split Fitur Terbaik',     'Pada setiap node, algoritma mencari fitur dan threshold yang memaksimalkan <strong>Information Gain (Gini)</strong>.'],
        ['Bangun Pohon Keputusan',  'Pohon dibangun secara rekursif hingga kedalaman maksimum <strong>max_depth=7</strong> atau semua daun murni.'],
        ['Klasifikasi Daun',        'Data baru ditelusuri dari root hingga daun, dan kelas mayoritas di daun tersebut menjadi prediksi.'],
    ],
    'svm' => [
        ['Normalisasi Data',        'Data input dinormalisasi menggunakan <code>StandardScaler</code> khusus untuk SVM.'],
        ['Mapping ke Ruang Tinggi', 'Kernel <strong>RBF (Radial Basis Function)</strong> memetakan data ke ruang dimensi lebih tinggi.'],
        ['Cari Hyperplane Optimal', 'SVM mencari hyperplane dengan <strong>margin maksimum</strong> (C=10) yang memisahkan kelas Diabetes dan No Diabetes.'],
        ['Prediksi via SVC',        'Titik baru diklasifikasikan berdasarkan sisi hyperplane yang ia tempati, dengan probabilitas dari <code>predict_proba</code>.'],
    ],
];
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Tentang Model</h1>
        <p class="page-subtitle">Perbandingan performa ketiga model Machine Learning</p>
    </div>
    <span class="badge badge-success badge-lg">
        <i data-feather="cpu"></i> 3 Model Aktif
    </span>
</div>

<?php if ($apiError && !$allModels): ?>
<div class="alert alert-error">
    <i data-feather="wifi-off"></i>
    <div>
        <strong>Flask API tidak tersedia</strong>
        <p>Jalankan Flask dengan perintah: <code>python app.py</code> di folder <code>diabetesrisk-api/</code></p>
    </div>
</div>
<?php endif; ?>

<?php if ($apiError && $allModels): ?>
<div class="alert alert-warning">
    <i data-feather="wifi-off"></i>
    <span>Flask API offline — menampilkan data dari file lokal.</span>
</div>
<?php endif; ?>

<?php if ($allModels): ?>

<!-- ── Model Cards Overview ──────────────────────────────── -->
<div class="model-overview-grid">
    <?php foreach ($modelMeta as $key => $meta):
        $m = $allModels[$key] ?? null;
        if (!$m) continue;
        $acc = round(($m['accuracy'] ?? 0) * 100, 2);
    ?>
    <div class="model-overview-card" onclick="switchTab('<?= $key ?>')" id="overview-<?= $key ?>">
        <div class="model-overview-icon" style="background:<?= $meta['bg'] ?>">
            <i data-feather="<?= $meta['icon'] ?>"></i>
        </div>
        <div class="model-overview-info">
            <span class="model-overview-short"><?= $meta['short'] ?></span>
            <span class="model-overview-label"><?= $meta['label'] ?></span>
            <span class="model-overview-acc"><?= $acc ?>%</span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Comparison Radar Chart ────────────────────────────── -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title"><i data-feather="radio"></i> Perbandingan Semua Model</h3>
    </div>
    <div class="card-body chart-container-md">
        <canvas id="comparisonChart"></canvas>
    </div>
</div>

<!-- ── Model Tabs ────────────────────────────────────────── -->
<div class="tab-nav mt-4">
    <?php foreach ($modelMeta as $key => $meta): ?>
    <button class="tab-btn <?= $key === 'knn' ? 'active' : '' ?>"
            onclick="switchTab('<?= $key ?>')" id="tab-<?= $key ?>">
        <i data-feather="<?= $meta['icon'] ?>"></i>
        <?= $meta['short'] ?>
    </button>
    <?php endforeach; ?>
</div>

<!-- ── Tab Panels ────────────────────────────────────────── -->
<?php foreach ($modelMeta as $key => $meta):
    $m = $allModels[$key] ?? null;
    if (!$m) continue;
    $metrics = [
        ['Akurasi',   $m['accuracy']  ?? 0, 'check-square', '#16a34a'],
        ['Precision', $m['precision'] ?? 0, 'target',        '#0ea5e9'],
        ['Recall',    $m['recall']    ?? 0, 'bell',          '#f59e0b'],
        ['F1-Score',  $m['f1_score']  ?? 0, 'award',         '#8b5cf6'],
    ];
?>
<div class="tab-panel <?= $key === 'knn' ? 'active' : '' ?>" id="panel-<?= $key ?>">

    <!-- Hero Card -->
    <div class="model-hero-card mt-4">
        <div class="model-hero-icon" style="background:<?= $meta['bg'] ?>">
            <i data-feather="<?= $meta['icon'] ?>"></i>
        </div>
        <div class="model-hero-info">
            <h2><?= htmlspecialchars($m['model_name'] ?? $meta['label']) ?></h2>
            <p><?= $meta['desc'] ?></p>
            <div class="model-params">
                <?php foreach (($m['best_parameters'] ?? []) as $pk => $pv): ?>
                <span class="param-chip">
                    <strong><?= htmlspecialchars($pk) ?>:</strong>
                    <?= htmlspecialchars((string)$pv) ?>
                </span>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="metrics-grid mt-4">
        <?php foreach ($metrics as [$label, $val, $icon, $color]):
            $pct = round($val * 100, 2);
        ?>
        <div class="metric-card">
            <div class="metric-header">
                <span class="metric-icon" style="color:<?= $color ?>">
                    <i data-feather="<?= $icon ?>"></i>
                </span>
                <span class="metric-label"><?= $label ?></span>
            </div>
            <div class="metric-value"><?= $pct ?>%</div>
            <div class="metric-bar-track">
                <div class="metric-bar-fill" style="width:<?= $pct ?>%; background:<?= $color ?>"></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- How It Works -->
    <div class="card mt-4">
        <div class="card-header">
            <h3 class="card-title"><i data-feather="book-open"></i> Cara Kerja <?= $meta['label'] ?></h3>
        </div>
        <div class="card-body">
            <div class="knn-steps">
                <?php foreach ($modelSteps[$key] as $i => [$title, $desc]): ?>
                <div class="knn-step">
                    <div class="knn-step-num"><?= $i + 1 ?></div>
                    <div>
                        <h5><?= $title ?></h5>
                        <p><?= $desc ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

</div><!-- /.tab-panel -->
<?php endforeach; ?>

<script>
// ── Tab Switcher ───────────────────────────────────────────
function switchTab(key) {
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    document.getElementById('tab-' + key).classList.add('active');
    document.getElementById('panel-' + key).classList.add('active');
    feather.replace();
}

// ── Comparison Radar Chart ─────────────────────────────────
const ctx = document.getElementById('comparisonChart').getContext('2d');
new Chart(ctx, {
    type: 'radar',
    data: {
        labels: ['Akurasi', 'Precision', 'Recall', 'F1-Score'],
        datasets: [
            {
                label: 'KNN',
                data: [
                    <?= round(($allModels['knn']['accuracy']  ?? 0) * 100, 2) ?>,
                    <?= round(($allModels['knn']['precision'] ?? 0) * 100, 2) ?>,
                    <?= round(($allModels['knn']['recall']    ?? 0) * 100, 2) ?>,
                    <?= round(($allModels['knn']['f1_score']  ?? 0) * 100, 2) ?>
                ],
                backgroundColor: 'rgba(59,130,246,0.15)',
                borderColor: 'rgba(59,130,246,0.9)',
                pointBackgroundColor: 'rgba(59,130,246,1)',
                pointRadius: 5,
            },
            {
                label: 'Decision Tree',
                data: [
                    <?= round(($allModels['dt']['accuracy']  ?? 0) * 100, 2) ?>,
                    <?= round(($allModels['dt']['precision'] ?? 0) * 100, 2) ?>,
                    <?= round(($allModels['dt']['recall']    ?? 0) * 100, 2) ?>,
                    <?= round(($allModels['dt']['f1_score']  ?? 0) * 100, 2) ?>
                ],
                backgroundColor: 'rgba(16,185,129,0.15)',
                borderColor: 'rgba(16,185,129,0.9)',
                pointBackgroundColor: 'rgba(16,185,129,1)',
                pointRadius: 5,
            },
            {
                label: 'SVM',
                data: [
                    <?= round(($allModels['svm']['accuracy']  ?? 0) * 100, 2) ?>,
                    <?= round(($allModels['svm']['precision'] ?? 0) * 100, 2) ?>,
                    <?= round(($allModels['svm']['recall']    ?? 0) * 100, 2) ?>,
                    <?= round(($allModels['svm']['f1_score']  ?? 0) * 100, 2) ?>
                ],
                backgroundColor: 'rgba(139,92,246,0.15)',
                borderColor: 'rgba(139,92,246,0.9)',
                pointBackgroundColor: 'rgba(139,92,246,1)',
                pointRadius: 5,
            },
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        scales: {
            r: {
                min: 0,
                max: 100,
                ticks: { stepSize: 20 },
                pointLabels: { font: { size: 13 } },
            }
        },
        plugins: {
            legend: {
                display: true,
                position: 'bottom',
                labels: { padding: 20, font: { size: 13 } }
            }
        }
    }
});
</script>

<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
