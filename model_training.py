import json
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.model_selection import train_test_split
from sklearn.svm import SVC
import joblib
from nltk.tokenize import word_tokenize
import nltk

nltk.download('punkt_tab')

# Load dataset
with open('intents.json', 'r') as file:
    data = json.load(file)

# Prepare training data
questions, labels = [], []
for intent in data['intents']:
    for pattern in intent['patterns']:
        questions.append(pattern)
        labels.append(intent['tag'])

vectorizer = TfidfVectorizer(tokenizer=word_tokenize, lowercase=True)
X = vectorizer.fit_transform(questions)
y = np.array(labels)

X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

# Train a Support Vector Machine (SVM)
model = SVC(kernel='linear', probability=True)
model.fit(X_train, y_train)

# Save the model and vectorizer
joblib.dump(model, 'best_model.pkl')
joblib.dump(vectorizer, 'vectorizer.pkl')

print("Model trained and saved as 'best_model.pkl'.")
