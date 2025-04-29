<?php
include 'db.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header("Location: manage-products.php?error=Invalid product ID");
    exit();
}

// Start transaction
$conn->begin_transaction();

try {
    // Delete from collections first
    $conn->query("DELETE FROM collection_products WHERE product_id = $product_id");
    
    // Get image paths to delete files
    $images = $conn->query("SELECT image_path FROM product_images WHERE product_id = $product_id");
    if ($images && $images->num_rows > 0) {
        while ($row = $images->fetch_assoc()) {
            $file_path = 'uploads/' . $row['image_path'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }
    
    // Delete product images
    $conn->query("DELETE FROM product_images WHERE product_id = $product_id");
    
    // Delete product
    $conn->query("DELETE FROM products WHERE id = $product_id");
    
    // Commit transaction
    $conn->commit();
    
    header("Location: manage-products.php?success=Product deleted successfully");
    exit();
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    header("Location: manage-products.php?error=" . urlencode($e->getMessage()));
    exit();
}
?>