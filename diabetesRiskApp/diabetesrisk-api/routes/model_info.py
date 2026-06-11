"""
routes/model_info.py — GET /model-info
Mengembalikan metrik model dari file metrics JSON.
"""
from flask import Blueprint, request, jsonify
from model_loader import get_model, list_available_models
from config import DEFAULT_MODEL

model_info_bp = Blueprint("model_info", __name__)


@model_info_bp.route("/model-info", methods=["GET"])
def model_info():
    model_key = request.args.get("model_key", DEFAULT_MODEL)
    try:
        bundle  = get_model(model_key)
        metrics = bundle["metrics"]
    except ValueError as e:
        return jsonify({"error": str(e)}), 404

    return jsonify({
        "model_key":       model_key,
        "model_name":      metrics.get("model_name"),
        "accuracy":        metrics.get("accuracy"),
        "precision":       metrics.get("precision"),
        "recall":          metrics.get("recall"),
        "f1_score":        metrics.get("f1_score"),
        "best_parameters": metrics.get("best_parameters", {}),
        "available_models": list_available_models(),
    }), 200
