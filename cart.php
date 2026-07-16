<?php
require_once __DIR__ . '/config/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_qty']) && isset($_POST['qty']) && is_array($_POST['qty'])) {
        foreach ($_POST['qty'] as $id => $qty) {
            $qty = (int)$qty;
            if ($qty < 1) $qty = 1;
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['qty'] = $qty;
            }
        }
    }
    if (isset($_POST['remove_id'])) {
        unset($_SESSION['cart'][$_POST['remove_id']]);
    }
    header('Location: cart.php');
    exit;
}

$pageTitle = 'My Cart';
require_once __DIR__ . '/includes/header.php';
$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : array();

$cartTotal = 0;
foreach ($cart as $item) {
    $cartTotal += $item['qty'] * $item['price'];
}
?>
<section class="section">
  <div class="container">
    <h1>My Cart</h1>
    <?php
    $msg = null;
    if (!empty($_SESSION['flash']['cart'])) {
        $msg = $_SESSION['flash']['cart'];
        unset($_SESSION['flash']['cart']);
    }
    if ($msg) echo '<div class="alert alert-success">' . htmlspecialchars($msg ?? '', ENT_QUOTES, 'UTF-8') . '</div>';
    ?>

    <?php if (empty($cart)): ?>
      <div class="center" style="padding:60px 0;">
        <p style="font-size:3rem;margin-bottom:10px;">🛒</p>
        <p style="color:var(--muted);">Your cart is empty.</p>
        <a href="products.php" class="btn btn-primary" style="margin-top:14px;">Browse Products</a>
      </div>
    <?php else: ?>
    <form method="post">
      <div style="overflow-x:auto;">
      <table class="cart-table">
        <thead><tr><th>Product</th><th>Price</th><th>Quantity</th><th>Subtotal</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($cart as $id => $item): ?>
          <tr>
            <td data-label="Product">
              <div class="cart-prod">
                <img src="https://images.unsplash.com/photo-1550583724-b2692b85b150?q=80&w=200&auto=format&fit=crop" alt="">
                <span><?php echo htmlspecialchars($item['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?><br><span style="font-size:.78rem;color:var(--muted);"><?php echo htmlspecialchars($item['unit'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></span>
              </div>
            </td>
            <td data-label="Price"><?php echo CURRENCY . number_format($item['price'], 2); ?></td>
            <td data-label="Quantity">
              <div class="qty-box">
                <button type="button" class="qty-minus" style="border:none;background:none;font-size:1rem;cursor:pointer;">−</button>
                <input type="number" name="qty[<?php echo $id; ?>]" value="<?php echo $item['qty']; ?>" min="1" style="width:54px;">
                <button type="button" class="qty-plus" style="border:none;background:none;font-size:1rem;cursor:pointer;">+</button>
              </div>
            </td>
            <td data-label="Subtotal"><?php echo CURRENCY . number_format($item['price'] * $item['qty'], 2); ?></td>
            <td data-label=""><button type="submit" name="remove_id" value="<?php echo $id; ?>" style="background:none;border:none;cursor:pointer;color:#A3402B;font-weight:700;">Remove</button></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      </div>
      <div style="display:flex;justify-content:space-between;margin-top:20px;flex-wrap:wrap;gap:16px;">
        <button type="submit" name="update_qty" value="1" class="btn btn-outline">Update Cart</button>
        <div style="max-width:340px;width:100%;" class="cart-summary">
          <div class="summary-row"><span>Subtotal</span><span><?php echo CURRENCY . number_format($cartTotal, 2); ?></span></div>
          <div class="summary-row"><span>Delivery</span><span>Free</span></div>
          <div class="summary-row total"><span>Total</span><span><?php echo CURRENCY . number_format($cartTotal, 2); ?></span></div>
          <a href="checkout.php" class="btn btn-primary btn-block" style="margin-top:14px;">Proceed to Checkout</a>
        </div>
      </div>
    </form>
    <?php endif; ?>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
