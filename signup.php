<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm-password']);

    // Check if fields are empty
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error'] = "All fields are required!";
        header("Location: signup.html");
        exit();
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $_SESSION['error'] = "Passwords do not match!";
        header("Location: signup.html");
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "Email is already registered!";
        header("Location: signup.html");
        exit();
    }
    $stmt->close();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $email, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Account created successfully! Please log in.";
        header("Location: login.html");
        exit();
    } else {
        $_SESSION['error'] = "Something went wrong! Try again.";
        header("Location: signup.html");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
