<?php
/**
 * pages/history.php — Riwayat Prediksi
 */
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/database.php';
requireLogin();

$user   = currentUser();
$pdo    = getDB();
$userId = $user['id'];

// ── Pagination ─────────────────────────────────────────────
$perPage = 10;
$page    = max(1, (int) ($_GET['p'] ?? 1));
$offset  = ($page - 1) * $perPage;

// ── Filter ─────────────────────────────────────────────────
$filter = $_GET['filter'] ?? 'all';
$where  = 'WHERE user_id = ?';
$params = [$userId];
if ($filter === 'diabetes') {
    $where   .= " AND prediction_result = 'Diabetes'";
} elseif ($filter === 'no_diabetes') {
    $where   .= " AND prediction_result = 'No Diabetes'";
}

// Total rows
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM predictions $where");
$countStmt->execute($params);
$totalRows  = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalRows / $perPage));

// Fetch rows
$stmt = $pdo->prepare("SELECT * FROM predictions $where ORDER BY created_at DESC LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$predictions = $stmt->fetchAll();

$pageTitle = 'Riwayat Prediksi';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Riwayat Prediksi</h1>
        <p class="page-subtitle">Total <?= $totalRows ?> prediksi tersimpan</p>
    </div>
    <a href="/diabetesrisk-php/?page=predict" class="btn btn-primary">
        <i data-feather="plus"></i>
        Prediksi Baru
    </a>
</div>

<!-- ── Filter Bar ──────────────────────────────────────────── -->
<div class="filter-bar">
    <a href="?page=history&filter=all"         class="filter-btn <?= $filter === 'all'         ? 'active' : '' ?>">Semua</a>
    <a href="?page=history&filter=diabetes"    class="filter-btn <?= $filter === 'diabetes'    ? 'active' : '' ?>">Diabetes</a>
    <a href="?page=history&filter=no_diabetes" class="filter-btn <?= $filter === 'no_diabetes' ? 'active' : '' ?>">No Diabetes</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <?php if (empty($predictions)): ?>
        <div class="empty-state p-6">
            <i data-feather="inbox"></i>
            <p>Tidak ada riwayat prediksi untuk filter ini.</p>
            <a href="/diabetesrisk-php/?page=predict" class="btn btn-primary btn-sm">Buat Prediksi</a>
        </div>
        <?php else: ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Tanggal & Waktu</th>
                        <th>Kehamilan</th>
                        <th>Glukosa</th>
                        <th>Tekanan Darah</th>
                        <th>Kulit (mm)</th>
                        <th>Insulin</th>
                        <th>BMI</th>
                        <th>Pedigree</th>
                        <th>Usia</th>
                        <th>Hasil</th>
                        <th>Risiko</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($predictions as $i => $p): ?>
                    <tr>
                        <td><?= $offset + $i + 1 ?></td>
                        <td class="text-nowrap"><?= date('d M Y\nH:i', strtotime($p['created_at'])) ?></td>
                        <td><?= $p['pregnancies'] ?></td>
                        <td><?= $p['glucose'] ?></td>
                        <td><?= $p['blood_pressure'] ?></td>
                        <td><?= $p['skin_thickness'] ?></td>
                        <td><?= $p['insulin'] ?></td>
                        <td><?= $p['bmi'] ?></td>
                        <td><?= $p['diabetes_pedigree_function'] ?></td>
                        <td><?= $p['age'] ?></td>
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

        <!-- ── Pagination ───────────────────────────────────── -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination-bar">
            <?php if ($page > 1): ?>
                <a href="?page=history&filter=<?= $filter ?>&p=<?= $page - 1 ?>" class="page-btn">
                    <i data-feather="chevron-left"></i>
                </a>
            <?php endif; ?>

            <?php for ($pg = max(1, $page - 2); $pg <= min($totalPages, $page + 2); $pg++): ?>
                <a href="?page=history&filter=<?= $filter ?>&p=<?= $pg ?>"
                   class="page-btn <?= $pg === $page ? 'active' : '' ?>">
                    <?= $pg ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="?page=history&filter=<?= $filter ?>&p=<?= $page + 1 ?>" class="page-btn">
                    <i data-feather="chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
