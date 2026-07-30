<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes_header.php';
require_once __DIR__ . '/../config/db.php';

// Fallback helpers if the app's DB helpers are not loaded
if (!function_exists('db_value')) {
  function db_value($conn, $sql) {
    $res = mysqli_query($conn, $sql);
    if (!$res) return null;
    $row = mysqli_fetch_row($res);
    mysqli_free_result($res);
    return $row ? $row[0] : null;
  }
}

if (!function_exists('db_fetch_all')) {
  function db_fetch_all($conn, $sql) {
    $out = [];
    $res = mysqli_query($conn, $sql);
    if (!$res) return $out;
    while ($row = mysqli_fetch_assoc($res)) $out[] = $row;
    mysqli_free_result($res);
    return $out;
  }
}

if (!function_exists('e')) {
  function e($v) { return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
}


$totalProducts = db_value($conn, "SELECT COUNT(*) FROM products");
$totalOrders = db_value($conn, "SELECT COUNT(*) FROM orders");
$totalUsers = db_value($conn, "SELECT COUNT(*) FROM users WHERE role='user'");
$totalRevenue = db_value($conn, "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status != 'cancelled'");
$pendingOrders = db_value($conn, "SELECT COUNT(*) FROM orders WHERE status='pending'");

$recentOrders = db_fetch_all($conn, "SELECT o.*, u.name AS customer FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 8");
?>

<div class="grid grid-4" style="margin-bottom:30px;">
  <div class="stat-card"><div class="num"><?php echo CURRENCY . number_format($totalRevenue,0); ?></div><div class="lbl">Total Revenue</div></div>
  <div class="stat-card"><div class="num"><?php echo $totalOrders; ?></div><div class="lbl">Total Orders (<?php echo $pendingOrders; ?> pending)</div></div>
  <div class="stat-card"><div class="num"><?php echo $totalProducts; ?></div><div class="lbl">Products Listed</div></div>
  <div class="stat-card"><div class="num"><?php echo $totalUsers; ?></div><div class="lbl">Registered Customers</div></div>
</div>

<div class="profile-card">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
    <h3 style="margin:0;">Recent Orders</h3>
    <a href="orders.php" class="btn btn-outline btn-sm">View All</a>
  </div>
  <div style="overflow-x:auto;">
  <table class="admin-table">
    <thead><tr><th>Order #</th><th>Customer</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
    <tbody>
      <?php foreach ($recentOrders as $o): ?>
      <tr>
        <td>#<?php echo $o['id']; ?></td>
        <td><?php echo e($o['customer']); ?></td>
        <td><?php echo CURRENCY . number_format($o['total_amount'],2); ?></td>
        <td><span class="status-pill status-<?php echo $o['status']; ?>"><?php echo e($o['status']); ?></span></td>
        <td><?php echo date('d M Y', strtotime($o['created_at'])); ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($recentOrders)): ?><tr><td colspan="5" style="text-align:center;color:var(--muted);">No orders yet.</td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes_footer.php'; ?>
