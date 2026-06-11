import joblib
scaler = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/scaler_svm.pkl')

for i, name in enumerate(scaler.feature_names_in_):
    print(f"Feature: {name}")
    print(f"  Mean: {scaler.mean_[i]:.4f}")
    print(f"  Scale (std): {scaler.scale_[i]:.4f}")
