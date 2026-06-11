import joblib
model = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/svm_model.pkl')
print("SVM Bias (intercept_):", model.intercept_)
print("SVM Gamma:", model._gamma)
