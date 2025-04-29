<?php 
include 'db.php';
include 'functions.php'; // We'll create this file next

// Get all categories and subcategories for dropdowns
$categories = getCategories($conn);
$subcategories = getSubcategories($conn);
?>
<!DOCTYPE html>
<html>
<head>
  <title>Add Product</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <main>
    <h2>Add New Product</h2>

    <!-- Add Category/Subcategory/Collection -->
    <div class="flex-row">
      <form action="save-category.php" method="POST" class="mini-form">
        <h3>Add Category</h3>
        <label>Category Name:</label>
        <input type="text" name="category_name" required>
        <button type="submit"><i class="fas fa-plus"></i> Add Category</button>
      </form>

      <form action="save-subcategory.php" method="POST" class="mini-form">
        <h3>Add Subcategory</h3>
        <label>Parent Category:</label>
        <select name="parent_category" required>
          <option value="">Select Category</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
          <?php endforeach; ?>
        </select>
        <label>Subcategory Name:</label>
        <input type="text" name="subcategory_name" required>
        <button type="submit"><i class="fas fa-plus"></i> Add Subcategory</button>
      </form>

      <form action="save-collection.php" method="POST" class="mini-form">
        <h3>Add Collection</h3>
        <label>Collection Name:</label>
        <input type="text" name="collection_name" required>
        <label>Description:</label>
        <textarea name="collection_description"></textarea>
        <button type="submit"><i class="fas fa-plus"></i> Add Collection</button>
      </form>
    </div>

    <!-- Add Product Form -->
    <form action="save-product.php" method="POST" enctype="multipart/form-data" class="product-form">
      <div class="form-section">
        <h3>Basic Information</h3>
        <label>Category:</label>
        <select name="category_id" id="category_id" required>
          <option value="">Select Category</option>
          <?php foreach ($categories as $category): ?>
            <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
          <?php endforeach; ?>
        </select>

        <label>Subcategory:</label>
        <select name="subcategory_id" id="subcategory_id" required>
          <option value="">Select Subcategory</option>
          <?php foreach ($subcategories as $subcategory): ?>
            <option value="<?= $subcategory['id'] ?>" data-category="<?= $subcategory['category_id'] ?>">
              <?= $subcategory['name'] ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Product Name:</label>
        <input type="text" name="title" required>

        <label>Description:</label>
        <textarea name="description" required></textarea>
      </div>

      <div class="form-section">
        <h3>Pricing</h3>
        <label>MRP (Original Price):</label>
        <input type="number" name="mrp" step="0.01" required>

        <label>Sale Price (If Discounted):</label>
        <input type="number" name="sale_price" step="0.01">
      </div>

      <div class="form-section">
        <h3>Variants</h3>
        <label>Available Sizes (Comma Separated):</label>
        <input type="text" name="size" placeholder="e.g. S,M,L,XL">

        <label>Available Colors (Comma Separated):</label>
        <input type="text" name="color" id="color-input" placeholder="e.g. Red,Black,Blue">
        
        <div id="color-images-container">
          <!-- Will be populated by JavaScript -->
        </div>
      </div>

      <div class="form-section">
        <h3>Images</h3>
        <label>Main Product Image:</label>
        <input type="file" name="main_image" accept="image/*" required>
        
        <label>Additional Images (Max 4):</label>
        <input type="file" name="additional_images[]" accept="image/*" multiple>
        
        <div id="color-specific-images">
          <h4>Color-Specific Images</h4>
          <p>Upload images that will change when a color is selected</p>
          <!-- Will be populated by JavaScript based on colors -->
        </div>
      </div>

      <div class="form-section">
        <h3>Collections (Optional)</h3>
        <?php $collections = getCollections($conn); ?>
        <?php if (!empty($collections)): ?>
          <label>Add to Collections:</label>
          <div class="checkbox-group">
            <?php foreach ($collections as $collection): ?>
              <label>
                <input type="checkbox" name="collections[]" value="<?= $collection['id'] ?>">
                <?= $collection['name'] ?>
              </label>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p>No collections created yet. Create one above.</p>
        <?php endif; ?>
      </div>

      <button type="submit" class="submit-btn"><i class="fas fa-save"></i> Add Product</button>
    </form>
  </main>

  <script src="script.js"></script>
</body>
</html>