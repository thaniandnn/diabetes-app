"""
model_loader.py — Load ML models at startup.
Model di-load SEKALI saat aplikasi Flask pertama kali dijalankan.
Tidak ada training ulang.
"""
import joblib
import json
import os
from config import MODEL_REGISTRY


# ── Storage ────────────────────────────────────────────────
_loaded_models = {}   # key -> {"model": ..., "scaler": ..., "metrics": ...}


def load_all_models():
    """
    Load semua model yang terdaftar di MODEL_REGISTRY.
    Dipanggil satu kali di app.py saat startup.
    """
    for key, paths in MODEL_REGISTRY.items():
        model_path   = paths["model_file"]
        scaler_path  = paths.get("scaler_file")   # bisa None untuk DT
        metrics_path = paths["metrics_file"]

        # Validasi file — scaler_path dilewati kalau None
        missing = []
        for label, path in [("model", model_path), ("metrics", metrics_path)]:
            if not os.path.isfile(path):
                missing.append(f"{label}: {path}")

        if scaler_path and not os.path.isfile(scaler_path):
            missing.append(f"scaler: {scaler_path}")

        if missing:
            print(f"[WARNING] Model '{key}' tidak dapat di-load. File tidak ditemukan:")
            for m in missing:
                print(f"  - {m}")
            continue

        try:
            model = joblib.load(model_path)

            # Scaler hanya di-load jika ada (KNN dan SVM butuh scaler, DT tidak)
            scaler = joblib.load(scaler_path) if scaler_path else None

            with open(metrics_path, "r") as f:
                metrics = json.load(f)

            _loaded_models[key] = {
                "model":   model,
                "scaler":  scaler,
                "metrics": metrics,
            }

            scaler_info = scaler_path if scaler_path else "tidak ada (tidak diperlukan)"
            print(f"[OK] Model '{key}' berhasil di-load")
            print(f"     model  : {model_path}")
            print(f"     scaler : {scaler_info}")

        except Exception as e:
            print(f"[ERROR] Gagal load model '{key}': {e}")

    if not _loaded_models:
        raise RuntimeError(
            "Tidak ada model yang berhasil di-load. "
            "Pastikan file .pkl ada di folder models/."
        )


def get_model(key: str):
    """
    Ambil bundle (model, scaler, metrics) untuk key tertentu.
    Raise ValueError jika model tidak tersedia.
    """
    if key not in _loaded_models:
        available = list(_loaded_models.keys())
        raise ValueError(
            f"Model '{key}' tidak tersedia. "
            f"Model yang tersedia: {available}"
        )
    return _loaded_models[key]


def list_available_models():
    """Return list of loaded model keys."""
    return list(_loaded_models.keys())