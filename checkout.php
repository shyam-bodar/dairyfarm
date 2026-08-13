<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/mailer.php';

// Ensure sessions are started
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

// Include common functions if available
@include_once __DIR__ . '/includes/functions.php';

// Provide a fallback requireLogin if it's not defined in includes/functions.php
if (!function_exists('requireLogin')) {
  function requireLogin()
  {
    if (empty($_SESSION['user_id'])) {
      header('Location: login.php');
      exit;
    }
  }
}

// Provide a fallback cartTotal if it's not defined in includes/functions.php
if (!function_exists('cartTotal')) {
  function cartTotal()
  {
    $cart = $_SESSION['cart'] ?? [];
    $total = 0;
    foreach ($cart as $item) {
      $price = isset($item['price']) ? (float) $item['price'] : 0;
      $qty = isset($item['qty']) ? (int) $item['qty'] : 0;
      $total += $price * $qty;
    }
    return $total;
  }
}

// Provide a fallback db_exec if it's not defined in includes/functions.php
if (!function_exists('db_exec')) {
  /**
   * Simple wrapper to prepare, bind and execute mysqli statements.
   * - $types is a string like "is" or "idssss" matching $params
   * - Returns insert id for INSERT, true for other successful statements,
   *   or fetched rows for SELECT queries.
   */
  function db_exec($conn, $sql, $types = null, $params = [])
  {
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
      throw new Exception('DB prepare failed: ' . mysqli_error($conn));
    }
    if ($types && $params) {
      // mysqli_stmt_bind_param requires references
      $bind_names[] = $types;
      for ($i = 0; $i < count($params); $i++) {
        $bind_name = 'bind' . $i;
        $$bind_name = $params[$i];
        $bind_names[] = &$$bind_name;
      }
      call_user_func_array(array($stmt, 'bind_param'), $bind_names);
    }
    if (!mysqli_stmt_execute($stmt)) {
      $err = mysqli_stmt_error($stmt);
      mysqli_stmt_close($stmt);
      throw new Exception('DB execute failed: ' . $err);
    }
    $upper = strtoupper(ltrim($sql));
    if (strpos($upper, 'INSERT') === 0) {
      $id = mysqli_insert_id($conn);
      mysqli_stmt_close($stmt);
      return $id;
    }
    if (strpos($upper, 'SELECT') === 0) {
      $res = mysqli_stmt_get_result($stmt);
      $rows = $res ? mysqli_fetch_all($res, MYSQLI_ASSOC) : [];
      mysqli_stmt_close($stmt);
      return $rows;
    }
    mysqli_stmt_close($stmt);
    return true;
  }
}

// Provide a fallback flash helper if it's not defined in includes/functions.php
if (!function_exists('flash')) {
  function flash($key, $message)
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
    $_SESSION['flash'][$key] = $message;
  }
}

requireLogin();

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['shipping_name'] ?? '');
    $phone = trim($_POST['shipping_phone'] ?? '');
    $address = trim($_POST['shipping_address'] ?? '');
    $payment = $_POST['payment_method'] ?? 'Cash on Delivery';

    if ($name === '' || $phone === '' || $address === '') {
        $error = 'Please fill in all shipping details.';
    } else {
        $total = cartTotal();
       mysqli_begin_transaction($conn);
try {
    // Insert order
    $orderId = db_exec(
        $conn,
        "INSERT INTO orders (user_id, total_amount, payment_method, status) VALUES (?,?,?, 'pending')",
        "iss",
        [$_SESSION['user_id'], $total, $payment]
    );

    // Insert shipping details
    db_exec(
        $conn,
        "INSERT INTO shipping_details (order_id, name, phone, address) VALUES (?,?,?,?)",
        "isss",
        [$orderId, $name, $phone, $address]
    );

    // Insert order items
    foreach ($cart as $pid => $item) {
        db_exec(
            $conn,
            "INSERT INTO order_items (order_id, product_id, quantity) VALUES (?,?,?)",
            "iii",
            [$orderId, $pid, $item['qty']]
        );
        db_exec(
            $conn,
            "UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?",
            "ii",
            [$item['qty'], $pid]
        );
    }

    mysqli_commit($conn);

    // ---- Order Confirmation Email moklo ----
    $userRow = db_exec($conn, "SELECT name, email FROM users WHERE id = ?", "i", [$_SESSION['user_id']]);
    if (!empty($userRow)) {
        $userEmail = $userRow[0]['email'];
        $userName  = $userRow[0]['name'];

        $emailItems = [];
        foreach ($cart as $item) {
            $emailItems[] = [
                'name'  => $item['name'],
                'qty'   => $item['qty'],
                'price' => $item['price'],
            ];
        }

        sendOrderConfirmationEmail(
            $userEmail,
            $userName,
            $orderId,
            $total,
            $payment,
            $emailItems,
            ['name' => $name, 'phone' => $phone, 'address' => $address]
        );
    }

    unset($_SESSION['cart']);
    flash('order', "Order #$orderId placed successfully! A confirmation email has been sent to your inbox.");
    header('Location: profile.php');
    exit;
} catch (Exception $e) {
    mysqli_rollback($conn);
    $error = 'Something went wrong: ' . $e->getMessage();
}


    }
}

$pageTitle = 'Checkout';
require_once __DIR__ . '/includes/header.php';


 

?>

<section class="section">
  <div class="container">
    <h1>Checkout</h1>
    <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
    <div class="grid grid-2" style="align-items:flex-start;">
      <form method="post" class="form-card" style="margin:0;max-width:none;">
        <h3>Shipping Details</h3>
        <div class="form-group">
          <label>Full Name</label>
          <input type="text" name="shipping_name" required value="<?php echo $_SESSION['user_name'] ?? ''; ?>">
        </div>
        <div class="form-group">
          <label>Phone Number</label>
          <input type="tel" name="shipping_phone" required>
        </div>
        <div class="form-group">
          <label>Delivery Address</label>
          <textarea name="shipping_address" required placeholder="House no, street, area, city, pincode" ></textarea>
        </div>
        <div class="form-group">
          <label>Payment Method</label>
          <select name="payment_method">
            <option value="Cash on Delivery">Cash on Delivery</option>
            
           
          </select>
        </div>
        <button type="submit" class="btn btn-primary btn-block">Place Order</button>
      </form>

      <div class="cart-summary">
        <h3>Order Summary</h3>
        <?php foreach ($cart as $item): ?>
          <div class="summary-row"><span><?php echo $item['name']; ?> × <?php echo $item['qty']; ?></span><span><?php echo CURRENCY . number_format($item['price']*$item['qty'],2); ?></span></div>
        <?php endforeach; ?>
        <div class="summary-row"><span>Delivery</span><span>Free</span></div>
        <div class="summary-row total"><span>Total</span><span><?php echo CURRENCY . number_format(cartTotal(),2); ?></span></div>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>