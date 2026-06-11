"""
config.py — DiabetesRisk API Configuration
Tambahkan model baru (SVM, Decision Tree, dll.) di MODEL_REGISTRY
tanpa mengubah struktur aplikasi lain.
"""
import os

BASE_DIR = os.path.dirname(os.path.abspath(__file__))

# ── Directories ────────────────────────────────────────────
MODELS_DIR  = os.path.join(BASE_DIR, "models")
METRICS_DIR = os.path.join(BASE_DIR, "metrics")

# ── Server ─────────────────────────────────────────────────
API_HOST = "0.0.0.0"
API_PORT = 5001
DEBUG    = False

# ── Model Registry ─────────────────────────────────────────
# Format: "key": {"model_file": "...", "scaler_file": "...", "metrics_file": "..."}
# Tambahkan entri baru untuk setiap model tambahan.
MODEL_REGISTRY = {
    "knn": {
        "model_file":   os.path.join(MODELS_DIR,  "knn_model.pkl"),
        "scaler_file":  os.path.join(MODELS_DIR,  "scaler.pkl"),
        "metrics_file": os.path.join(METRICS_DIR, "knn_metrics.json"),
    },
   "dt": {
    "model_file":   "models/model_decision_tree.pkl",
    "scaler_file":  None,  
    "metrics_file": "metrics/dt_metrics.json",
},
    "svm": {
        "model_file":   os.path.join(MODELS_DIR,  "svm_model.pkl"),
        "scaler_file":  os.path.join(MODELS_DIR,  "scaler_svm.pkl"),
        "metrics_file": os.path.join(METRICS_DIR, "svm_metrics.json"),
    },
}

# Model aktif default
DEFAULT_MODEL = "knn"
