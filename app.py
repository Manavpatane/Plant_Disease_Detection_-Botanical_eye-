import os
import numpy as np
from flask import Flask, render_template, request, jsonify
from werkzeug.utils import secure_filename
from tensorflow.keras.preprocessing.image import load_img, img_to_array
from keras.models import load_model
import matplotlib.pyplot as plt
import io
import base64
from chatbot_logic import chatbot  # Import chatbot logic

# Flask app
app = Flask(__name__)

# Load the model
model = load_model("model.h5")
print("Model loaded successfully.")

# Labels for predictions
labels = {
    0: 'Apple__black_rot', 1: 'Apple__healthy', 2: 'Apple__rust', 3: 'Apple__scab',
    4: 'Cassava__bacterial_blight', 5: 'Cassava__brown_streak_disease',
    6: 'Cassava__green_mottle', 7: 'Cassava__healthy', 8: 'Cassava__mosaic_disease',
    9: 'Cherry__healthy', 10: 'Cherry__powdery_mildew', 11: 'Chili__healthy',
    12: 'Chili__leaf_curl', 13: 'Chili__leaf_spot', 14: 'Chili__whitefly',
    15: 'Chili__yellowish', 16: 'Coffee__cercospora_leaf_spot', 17: 'Coffee__healthy',
    18: 'Coffee__red_spider_mite', 19: 'Coffee__rust', 20: 'Corn__common_rust',
    21: 'Corn__gray_leaf_spot', 22: 'Corn__healthy', 23: 'Corn__northern_leaf_blight',
    24: 'Cucumber__diseased', 25: 'Cucumber__healthy', 26: 'Gauva__diseased',
    27: 'Gauva__healthy', 28: 'Grape__black_measles', 29: 'Grape__black_rot',
    30: 'Grape__healthy', 31: 'Grape__leaf_blight_isariopsis_leaf_spot',
    32: 'Jamun__diseased', 33: 'Jamun__healthy', 34: 'Lemon__diseased',
    35: 'Lemon__healthy', 36: 'Mango__diseased', 37: 'Mango__healthy',
    38: 'Peach__bacterial_spot', 39: 'Peach__healthy', 40: 'Pepper_bell__bacterial_spot',
    41: 'Pepper_bell__healthy', 42: 'Pomegranate__diseased', 43: 'Pomegranate__healthy',
    44: 'Potato__early_blight', 45: 'Potato__healthy', 46: 'Potato__late_blight',
    47: 'Rice__brown_spot', 48: 'Rice__healthy', 49: 'Rice__hispa', 50: 'Rice__leaf_blast',
    51: 'Rice__neck_blast', 52: 'Soybean__bacterial_blight', 53: 'Soybean__caterpillar',
    54: 'Soybean__diabrotica_speciosa', 55: 'Soybean__downy_mildew', 56: 'Soybean__healthy',
    57: 'Soybean__mosaic_virus', 58: 'Soybean__powdery_mildew', 59: 'Soybean__rust',
    60: 'Soybean__southern_blight', 61: 'Strawberry___leaf_scorch', 62: 'Strawberry__healthy',
    63: 'Sugarcane__bacterial_blight', 64: 'Sugarcane__healthy', 65: 'Sugarcane__red_rot',
    66: 'Sugarcane__red_stripe', 67: 'Sugarcane__rust', 68: 'Tea__algal_leaf',
    69: 'Tea__anthracnose', 70: 'Tea__bird_eye_spot', 71: 'Tea__brown_blight',
    72: 'Tea__healthy', 73: 'Tea__red_leaf_spot', 74: 'Tomato__bacterial_spot',
    75: 'Tomato__early_blight', 76: 'Tomato__healthy', 77: 'Tomato__late_blight',
    78: 'Tomato__leaf_mold', 79: 'Tomato__mosaic_virus', 80: 'Tomato__septoria_leaf_spot',
    81: 'Tomato__spider_mites_two_spotted_spider_mite', 82: 'Tomato__target_spot',
    83: 'Tomato__yellow_leaf_curl_virus', 84: 'Wheat__brown_rust', 85: 'Wheat__healthy',
    86: 'Wheat__septoria', 87: 'Wheat__yellow_rust'
}

# Random probabilities for diseases for the plot
disease_names = list(labels.values())
disease_probabilities = np.random.rand(len(disease_names))

# Upload folder
UPLOAD_FOLDER = "uploads"
os.makedirs(UPLOAD_FOLDER, exist_ok=True)
app.config["UPLOAD_FOLDER"] = UPLOAD_FOLDER

# Prediction function
def getResult(image_path):
    img = load_img(image_path, target_size=(225, 225))
    x = img_to_array(img) / 255.0
    x = np.expand_dims(x, axis=0)
    predictions = model.predict(x)[0]
    return predictions

# Routes
@app.route("/")
def home():
    return render_template("index.html")

@app.route("/predict", methods=["POST"])
def predict():
    if "file" not in request.files:
        return "No file uploaded", 400
    file = request.files["file"]
    if file.filename == "":
        return "No file selected", 400

    # Save file to upload folder
    file_path = os.path.join(app.config["UPLOAD_FOLDER"], secure_filename(file.filename))
    file.save(file_path)

    # Get predictions
    predictions = getResult(file_path)
    predicted_label = labels[np.argmax(predictions)]

    # Return the prediction result
    return render_template("prediction_result.html", prediction=predicted_label)

# Disease probability plot
@app.route('/disease-prediction', methods=['GET', 'POST'])
def index():
    if request.method == 'POST':
        selected_disease = request.form['disease_name']
        return generate_plot(selected_disease)
    return render_template('index1.html', diseases=disease_names)

def generate_plot(disease_name):
    if disease_name in disease_names:
        index = disease_names.index(disease_name)
        probability = disease_probabilities[index]

        # Plotting
        plt.figure(figsize=(8, 5))
        plt.bar(disease_name, probability, color="lightgreen")
        plt.title(f"Probability of {disease_name}", fontsize=14)
        plt.xlabel("Disease", fontsize=12)
        plt.ylabel("Probability", fontsize=12)
        plt.text(0, probability + 0.01, f"{probability:.2f}", ha="center", fontsize=12)
        plt.tight_layout()

        # Save to buffer
        buf = io.BytesIO()
        plt.savefig(buf, format="png")
        buf.seek(0)
        plt.close()

        # Encode to base64
        plot_url = base64.b64encode(buf.getvalue()).decode("utf8")
        return render_template("plot.html", plot_url=plot_url)
    else:
        return f"Error: Disease '{disease_name}' is not in the list."

# Chatbot functionality
@app.route("/chatbot")
def chatbot_home():
    return render_template("chatbot.html")

@app.route("/chat", methods=["POST"])
def chat():
    user_input = request.form.get("message")
    if not user_input:
        return jsonify({"error": "No message provided"}), 400

    response = chatbot.get_response(user_input)
    return jsonify({"response": response})

if __name__ == "__main__":
    app.run(host="127.0.0.1", port=5000, debug=True)
