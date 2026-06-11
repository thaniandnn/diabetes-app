import joblib
import numpy as np

model = joblib.load('/Applications/XAMPP/xamppfiles/htdocs/diabetes-app/diabetesRiskApp/diabetesrisk-api/models/model_decision_tree.pkl')

# Pregnancies: 2, Glucose: 200, Blood Pressure: 100, Skin Thickness: 25, Insulin: 200, BMI: 28, DPF: 1, Age: 20
X = np.array([[2, 200, 100, 25, 200, 28.0, 1.0, 20]])

pred = model.predict(X)[0]
print("DT Prediction:", pred)

# Get the decision path
node_indicator = model.decision_path(X)
leaf_id = model.apply(X)
feature = model.tree_.feature
threshold = model.tree_.threshold

print("\nDecision Path for this sample:")
node_index = node_indicator.indices[node_indicator.indptr[0]:node_indicator.indptr[1]]

features_names = ['Pregnancies', 'Glucose', 'BloodPressure', 'SkinThickness', 'Insulin', 'BMI', 'DiabetesPedigreeFunction', 'Age']

for node_id in node_index:
    if leaf_id[0] == node_id:
        print(f"Leaf node {node_id}: predicted class {model.classes_[np.argmax(model.tree_.value[node_id])]}")
        continue
    
    if (X[0, feature[node_id]] <= threshold[node_id]):
        threshold_sign = "<="
    else:
        threshold_sign = ">"
    
    print(f"Node {node_id}: {features_names[feature[node_id]]} = {X[0, feature[node_id]]} {threshold_sign} {threshold[node_id]:.2f}")

