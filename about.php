
<?php
require_once __DIR__ . '/config/db.php';
$pageTitle = 'About Us';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section-sm" style="padding-top:50px;">
  <div class="container center">
    <span class="eyebrow">Our Story</span>
    <h1 style="max-width:700px;margin:0 auto;">Three generations of honest farming</h1>
  </div>
</section>

<section class="section">
  <div class="container about-split">
    <img src="<?php echo SITE_URL; ?>assets/images/abot.png" alt="Farmer in dairy farm">
    <div>
      <span class="eyebrow">Since 1998</span>
      <h2>From a single cowshed to your daily table</h2>
      <p style="color:var(--muted);">Greenpasture Dairy began as a small family cowshed with just four cows. Today, we care for over 40 grass-fed cows and buffaloes across 12 acres of open pasture, while keeping the same values our founders started with: no shortcuts, no additives, and complete transparency about where your dairy comes from.</p>
      <p style="color:var(--muted);">Every product, from our golden bilona ghee to our thick set curd, is made in small batches using traditional methods, refined with modern hygiene and cold-chain delivery so freshness reaches you intact.</p>
      <div class="grid grid-2" style="margin-top:20px;">
        <div><h3 style="font-size:1.6rem;">25+</h3><p style="color:var(--muted);font-size:.85rem;margin:0;">Years of Experience</p></div>
        <div><h3 style="font-size:1.6rem;">12</h3><p style="color:var(--muted);font-size:.85rem;margin:0;">Acres of Pasture</p></div>
      </div>
    </div>
  </div>
</section>

<section class="section section-cream">
  <div class="container">
    <div class="center" style="max-width:600px;margin:0 auto 40px;">
      <span class="eyebrow">Our Process</span>
      <h2>From pasture to your doorstep</h2>
    </div>
    <ul class="timeline" style="max-width:700px;margin:0 auto;">
      <li><span class="num">01</span><div><h3 style="font-size:1.05rem;">Morning Milking</h3><p style="color:var(--muted);margin:0;">Cows are milked at dawn under strict hygiene protocols.</p></div></li>
      <li><span class="num">02</span><div><h3 style="font-size:1.05rem;">Cooling & Testing</h3><p style="color:var(--muted);margin:0;">Milk is chilled instantly and lab-tested for purity and fat content.</p></div></li>
      <li><span class="num">03</span><div><h3 style="font-size:1.05rem;">Processing</h3><p style="color:var(--muted);margin:0;">Curd, ghee, paneer and butter are prepared fresh using traditional methods.</p></div></li>
      <li><span class="num">04</span><div><h3 style="font-size:1.05rem;">Cold-Chain Delivery</h3><p style="color:var(--muted);margin:0;">Products are packed and delivered to you the same day, kept cool throughout.</p></div></li>
    </ul>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="center" style="max-width:600px;margin:0 auto 40px;">
      <span class="eyebrow">Our Values</span>
      <h2>What we stand for</h2>
    </div>
    <div class="grid grid-3">
      <div class="feature-card"><div class="ico">🤝</div><h3>Animal Welfare</h3><p style="color:var(--muted);font-size:.9rem;">Our herd is free-grazing, well-fed, and cared for by a dedicated veterinary team.</p></div>
      <div class="feature-card"><div class="ico">🌾</div><h3>Sustainability</h3><p style="color:var(--muted);font-size:.9rem;">We compost waste, conserve water, and rotate pastures to keep our land healthy.</p></div>
      <div class="feature-card"><div class="ico">💛</div><h3>Community First</h3><p style="color:var(--muted);font-size:.9rem;">We employ locally and support nearby small farms with fair purchasing practices.</p></div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
