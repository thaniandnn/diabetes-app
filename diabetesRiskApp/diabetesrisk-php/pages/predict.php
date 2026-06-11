<?php
/**
 * pages/predict.php — Form Prediksi Diabetes
 */
require_once __DIR__ . '/../includes/auth.php';
requireLogin();

$pageTitle = 'Prediksi Baru';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Prediksi Diabetes</h1>
        <p class="page-subtitle">Masukkan data klinis pasien untuk mendapatkan hasil prediksi AI</p>
    </div>
</div>

<!-- ── Model Selector (full-width, di atas grid) ────────────── -->
<div id="modelSelectorCard" style="background:#fff; border:1px solid #e5e7eb; border-radius:12px; box-shadow:0 1px 3px rgba(0,0,0,.1); overflow:hidden; margin-bottom:20px;">
    <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #f3f4f6;">
        <h3 style="font-size:.9375rem; font-weight:600; color:#1f2937; display:flex; align-items:center; gap:8px; margin:0;">
            <i data-feather="cpu" style="color:#16a34a;"></i>
            Pilih Model Prediksi
        </h3>
        <span class="badge badge-info" id="activemodelBadge">KNN Model</span>
    </div>
    <div class="card-body">
        <div class="model-selector-grid">

            <label class="model-option active" id="opt-knn">
                <input type="radio" name="model_key" value="knn" checked hidden>
                <div class="model-option-icon" style="background: linear-gradient(135deg,#3b82f6,#1d4ed8)">
                    <i data-feather="git-merge"></i>
                </div>
                <div class="model-option-body">
                    <span class="model-option-name">K-Nearest Neighbor</span>

                </div>
                <span class="model-option-check"><i data-feather="check-circle"></i></span>
            </label>

            <label class="model-option" id="opt-dt">
                <input type="radio" name="model_key" value="dt" hidden>
                <div class="model-option-icon" style="background: linear-gradient(135deg,#10b981,#065f46)">
                    <i data-feather="git-branch"></i>
                </div>
                <div class="model-option-body">
                    <span class="model-option-name">Decision Tree</span>

                </div>
                <span class="model-option-check"><i data-feather="check-circle"></i></span>
            </label>

            <label class="model-option" id="opt-svm">
                <input type="radio" name="model_key" value="svm" hidden>
                <div class="model-option-icon" style="background: linear-gradient(135deg,#8b5cf6,#5b21b6)">
                    <i data-feather="maximize-2"></i>
                </div>
                <div class="model-option-body">
                    <span class="model-option-name">Support Vector Machine</span>

                </div>
                <span class="model-option-check"><i data-feather="check-circle"></i></span>
            </label>

        </div>
    </div>
</div><!-- /#modelSelectorCard -->

