<?php
session_start();
include 'db.php';

// PayU Test Credentials
$MERCHANT_KEY = "gtKFFx";
$SALT = "eCwWELxi";

// Verify payment response
$status = $_POST["status"];
$txnid = $_POST["txnid"];
$amount = $_POST["amount"];
$productinfo = $_POST["productinfo"];
$firstname = $_POST["firstname"];
$email = $_POST["email"];
$posted_hash = $_POST["hash"];

// Verify hash
$keyString = "$MERCHANT_KEY|$txnid|$amount|$productinfo|$firstname|$email|||||||||||$SALT";
$calculated_hash = strtolower(hash('sha512', $keyString));

if ($calculated_hash == $posted_hash) {
    // Update order status in database
    $order_id = $conn->query("SELECT order_id FROM orders WHERE payment_id = '$txnid'")->fetch_assoc()['order_id'];
    
    if ($status == 'success') {
        $conn->query("UPDATE orders SET status = 'processing', payment_status = 'paid' WHERE order_id = $order_id");
        
        // Record payment transaction
        $conn->query("INSERT INTO payment_transactions (
            order_id, transaction_id, amount, status, gateway, raw_response
        ) VALUES (
            $order_id, '$txnid', $amount, 'success', 'payu', '" . json_encode($_POST) . "'
        )");
        
        header("Location: order-success.php?order_id=$order_id");
    } else {
        $conn->query("UPDATE orders SET payment_status = 'failed' WHERE order_id = $order_id");
        header("Location: order-failure.php?order_id=$order_id&reason=payment_failed");
    }
} else {
    // Hash verification failed
    header("Location: order-failure.php?reason=hash_verification_failed");
}
exit();
?>