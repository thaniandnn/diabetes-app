"""
routes/predict.py — POST /predict
Menerima 8 fitur klinis, melakukan scaling + prediksi.
"""
from flask import Blueprint, request, jsonify
import numpy as np
from model_loader import get_model
from config import DEFAULT_MODEL

predict_bp = Blueprint("predict", __name__)

# Urutan fitur HARUS sama dengan saat training
FEATURE_NAMES = [
    "pregnancies",
    "glucose",
    "blood_pressure",
    "skin_thickness",
    "insulin",
    "bmi",
    "diabetes_pedigree_function",
    "age",
]

# Index tiap fitur untuk kemudahan akses
FEATURE_IDX = {name: i for i, name in enumerate(FEATURE_NAMES)}

# Median dari dataset Pima Indians (digunakan untuk imputasi nilai ekstrem)
MEDIAN_VALUES = {
    "glucose":        117.0,
    "blood_pressure":  72.0,
    "skin_thickness":  23.0,
    "insulin":         30.5,
    "bmi":             32.0,
}

# Batas minimum nilai yang masuk akal secara medis
MIN_VALID = {
    "glucose":        50.0,
    "blood_pressure": 40.0,
    "bmi":            10.0,
    "skin_thickness":  0.0,
    "insulin":         0.0,
}


def impute_features(X: np.ndarray) -> np.ndarray:
    """
    Ganti nilai yang tidak logis secara medis dengan median training data.
    Nilai 0 pada kolom medis dan nilai di bawah batas minimum akan diimputasi.
    """
    X = X.copy()
    for col, median_val in MEDIAN_VALUES.items():
        idx     = FEATURE_IDX[col]
        min_val = MIN_VALID.get(col, 0.0)
        val     = X[0][idx]
        if val <= 0 or val < min_val:
            X[0][idx] = median_val
    return X


@predict_bp.route("/predict", methods=["POST"])
def predict():
    data = request.get_json(silent=True)

    if not data:
        return jsonify({"error": "Request body harus berupa JSON."}), 400

    # ── Validasi input ─────────────────────────────────────
    missing = [f for f in FEATURE_NAMES if f not in data]
    if missing:
        return jsonify({
            "error": f"Field berikut tidak ditemukan: {missing}"
        }), 400

    try:
        features = [float(data[f]) for f in FEATURE_NAMES]
    except (ValueError, TypeError) as e:
        return jsonify({"error": f"Nilai fitur tidak valid: {str(e)}"}), 400

    # ── Pilih model ────────────────────────────────────────
    model_key = data.get("model_key", DEFAULT_MODEL)
    try:
        bundle = get_model(model_key)
    except ValueError as e:
        return jsonify({"error": str(e)}), 400

    model  = bundle["model"]
    scaler = bundle["scaler"]

    # ── Preprocessing + Predict ────────────────────────────
    try:
        X = np.array([features])

        # Imputasi nilai ekstrem / tidak logis
        X = impute_features(X)

        # Feature engineering untuk SVM (menambahkan 3 fitur interaksi)
        if model_key == "svm":
            glucose     = X[0][FEATURE_IDX["glucose"]]
            bmi         = X[0][FEATURE_IDX["bmi"]]
            age         = X[0][FEATURE_IDX["age"]]
            pregnancies = X[0][FEATURE_IDX["pregnancies"]]
            insulin     = X[0][FEATURE_IDX["insulin"]]

            glucose_bmi     = glucose * bmi
            age_pregnancies = age * pregnancies
            insulin_glucose = insulin / glucose  # Feature aslinya ternyata pembagian (rasio), bukan perkalian!

            X = np.append(X, [[glucose_bmi, age_pregnancies, insulin_glucose]], axis=1)

        # Scaling hanya untuk model yang membutuhkan (KNN, SVM)
        # DT tidak perlu scaling — scaler-nya None
        if scaler is not None:
            X_input = scaler.transform(X)
        else:
            X_input = X

        # SVM gunakan threshold 0.35 agar lebih sensitif mendeteksi Diabetes
        if model_key == "svm":
            proba    = model.predict_proba(X_input)[0][1]  # probabilitas kelas Diabetes
            raw_pred = 1 if proba >= 0.35 else 0
        else:
            raw_pred = model.predict(X_input)[0]

    except Exception as e:
        return jsonify({"error": f"Prediksi gagal: {str(e)}"}), 500

    # ── Interpretasi hasil ─────────────────────────────────
    if int(raw_pred) == 1:
        prediction = "Diabetes"
        risk_level = "Tinggi"
    else:
        prediction = "No Diabetes"
        risk_level = "Rendah"

    return jsonify({
        "prediction": prediction,
        "risk_level": risk_level,
        "model_used": model_key,
    }), 200