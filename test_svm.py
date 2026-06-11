import joblib
import numpy as np
scaler = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/scaler_svm.pkl')

X = np.array([[8, 200, 90, 35, 300, 40, 2.0, 55]])
# Calculate engineered features
glucose = X[0][1]
bmi = X[0][5]
age = X[0][7]
pregnancies = X[0][0]
insulin = X[0][4]

glucose_bmi = glucose * bmi
age_pregnancies = age * pregnancies
insulin_glucose = insulin * glucose

X_eng = np.append(X, [[glucose_bmi, age_pregnancies, insulin_glucose]], axis=1)

print("Original shape:", X.shape)
print("Engineered shape:", X_eng.shape)
try:
    scaled = scaler.transform(X_eng)
    print("Scaling successful! Scaled shape:", scaled.shape)
except Exception as e:
    print("Scaling failed:", e)

