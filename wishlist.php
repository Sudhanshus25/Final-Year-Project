<?php
session_start();
include 'admin/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle wishlist actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_item'])) {
    $product_id = (int)$_POST['product_id'];
    $stmt = $conn->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
}

// Get wishlist items
$wishlist_items = $conn->query("
    SELECT p.*, w.id as wishlist_id
    FROM wishlist w
    JOIN products p ON w.product_id = p.id
    WHERE w.user_id = $user_id
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Wishlist</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="wishlist-container">
        <h1>My Wishlist</h1>
        
        <?php if ($wishlist_items->num_rows > 0): ?>
            <div class="wishlist-grid">
                <?php while ($item = $wishlist_items->fetch_assoc()): ?>
                <div class="wishlist-item">
                    <div class="item-image">
                        <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                        <form method="POST" class="remove-form">
                            <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                            <button type="submit" name="remove_item" class="remove-btn">×</button>
                        </form>
                    </div>
                    <div class="item-details">
                        <h3><?= htmlspecialchars($item['title']) ?></h3>
                        <div class="item-price">
                            ₹<?= number_format($item['sale_price'] ?? $item['mrp'], 2) ?>
                            <?php if ($item['sale_price'] && $item['sale_price'] < $item['mrp']): ?>
                                <span class="original-price">₹<?= number_format($item['mrp'], 2) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="item-actions">
                            <a href="product.php?id=<?= $item['id'] ?>" class="view-btn">View Product</a>
                            <form action="cart-actions.php" method="POST" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                <input type="hidden" name="action" value="add">
                                <button type="submit" class="add-to-cart-btn">Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-wishlist">
                <p>Your wishlist is empty</p>
                <a href="products.php" class="continue-shopping">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>