import joblib
import numpy as np
model = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/model_decision_tree.pkl')

# User's inputs from the screenshot:
# Pregnancies: 8
# Glucose: 200
# Blood Pressure: 90
# Skin Thickness: 35
# Insulin: 300
# BMI: 40
# DPF: 2.0
# Age: 55

X = np.array([[8, 200, 90, 35, 300, 40, 2.0, 55]])
print("Raw features prediction:", model.predict(X)[0])

# Wait, the user's JS script might be sending the wrong order or something.
