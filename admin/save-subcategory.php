<?php
include 'db.php';

$parent = $_POST['parent_category'];
$subcategory = $_POST['subcategory_name'];
$conn->query("INSERT INTO subcategories (category_id, name) VALUES ('$parent', '$subcategory')");
header('Location: add-product.php');
?>
