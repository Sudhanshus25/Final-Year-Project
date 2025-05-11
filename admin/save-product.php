<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// echo "<h2>Debugging Product Submission</h2>";

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // echo "<h3>POST Data Received:</h3>";
    // echo "<pre>"; print_r($_POST); echo "</pre>";
    // echo "<h3>FILES Data:</h3>";
    // echo "<pre>"; print_r($_FILES); echo "</pre>";

    // Sanitize input
    $title = sanitizeInput($_POST['title'], $conn);
    $description = sanitizeInput($_POST['description'], $conn);
    $category_id = (int)$_POST['category_id'];
    $subcategory_id = (int)$_POST['subcategory_id'];
    $mrp = (float)$_POST['mrp'];
    $sale_price = isset($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $sizes = isset($_POST['size']) ? sanitizeInput($_POST['size'], $conn) : '';
    $colors = isset($_POST['color']) ? sanitizeInput($_POST['color'], $conn) : '';

    // echo "<h3>Sanitized Values:</h3>";
    // echo "Title: $title<br>";
    // echo "Description: $description<br>";
    // echo "Category ID: $category_id<br>";
    // echo "Subcategory ID: $subcategory_id<br>";
    // echo "MRP: $mrp<br>";
    // echo "Sale Price: " . ($sale_price ?? 'NULL') . "<br>";
    // echo "Sizes: $sizes<br>";
    // echo "Colors: $colors<br>";

    // Create uploads directory if it doesn't exist
    if (!is_dir('uploads')) {
        // echo "Creating uploads directory...<br>";
        mkdir('uploads', 0755, true);
    }

    // Check directory permissions
    // echo "Uploads directory exists: " . (is_dir('uploads') ? 'Yes' : 'No') . "<br>";
    // echo "Uploads directory writable: " . (is_writable('uploads') ? 'Yes' : 'No') . "<br>";

    // Handle main image upload
    if (isset($_FILES['main_image'])) {
        // echo "<h3>Processing Main Image</h3>";
        // echo "File name: " . $_FILES['main_image']['name'] . "<br>";
        // echo "File size: " . $_FILES['main_image']['size'] . "<br>";
        // echo "File type: " . $_FILES['main_image']['type'] . "<br>";
        // echo "Temp path: " . $_FILES['main_image']['tmp_name'] . "<br>";
        // echo "Error code: " . $_FILES['main_image']['error'] . "<br>";

        $imageName = time() . '-' . basename($_FILES['main_image']['name']);
        $imagePath = 'uploads/' . $imageName;

        if (move_uploaded_file($_FILES['main_image']['tmp_name'], $imagePath)) {
            // echo "Image uploaded successfully to: $imagePath<br>";

            // Insert into database
            $sql = "INSERT INTO products (title, description, category_id, subcategory_id, mrp, sale_price, sizes, colors, image) 
                    VALUES ('$title', '$description', '$category_id', '$subcategory_id', '$mrp', '$sale_price', '$sizes', '$colors', '$imageName')";

            // echo "<h3>SQL Query:</h3>";
            // echo $sql . "<br>";

            if ($conn->query($sql)) {
                // echo "<h3 style='color:green;'>Product added successfully!</h3>";
                // echo "Insert ID: " . $conn->insert_id . "<br>";
                
                // Verify the record exists
                $last_id = $conn->insert_id;
                $check = $conn->query("SELECT * FROM products WHERE id = $last_id");
                echo "Records found: " . $check->num_rows . "<br>";
                
                if ($check->num_rows > 0) {
                    // echo "<pre>"; print_r($check->fetch_assoc()); echo "</pre>";
                }
                
                // header("Location: manage-products.php?success=Product added successfully");
            } else {
                // echo "<h3 style='color:red;'>Database error: " . $conn->error . "</h3>";
            }
        } else {
            // echo "<h3 style='color:red;'>Image upload failed!</h3>";
            // echo "Possible reasons:<br>";
            // echo "- Invalid file<br>";
            // echo "- Permission issues<br>";
            // echo "- File size too large<br>";
        }
    } else {
        // echo "<h3 style='color:red;'>No main image uploaded!</h3>";
    }
} else {
    // echo "<h3 style='color:red;'>Invalid request method!</h3>";
}

// echo "<h3>Debugging Complete</h3>";
?>