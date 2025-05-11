<?php
include 'admin/db.php';

// Get category and subcategory IDs from URL
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$subcategory_id = isset($_GET['subcategory_id']) ? (int)$_GET['subcategory_id'] : 0;

// Get category and subcategory names for display
$category_name = '';
$subcategory_name = '';

if ($category_id > 0) {
    $cat_result = $conn->query("SELECT name FROM categories WHERE id = $category_id");
    if ($cat_result && $cat_result->num_rows > 0) {
        $category_name = $cat_result->fetch_assoc()['name'];
    }
}

if ($subcategory_id > 0) {
    $subcat_result = $conn->query("SELECT name FROM subcategories WHERE id = $subcategory_id");
    if ($subcat_result && $subcat_result->num_rows > 0) {
        $subcategory_name = $subcat_result->fetch_assoc()['name'];
    }
}

// Build SQL query with filters
$sql = "SELECT p.*, c.name AS category_name, s.name AS subcategory_name 
        FROM products p
        JOIN categories c ON p.category_id = c.id
        JOIN subcategories s ON p.subcategory_id = s.id
        WHERE 1=1";

if ($category_id > 0) {
    $sql .= " AND p.category_id = $category_id";
}

if ($subcategory_id > 0) {
    $sql .= " AND p.subcategory_id = $subcategory_id";
}

// Handle sorting
$sortOptions = ['popularity', 'price-low-to-high', 'price-high-to-low', 'name-a-z', 'name-z-a'];
$currentSort = isset($_GET['sort']) ? $_GET['sort'] : 'popularity';

switch ($currentSort) {
    case 'price-low-to-high':
        $sql .= " ORDER BY p.sale_price ASC";
        break;
    case 'price-high-to-low':
        $sql .= " ORDER BY p.sale_price DESC";
        break;
    case 'name-a-z':
        $sql .= " ORDER BY p.title ASC";
        break;
    case 'name-z-a':
        $sql .= " ORDER BY p.title DESC";
        break;
    default:
        $sql .= " ORDER BY p.id DESC"; // Default sort by newest
}

$products = $conn->query($sql);
$productCount = $products ? $products->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="multi-product.css">
    <title>Sudhanshu - <?= htmlspecialchars($category_name) ?><?= $subcategory_name ? ' / ' . htmlspecialchars($subcategory_name) : '' ?></title>
</head>
<body>
    <?php include 'header.php' ?> 

   <section class="announcement-bar">
  <p>FREE SHIPPING on all orders above &#8377 499</p>
</section>

<section class="product-page">
<div class="navigation">
  <span>Home</span>
  <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="none" viewBox="0 0 24 25" class="" stroke="none" style="height: 12px; width: 12px;"><path stroke="#737E93" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m8 4.5 8 8-8 8"></path></svg>
  <?php if ($category_name): ?>
    <span><?= htmlspecialchars($category_name) ?></span>
  <?php endif; ?>
  <?php if ($subcategory_name): ?>
    <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="none" viewBox="0 0 24 25" class="" stroke="none" style="height: 12px; width: 12px;"><path stroke="#737E93" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m8 4.5 8 8-8 8"></path></svg>
    <span><?= htmlspecialchars($subcategory_name) ?></span>
  <?php endif; ?>
</div>

<div class="container">

  <div class="filters-container">
    <div class="title">
      <h2>FILTER</h2>
    </div>
    <?php
      include 'filters.php';
      $filters = [
        "Category" => ["T-Shirt", "Top", "Hoodies", "Shirts"],
        "Sizes" => ["XS", "S", "M", "L", "XL"],
        "Brand" => ["Bewakoof", "H&M", "Zara"],
        "Color" => ["Black", "White", "Red", "Blue"]
      ];
      renderFilters($filters);
    ?>
  </div>

  <div class="product-section">
       <!-- New Section for Title & Sorting -->
  <div class="product-header">
    <div class="product-title">
      <h2>
        <?php 
          echo htmlspecialchars($category_name);
          if ($subcategory_name) {
              echo ' / ' . htmlspecialchars($subcategory_name);
          }
        ?>
      </h2>
      <span class="product-count"><?= $productCount ?> Products</span>
    </div>
    <form id="sortForm" method="GET" class="sort-section">
      <!-- Preserve category/subcategory in sort form -->
      <?php if ($category_id): ?>
        <input type="hidden" name="category_id" value="<?= $category_id ?>">
      <?php endif; ?>
      <?php if ($subcategory_id): ?>
        <input type="hidden" name="subcategory_id" value="<?= $subcategory_id ?>">
      <?php endif; ?>
      
      <label for="sort">
        <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" viewBox="0 0 24 24">
          <path fill="#666" d="M4 6h16v2H4zm0 5h12v2H4zm0 5h8v2H4z"/>
        </svg> Sort by:
      </label>
      <select name="sort" id="sort" onchange="document.getElementById('sortForm').submit()">
        <option value="popularity" <?= $currentSort === 'popularity' ? 'selected' : '' ?>>Popularity</option>
        <option value="price-low-to-high" <?= $currentSort === 'price-low-to-high' ? 'selected' : '' ?>>Price: Low to High</option>
        <option value="price-high-to-low" <?= $currentSort === 'price-high-to-low' ? 'selected' : '' ?>>Price: High to Low</option>
        <option value="name-a-z" <?= $currentSort === 'name-a-z' ? 'selected' : '' ?>>Name: A-Z</option>
        <option value="name-z-a" <?= $currentSort === 'name-z-a' ? 'selected' : '' ?>>Name: Z-A</option>
      </select>
    </form>
  </div>


    <!-- Products Grid -->
  <main class="product-grid" id="productGrid">
    <?php if ($products && $products->num_rows > 0): ?>
      <?php while ($product = $products->fetch_assoc()): ?>
        <div class="product-card">
    <img src="admin/uploads/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['title']) ?>">
    <h4><?= htmlspecialchars($product['title']) ?></h4>
    <p class="product-price">
        <?php if ($product['sale_price'] && $product['sale_price'] < $product['mrp']): ?>
            <span class="current-price">₹<?= number_format($product['sale_price'], 2) ?></span>
            <span class="original-price">₹<?= number_format($product['mrp'], 2) ?></span>
            <span class="discount-badge"><?= round(($product['mrp'] - $product['sale_price']) / $product['mrp'] * 100) ?>% OFF</span>
        <?php else: ?>
            <span class="current-price">₹<?= number_format($product['mrp'], 2) ?></span>
        <?php endif; ?>
    </p>
</div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="no-products" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
        No products found in this category.
      </div>
    <?php endif; ?>
  </main>
  </div>
</div>
</section>

<script src="filters.js"></script>
</body>
</html>