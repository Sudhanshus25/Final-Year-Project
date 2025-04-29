<?php
$servername = "localhost"; // Change if using a remote database
$username = "root"; // Change if using a different user
$password = ""; // Your MySQL password (leave empty if using XAMPP)
$dbname = "easycart"; // Change to your actual database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
