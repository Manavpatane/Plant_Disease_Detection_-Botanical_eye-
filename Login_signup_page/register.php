<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        echo "<script>alert('Signup Successful!');window.location='index.html';</script>";
    } else {
        echo "<script>alert('Error: Username or Email already taken!');window.location='signup.html';</script>";
    }
    $stmt->close();
}
$conn->close();
?>
