<?php
require_once __DIR__ . '/config/db.php';
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';

$result = mysqli_query($conn, "
  SELECT p.*, c.name AS category
  FROM products p
  LEFT JOIN categories c ON p.category_id = c.id
  WHERE p.status='active'
  ORDER BY p.created_at DESC
  LIMIT 4
");

$featured = [];

if($result){
    $featured = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

?>

<section class="hero">
  <div class="container hero-grid">
    <div>
      <span class="eyebrow" style="color:var(--gold-light)">Since 1998 · Family Owned</span>
      <h1>Black black <em>buffalo</em> white white milk.</h1>
      <p>We milk, pasteurize and bottle every morning before sunrise — no middlemen, no powders, no shortcuts. Just honest dairy from our pastures to your fridge.</p>
      <div class="hero-actions">
        <a href="products.php" class="btn btn-gold">Shop Products</a>
        <a href="about.php" class="btn btn-outline" style="color:var(--cream);border-color:rgba(255,255,255,.5);">Our Story</a>
      </div>
      <div class="hero-stats">
        <div><strong>25+</strong><span>Years Farming</span></div>
        <div><strong>40</strong><span>Happy Cows & Buffaloes</span></div>
        <div><strong>5,000+</strong><span>Daily Customers</span></div>
      </div>
    </div>
    <div class="hero-visual">
      <img src="./assets/images/indeximg.png" alt="Dairy cow in green pasture">
      <div class="hero-badge">100% Fresh<br>Guarantee</div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="center" style="max-width:600px;margin:0 auto 40px;">
      <span class="eyebrow">Why Choose Us</span>
      <h2>Quality you can taste, trust you can verify</h2>
    </div>
    <div class="grid grid-4">
      <div class="feature-card">
        <div class="ico">🐄</div>
        <h3>Grass-Fed Herd</h3>
        <p style="color:var(--muted);font-size:.9rem;">Our cows graze freely on open pastures, producing naturally richer milk.</p>
      </div>
      <div class="feature-card">
        <div class="ico">🚚</div>
        <h3>Same-Day Delivery</h3>
        <p style="color:var(--muted);font-size:.9rem;">Order before 8 AM and receive your dairy fresh the very same evening.</p>
      </div>
      <div class="feature-card">
        <div class="ico">🧪</div>
        <h3>Lab Tested Daily</h3>
        <p style="color:var(--muted);font-size:.9rem;">Every batch is tested for purity and fat content before it leaves the farm.</p>
      </div>
      <div class="feature-card">
        <div class="ico">🌿</div>
        <h3>Zero Preservatives</h3>
        <p style="color:var(--muted);font-size:.9rem;">Nothing artificial — just milk, culture, and time-honoured technique.</p>
      </div>
    </div>
  </div>
</section>

<section class="section section-cream">
    <div class="container">

        <div class="center" style="margin-bottom:40px;">
            <span class="eyebrow">Best Sellers</span>
            <h2>Fresh off the farm today</h2>
        </div>

        <div class="grid grid-4">

            <?php if(!empty($featured)){ ?>

                <?php foreach($featured as $product){ ?>

                    <div class="product-card">

                        <div class="product-thumb">

                            <img src="assets/images/products/<?php echo $product['image']; ?>"
                                 alt="<?php echo htmlspecialchars($product['name']); ?>">

                            <span class="product-cat">
                                <?php echo htmlspecialchars($product['category']); ?>
                            </span>

                        </div>

                        <div class="product-body">

                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>

                            <p class="desc">
                                <?php
                                echo substr(strip_tags($product['description']),0,80);
                                ?>...
                            </p>

                            <div class="product-price-row">

                                <div>
                                    <span class="price">
                                        ₹<?php echo number_format($product['price'],2); ?>
                                    </span>
                                    <br>
                                    <span class="unit">
                                        <?php echo $product['unit']; ?>
                                    </span>
                                </div>

                                <?php if($product['stock']<=5){ ?>

                                    <span class="stock-low">
                                        Only <?php echo $product['stock']; ?> Left
                                    </span>

                                <?php } ?>

                            </div>

                        </div>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <p>No Products Available</p>

            <?php } ?>

        </div>

        <div class="center" style="margin-top:40px;">
            <a href="products.php" class="btn btn-outline">
                View All Products
            </a>
        </div>

    </div>
</section>

<section class="section-sm" style="background:var(--pasture);color:#fff;border-radius:30px;width:92%;max-width:1180px;margin:0 auto 60px;text-align:center;padding:50px 30px;">
  <h2 style="color:#fff;">Ready to taste the difference?</h2>
  <p style="color:#D8D6C8;max-width:480px;margin:0 auto 24px;">Join thousands of families enjoying farm-fresh dairy delivered straight to their doorstep.</p>
  <a href="products.php" class="btn btn-gold">Start Shopping</a>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
