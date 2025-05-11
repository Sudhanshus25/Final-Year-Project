<?php
session_start();
include 'admin/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] as $cart_id => $quantity) {
            $quantity = (int)$quantity;
            if ($quantity > 0) {
                $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
                $stmt->bind_param("iii", $quantity, $cart_id, $user_id);
                $stmt->execute();
            } else {
                $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
                $stmt->bind_param("ii", $cart_id, $user_id);
                $stmt->execute();
            }
        }
    } elseif (isset($_POST['remove_item'])) {
        $cart_id = (int)$_POST['cart_id'];
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cart_id, $user_id);
        $stmt->execute();
    }
}

// Get cart items
$cart_items = $conn->query("
    SELECT c.id as cart_id, p.*, c.quantity, 
           (p.sale_price * c.quantity) as item_total,
           p.sale_price as item_price
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");

$total = 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Your Shopping Cart</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="cart-container">
        <h1>Your Shopping Cart</h1>
        
        <?php if ($cart_items->num_rows > 0): ?>
        <form action="cart.php" method="POST">
            <div class="cart-items">
                <div class="cart-header">
                    <div class="header-product">Product</div>
                    <div class="header-price">Price</div>
                    <div class="header-quantity">Quantity</div>
                    <div class="header-total">Total</div>
                    <div class="header-actions">Actions</div>
                </div>
                
                <?php while ($item = $cart_items->fetch_assoc()): 
                    $total += $item['item_total'];
                ?>
                <div class="cart-item">
                    <div class="item-product">
                        <img src="uploads/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['title']) ?>">
                        <div class="product-details">
                            <h3><?= htmlspecialchars($item['title']) ?></h3>
                            <p><?= htmlspecialchars($item['category_name']) ?></p>
                        </div>
                    </div>
                    <div class="item-price">
                        ₹<?= number_format($item['item_price'], 2) ?>
                    </div>
                    <div class="item-quantity">
                        <input type="number" name="quantity[<?= $item['cart_id'] ?>]" 
                               value="<?= $item['quantity'] ?>" min="1">
                    </div>
                    <div class="item-total">
                        ₹<?= number_format($item['item_total'], 2) ?>
                    </div>
                    <div class="item-actions">
                        <button type="submit" name="remove_item" class="remove-btn">
                            <input type="hidden" name="cart_id" value="<?= $item['cart_id'] ?>">
                            Remove
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            
            <div class="cart-summary">
                <div class="summary-total">
                    <span>Subtotal:</span>
                    <span>₹<?= number_format($total, 2) ?></span>
                </div>
                <div class="summary-actions">
                    <button type="submit" name="update_cart" class="update-btn">Update Cart</button>
                    <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                </div>
            </div>
        </form>
        <?php else: ?>
            <div class="empty-cart">
                <p>Your cart is empty</p>
                <a href="products.php" class="continue-shopping">Continue Shopping</a>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>