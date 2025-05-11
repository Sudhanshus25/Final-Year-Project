<?php
session_start();
include 'admin/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get cart items
$cart_items = $conn->query("
    SELECT c.id as cart_id, p.*, c.quantity, 
           (p.sale_price * c.quantity) as item_total
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = $user_id
");

$total = 0;
while ($item = $cart_items->fetch_assoc()) {
    $total += $item['item_total'];
}

// Handle checkout submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Generate unique order number
    $order_number = 'ORD-' . strtoupper(uniqid());
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Create order
        $stmt = $conn->prepare("
            INSERT INTO orders (user_id, order_number, total_amount, payment_status, shipping_address, billing_address)
            VALUES (?, ?, ?, 'pending', ?, ?)
        ");
        $shipping_address = json_encode([
            'name' => $_POST['shipping_name'],
            'address' => $_POST['shipping_address'],
            'city' => $_POST['shipping_city'],
            'state' => $_POST['shipping_state'],
            'zip' => $_POST['shipping_zip'],
            'phone' => $_POST['shipping_phone']
        ]);
        $billing_address = $_POST['same_as_shipping'] ? $shipping_address : json_encode([
            'name' => $_POST['billing_name'],
            'address' => $_POST['billing_address'],
            'city' => $_POST['billing_city'],
            'state' => $_POST['billing_state'],
            'zip' => $_POST['billing_zip']
        ]);
        
        $stmt->bind_param("isdss", $user_id, $order_number, $total, $shipping_address, $billing_address);
        $stmt->execute();
        $order_id = $conn->insert_id;
        
        // Add order items
        $cart_items = $conn->query("
            SELECT p.id as product_id, p.sale_price as price, c.quantity
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = $user_id
        ");
        
        while ($item = $cart_items->fetch_assoc()) {
            $stmt = $conn->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, price)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt->execute();
        }
        
        // Clear cart
        $conn->query("DELETE FROM cart WHERE user_id = $user_id");
        
        // Commit transaction
        $conn->commit();
        
        // Redirect to payment gateway
        header("Location: payment.php?order_id=$order_id");
        exit();
        
    } catch (Exception $e) {
        $conn->rollback();
        $error = "Order processing failed: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="checkout-container">
        <h1>Checkout</h1>
        
        <?php if (isset($error)): ?>
            <div class="error-message"><?= $error ?></div>
        <?php endif; ?>
        
        <form action="checkout.php" method="POST">
            <div class="checkout-grid">
                <div class="shipping-address">
                    <h2>Shipping Address</h2>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="shipping_name" required>
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="shipping_address" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="shipping_city" required>
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="shipping_state" required>
                    </div>
                    <div class="form-group">
                        <label>ZIP Code</label>
                        <input type="text" name="shipping_zip" required>
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="tel" name="shipping_phone" required>
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="same_as_shipping" id="same_as_shipping" checked>
                            Billing address same as shipping
                        </label>
                    </div>
                </div>
                
                <div class="billing-address" id="billing_address">
                    <h2>Billing Address</h2>
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="billing_name">
                    </div>
                    <div class="form-group">
                        <label>Address</label>
                        <textarea name="billing_address"></textarea>
                    </div>
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="billing_city">
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="billing_state">
                    </div>
                    <div class="form-group">
                        <label>ZIP Code</label>
                        <input type="text" name="billing_zip">
                    </div>
                </div>
                
                <div class="order-summary">
                    <h2>Order Summary</h2>
                    <div class="summary-items">
                        <?php 
                        $cart_items = $conn->query("
                            SELECT p.title, p.sale_price, c.quantity
                            FROM cart c
                            JOIN products p ON c.product_id = p.id
                            WHERE c.user_id = $user_id
                        ");
                        while ($item = $cart_items->fetch_assoc()): ?>
                            <div class="summary-item">
                                <span><?= htmlspecialchars($item['title']) ?> × <?= $item['quantity'] ?></span>
                                <span>₹<?= number_format($item['sale_price'] * $item['quantity'], 2) ?></span>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <div class="summary-total">
                        <span>Total</span>
                        <span>₹<?= number_format($total, 2) ?></span>
                    </div>
                    <div class="payment-methods">
                        <h3>Payment Method</h3>
                        <div class="payment-option">
                            <input type="radio" name="payment_method" value="credit_card" id="credit_card" checked>
                            <label for="credit_card">Credit Card</label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" name="payment_method" value="paypal" id="paypal">
                            <label for="paypal">PayPal</label>
                        </div>
                        <div class="payment-option">
                            <input type="radio" name="payment_method" value="razorpay" id="razorpay">
                            <label for="razorpay">Razorpay</label>
                        </div>
                    </div>
                    <button type="submit" class="place-order-btn">Place Order</button>
                </div>
            </div>
        </form>
    </main>

    <script>
        // Toggle billing address visibility
        document.getElementById('same_as_shipping').addEventListener('change', function() {
            document.getElementById('billing_address').style.display = this.checked ? 'none' : 'block';
        });
        // Initialize
        document.getElementById('billing_address').style.display = 'none';
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>