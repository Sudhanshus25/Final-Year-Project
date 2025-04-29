<?php
include 'db.php';
include 'functions.php';

$products = getProducts($conn);
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Products</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <main>
    <h2>Manage Products</h2>
    
    <?php if ($success): ?>
      <div class="alert success">
        <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
      <div class="alert error">
        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <div class="actions">
      <a href="add-product.php" class="btn"><i class="fas fa-plus"></i> Add New Product</a>
    </div>

    <div class="product-grid">
      <?php if (!empty($products)): ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Image</th>
              <th>Name</th>
              <th>Category</th>
              <th>Price</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $product): ?>
              <tr>
                <td><?= $product['id'] ?></td>
                <td>
                  <?php 
                    $images = getProductImages($conn, $product['id']);
                    if (!empty($images)) {
                        echo '<img src="uploads/' . htmlspecialchars($images[0]['image_path']) . '" alt="' . htmlspecialchars($product['title']) . '" class="product-thumb">';
                    } else {
                        echo '<div class="no-image">No Image</div>';
                    }
                  ?>
                </td>
                <td><?= htmlspecialchars($product['title']) ?></td>
                <td><?= htmlspecialchars($product['category_name']) ?> / <?= htmlspecialchars($product['subcategory_name']) ?></td>
                <td>
                  <span class="mrp">₹<?= number_format($product['mrp'], 2) ?></span>
                  <?php if ($product['sale_price']): ?>
                    <span class="sale-price">₹<?= number_format($product['sale_price'], 2) ?></span>
                  <?php endif; ?>
                </td>
                <td class="actions">
                  <a href="edit-product.php?id=<?= $product['id'] ?>" class="btn small"><i class="fas fa-edit"></i> Edit</a>
                  <a href="delete-product.php?id=<?= $product['id'] ?>" class="btn small danger" onclick="return confirm('Are you sure you want to delete this product?')">
                    <i class="fas fa-trash"></i> Delete
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="no-products">No products found. <a href="add-product.php">Add your first product</a>.</p>
      <?php endif; ?>
    </div>
  </main>

  <script src="script.js"></script>
</body>
</html>