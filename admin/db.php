<?php
$host = "localhost";
$user = "root";
$password = "";
$db = "easycart";

$conn = new mysqli($host, $user, $password, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to utf8
$conn->set_charset("utf8mb4");

// Function to sanitize input
function sanitizeInput($data, $conn) {
    if (empty($data)) {
        return '';
    }
    return htmlspecialchars(strip_tags(trim($conn->real_escape_string($data))));
}
?>
