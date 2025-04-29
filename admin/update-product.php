<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    if (!$product_id) {
        header("Location: manage-products.php?error=Invalid product ID");
        exit();
    }

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

    // Start transaction
    $conn->begin_transaction();

    try {
        // Update product in database
        $stmt = $conn->prepare("UPDATE products SET title=?, description=?, category_id=?, subcategory_id=?, mrp=?, sale_price=?, sizes=?, colors=? WHERE id=?");
        $stmt->bind_param("ssiiddssi", $title, $description, $category_id, $subcategory_id, $mrp, $sale_price, $sizes, $colors, $product_id);
        $stmt->execute();
        $stmt->close();

        // Handle main image upload/delete
        if (isset($_POST['delete_main_image']) && $_POST['delete_main_image'] == '1') {
            // Delete existing main image
            $conn->query("DELETE FROM product_images WHERE product_id = $product_id AND is_primary = 1");
        }

        if (isset($_FILES['main_image']) && $_FILES['main_image']['error'] === UPLOAD_ERR_OK) {
            // Delete existing main image if exists
            $conn->query("DELETE FROM product_images WHERE product_id = $product_id AND is_primary = 1");
            
            // Upload new main image
            $main_image_name = uploadImage($_FILES['main_image'], $product_id, true);
            if (!$main_image_name) {
                throw new Exception("Main image upload failed");
            }
        }

        // Handle additional images delete
        if (isset($_POST['delete_additional_images'])) {
            foreach ($_POST['delete_additional_images'] as $image_id) {
                $image_id = (int)$image_id;
                $conn->query("DELETE FROM product_images WHERE id = $image_id AND product_id = $product_id");
            }
        }

        // Handle additional images upload
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

        // Handle color images delete
        if (isset($_POST['delete_color_image'])) {
            foreach ($_POST['delete_color_image'] as $image_id) {
                $image_id = (int)$image_id;
                $conn->query("DELETE FROM product_images WHERE id = $image_id AND product_id = $product_id");
            }
        }

        // Handle color-specific images upload
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

        // Update collections
        $conn->query("DELETE FROM collection_products WHERE product_id = $product_id");
        if (!empty($collections)) {
            foreach ($collections as $collection_id) {
                $collection_id = (int)$collection_id;
                $conn->query("INSERT INTO collection_products (collection_id, product_id) VALUES ($collection_id, $product_id)");
            }
        }

        // Commit transaction
        $conn->commit();
        
        header("Location: manage-products.php?success=Product updated successfully");
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        header("Location: edit-product.php?id=$product_id&error=" . urlencode($e->getMessage()));
        exit();
    }
} else {
    header("Location: manage-products.php?error=Invalid request");
    exit();
}
?>