<?php
include 'db.php';
include 'functions.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$product_id) {
    header("Location: manage-products.php?error=Invalid product ID");
    exit();
}

// Get product data
$product = $conn->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
if (!$product) {
    header("Location: manage-products.php?error=Product not found");
    exit();
}

// Get product images
$images = getProductImages($conn, $product_id);
$main_image = null;
$additional_images = [];
$color_images = [];

foreach ($images as $image) {
    if ($image['is_primary']) {
        $main_image = $image;
    } elseif ($image['color']) {
        $color_images[$image['color']] = $image;
    } else {
        $additional_images[] = $image;
    }
}

// Get collections this product belongs to
$product_collections = [];
$result = $conn->query("SELECT collection_id FROM collection_products WHERE product_id = $product_id");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $product_collections[] = $row['collection_id'];
    }
}

$categories = getCategories($conn);
$subcategories = getSubcategories($conn, $product['category_id']);
$collections = getCollections($conn);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Product</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <main>
    <h2>Edit Product: <?= htmlspecialchars($product['title']) ?></h2>
    
    <form action="update-product.php" method="POST" enctype="multipart/form-data" class="product-form">
      <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
      
      <div class="form-section">
        <h3>Basic Information</h3>
        <label>Category:</label>
        <select name="category_id" id="category_id" required>
          <option value="">Select Category</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($category['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Subcategory:</label>
        <select name="subcategory_id" id="subcategory_id" required>
          <option value="">Select Subcategory</option>
          <?php foreach ($subcategories as $subcategory): ?>
            <option value="<?= $subcategory['id'] ?>" 
                    data-category="<?= $subcategory['category_id'] ?>"
                    <?= $subcategory['id'] == $product['subcategory_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($subcategory['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Product Name:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>

        <label>Description:</label>
        <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
      </div>

      <div class="form-section">
        <h3>Pricing</h3>
        <label>MRP (Original Price):</label>
        <input type="number" name="mrp" step="0.01" value="<?= $product['mrp'] ?>" required>

        <label>Sale Price (If Discounted):</label>
        <input type="number" name="sale_price" step="0.01" value="<?= $product['sale_price'] ?>">
      </div>

      <div class="form-section">
        <h3>Variants</h3>
        <label>Available Sizes (Comma Separated):</label>
        <input type="text" name="size" value="<?= htmlspecialchars($product['sizes']) ?>" placeholder="e.g. S,M,L,XL">

        <label>Available Colors (Comma Separated):</label>
        <input type="text" name="color" id="color-input" value="<?= htmlspecialchars($product['colors']) ?>" placeholder="e.g. Red,Black,Blue">
        
        <div id="color-images-container">
          <!-- Will be populated by JavaScript -->
        </div>
        
        <!-- Display existing color images -->
        <?php if (!empty($color_images)): ?>
          <div class="existing-images">
            <h4>Existing Color Images</h4>
            <div class="image-grid">
              <?php foreach ($color_images as $color => $image): ?>
                <div class="image-item">
                  <img src="uploads/<?= htmlspecialchars($image['image_path']) ?>" alt="<?= htmlspecialchars($color) ?>">
                  <span><?= htmlspecialchars($color) ?></span>
                  <label>
                    <input type="checkbox" name="delete_color_image[]" value="<?= $image['id'] ?>">
                    Delete
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <div class="form-section">
        <h3>Images</h3>
        
        <!-- Main Image -->
        <label>Main Product Image:</label>
        <?php if ($main_image): ?>
          <div class="current-image">
            <img src="uploads/<?= htmlspecialchars($main_image['image_path']) ?>" alt="Main Product Image" style="max-width: 200px; display: block; margin-bottom: 10px;">
            <label>
              <input type="checkbox" name="delete_main_image" value="1">
              Delete current image
            </label>
          </div>
        <?php endif; ?>
        <input type="file" name="main_image" accept="image/*">
        
        <!-- Additional Images -->
        <label>Additional Images:</label>
        <?php if (!empty($additional_images)): ?>
          <div class="existing-images">
            <div class="image-grid">
              <?php foreach ($additional_images as $image): ?>
                <div class="image-item">
                  <img src="uploads/<?= htmlspecialchars($image['image_path']) ?>" alt="Additional Image">
                  <label>
                    <input type="checkbox" name="delete_additional_images[]" value="<?= $image['id'] ?>">
                    Delete
                  </label>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        <?php endif; ?>
        <input type="file" name="additional_images[]" accept="image/*" multiple>
        
        <!-- Color-specific Images -->
        <div id="color-specific-images">
          <h4>Color-Specific Images</h4>
          <p>Upload new images that will change when a color is selected</p>
          <!-- Will be populated by JavaScript based on colors -->
        </div>
      </div>

      <div class="form-section">
        <h3>Collections</h3>
        <?php if (!empty($collections)): ?>
          <label>Add to Collections:</label>
          <div class="checkbox-group">
            <?php foreach ($collections as $collection): ?>
              <label>
                <input type="checkbox" name="collections[]" value="<?= $collection['id'] ?>"
                  <?= in_array($collection['id'], $product_collections) ? 'checked' : '' ?>>
                <?= htmlspecialchars($collection['name']) ?>
              </label>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p>No collections created yet.</p>
        <?php endif; ?>
      </div>

      <button type="submit" class="submit-btn"><i class="fas fa-save"></i> Update Product</button>
    </form>
  </main>

  <script src="script.js"></script>
  <script>
    // Initialize color inputs after page load
    document.addEventListener('DOMContentLoaded', function() {
      if (document.getElementById('color-input').value) {
        updateColorImageInputs();
      }
    });
  </script>
</body>
</html>