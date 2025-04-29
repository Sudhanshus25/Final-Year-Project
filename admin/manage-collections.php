<?php
include 'db.php';
include 'functions.php';

$collections = getCollections($conn);
$success = isset($_GET['success']) ? $_GET['success'] : null;
$error = isset($_GET['error']) ? $_GET['error'] : null;
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Collections</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <?php include 'header.php'; ?>

  <main>
    <h2>Manage Collections</h2>
    
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
        <h3>Add New Collection</h3>
        <form action="save-collection.php" method="POST">
          <label>Collection Name:</label>
          <input type="text" name="collection_name" required>
          <label>Description:</label>
          <textarea name="collection_description"></textarea>
          <button type="submit"><i class="fas fa-plus"></i> Add Collection</button>
        </form>
      </div>
    </div>

    <div class="collections-list">
      <h3>Existing Collections</h3>
      <?php if (!empty($collections)): ?>
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Name</th>
              <th>Description</th>
              <th>Products</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($collections as $collection): ?>
              <?php
                // Count products in this collection
                $product_count = $conn->query("SELECT COUNT(*) as count FROM collection_products WHERE collection_id = {$collection['id']}")->fetch_assoc()['count'];
              ?>
              <tr>
                <td><?= $collection['id'] ?></td>
                <td><?= htmlspecialchars($collection['name']) ?></td>
                <td><?= htmlspecialchars($collection['description']) ?></td>
                <td><?= $product_count ?></td>
                <td class="actions">
                  <a href="edit-collection.php?id=<?= $collection['id'] ?>" class="btn small"><i class="fas fa-edit"></i> Edit</a>
                  <a href="view-collection.php?id=<?= $collection['id'] ?>" class="btn small"><i class="fas fa-eye"></i> View</a>
                  <a href="delete-collection.php?id=<?= $collection['id'] ?>" class="btn small danger" onclick="return confirm('Are you sure?')">
                    <i class="fas fa-trash"></i> Delete
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p>No collections found.</p>
      <?php endif; ?>
    </div>
  </main>

  <script src="script.js"></script>
</body>
</html>