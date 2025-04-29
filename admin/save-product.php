<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $title = sanitizeInput($_POST['title'], $conn);
    $description = sanitizeInput($_POST['description'], $conn);
    $category_id = (int)$_POST['category_id'];
    $subcategory_id = (int)$_POST['subcategory_id'];
    $mrp = (float)$_POST['mrp'];
    $sale_price = isset($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $sizes = isset($_POST['size']) ? sanitizeInput($_POST['size'], $conn) : '';
    $colors = isset($_POST['color']) ? sanitizeInput($_POST['color'], $conn) : '';
    $collections = isset($_POST['collections']) ? $_POST['collections'] : [];

    // Create uploads directory if it doesn't exist
    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert product into database
        $stmt = $conn->prepare("INSERT INTO products (title, description, category_id, subcategory_id, mrp, sale_price, sizes, colors) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiddss", $title, $description, $category_id, $subcategory_id, $mrp, $sale_price, $sizes, $colors);
        $stmt->execute();
        $product_id = $conn->insert_id;
        $stmt->close();

        // Handle main image upload
        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            $main_image_name = uploadImage($_FILES['main_image'], $product_id, true);
            if (!$main_image_name) {
                throw new Exception("Main image upload failed");
            }
        }

        // Handle additional images
        if (!empty($_FILES['additional_images']['name'][0])) {
            foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['additional_images']['error'][$key] === UPLOAD_ERR_OK) {
                    $file = [
                        'name' => $_FILES['additional_images']['name'][$key],
                        'tmp_name' => $tmp_name,
                        'error' => $_FILES['additional_images']['error'][$key]
                    ];
                    uploadImage($file, $product_id, false);
                }
            }
        }

        // Handle color-specific images
        $color_array = !empty($colors) ? explode(',', $colors) : [];
        foreach ($color_array as $color) {
            $color = trim($color);
            if (isset($_FILES['color_image_'.$color]) && $_FILES['color_image_'.$color]['error'] === UPLOAD_ERR_OK) {
                $color_image_name = uploadImage($_FILES['color_image_'.$color], $product_id, false, $color);
                if (!$color_image_name) {
                    throw new Exception("Color image upload failed for $color");
                }
            }
        }

        // Add to collections
        if (!empty($collections)) {
            foreach ($collections as $collection_id) {
                $collection_id = (int)$collection_id;
                $conn->query("INSERT INTO collection_products (collection_id, product_id) VALUES ($collection_id, $product_id)");
            }
        }

        // Commit transaction
        $conn->commit();
        
        header("Location: manage-products.php?success=Product added successfully");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        header("Location: add-product.php?error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: add-product.php?error=Invalid request");
    exit();
}

function uploadImage($file, $product_id, $is_primary = false, $color = null) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_extensions)) {
        return false;
    }
    
    $image_name = 'product_' . $product_id . '_' . time() . '_' . uniqid() . '.' . $file_extension;
    $image_path = 'uploads/' . $image_name;
    
    if (move_uploaded_file($file['tmp_name'], $image_path)) {
        global $conn;
        $color = $color ? sanitizeInput($color, $conn) : null;
        $is_primary = $is_primary ? 1 : 0;
        
        $stmt = $conn->prepare("INSERT INTO product_images (product_id, image_path, color, is_primary) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $product_id, $image_name, $color, $is_primary);
        $stmt->execute();
        $stmt->close();
        
        return $image_name;
    }
    
    return false;
}
?>