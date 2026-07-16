<?php
require_once __DIR__ . '/config/db.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . SITE_URL . 'login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$result = mysqli_query($conn, "SELECT * FROM users WHERE id=$userId");
$user   = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $nameRaw = trim($_POST['name'] ?? '');
    $name    = mysqli_real_escape_string($conn, $nameRaw);
    $phone   = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $address = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
    mysqli_query($conn, "UPDATE users SET name='$name', phone='$phone', address='$address' WHERE id=$userId");
    $_SESSION['user_name'] = $nameRaw;
    $_SESSION['flash']['profile'] = 'Profile updated successfully.';
    header('Location: profile.php');
    exit;
}

$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id=$userId ORDER BY created_at DESC");

$pageTitle = 'My Profile';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:14px;margin-bottom:30px;">
      <div style="display:flex;align-items:center;gap:14px;">
        <span class="avatar" style="width:54px;height:54px;font-size:1.3rem;"><?php echo strtoupper(substr($user['name'] ?? 'U', 0, 1)); ?></span>
        <div>
          <h2 style="margin:0;"><?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h2>
          <p style="margin:0;color:var(--muted);font-size:.9rem;"><?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
      </div>
      <a href="logout.php" class="btn btn-outline">Logout</a>
    </div>

    <?php
    $m = null;
    if (!empty($_SESSION['flash']['order'])) {
        $m = $_SESSION['flash']['order'];
        unset($_SESSION['flash']['order']);
    }
    if ($m) echo '<div class="alert alert-success">' . htmlspecialchars($m ?? '', ENT_QUOTES, 'UTF-8') . '</div>';
    ?>
    <?php
    $m = null;
    if (!empty($_SESSION['flash']['profile'])) {
        $m = $_SESSION['flash']['profile'];
        unset($_SESSION['flash']['profile']);
    }
    if ($m) echo '<div class="alert alert-success">' . htmlspecialchars($m ?? '', ENT_QUOTES, 'UTF-8') . '</div>';
    ?>

    <div class="grid grid-2" style="align-items:flex-start;">
      <div class="profile-card">
        <h3>Account Details</h3>
        <form method="post">
          <div class="form-group"><label>Full Name</label><input type="text" name="name" value="<?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required></div>
          <div class="form-group"><label>Email</label><input type="email" value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" disabled></div>
          <div class="form-group"><label>Phone</label><input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
          <div class="form-group"><label>Address</label><textarea name="address"><?php echo htmlspecialchars($user['address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></div>
          <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
        </form>
      </div>
      <div class="profile-card">
        <h3>My Orders</h3>
        <?php if (mysqli_num_rows($orders) == 0): ?>
          <p style="color:var(--muted);">No orders yet.</p>
          <a href="products.php" class="btn btn-primary btn-sm">Start Shopping</a>
        <?php else: ?>
          <?php while ($order = mysqli_fetch_assoc($orders)):
            $items = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id=" . $order['id']);
          ?>
            <div style="border:1px solid var(--line);border-radius:12px;padding:16px;margin-bottom:14px;">
              <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;">
                <strong>Order #<?php echo $order['id']; ?></strong>
                <span class="status-pill status-<?php echo $order['status']; ?>"><?php echo htmlspecialchars($order['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
              </div>
              <p style="font-size:.82rem;color:var(--muted);margin:6px 0;"><?php echo date('d M Y, h:i A', strtotime($order['created_at'])); ?></p>
              <ul style="margin:8px 0;padding-left:18px;font-size:.85rem;">
                <?php while ($it = mysqli_fetch_assoc($items)): ?>
                  <li><?php echo htmlspecialchars($it['product_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> × <?php echo $it['quantity']; ?></li>
                <?php endwhile; ?>
              </ul>
              <strong>Total: <?php echo CURRENCY . number_format($order['total_amount'], 2); ?></strong>
            </div>
          <?php endwhile; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
