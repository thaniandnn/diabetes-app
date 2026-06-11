import joblib
import numpy as np
model = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/svm_model.pkl')
scaler = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/scaler_svm.pkl')

# Pregnancies: 8, Glucose: 500, Blood Pressure: 80, Skin Thickness: 25, Insulin: 400, BMI: 20, DPF: 2, Age: 55
X = np.array([[8, 500, 80, 25, 400, 20.0, 2.0, 55]])

glucose = X[0][1]
bmi = X[0][5]
age = X[0][7]
pregnancies = X[0][0]
insulin = X[0][4]

glucose_bmi = glucose * bmi
age_pregnancies = age * pregnancies
insulin_glucose = insulin * glucose

X_eng = np.append(X, [[glucose_bmi, age_pregnancies, insulin_glucose]], axis=1)

X_scaled = scaler.transform(X_eng)
pred = model.predict(X_scaled)[0]

print("SVM Prediction:", pred)
