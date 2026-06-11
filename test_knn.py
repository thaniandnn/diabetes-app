import joblib
import numpy as np
model = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/knn_model.pkl')
scaler = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/scaler.pkl')

X = np.array([[8, 70, 90, 35, 40, 18.0, 2.0, 55]])

# Note: The impute_features logic would NOT trigger because all values are > 0 and >= MIN_VALID
# "glucose": 50.0 -> 70 is > 50
# "blood_pressure": 40.0 -> 90 is > 40
# "bmi": 10.0 -> 18.0 is > 10
# "skin_thickness": 0.0 -> 35 is > 0
# "insulin": 0.0 -> 40 is > 0
# So X remains unchanged by impute_features.

X_scaled = scaler.transform(X)
print("KNN Raw features prediction:", model.predict(X_scaled)[0])
print("KNN probabilities:", model.predict_proba(X_scaled))

