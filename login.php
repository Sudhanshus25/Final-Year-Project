<?php
session_start();
require 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Debugging step
    echo "Login request received.<br>";

    if (!isset($_POST['email']) || !isset($_POST['password'])) {
        echo "Email or Password is missing!";
        exit();
    }

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    echo "Email: $email <br>"; // Debugging
    echo "Checking database... <br>";

    $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
    
    if (!$stmt) {
        echo "Statement preparation failed: " . $conn->error;
        exit();
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    echo "Query executed. Rows found: " . $stmt->num_rows . "<br>";

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $name, $hashed_password);
        $stmt->fetch();

        echo "User found: $name <br>";

        if (password_verify($password, $hashed_password)) {
            echo "Password is correct.<br>";
            
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $name;
            header("Location: homepage.html");
            exit();
        } else {
            echo "Invalid password!";
            exit();
        }
    } else {
        echo "User not found!";
        exit();
    }

    $stmt->close();
}
?>
