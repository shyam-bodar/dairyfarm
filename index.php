<?php
require_once __DIR__ . '/config/db.php';
$pageTitle = 'Home';
require_once __DIR__ . '/includes/header.php';


$result = mysqli_query($conn, "SELECT * FROM products WHERE status='active' ORDER BY created_at DESC LIMIT 4");
$featured = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];
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
    <div class="center" style="max-width:600px;margin:0 auto 40px;">
      <span class="eyebrow">Best Sellers</span>
      <h2>Fresh off the farm today</h2>
    </div>

    <div class="center" style="margin-top:36px;">
      <a href="products.php" class="btn btn-outline">View All Products</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="center" style="max-width:600px;margin:0 auto 40px;">
      <span class="eyebrow">Testimonials</span>
      <h2>What our customers say</h2>
    </div>
    <div class="grid grid-3">
      <div class="testimonial">
        <div class="stars">★★★★★</div>
        <p>"The milk tastes completely different — creamier and genuinely fresh. We've stopped buying from the supermarket."</p>
        <div class="testi-author"><span class="avatar">R</span><div><strong>Riya Shah</strong><br><span style="font-size:.8rem;color:var(--muted);">Jamnagar</span></div></div>
      </div>
      <div class="testimonial">
        <div class="stars">★★★★★</div>
        <p>"Their ghee is exactly how my grandmother used to make it. Aromatic, golden, and never disappoints."</p>
        <div class="testi-author"><span class="avatar">K</span><div><strong>Kunal Patel</strong><br><span style="font-size:.8rem;color:var(--muted);">Rajkot</span></div></div>
      </div>
      <div class="testimonial">
        <div class="stars">★★★★★</div>
        <p>"Delivery is always on time and the team is super responsive whenever I need to adjust my order."</p>
        <div class="testi-author"><span class="avatar">S</span><div><strong>Sneha Joshi</strong><br><span style="font-size:.8rem;color:var(--muted);">Jamnagar</span></div></div>
      </div>
    </div>
  </div>
</section>

<section class="section-sm" style="background:var(--pasture);color:#fff;border-radius:30px;width:92%;max-width:1180px;margin:0 auto 60px;text-align:center;padding:50px 30px;">
  <h2 style="color:#fff;">Ready to taste the difference?</h2>
  <p style="color:#D8D6C8;max-width:480px;margin:0 auto 24px;">Join thousands of families enjoying farm-fresh dairy delivered straight to their doorstep.</p>
  <a href="products.php" class="btn btn-gold">Start Shopping</a>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
