import joblib
model = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/model_decision_tree.pkl')
print(model.tree_.threshold[:10])
