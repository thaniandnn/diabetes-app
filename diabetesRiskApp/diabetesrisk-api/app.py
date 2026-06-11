"""
app.py — DiabetesRisk Flask API Entry Point

Cara menjalankan:
    python app.py

API akan berjalan di http://localhost:5000
"""
import sys
from flask import Flask, jsonify
from flask_cors import CORS

# Load model di awal sebelum blueprint
import model_loader

try:
    model_loader.load_all_models()
except RuntimeError as e:
    print(f"\n[FATAL] {e}")
    print("Pastikan file model ada di folder models/:")
    print("  - models/knn_model.pkl")
    print("  - models/scaler.pkl")
    print("  - metrics/knn_metrics.json")
    sys.exit(1)

# ── Init Flask ─────────────────────────────────────────────
app = Flask(__name__)
CORS(app, resources={r"/*": {"origins": "*"}})

# ── Register Blueprints ────────────────────────────────────
from routes.predict    import predict_bp
from routes.model_info import model_info_bp

app.register_blueprint(predict_bp)
app.register_blueprint(model_info_bp)


# ── Health check ───────────────────────────────────────────
@app.route("/", methods=["GET"])
def index():
    return jsonify({
        "app":    "DiabetesRisk API",
        "status": "running",
        "models": model_loader.list_available_models(),
        "endpoints": {
            "POST /predict":    "Kirim data klinis, dapatkan prediksi",
            "GET  /model-info": "Lihat metrik performa model",
        }
    })


@app.errorhandler(404)
def not_found(e):
    return jsonify({"error": "Endpoint tidak ditemukan."}), 404


@app.errorhandler(405)
def method_not_allowed(e):
    return jsonify({"error": "Method tidak diizinkan."}), 405


# ── Run ────────────────────────────────────────────────────
if __name__ == "__main__":
    from config import API_HOST, API_PORT, DEBUG
    print(f"\n DiabetesRisk API — http://{API_HOST}:{API_PORT}")
    print(f" Model aktif: {model_loader.list_available_models()}\n")
    app.run(host=API_HOST, port=API_PORT, debug=DEBUG)
