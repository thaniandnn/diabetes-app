import joblib
scaler = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/scaler_svm.pkl')
print(getattr(scaler, "feature_names_in_", "No feature names found in scaler"))
