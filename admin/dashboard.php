<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes_header.php';

$totalProducts = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM products"))[0];
$totalOrders   = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders"))[0];
$totalUsers    = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='user'"))[0];
$totalRevenue  = mysqli_fetch_row(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status!='cancelled'"))[0];
$pendingOrders = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM orders WHERE status='pending'"))[0];

$recentOrders = mysqli_query($conn, "SELECT o.*, u.name AS customer FROM orders o JOIN users u ON o.user_id=u.id ORDER BY o.created_at DESC LIMIT 8");
?>

<div class="grid grid-4" style="margin-bottom:30px;">
  <div class="stat-card"><div class="num"><?php echo CURRENCY . number_format($totalRevenue, 0); ?></div><div class="lbl">Total Revenue</div></div>
  <div class="stat-card"><div class="num"><?php echo $totalOrders; ?></div><div class="lbl">Orders (<?php echo $pendingOrders; ?> pending)</div></div>
  <div class="stat-card"><div class="num"><?php echo $totalProducts; ?></div><div class="lbl">Products</div></div>
  <div class="stat-card"><div class="num"><?php echo $totalUsers; ?></div><div class="lbl">Customers</div></div>
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
      <?php while ($o = mysqli_fetch_assoc($recentOrders)): ?>
      <tr>
        <td>#<?php echo $o['id']; ?></td>
        <td><?php echo htmlspecialchars($o['customer'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
        <td><?php echo CURRENCY . number_format($o['total_amount'], 2); ?></td>
        <td><span class="status-pill status-<?php echo $o['status']; ?>"><?php echo htmlspecialchars($o['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></td>
        <td><?php echo date('d M Y', strtotime($o['created_at'])); ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes_footer.php'; ?>
