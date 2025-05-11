<?php
session_start();
include 'admin/db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's orders
$orders = $conn->query("
    SELECT o.id, o.order_number, o.total_amount, o.status, o.payment_status, o.created_at
    FROM orders o
    WHERE o.user_id = $user_id
    ORDER BY o.created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="orders-container">
        <h1>My Orders</h1>
        
        <?php if ($orders->num_rows > 0): ?>
            <table class="orders-table">
                <thead>
                    <tr>
                        <th>Order #</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?= $order['order_number'] ?></td>
                        <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                        <td>₹<?= number_format($order['total_amount'], 2) ?></td>
                        <td>
                            <span class="status-badge <?= $order['status'] ?>">
                                <?= ucfirst($order['status']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="payment-status <?= $order['payment_status'] ?>">
                                <?= ucfirst($order['payment_status']) ?>
                            </span>
                        </td>
                        <td>
                            <a href="order-details.php?id=<?= $order['id'] ?>" class="view-order">View Details</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="no-orders">
                <p>You haven't placed any orders yet.</p>
                <a href="products.php" class="shop-now">Start Shopping</a>
            </div>
        <?php endif; ?>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>