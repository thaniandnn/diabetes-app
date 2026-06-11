import joblib
import numpy as np
model = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/svm_model.pkl')
scaler = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/scaler_svm.pkl')

X = np.array([[3, 200, 180, 25, 200, 24.0, 1.0, 20]])

glucose = X[0][1]
bmi = X[0][5]
age = X[0][7]
pregnancies = X[0][0]
insulin = X[0][4]

glucose_bmi = glucose * bmi
age_pregnancies = age * pregnancies
insulin_glucose = insulin / glucose

X_eng = np.append(X, [[glucose_bmi, age_pregnancies, insulin_glucose]], axis=1)
X_scaled = scaler.transform(X_eng)

proba = model.predict_proba(X_scaled)[0]
print("Probabilities:", proba)
