<?php
include 'db.php';

$category = $_POST['category_name'];
$conn->query("INSERT INTO categories (name) VALUES ('$category')");
header('Location: add-product.php');
?>
