import joblib
import numpy as np
model = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/svm_model.pkl')
scaler = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/scaler_svm.pkl')

# Pregnancies: 3, Glucose: 200, Blood Pressure: 180, Skin Thickness: 25, Insulin: 200, BMI: 24, DPF: 1, Age: 20
X = np.array([[3, 200, 180, 25, 200, 24.0, 1.0, 20]])

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
print("Scaled features:")
for name, val in zip(scaler.feature_names_in_, X_scaled[0]):
    print(f"  {name}: {val:.2f}")

print("Decision Function:", model.decision_function(X_scaled)[0])

