document.addEventListener("DOMContentLoaded", function () {
    // Login & Signup Form Redirection
    const loginForm = document.getElementById("login-form");
    const signupForm = document.getElementById("signup-form");

    if (loginForm) {
        loginForm.addEventListener("submit", function (e) {
            e.preventDefault();
            // Simulating authentication
            let email = document.getElementById("login-email").value;
            let password = document.getElementById("login-password").value;

            if (email && password) {
                alert("Login Successful!");
                window.location.href = "home.html";
            } else {
                alert("Please enter valid credentials.");
            }
        });
    }

    if (signupForm) {
        signupForm.addEventListener("submit", function (e) {
            e.preventDefault();
            // Simulating user registration
            let name = document.getElementById("signup-name").value;
            let email = document.getElementById("signup-email").value;
            let password = document.getElementById("signup-password").value;

            if (name && email && password) {
                alert("Signup Successful! Redirecting to login...");
                window.location.href = "login.html";
            } else {
                alert("Please fill out all fields.");
            }
        });
    }

    // Image Upload & Disease Detection Simulation
    const uploadButton = document.getElementById("upload-button");
    const resultDisplay = document.getElementById("result");

    if (uploadButton) {
        uploadButton.addEventListener("click", function () {
            let fileInput = document.getElementById("file-input");

            if (fileInput.files.length > 0) {
                let fileName = fileInput.files[0].name;
                resultDisplay.innerHTML = `Detecting disease for: <b>${fileName}</b>...`;

                setTimeout(() => {
                    let diseases = ["Leaf Blight", "Powdery Mildew", "Rust", "Wilt"];
                    let detectedDisease = diseases[Math.floor(Math.random() * diseases.length)];
                    resultDisplay.innerHTML = `<b>Disease Detected:</b> ${detectedDisease}`;
                }, 2000);
            } else {
                alert("Please upload an image first.");
            }
        });
    }

    // Chatbot Functionality
    const chatbox = document.getElementById("chatbox");
    const chatbotToggle = document.getElementById("chatbot-toggle");
    const chatInput = document.getElementById("chatbox-input");
    const chatMessages = document.getElementById("chatbox-messages");
    const chatSend = document.getElementById("chatbox-send");

    if (chatbotToggle) {
        chatbotToggle.addEventListener("click", function () {
            chatbox.style.display = chatbox.style.display === "block" ? "none" : "block";
        });
    }

    if (chatSend) {
        chatSend.addEventListener("click", function () {
            let userMessage = chatInput.value.trim();

            if (userMessage) {
                chatMessages.innerHTML += `<div class='user-message'>You: ${userMessage}</div>`;

                setTimeout(() => {
                    let botResponses = {
                        "What is Leaf Blight?": "Leaf Blight is a fungal disease that causes yellowing and browning of leaves.",
                        "How to treat Rust?": "Rust can be treated using fungicides and proper crop rotation.",
                        "What is Powdery Mildew?": "Powdery Mildew appears as white powdery spots on leaves and stems, commonly found in dry conditions.",
                        "default": "I’m here to assist! Please ask about plant diseases."
                    };

                    let botReply = botResponses[userMessage] || botResponses["default"];
                    chatMessages.innerHTML += `<div class='bot-message'>Bot: ${botReply}</div>`;
                }, 1500);

                chatInput.value = "";
            }
        });
    }

    // Graph Visualization on Plot Graph Page
    if (document.getElementById("diseaseChart")) {
        const ctx = document.getElementById("diseaseChart").getContext("2d");

        new Chart(ctx, {
            type: "bar",
            data: {
                labels: ["Leaf Blight", "Powdery Mildew", "Rust", "Wilt"],
                datasets: [{
                    label: "Occurrences in Last Month",
                    data: [12, 9, 15, 8],
                    backgroundColor: ["red", "orange", "yellow", "green"]
                }]
            }
        });
    }

    // Smooth Scroll for Navbar
    document.querySelectorAll(".navbar a").forEach(link => {
        link.addEventListener("click", function (e) {
            if (this.hash !== "") {
                e.preventDefault();
                const hash = this.hash;
                document.querySelector(hash).scrollIntoView({
                    behavior: "smooth",
                    block: "start"
                });
            }
        });
    });
});
