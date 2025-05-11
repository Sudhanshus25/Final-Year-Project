<?php
session_start();
include 'db.php';

// Record failed payment attempt
if (isset($_POST['txnid'])) {
    $txnid = $_POST['txnid'];
    $status = $_POST['status'];
    $amount = $_POST['amount'];
    
    $order_id = $conn->query("SELECT order_id FROM orders WHERE payment_id = '$txnid'")->fetch_assoc()['order_id'];
    
    $conn->query("UPDATE orders SET payment_status = 'failed' WHERE order_id = $order_id");
    
    $conn->query("INSERT INTO payment_transactions (
        order_id, transaction_id, amount, status, gateway, raw_response
    ) VALUES (
        $order_id, '$txnid', $amount, '$status', 'payu', '" . json_encode($_POST) . "'
    )");
    
    header("Location: order-failure.php?order_id=$order_id&reason=$status");
    exit();
}

// Handle direct access with error reason
$reason = $_GET['reason'] ?? 'unknown_error';
$order_id = $_GET['order_id'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Payment Failed</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="payment-result-container">
        <div class="payment-failed">
            <h1>Payment Failed</h1>
            <div class="error-message">
                <?php
                $error_messages = [
                    'payment_failed' => 'The payment could not be processed.',
                    'hash_verification_failed' => 'Security verification failed.',
                    'user_cancelled' => 'You cancelled the payment.',
                    'unknown_error' => 'An unknown error occurred.'
                ];
                echo $error_messages[$reason] ?? $error_messages['unknown_error'];
                ?>
            </div>
            
            <?php if ($order_id): ?>
                <p>Order Reference: #<?= htmlspecialchars($order_id) ?></p>
                <div class="action-buttons">
                    <a href="checkout.php?order_id=<?= $order_id ?>" class="btn">Try Again</a>
                    <a href="contact.php" class="btn secondary">Contact Support</a>
                </div>
            <?php else: ?>
                <div class="action-buttons">
                    <a href="cart.php" class="btn">Return to Cart</a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html>