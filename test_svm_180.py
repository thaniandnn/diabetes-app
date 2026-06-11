import joblib
import numpy as np

model = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/svm_model.pkl')
scaler = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/scaler_svm.pkl')

# Realistic high values: Glucose 180, Insulin 200
X = np.array([[8, 180, 80, 25, 200, 35.0, 1.0, 50]])

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

print("SVM Prediction for realistic high:", model.predict(X_scaled)[0])
print("SVM Decision Function:", model.decision_function(X_scaled)[0])
