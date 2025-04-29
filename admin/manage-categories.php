<?php
include 'db.php';
include 'functions.php';

$categories = getCategories($conn);
$subcategories = getSubcategories($conn);
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Categories</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <main>
    <h2>Manage Categories</h2>
    
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

    <div class="flex-row">
      <div class="mini-form">
        <h3>Add New Category</h3>
        <form action="save-category.php" method="POST">
          <label>Category Name:</label>
          <input type="text" name="category_name" required>
          <button type="submit"><i class="fas fa-plus"></i> Add Category</button>
        </form>
      </div>

      <div class="mini-form">
        <h3>Add New Subcategory</h3>
        <form action="save-subcategory.php" method="POST">
          <label>Parent Category:</label>
          <select name="parent_category" required>
            <option value="">Select Category</option>
            <?php foreach ($categories as $category): ?>
              <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
            <?php endforeach; ?>
          </select>
          <label>Subcategory Name:</label>
          <input type="text" name="subcategory_name" required>
          <button type="submit"><i class="fas fa-plus"></i> Add Subcategory</button>
        </form>
      </div>
    </div>

    <div class="category-lists">
      <div class="category-section">
        <h3>Categories</h3>
        <?php if (!empty($categories)): ?>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Subcategories</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($categories as $category): ?>
                <tr>
                  <td><?= $category['id'] ?></td>
                  <td><?= htmlspecialchars($category['name']) ?></td>
                  <td>
                    <?php 
                      $cat_subcategories = array_filter($subcategories, function($sub) use ($category) {
                          return $sub['category_id'] == $category['id'];
                      });
                      echo count($cat_subcategories);
                    ?>
                  </td>
                  <td class="actions">
                    <a href="edit-category.php?id=<?= $category['id'] ?>" class="btn small"><i class="fas fa-edit"></i> Edit</a>
                    <a href="delete-category.php?id=<?= $category['id'] ?>" class="btn small danger" onclick="return confirm('Are you sure? This will also delete all subcategories under this category.')">
                      <i class="fas fa-trash"></i> Delete
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No categories found.</p>
        <?php endif; ?>
      </div>

      <div class="category-section">
        <h3>Subcategories</h3>
        <?php if (!empty($subcategories)): ?>
          <table>
            <thead>
              <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Parent Category</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($subcategories as $subcategory): ?>
                <tr>
                  <td><?= $subcategory['id'] ?></td>
                  <td><?= htmlspecialchars($subcategory['name']) ?></td>
                  <td><?= htmlspecialchars($subcategory['category_name']) ?></td>
                  <td class="actions">
                    <a href="edit-subcategory.php?id=<?= $subcategory['id'] ?>" class="btn small"><i class="fas fa-edit"></i> Edit</a>
                    <a href="delete-subcategory.php?id=<?= $subcategory['id'] ?>" class="btn small danger" onclick="return confirm('Are you sure? This will affect products in this subcategory.')">
                      <i class="fas fa-trash"></i> Delete
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p>No subcategories found.</p>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <script src="script.js"></script>
</body>
</html>