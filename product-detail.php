<?php
require_once __DIR__ . '/config/db.php';

$id = (int)($_GET['id'] ?? 0);
$result = mysqli_query($conn, "SELECT * FROM products WHERE id=$id AND status='active'");
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header('Location: products.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_to_cart'])) {

    $qty = (int)($_POST['qty'] ?? 1);

    if ($qty < 1) {
        $qty = 1;
    }

    /* Stock કરતાં વધુ Quantity ન જાય */
    if ($qty > $product['stock']) {
        $qty = $product['stock'];
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = array();
    }

    if (isset($_SESSION['cart'][$id])) {

        $newQty = $_SESSION['cart'][$id]['qty'] + $qty;

        /* Cart ની Total Quantity પણ Stock કરતાં વધુ ન થાય */
        if ($newQty > $product['stock']) {
            $newQty = $product['stock'];
        }

        $_SESSION['cart'][$id]['qty'] = $newQty;

    } else {

        $_SESSION['cart'][$id] = array(
            'name'  => $product['name'],
            'price' => $product['price'],
            'unit'  => $product['unit'],
            'qty'   => $qty
        );

    }

    $_SESSION['flash']['cart'] = $product['name'] . ' added to cart!';

    header('Location: cart.php');
    exit;
}

$pageTitle = $product['name'];
require_once __DIR__ . '/includes/header.php';

$relRes = mysqli_query($conn, "SELECT * FROM products WHERE category='" . mysqli_real_escape_string($conn, $product['category']) . "' AND id!=$id AND status='active' LIMIT 4");
?>

<section class="section">
  <div class="container">
    <div class="grid grid-2" style="align-items:flex-start;">
      <div class="product-thumb" style="height:420px;border-radius:var(--radius);">
        <?php if (!empty($product['image']) && file_exists(__DIR__ . '/assets/images/products/' . $product['image'])): ?>
          <img src="<?php echo SITE_URL; ?>assets/images/products/<?php echo htmlspecialchars($product['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" style="height:100%;">
        <?php else: ?>
          <img src="https://images.unsplash.com/photo-1550583724-b2692b85b150?q=80&w=900&auto=format&fit=crop" alt="<?php echo htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" style="height:100%;">
        <?php endif; ?>
      </div>
      <div>
        <span class="eyebrow"><?php echo htmlspecialchars($product['category'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
        <h1><?php echo htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h1>
        <p style="color:var(--muted);"><?php echo htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
        <div style="display:flex;align-items:baseline;gap:8px;margin:20px 0;">
          <span class="price" style="font-size:2rem;"><?php echo CURRENCY . number_format($product['price'], 2); ?></span>
          <span class="unit">/ <?php echo htmlspecialchars($product['unit'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
        </div>
        <?php if ($product['stock'] > 0): ?>
          <p style="color:var(--pasture);font-weight:600;font-size:.9rem;">✓ In Stock (<?php echo $product['stock']; ?> available)</p>
        <?php else: ?>
          <p style="color:#A3402B;font-weight:600;font-size:.9rem;">✗ Out of Stock</p>
        <?php endif; ?>
        <form method="post" style="display:flex;gap:14px;align-items:center;margin-top:20px;flex-wrap:wrap;">
          <div class="qty-box" style="background:var(--white);border:1.5px solid var(--line);border-radius:10px;padding:6px 10px;">
            <button type="button" class="qty-minus" style="border:none;background:none;font-size:1.1rem;cursor:pointer;">−</button>
          <input
                  type="number"
                  name="qty"
                  value="1"
                  min="1"
                  max="<?php echo $product['stock']; ?>">
            <button type="button" class="qty-plus" style="border:none;background:none;font-size:1.1rem;cursor:pointer;">+</button>
          </div>
          <button type="submit" name="add_to_cart" class="btn btn-primary" <?php if($product['stock']<=0) echo 'disabled'; ?>>Add to Cart</button>
        </form>
        <div style="margin-top:30px;display:flex;gap:24px;flex-wrap:wrap;font-size:.85rem;color:var(--muted);">
          <span>🚚 Same-day delivery</span><span>🌿 No preservatives</span><span>🧪 Lab tested</span>
        </div>
      </div>
    </div>
  </div>
</section>

<?php if (mysqli_num_rows($relRes) > 0): ?>
<section class="section section-cream">
  <div class="container">
    <h2 class="center" style="margin-bottom:30px;">You may also like</h2>
    <div class="grid grid-4">
      <?php while ($p = mysqli_fetch_assoc($relRes)): ?>
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
          <div class="product-price-row">
            <span class="price"><?php echo CURRENCY . number_format($p['price'], 2); ?></span>
          </div>
          <a href="product-detail.php?id=<?php echo $p['id']; ?>" class="btn btn-primary btn-sm btn-block">View Product</a>
        </div>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
