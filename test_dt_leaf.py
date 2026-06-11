import joblib
model = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/model_decision_tree.pkl')

print("Leaf node 69 values (class 0, class 1):", model.tree_.value[69])
