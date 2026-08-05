<?php
require_once __DIR__ . '/config/db.php';

$pageTitle = 'Products';
require_once __DIR__ . '/includes/header.php';

$category = isset($_GET['category']) ? $_GET['category'] : '';
$search   = isset($_GET['q']) ? trim($_GET['q']) : '';

$where = "WHERE status='active'";
if ($category != '') {
    $c = mysqli_real_escape_string($conn, $category);
    $where .= " AND category='$c'";
}
if ($search != '') {
    $s = mysqli_real_escape_string($conn, $search);
    $where .= " AND name LIKE '%$s%'";
}

$result  = mysqli_query($conn, "SELECT * FROM products $where ORDER BY created_at DESC");
$catRes  = mysqli_query($conn, "SELECT DISTINCT category FROM products WHERE status='active' ORDER BY category");
?>

<section class="section-sm" style="padding-top:50px;">
  <div class="container center">
    <span class="eyebrow">Shop</span>
    <h1>Our Fresh Dairy Products</h1>
    <p style="max-width:560px;margin:0 auto;color:var(--muted);">Pure, farm-fresh dairy made daily.</p>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="container">
    <form method="get" style="display:flex;gap:14px;flex-wrap:wrap;align-items:center;justify-content:space-between;margin-bottom:30px;background:var(--white);padding:16px;border-radius:var(--radius);border:1px solid var(--line);">
      <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="products.php" class="btn btn-sm <?php echo $category==''?'btn-primary':'btn-outline'; ?>">All</a>
        <?php while ($cat = mysqli_fetch_assoc($catRes)): ?>
         <a href="products.php?category=<?php echo urlencode($cat['category']); ?>&q=<?php echo urlencode($search); ?>"
   class="btn btn-sm <?php echo ($category == $cat['category']) ? 'btn-primary' : 'btn-outline'; ?>">
    <?php echo htmlspecialchars($cat['category'], ENT_QUOTES, 'UTF-8'); ?>
</a>
        <?php endwhile; ?>
      </div>
      <div style="display:flex;gap:8px;">
        <input type="text" name="q" placeholder="Search products..." value="<?php echo htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8'); ?>" style="min-width:220px;">
        <button class="btn btn-primary btn-sm" type="submit">Search</button>
      </div>
    </form>

    <?php if (mysqli_num_rows($result) == 0): ?>
      <p class="center" style="color:var(--muted);padding:40px 0;">No products found.</p>
    <?php else: ?>
    <div class="grid grid-4">
      <?php while ($p = mysqli_fetch_assoc($result)): ?>
      <div class="product-card">
        <div class="product-thumb">
          <span class="product-cat"><?php echo htmlspecialchars($p['category'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
          <?php if (!empty($p['image']) && file_exists(__DIR__ . '/assets/images/products/' . $p['image'])): ?>
            <img src="<?php echo SITE_URL; ?>assets/images/products/<?php echo htmlspecialchars($p['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($p['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <?php else: ?>
            <img src="https://images.unsplash.com/photo-1550583724-b2692b85b150?q=80&w=500&auto=format&fit=crop" alt="<?php echo htmlspecialchars($p['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
          <?php endif; ?>
        </div>
        <div class="product-body">
          <h3><?php echo htmlspecialchars($p['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h3>
          <p class="desc"><?php echo htmlspecialchars(mb_strimwidth($p['description'], 0, 70, '...') ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
          <div class="product-price-row">
            <div><span class="price"><?php echo CURRENCY . number_format($p['price'], 2); ?></span> <span class="unit">/ <?php echo htmlspecialchars($p['unit'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span></div>
            <?php if ($p['stock'] < 10): ?><span class="stock-low">Low stock</span><?php endif; ?>
          </div>
          <a href="product-detail.php?id=<?php echo $p['id']; ?>" class="btn btn-primary btn-sm btn-block">View Product</a>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