<div class="predict-layout">
    <!-- ── Form Panel ──────────────────────────────────── -->
    <div class="predict-form-panel">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i data-feather="edit-3"></i>
                    Data Klinis Pasien
                </h3>
            </div>
            <div class="card-body">
                <form id="predictForm" novalidate>
                    <div class="form-grid-2">

                        <!-- Pregnancies -->
                        <div class="form-group">
                            <label class="form-label" for="pregnancies">
                                Kehamilan (Pregnancies)
                                <span class="form-tooltip" title="Jumlah kehamilan yang pernah dialami">?</span>
                            </label>
                            <div class="input-wrapper">
                                <i data-feather="user" class="input-icon"></i>
                                <input type="number" id="pregnancies" name="pregnancies"
                                       class="form-input" placeholder="0"
                                       min="0" max="20" step="1" required>
                            </div>
                            <span class="form-hint">Rentang: 0 – 17</span>
                        </div>

                        <!-- Glucose -->
                        <div class="form-group">
                            <label class="form-label" for="glucose">
                                Glukosa (mg/dL)
                                <span class="form-tooltip" title="Konsentrasi glukosa plasma 2 jam setelah tes toleransi glukosa oral">?</span>
                            </label>
                            <div class="input-wrapper">
                                <i data-feather="droplet" class="input-icon"></i>
                                <input type="number" id="glucose" name="glucose"
                                       class="form-input" placeholder="120"
                                       min="0" max="300" step="1" required>
                            </div>
                            <span class="form-hint">Normal: 70–140 mg/dL</span>
                        </div>

                        <!-- Blood Pressure -->
                        <div class="form-group">
                            <label class="form-label" for="blood_pressure">
                                Tekanan Darah (mmHg)
                                <span class="form-tooltip" title="Tekanan darah diastolik">?</span>
                            </label>
                            <div class="input-wrapper">
                                <i data-feather="heart" class="input-icon"></i>
                                <input type="number" id="blood_pressure" name="blood_pressure"
                                       class="form-input" placeholder="80"
                                       min="0" max="200" step="1" required>
                            </div>
                            <span class="form-hint">Normal: 60–80 mmHg</span>
                        </div>

                        <!-- Skin Thickness -->
                        <div class="form-group">
                            <label class="form-label" for="skin_thickness">
                                Ketebalan Kulit (mm)
                                <span class="form-tooltip" title="Ketebalan lipatan kulit trisep">?</span>
                            </label>
                            <div class="input-wrapper">
                                <i data-feather="layers" class="input-icon"></i>
                                <input type="number" id="skin_thickness" name="skin_thickness"
                                       class="form-input" placeholder="25"
                                       min="0" max="100" step="1" required>
                            </div>
                            <span class="form-hint">Rentang: 0 – 99 mm</span>
                        </div>

                        <!-- Insulin -->
                        <div class="form-group">
                            <label class="form-label" for="insulin">
                                Insulin (µU/mL)
                                <span class="form-tooltip" title="Insulin serum 2 jam">?</span>
                            </label>
                            <div class="input-wrapper">
                                <i data-feather="zap" class="input-icon"></i>
                                <input type="number" id="insulin" name="insulin"
                                       class="form-input" placeholder="90"
                                       min="0" max="1000" step="1" required>
                            </div>
                            <span class="form-hint">Normal: 16–166 µU/mL</span>
                        </div>

                        <!-- BMI -->
                        <div class="form-group">
                            <label class="form-label" for="bmi">
                                BMI (kg/m²)
                                <span class="form-tooltip" title="Body Mass Index = berat (kg) / tinggi² (m)">?</span>
                            </label>
                            <div class="input-wrapper">
                                <i data-feather="bar-chart" class="input-icon"></i>
                                <input type="number" id="bmi" name="bmi"
                                       class="form-input" placeholder="27.5"
                                       min="0" max="70" step="0.1" required>
                            </div>
                            <span class="form-hint">Normal: 18.5 – 24.9</span>
                        </div>

                        <!-- Diabetes Pedigree Function -->
                        <div class="form-group">
                            <label class="form-label" for="diabetes_pedigree_function">
                                Diabetes Pedigree Function
                                <span class="form-tooltip" title="Fungsi yang memodelkan riwayat keluarga terkait diabetes">?</span>
                            </label>
                            <div class="input-wrapper">
                                <i data-feather="git-branch" class="input-icon"></i>
                                <input type="number" id="diabetes_pedigree_function" name="diabetes_pedigree_function"
                                       class="form-input" placeholder="0.5"
                                       min="0" max="3" step="0.001" required>
                            </div>
                            <span class="form-hint">Rentang: 0.078 – 2.42</span>
                        </div>

                        <!-- Age -->
                        <div class="form-group">
                            <label class="form-label" for="age">
                                Usia (tahun)
                                <span class="form-tooltip" title="Usia pasien dalam tahun">?</span>
                            </label>
                            <div class="input-wrapper">
                                <i data-feather="calendar" class="input-icon"></i>
                                <input type="number" id="age" name="age"
                                       class="form-input" placeholder="35"
                                       min="1" max="120" step="1" required>
                            </div>
                            <span class="form-hint">Rentang: 21 – 81 tahun</span>
                        </div>

                    </div><!-- /.form-grid-2 -->

                    <div class="form-actions">
                        <button type="button" class="btn btn-outline" onclick="resetForm()">
                            <i data-feather="refresh-cw"></i>
                            Reset
                        </button>
                        <button type="button" class="btn btn-outline" onclick="fillSample()">
                            <i data-feather="file-text"></i>
                            Isi Contoh Data
                        </button>
                        <button type="submit" class="btn btn-primary" id="predictBtn">
                            <i data-feather="cpu"></i>
                            <span>Prediksi Sekarang</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ── Result Panel ────────────────────────────────── -->
    <div class="predict-result-panel">

        <!-- Waiting State -->
        <div class="result-placeholder" id="resultPlaceholder">
            <div class="result-placeholder-icon">
                <i data-feather="cpu"></i>
            </div>
            <h4>Hasil Prediksi</h4>
            <p>Isi form di samping dan klik <strong>Prediksi Sekarang</strong> untuk mendapatkan hasil analisis AI.</p>
        </div>

        <!-- Loading State -->
        <div class="result-loading" id="resultLoading" style="display:none;">
            <div class="spinner-ring"></div>
            <p id="loadingText">Memproses data dengan model…</p>
        </div>

        <!-- Result Card -->
        <div class="result-card" id="resultCard" style="display:none;">
            <div class="result-header" id="resultHeader">
                <div class="result-icon" id="resultIcon">
                    <i data-feather="check-circle"></i>
                </div>
                <div class="result-title-block">
                    <span class="result-label">Hasil Prediksi</span>
                    <h2 class="result-prediction" id="resultPrediction">—</h2>
                </div>
            </div>
            <div class="result-body">
                <div class="result-meta-row">
                    <span>Tingkat Risiko</span>
                    <span class="badge" id="resultRiskBadge">—</span>
                </div>
                <div class="result-meta-row">
                    <span>Model Digunakan</span>
                    <span class="badge badge-info" id="resultModelName">—</span>
                </div>
                <div class="result-meta-row">
                    <span>Akurasi Model</span>
                    <span><strong id="resultModelAccuracy">—</strong></span>
                </div>
                <div class="result-divider"></div>
                <div class="result-recommendation" id="resultRecommendation"></div>
            </div>
            <div class="result-footer">
                <button class="btn btn-outline btn-sm" onclick="resetForm()">
                    <i data-feather="plus"></i> Prediksi Baru
                </button>
                <a href="/diabetesrisk-php/?page=history" class="btn btn-outline btn-sm">
                    <i data-feather="clock"></i> Lihat Riwayat
                </a>
            </div>
        </div>

        <!-- Error Card -->
        <div class="result-error" id="resultError" style="display:none;">
            <i data-feather="alert-triangle"></i>
            <h4>Terjadi Kesalahan</h4>
            <p id="resultErrorMsg">Tidak dapat menghubungi API. Pastikan Flask server berjalan.</p>
            <button class="btn btn-outline btn-sm" onclick="document.getElementById('predictForm').dispatchEvent(new Event('submit'))">
                Coba Lagi
            </button>
        </div>

    </div><!-- /.predict-result-panel -->
