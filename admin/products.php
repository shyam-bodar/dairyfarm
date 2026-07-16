<?php
$pageTitle = 'Products';
require_once __DIR__ . '/includes_header.php';

// Delete product
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Also delete the image file
    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT image FROM products WHERE id=$id"));
    if ($row && $row['image'] != '' && $row['image'] != 'default.jpg') {
        $imgPath = __DIR__ . '/../assets/images/products/' . $row['image'];
        if (file_exists($imgPath)) unlink($imgPath);
    }
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    header('Location: products.php');
    exit;
}

// Toggle active/inactive
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    mysqli_query($conn, "UPDATE products SET status = IF(status='active','inactive','active') WHERE id=$id");
    header('Location: products.php');
    exit;
}

$result = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
?>

<div style="display:flex;justify-content:flex-end;margin-bottom:16px;">
  <a href="add-product.php" class="btn btn-primary">+ Add New Product</a>
</div>

<div style="overflow-x:auto;">
<table class="admin-table">
  <thead>
    <tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th>Actions</th></tr>
  </thead>
  <tbody>
    <?php while ($p = mysqli_fetch_assoc($result)): ?>
    <tr>
      <td>
        <?php if (!empty($p['image']) && file_exists(__DIR__ . '/../assets/images/products/' . $p['image'])): ?>
          <img src="<?php echo SITE_URL; ?>assets/images/products/<?php echo htmlspecialchars($p['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="thumb-sm">
        <?php else: ?>
          <img src="https://images.unsplash.com/photo-1550583724-b2692b85b150?q=80&w=100&auto=format&fit=crop" class="thumb-sm">
        <?php endif; ?>
      </td>
      <td><?php echo htmlspecialchars($p['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
      <td><?php echo htmlspecialchars($p['category'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
      <td><?php echo CURRENCY . number_format($p['price'], 2); ?> / <?php echo htmlspecialchars($p['unit'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
      <td><?php echo $p['stock']; ?></td>
      <td><span class="status-pill <?php echo $p['status']=='active' ? 'status-confirmed' : 'status-cancelled'; ?>"><?php echo htmlspecialchars($p['status'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></td>
      <td>
        <a href="edit-product.php?id=<?php echo $p['id']; ?>" class="action-link edit">Edit</a>
        <a href="products.php?toggle=<?php echo $p['id']; ?>" class="action-link edit"><?php echo $p['status']=='active' ? 'Deactivate' : 'Activate'; ?></a>
        <a href="products.php?delete=<?php echo $p['id']; ?>" class="action-link del" onclick="return confirm('Delete this product?')">Delete</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>

<?php require_once __DIR__ . '/includes_footer.php'; ?>
