import joblib
import numpy as np

model = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/svm_model.pkl')
scaler = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/scaler_svm.pkl')

found_diabetic = False
for i in range(1000):
    # Random realistic values
    pregnancies = np.random.uniform(0, 15)
    glucose = np.random.uniform(70, 200)
    bp = np.random.uniform(50, 110)
    skin = np.random.uniform(10, 50)
    insulin = np.random.uniform(15, 300)
    bmi = np.random.uniform(18, 50)
    dpf = np.random.uniform(0.1, 2.5)
    age = np.random.uniform(21, 80)
    
    glucose_bmi = glucose * bmi
    age_pregnancies = age * pregnancies
    insulin_glucose = insulin * glucose
    
    X = np.array([[pregnancies, glucose, bp, skin, insulin, bmi, dpf, age, glucose_bmi, age_pregnancies, insulin_glucose]])
    X_scaled = scaler.transform(X)
    
    pred = model.predict(X_scaled)[0]
    if pred == 1:
        print(f"Found diabetic prediction! Glucose: {glucose}, BMI: {bmi}, Age: {age}")
        found_diabetic = True
        break

if not found_diabetic:
    print("Checked 1000 random points. ALL were predicted as 0 (No Diabetes).")

