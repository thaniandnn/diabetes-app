import joblib
model = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/svm_model.pkl')
print("probability=True?", getattr(model, "probability", False))