</div><!-- /.predict-layout -->

<script>
// ── Model info map ─────────────────────────────────────────
const MODEL_INFO = {
    knn: { name: 'K-Nearest Neighbor',      accuracy: '71%' },
    dt:  { name: 'Decision Tree',            accuracy: '69%' },
    svm: { name: 'Support Vector Machine',   accuracy: '72%' },
};

// ── Model selector handler ─────────────────────────────────
document.querySelectorAll('input[name="model_key"]').forEach(radio => {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.model-option').forEach(el => el.classList.remove('active'));
        this.closest('.model-option').classList.add('active');
        const key = this.value;
        const info = MODEL_INFO[key];
        document.getElementById('activemodelBadge').textContent =
            key.toUpperCase() + ' Model';
    });
});

function getSelectedModel() {
    const checked = document.querySelector('input[name="model_key"]:checked');
    return checked ? checked.value : 'knn';
}

// ── Predict Form Handler ───────────────────────────────────
document.getElementById('predictForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    if (!validateForm()) return;

    const modelKey = getSelectedModel();
    const modelInfo = MODEL_INFO[modelKey];

    document.getElementById('loadingText').textContent =
        `Memproses data dengan model ${modelInfo.name}…`;
    showLoading();

    const formData = {
        pregnancies:                parseInt(document.getElementById('pregnancies').value),
        glucose:                    parseInt(document.getElementById('glucose').value),
        blood_pressure:             parseInt(document.getElementById('blood_pressure').value),
        skin_thickness:             parseInt(document.getElementById('skin_thickness').value),
        insulin:                    parseInt(document.getElementById('insulin').value),
        bmi:                        parseFloat(document.getElementById('bmi').value),
        diabetes_pedigree_function: parseFloat(document.getElementById('diabetes_pedigree_function').value),
        age:                        parseInt(document.getElementById('age').value),
        model_key:                  modelKey,
    };

    try {
        const response = await fetch('/diabetesrisk-php/api/predict_handler.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData),
        });
        const data = await response.json();

        if (data.error) {
            showError(data.error);
        } else {
            data._modelKey = modelKey;
            showResult(data);
        }
    } catch (err) {
        showError('Tidak dapat menghubungi server. Pastikan Flask API berjalan di port 5000.');
    }
});

