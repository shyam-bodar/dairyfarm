<?php
require_once __DIR__ . '/config/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . 'login.php');
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$cartTotal = 0;
foreach ($cart as $item) {
    $cartTotal += $item['qty'] * $item['price'];
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name    = mysqli_real_escape_string($conn, trim($_POST['shipping_name'] ?? ''));
    $phone   = mysqli_real_escape_string($conn, trim($_POST['shipping_phone'] ?? ''));
    $address = mysqli_real_escape_string($conn, trim($_POST['shipping_address'] ?? ''));
    $payment = mysqli_real_escape_string($conn, $_POST['payment_method'] ?? '');

    if ($name == '' || $phone == '' || $address == '') {
        $error = 'Please fill in all shipping details.';
    } else {
        $total  = $cartTotal;
        $userId = $_SESSION['user_id'];

        mysqli_query($conn, "INSERT INTO orders (user_id, total_amount, shipping_name, shipping_phone, shipping_address, payment_method, status) VALUES ('$userId','$total','$name','$phone','$address','$payment','pending')");
        $orderId = mysqli_insert_id($conn);

        foreach ($cart as $pid => $item) {
            $pname = mysqli_real_escape_string($conn, $item['name']);
            $price = $item['price'];
            $qty   = (int)$item['qty'];
            mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, product_name, price, quantity) VALUES ('$orderId','$pid','$pname','$price','$qty')");
            mysqli_query($conn, "UPDATE products SET stock = GREATEST(0, stock - $qty) WHERE id = $pid");
        }

        unset($_SESSION['cart']);
        $_SESSION['flash']['order'] = "Order #$orderId placed successfully! We'll deliver it soon.";
        header('Location: profile.php');
        exit;
    }
}

$pageTitle = 'Checkout';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container">
    <h1>Checkout</h1>
    <?php if ($error != '') echo '<div class="alert alert-error">' . htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8') . '</div>'; ?>
    <div class="grid grid-2" style="align-items:flex-start;">
      <form method="post" class="form-card" style="margin:0;max-width:none;">
        <h3>Shipping Details</h3>
        <div class="form-group"><label>Full Name</label><input type="text" name="shipping_name" required value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
        <div class="form-group"><label>Phone Number</label><input type="tel" name="shipping_phone" required></div>
        <div class="form-group"><label>Delivery Address</label><textarea name="shipping_address" required placeholder="House no, street, area, city, pincode"></textarea></div>
        <div class="form-group">
          <label>Payment Method</label>
          <select name="payment_method">
            <option value="Cash on Delivery">Cash on Delivery</option>
            <option value="UPI">UPI</option>
            <option value="Card on Delivery">Card on Delivery</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Place Order</button>
      </form>
      <div class="cart-summary">
        <h3>Order Summary</h3>
        <?php foreach ($cart as $item): ?>
          <div class="summary-row"><span><?php echo htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> × <?php echo $item['qty']; ?></span><span><?php echo CURRENCY . number_format($item['price'] * $item['qty'], 2); ?></span></div>
        <?php endforeach; ?>
        <div class="summary-row"><span>Delivery</span><span>Free</span></div>
        <div class="summary-row total"><span>Total</span><span><?php echo CURRENCY . number_format($cartTotal, 2); ?></span></div>
      </div>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
