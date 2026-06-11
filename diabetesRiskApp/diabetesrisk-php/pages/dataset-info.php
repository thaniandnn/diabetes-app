<?php
/**
 * pages/dataset-info.php — Informasi Dataset Pima Indians Diabetes
 */
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pageTitle = 'Informasi Dataset';
require_once __DIR__ . '/../includes/header.php';

$features = [
    ['Pregnancies',              'Jumlah kehamilan',                      '0–17',   '3.8',  'Numerik'],
    ['Glucose',                  'Konsentrasi glukosa plasma 2 jam (OGTT)', '0–199', '120.9','Numerik'],
    ['BloodPressure',            'Tekanan darah diastolik (mmHg)',         '0–122',  '69.1', 'Numerik'],
    ['SkinThickness',            'Ketebalan lipatan kulit trisep (mm)',    '0–99',   '20.5', 'Numerik'],
    ['Insulin',                  'Insulin serum 2 jam (µU/mL)',            '0–846',  '79.8', 'Numerik'],
    ['BMI',                      'Indeks massa tubuh (kg/m²)',             '0–67.1', '32.0', 'Numerik'],
    ['DiabetesPedigreeFunction', 'Fungsi riwayat keluarga diabetes',      '0.078–2.42', '0.47', 'Numerik'],
    ['Age',                      'Usia pasien (tahun)',                    '21–81',  '33.2', 'Numerik'],
    ['Outcome',                  'Label: 0 = No Diabetes, 1 = Diabetes',  '0 / 1',  '—',    'Kategori'],
];
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Informasi Dataset</h1>
        <p class="page-subtitle">Detail dataset yang digunakan untuk melatih model prediksi diabetes</p>
    </div>
</div>

<!-- ── Dataset Overview ───────────────────────────────────── -->
<div class="info-grid">
    <div class="info-card">
        <div class="info-icon info-icon--blue">
            <i data-feather="database"></i>
        </div>
        <h4>Nama Dataset</h4>
        <p>Pima Indians Diabetes Dataset</p>
    </div>
    <div class="info-card">
        <div class="info-icon info-icon--green">
            <i data-feather="users"></i>
        </div>
        <h4>Jumlah Sampel</h4>
        <p>768 pasien wanita</p>
    </div>
    <div class="info-card">
        <div class="info-icon info-icon--orange">
            <i data-feather="list"></i>
        </div>
        <h4>Jumlah Fitur</h4>
        <p>8 fitur klinis + 1 label</p>
    </div>
    <div class="info-card">
        <div class="info-icon info-icon--purple">
            <i data-feather="pie-chart"></i>
        </div>
        <h4>Distribusi Kelas</h4>
        <p>268 Diabetes · 500 No Diabetes</p>
    </div>
</div>

<!-- ── Sumber ──────────────────────────────────────────────── -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title"><i data-feather="book-open"></i> Tentang Dataset</h3>
    </div>
    <div class="card-body">
        <p>Dataset ini berasal dari <strong>National Institute of Diabetes and Digestive and Kidney Diseases</strong>. Tujuannya adalah memprediksi secara diagnostik apakah seorang pasien menderita diabetes berdasarkan pengukuran diagnostik tertentu.</p>
        <p class="mt-2">Semua pasien dalam dataset ini adalah wanita keturunan Pima Indian berusia minimal 21 tahun. Dataset ini banyak digunakan sebagai benchmark dalam penelitian machine learning di bidang kesehatan.</p>
        <div class="source-badges">
            <span class="badge badge-info">Kaggle</span>
            <span class="badge badge-info">UCI ML Repository</span>
            <span class="badge badge-info">Supervised Learning</span>
            <span class="badge badge-info">Binary Classification</span>
        </div>
    </div>
</div>

<!-- ── Feature Table ──────────────────────────────────────── -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title"><i data-feather="table"></i> Deskripsi Fitur</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nama Fitur</th>
                        <th>Deskripsi</th>
                        <th>Rentang Nilai</th>
                        <th>Rata-rata</th>
                        <th>Tipe Data</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($features as $f): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($f[0]) ?></strong></td>
                        <td><?= htmlspecialchars($f[1]) ?></td>
                        <td><code><?= htmlspecialchars($f[2]) ?></code></td>
                        <td><?= htmlspecialchars($f[3]) ?></td>
                        <td><span class="badge badge-info"><?= htmlspecialchars($f[4]) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Distribution Chart ─────────────────────────────────── -->
<div class="card mt-4">
    <div class="card-header">
        <h3 class="card-title"><i data-feather="pie-chart"></i> Distribusi Kelas Dataset</h3>
    </div>
    <div class="card-body chart-container-md">
        <canvas id="datasetDonut"></canvas>
    </div>
</div>

<script>
const donutCtx = document.getElementById('datasetDonut').getContext('2d');
new Chart(donutCtx, {
    type: 'doughnut',
    data: {
        labels: ['No Diabetes (500 sampel)', 'Diabetes (268 sampel)'],
        datasets: [{
            data: [500, 268],
            backgroundColor: ['rgba(22,163,74,0.85)', 'rgba(239,68,68,0.85)'],
            borderWidth: 0,
            hoverOffset: 10,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        cutout: '65%',
        plugins: {
            legend: { position: 'bottom', labels: { padding: 20 } }
        }
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