function validateForm() {
    const inputs = document.querySelectorAll('#predictForm input[required]');
    let valid = true;
    inputs.forEach(input => {
        if (!input.value || input.value === '') {
            input.classList.add('input-error');
            valid = false;
        } else {
            input.classList.remove('input-error');
        }
    });
    if (!valid) {
        showToast('Harap isi semua field terlebih dahulu.', 'error');
    }
    return valid;
}

function showLoading() {
    document.getElementById('resultPlaceholder').style.display = 'none';
    document.getElementById('resultCard').style.display = 'none';
    document.getElementById('resultError').style.display = 'none';
    document.getElementById('resultLoading').style.display = 'flex';
}

function showResult(data) {
    document.getElementById('resultLoading').style.display = 'none';
    const card = document.getElementById('resultCard');
    card.style.display = 'block';

    // Update model info in result
    const modelKey  = data._modelKey || data.model_used || 'knn';
    const modelInfo = MODEL_INFO[modelKey] || MODEL_INFO.knn;
    document.getElementById('resultModelName').textContent     = modelInfo.name;
    document.getElementById('resultModelAccuracy').textContent = modelInfo.accuracy;

    const isDiabetes = data.prediction === 'Diabetes';
    const header     = document.getElementById('resultHeader');
    const icon       = document.getElementById('resultIcon');
    const pred       = document.getElementById('resultPrediction');
    const riskBadge  = document.getElementById('resultRiskBadge');
    const rec        = document.getElementById('resultRecommendation');

    header.className = 'result-header ' + (isDiabetes ? 'result-header--danger' : 'result-header--success');
    icon.innerHTML   = isDiabetes
        ? '<i data-feather="alert-triangle"></i>'
        : '<i data-feather="check-circle"></i>';
    pred.textContent = data.prediction;
    riskBadge.textContent  = data.risk_level;
    riskBadge.className    = 'badge ' + (isDiabetes ? 'badge-danger' : 'badge-success');

    rec.innerHTML = isDiabetes
        ? `<h5>⚠️ Rekomendasi</h5>
           <ul class="recommendation-list">
               <li>Segera konsultasikan hasil ini ke dokter atau tenaga medis.</li>
               <li>Pantau kadar glukosa darah secara rutin.</li>
               <li>Terapkan pola makan rendah gula dan karbohidrat olahan.</li>
               <li>Lakukan olahraga aerobik minimal 30 menit per hari.</li>
               <li>Hindari rokok dan alkohol.</li>
           </ul>`
        : `<h5>✅ Rekomendasi</h5>
           <ul class="recommendation-list">
               <li>Pertahankan gaya hidup sehat Anda!</li>
               <li>Tetap lakukan pemeriksaan rutin setidaknya 1x per tahun.</li>
               <li>Jaga berat badan ideal dan konsumsi makanan bergizi.</li>
               <li>Olahraga teratur minimal 150 menit per minggu.</li>
           </ul>`;

    feather.replace();
    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    showToast('Prediksi berhasil disimpan!', 'success');
}

function showError(msg) {
    document.getElementById('resultLoading').style.display = 'none';
    document.getElementById('resultError').style.display = 'flex';
    document.getElementById('resultErrorMsg').textContent = msg;
    feather.replace();
}

function resetForm() {
    document.getElementById('predictForm').reset();
    document.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
    document.getElementById('resultPlaceholder').style.display = 'flex';
    document.getElementById('resultCard').style.display = 'none';
    document.getElementById('resultError').style.display = 'none';
    document.getElementById('resultLoading').style.display = 'none';
}

function fillSample() {
    document.getElementById('pregnancies').value                = 2;
    document.getElementById('glucose').value                    = 120;
    document.getElementById('blood_pressure').value             = 80;
    document.getElementById('skin_thickness').value             = 25;
    document.getElementById('insulin').value                    = 90;
    document.getElementById('bmi').value                        = 27.5;
    document.getElementById('diabetes_pedigree_function').value = 0.5;
    document.getElementById('age').value                        = 35;
    showToast('Contoh data telah diisi.', 'info');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
