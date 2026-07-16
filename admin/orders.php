<?php
$pageTitle = 'Orders';
require_once __DIR__ . '/includes_header.php';

// Update order status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'])) {
    $oid    = (int)($_POST['order_id'] ?? 0);
    $status = mysqli_real_escape_string($conn, $_POST['status'] ?? '');
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$oid");
    header('Location: orders.php');
    exit;
}

$filter = isset($_GET['status']) ? $_GET['status'] : '';
$where  = '';
if ($filter != '') {
    $f     = mysqli_real_escape_string($conn, $filter);
    $where = "WHERE o.status='$f'";
}

$orders   = mysqli_query($conn, "SELECT o.*, u.name AS customer, u.email FROM orders o JOIN users u ON o.user_id=u.id $where ORDER BY o.created_at DESC");
$statuses = array('pending', 'confirmed', 'shipped', 'delivered', 'cancelled');
?>

<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
  <a href="orders.php" class="btn btn-sm <?php echo $filter=='' ? 'btn-primary' : 'btn-outline'; ?>">All</a>
  <?php foreach ($statuses as $s): ?>
    <a href="orders.php?status=<?php echo $s; ?>" class="btn btn-sm <?php echo $filter==$s ? 'btn-primary' : 'btn-outline'; ?>"><?php echo ucfirst($s); ?></a>
  <?php endforeach; ?>
</div>

<?php if (mysqli_num_rows($orders) == 0): ?>
  <p class="center" style="color:var(--muted);padding:40px 0;">No orders found.</p>
<?php endif; ?>

<?php while ($o = mysqli_fetch_assoc($orders)):
  $items = mysqli_query($conn, "SELECT * FROM order_items WHERE order_id=" . $o['id']);
?>
<div class="profile-card" style="margin-bottom:18px;">
  <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;align-items:flex-start;">
    <div>
      <strong>Order #<?php echo $o['id']; ?></strong>
      <span style="color:var(--muted);font-size:.85rem;"> · <?php echo date('d M Y, h:i A', strtotime($o['created_at'])); ?></span>
      <p style="margin:6px 0 0;font-size:.88rem;"><?php echo htmlspecialchars($o['customer'] ?? '', ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($o['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>)</p>
      <p style="margin:2px 0 0;font-size:.85rem;color:var(--muted);">📍 <?php echo htmlspecialchars($o['shipping_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> · <?php echo htmlspecialchars($o['shipping_phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
      <p style="margin:2px 0 0;font-size:.85rem;color:var(--muted);"><?php echo htmlspecialchars($o['shipping_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
      <p style="margin:2px 0 0;font-size:.85rem;color:var(--muted);">💳 <?php echo htmlspecialchars($o['payment_method'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <span class="status-pill status-<?php echo $o['status']; ?>"><?php echo htmlspecialchars($o['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
  </div>

  <ul style="margin:14px 0;padding-left:18px;font-size:.88rem;">
    <?php while ($it = mysqli_fetch_assoc($items)): ?>
      <li><?php echo htmlspecialchars($it['product_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?> × <?php echo $it['quantity']; ?> — <?php echo CURRENCY . number_format($it['price'] * $it['quantity'], 2); ?></li>
    <?php endwhile; ?>
  </ul>

  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;border-top:1px solid var(--line);padding-top:14px;">
    <strong>Total: <?php echo CURRENCY . number_format($o['total_amount'], 2); ?></strong>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <!-- Quick action buttons -->
      <?php if ($o['status'] == 'pending'): ?>
        <form method="post" style="display:inline;">
          <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
          <input type="hidden" name="status" value="confirmed">
          <button type="submit" class="btn btn-gold btn-sm">✓ Confirm</button>
        </form>
        <form method="post" style="display:inline;" onsubmit="return confirm('Cancel this order?')">
          <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
          <input type="hidden" name="status" value="cancelled">
          <button type="submit" class="btn btn-danger btn-sm">✗ Cancel</button>
        </form>
      <?php endif; ?>
      <!-- Full status change -->
      <form method="post" style="display:flex;gap:8px;align-items:center;">
        <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
        <select name="status">
          <?php foreach ($statuses as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo $o['status']==$s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
          <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Update</button>
      </form>
    </div>
  </div>
</div>
<?php endwhile; ?>

<?php require_once __DIR__ . '/includes_footer.php'; ?>
