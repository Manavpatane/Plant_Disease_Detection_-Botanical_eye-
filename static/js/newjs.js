document.addEventListener("DOMContentLoaded", () => {
    const btnPredict = document.querySelector("#btn-predict");
    if (btnPredict) {
        btnPredict.addEventListener("click", async (e) => {
            e.preventDefault();
            const formData = new FormData();
            const fileInput = document.querySelector("#imageUpload");
            if (fileInput.files.length === 0) {
                alert("Please upload a file.");
                return;
            }

            formData.append("file", fileInput.files[0]);

            try {
                const response = await fetch("/predict", {
                    method: "POST",
                    body: formData,
                });

                const result = await response.text();
                alert(`Prediction: ${result}`);
            } catch (error) {
                console.error(error);
            }
        });
    }
});
