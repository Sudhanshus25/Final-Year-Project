<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Verify order belongs to user
$order = $conn->query("
    SELECT o.* 
    FROM orders o
    WHERE o.id = $order_id AND o.user_id = $user_id
")->fetch_assoc();

if (!$order) {
    header("Location: orders.php");
    exit();
}

// Handle payment completion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_id = $_POST['payment_id'];
    $status = $_POST['status'];
    
    // Update order status
    $stmt = $conn->prepare("
        UPDATE orders 
        SET payment_status = ?, payment_id = ?
        WHERE id = ?
    ");
    $stmt->bind_param("ssi", $status, $payment_id, $order_id);
    $stmt->execute();
    
    if ($status === 'paid') {
        header("Location: order-success.php?order_id=$order_id");
    } else {
        header("Location: order-failed.php?order_id=$order_id");
    }
    exit();
}

// For Razorpay integration (example)
$razorpay_key = 'YOUR_RAZORPAY_KEY';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete Payment</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>

    <main class="payment-container">
        <h1>Complete Your Payment</h1>
        <div class="payment-details">
            <div class="order-summary">
                <h3>Order Summary</h3>
                <p>Order #: <?= $order['order_number'] ?></p>
                <p>Amount: ₹<?= number_format($order['total_amount'], 2) ?></p>
            </div>
            
            <div class="payment-options">
                <h3>Select Payment Method</h3>
                
                <div class="payment-method active" id="razorpay-option">
                    <h4>Razorpay</h4>
                    <button id="rzp-button" class="pay-now-btn">Pay Now with Razorpay</button>
                </div>
                
                <div class="payment-method" id="paypal-option">
                    <h4>PayPal</h4>
                    <div id="paypal-button-container"></div>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Razorpay Integration
        var options = {
            "key": "<?= $razorpay_key ?>",
            "amount": "<?= $order['total_amount'] * 100 ?>", 
            "currency": "INR",
            "name": "Your Store Name",
            "description": "Payment for Order #<?= $order['order_number'] ?>",
            "image": "/your_logo.png",
            "order_id": "", // This will be generated in your backend
            "handler": function (response){
                // Submit form with payment details
                var form = document.createElement('form');
                form.method = 'POST';
                form.action = 'payment.php?order_id=<?= $order_id ?>';
                
                var input1 = document.createElement('input');
                input1.type = 'hidden';
                input1.name = 'payment_id';
                input1.value = response.razorpay_payment_id;
                form.appendChild(input1);
                
                var input2 = document.createElement('input');
                input2.type = 'hidden';
                input2.name = 'status';
                input2.value = 'paid';
                form.appendChild(input2);
                
                document.body.appendChild(form);
                form.submit();
            },
            "prefill": {
                "name": "Customer Name",
                "email": "customer@example.com",
                "contact": "9999999999"
            },
            "notes": {
                "address": "Customer Address"
            },
            "theme": {
                "color": "#F37254"
            }
        };
        
        var rzp1 = new Razorpay(options);
        document.getElementById('rzp-button').onclick = function(e){
            rzp1.open();
            e.preventDefault();
        };
        
        // PayPal Integration (would need additional setup)
        // paypal.Buttons({...}).render('#paypal-button-container');
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>