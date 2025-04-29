<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['collection_name'], $conn);
    $description = isset($_POST['collection_description']) ? sanitizeInput($_POST['collection_description'], $conn) : '';
    
    $stmt = $conn->prepare("INSERT INTO collections (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);
    
    if ($stmt->execute()) {
        header("Location: manage-collections.php?success=Collection added successfully");
    } else {
        header("Location: manage-collections.php?error=" . urlencode($conn->error));
    }
    $stmt->close();
} else {
    header("Location: manage-collections.php?error=Invalid request");
}
?>