<?php
/**
 * pages/dashboard.php — Dashboard Utama
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
requireLogin();

$user = currentUser();
$pdo  = getDB();

// ── Statistik total ────────────────────────────────────────
$userId = $user['id'];

$totalStmt = $pdo->prepare('SELECT COUNT(*) FROM predictions WHERE user_id = ?');
$totalStmt->execute([$userId]);
$totalPredictions = (int) $totalStmt->fetchColumn();

$diabetesStmt = $pdo->prepare("SELECT COUNT(*) FROM predictions WHERE user_id = ? AND prediction_result = 'Diabetes'");
$diabetesStmt->execute([$userId]);
$totalDiabetes = (int) $diabetesStmt->fetchColumn();

$noDiabetes = $totalPredictions - $totalDiabetes;

// ── Prediksi terbaru (5) ───────────────────────────────────
$recentStmt = $pdo->prepare(
    'SELECT * FROM predictions WHERE user_id = ? ORDER BY created_at DESC LIMIT 5'
);
$recentStmt->execute([$userId]);
$recentPredictions = $recentStmt->fetchAll();

// ── Data chart 7 hari terakhir ─────────────────────────────
$chartStmt = $pdo->prepare(
    "SELECT DATE(created_at) as tgl, COUNT(*) as total,
            SUM(prediction_result = 'Diabetes') as diabetes_count
     FROM predictions
     WHERE user_id = ? AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
     GROUP BY DATE(created_at)
     ORDER BY tgl ASC"
);
$chartStmt->execute([$userId]);
$chartData = $chartStmt->fetchAll();

$pageTitle = 'Dashboard';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-subtitle">Selamat datang kembali, <strong><?= htmlspecialchars($user['nama']) ?></strong>!</p>
    </div>
    <a href="/diabetesrisk-php/?page=predict" class="btn btn-primary">
        <i data-feather="plus"></i>
        Prediksi Baru
    </a>
</div>

<!-- ── Stat Cards ─────────────────────────────────────────── -->
<div class="stats-grid">
    <div class="stat-card stat-card--total">
        <div class="stat-icon">
            <i data-feather="activity"></i>
        </div>
        <div class="stat-body">
            <span class="stat-label">Total Prediksi</span>
            <span class="stat-value"><?= $totalPredictions ?></span>
        </div>
        <div class="stat-trend">Semua waktu</div>
    </div>

    <div class="stat-card stat-card--danger">
        <div class="stat-icon">
            <i data-feather="alert-triangle"></i>
        </div>
        <div class="stat-body">
            <span class="stat-label">Risiko Tinggi</span>
            <span class="stat-value"><?= $totalDiabetes ?></span>
        </div>
        <div class="stat-trend">Diabetes terdeteksi</div>
    </div>

    <div class="stat-card stat-card--success">
        <div class="stat-icon">
            <i data-feather="check-circle"></i>
        </div>
        <div class="stat-body">
            <span class="stat-label">Risiko Rendah</span>
            <span class="stat-value"><?= $noDiabetes ?></span>
        </div>
        <div class="stat-trend">No Diabetes</div>
    </div>

    <div class="stat-card stat-card--info">
        <div class="stat-icon">
            <i data-feather="cpu"></i>
        </div>
        <div class="stat-body">
            <span class="stat-label">Akurasi Model</span>
            <span class="stat-value">70.78%</span>
        </div>
        <div class="stat-trend">KNN (k=3)</div>
    </div>
</div>

<!-- ── Chart + Recent Table ───────────────────────────────── -->
<div class="dashboard-grid">
    <!-- Chart -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i data-feather="bar-chart-2"></i>
                Prediksi 7 Hari Terakhir
            </h3>
        </div>
        <div class="card-body">
            <?php if ($totalPredictions > 0): ?>
            <canvas id="weeklyChart" height="200"></canvas>
            <?php else: ?>
            <div class="empty-state">
                <i data-feather="bar-chart-2"></i>
                <p>Belum ada data prediksi.</p>
                <a href="/diabetesrisk-php/?page=predict" class="btn btn-primary btn-sm">Mulai Prediksi</a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Distribution Donut -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i data-feather="pie-chart"></i>
                Distribusi Hasil
            </h3>
        </div>
        <div class="card-body">
            <?php if ($totalPredictions > 0): ?>
            <canvas id="donutChart" height="200"></canvas>
            <?php else: ?>
            <div class="empty-state">
                <i data-feather="pie-chart"></i>
                <p>Belum ada data untuk ditampilkan.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ── Recent Predictions ─────────────────────────────────── -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title">
            <i data-feather="clock"></i>
            Prediksi Terbaru
        </h3>
        <a href="/diabetesrisk-php/?page=history" class="btn btn-outline btn-sm">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <?php if (empty($recentPredictions)): ?>
        <div class="empty-state p-4">
            <i data-feather="inbox"></i>
            <p>Belum ada riwayat prediksi.</p>
            <a href="/diabetesrisk-php/?page=predict" class="btn btn-primary btn-sm">Buat Prediksi Pertama</a>
        </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tanggal</th>
                        <th>Glukosa</th>
                        <th>BMI</th>
                        <th>Usia</th>
                        <th>Hasil</th>
                        <th>Risiko</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentPredictions as $i => $p): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= date('d M Y H:i', strtotime($p['created_at'])) ?></td>
                        <td><?= $p['glucose'] ?></td>
                        <td><?= $p['bmi'] ?></td>
                        <td><?= $p['age'] ?> th</td>
                        <td>
                            <span class="badge <?= $p['prediction_result'] === 'Diabetes' ? 'badge-danger' : 'badge-success' ?>">
                                <?= htmlspecialchars($p['prediction_result']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge <?= $p['risk_level'] === 'Tinggi' ? 'badge-danger' : 'badge-success' ?>">
                                <?= htmlspecialchars($p['risk_level']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($totalPredictions > 0): ?>
<script>
// ── Chart.js Data ──────────────────────────────────────────
const chartData = <?= json_encode($chartData) ?>;

// Weekly Bar Chart
const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
new Chart(weeklyCtx, {
    type: 'bar',
    data: {
        labels: chartData.map(d => {
            const date = new Date(d.tgl);
            return date.toLocaleDateString('id-ID', { month: 'short', day: 'numeric' });
        }),
        datasets: [
            {
                label: 'Diabetes',
                data: chartData.map(d => d.diabetes_count),
                backgroundColor: 'rgba(239, 68, 68, 0.8)',
                borderRadius: 6,
            },
            {
                label: 'No Diabetes',
                data: chartData.map(d => d.total - d.diabetes_count),
                backgroundColor: 'rgba(22, 163, 74, 0.8)',
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } },
            x: { grid: { display: false } }
        }
    }
});

// Donut Chart
const donutCtx = document.getElementById('donutChart').getContext('2d');
new Chart(donutCtx, {
    type: 'doughnut',
    data: {
        labels: ['Diabetes', 'No Diabetes'],
        datasets: [{
            data: [<?= $totalDiabetes ?>, <?= $noDiabetes ?>],
            backgroundColor: ['rgba(239,68,68,0.85)', 'rgba(22,163,74,0.85)'],
            borderWidth: 0,
            hoverOffset: 8,
        }]
    },
    options: {
        responsive: true,
        cutout: '68%',
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
