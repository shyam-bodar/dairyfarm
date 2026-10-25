<?php
$pageTitle = 'Orders';
require_once __DIR__ . '/includes_header.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/mailer.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
  $status = $_POST['status'] ?? '';
  $order_id = (int)($_POST['order_id']);
  if ($stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?")) {
    $stmt->bind_param('si', $status, $order_id);
    $stmt->execute();
    $stmt->close();

   
    if ($stmt2 = $conn->prepare(
      "SELECT u.name AS customer, u.email, o.total_amount
       FROM orders o JOIN users u ON o.user_id = u.id
       WHERE o.id = ?"
    )) {
      $stmt2->bind_param('i', $order_id);
      $stmt2->execute();
      $row = $stmt2->get_result()->fetch_assoc();
      $stmt2->close();

      if ($row) {
        sendOrderStatusEmail($row['email'], $row['customer'], $order_id, $status, $row['total_amount']);
      }
    }
  }
  header('Location: orders.php');
  exit;
}

$filter = $_GET['status'] ?? '';
$sql = "
  SELECT o.*, u.name AS customer, u.email,
         s.name AS shipping_name, s.phone AS shipping_phone, s.address AS shipping_address
  FROM orders o
  JOIN users u ON o.user_id = u.id
  LEFT JOIN shipping_details s ON o.id = s.order_id
";

if ($filter !== '') { $sql .= " WHERE o.status = ?"; }
$sql .= " ORDER BY o.created_at DESC";

$orders = [];
if ($stmt = $conn->prepare($sql)) {
  if ($filter !== '') {
    $stmt->bind_param('s', $filter);
  }
  $stmt->execute();
  $result = $stmt->get_result();
  $orders = $result->fetch_all(MYSQLI_ASSOC);
  $stmt->close();
}

$statuses = ['pending','confirmed','shipped','delivered','cancelled'];

$item_stmt = $conn->prepare("
  SELECT oi.*, p.name AS product_name, p.price
  FROM order_items oi
  JOIN products p ON oi.product_id = p.id
  WHERE oi.order_id = ?
");

?>

<div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;">
  <a href="orders.php" class="btn btn-sm <?php echo $filter==''?'btn-primary':'btn-outline'; ?>">All</a>
  <?php foreach ($statuses as $s): ?>
    <a href="orders.php?status=<?php echo $s; ?>" class="btn btn-sm <?php echo $filter==$s?'btn-primary':'btn-outline'; ?>"><?php echo ucfirst($s); ?></a>
  <?php endforeach; ?>
</div>

<?php foreach ($orders as $o):
  $items = [];
  if ($item_stmt) {
    $item_stmt->bind_param('i', $o['id']);
    $item_stmt->execute();
    $items = $item_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
  }
?>
<div class="profile-card" style="margin-bottom:18px;">
  <div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:10px;align-items:flex-start;">
    <div>
      <strong>Order #<?php echo $o['id']; ?></strong> · <span style="color:var(--muted);font-size:.85rem;"><?php echo date('d M Y, h:i A', strtotime($o['created_at'])); ?></span>
      <p style="margin:6px 0 0;font-size:.88rem;"><?php echo ($o['customer']); ?> (<?php echo ($o['email']); ?>)</p>
      <p style="margin:2px 0 0;font-size:.85rem;color:var(--muted);"><?php echo ($o['shipping_name']); ?> · <?php echo ($o['shipping_phone']); ?> · <?php echo ($o['shipping_address']); ?></p>
      <p style="margin:2px 0 0;font-size:.85rem;color:var(--muted);">Payment: <?php echo ($o['payment_method']); ?></p>
    </div>
    <span class="status-pill status-<?php echo $o['status']; ?>"><?php echo ($o['status']); ?></span>
  </div>
  <ul style="margin:14px 0;padding-left:18px;font-size:.88rem;">
    <?php foreach ($items as $it): ?>
     <li>
  <?php echo htmlspecialchars($it['product_name']); ?> × <?php echo $it['quantity']; ?> —
  <?php echo CURRENCY . number_format($it['price'] * $it['quantity'], 2); ?>
</li>

    <?php endforeach; ?>
  </ul>
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
    <strong>Total: <?php echo CURRENCY . number_format($o['total_amount'],2); ?></strong>
    <form method="post" style="display:flex;gap:8px;">
      <input type="hidden" name="order_id" value="<?php echo $o['id']; ?>">
      <select name="status">
        <?php foreach ($statuses as $s): ?>
          <option value="<?php echo $s; ?>" <?php echo $o['status']==$s?'selected':''; ?>><?php echo ucfirst($s); ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-primary btn-sm">Update</button>
      <?php if ($o['status']=='pending'): ?>
      <button type="submit" class="btn btn-gold btn-sm" onclick="this.form.status.value='confirmed';">Confirm</button>
      <button type="submit" class="btn btn-danger btn-sm" onclick="this.form.status.value='cancelled';">Cancel</button>
      <?php endif; ?>
    </form>
  </div>
</div>
<?php endforeach; ?>
<?php if (empty($orders)): ?><p class="center" style="color:var(--muted);padding:40px 0;">No orders found.</p><?php endif; ?>

<?php require_once __DIR__ . '/includes_footer.php'; ?>