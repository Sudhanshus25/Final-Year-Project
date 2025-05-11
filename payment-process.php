<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_id = (int)$_GET['order_id'];

// Verify order belongs to user
$order = $conn->query("
    SELECT o.*, u.email, CONCAT(c.first_name, ' ', c.last_name) AS customer_name
    FROM orders o
    JOIN customers c ON o.customer_id = c.customer_id
    JOIN users u ON c.user_id = u.user_id
    WHERE o.order_id = $order_id AND c.user_id = $user_id
")->fetch_assoc();

if (!$order) {
    header("Location: orders.php");
    exit();
}

// PayU Test Credentials
$MERCHANT_KEY = "gtKFFx";
$SALT = "eCwWELxi";
$PAYU_BASE_URL = "https://test.payu.in";

// Payment parameters
$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
$amount = $order['total_amount'];
$productinfo = "Order #" . $order['order_number'];
$firstname = $order['customer_name'];
$email = $order['email'];
$phone = "9876543210"; // Should be fetched from user profile

// Generate hash
$hash_string = "$MERCHANT_KEY|$txnid|$amount|$productinfo|$firstname|$email|||||||||||$SALT";
$hash = strtolower(hash('sha512', $hash_string));

// Save transaction ID in database
$conn->query("UPDATE orders SET payment_id = '$txnid' WHERE order_id = $order_id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Redirecting to PayU...</title>
</head>
<body>
    <form action="<?= $PAYU_BASE_URL ?>/_payment" method="POST" id="payuForm">
        <input type="hidden" name="key" value="<?= $MERCHANT_KEY ?>" />
        <input type="hidden" name="txnid" value="<?= $txnid ?>" />
        <input type="hidden" name="amount" value="<?= $amount ?>" />
        <input type="hidden" name="productinfo" value="<?= $productinfo ?>" />
        <input type="hidden" name="firstname" value="<?= $firstname ?>" />
        <input type="hidden" name="email" value="<?= $email ?>" />
        <input type="hidden" name="phone" value="<?= $phone ?>" />
        <input type="hidden" name="surl" value="http://yourdomain.com/payment-success.php" />
        <input type="hidden" name="furl" value="http://yourdomain.com/payment-failure.php" />
        <input type="hidden" name="hash" value="<?= $hash ?>" />
        <input type="hidden" name="service_provider" value="payu_paisa" />
    </form>

    <script>
        document.getElementById('payuForm').submit();
    </script>
</body>
</html>