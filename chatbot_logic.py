import json
import joblib
from nltk.tokenize import word_tokenize
import nltk

nltk.download('punkt')

class ChatbotLogic:
    def __init__(self):
        with open('intents.json', 'r') as file:
            self.data = json.load(file)

        self.model = joblib.load('best_model.pkl')
        self.vectorizer = joblib.load('vectorizer.pkl')

    def get_response(self, user_input):
        user_input_vector = self.vectorizer.transform([user_input])
        prediction = self.model.predict(user_input_vector)[0]

        for intent in self.data['intents']:
            if intent['tag'] == prediction:
                return intent['responses'][0]

        return "I'm sorry, I didn't understand that. Can you rephrase?"

chatbot = ChatbotLogic()
