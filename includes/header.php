<?php
// Ensure session is started (in case this page didn't already start one)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current = basename($_SERVER['PHP_SELF']);

// Number of items currently in the cart (plain inline PHP, no helper function)
$cartCount = 0;
if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $cartItem) {
        $cartCount += (int)$cartItem['qty'];
    }
}

// Is the visitor logged in? (plain inline PHP, no helper function)
$isLoggedIn = !empty($_SESSION['user_id']) || !empty($_SESSION['user_name']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? ($pageTitle) . ' | Krishna Dairy Farm' : 'Krishna Dairy Farm — Fresh From Our Farm'; ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="shortcut icon" href="<?php echo SITE_URL; ?>assets/images/logo.png" type="image/png">
<link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">

</head>
<body>
<header class="site-header">
  <div class="container nav-wrap">
   
    <a href="<?php echo SITE_URL; ?>index.php" class="logo">
  <img 
    src="<?php echo SITE_URL; ?>assets/images/logo.png" 
    alt="Krishna Dairy Farm Logo" 
    class="logo-img"
  >
  <span class="logo-text">Krishna Dairy Farm</span>
</a>
    <ul class="nav-links">
      <li><a href="<?php echo SITE_URL; ?>index.php" class="<?php echo $current=='index.php'?'active':''; ?>">Home</a></li>
      <li><a href="<?php echo SITE_URL; ?>about.php" class="<?php echo $current=='about.php'?'active':''; ?>">About</a></li>
      <li><a href="<?php echo SITE_URL; ?>products.php" class="<?php echo $current=='products.php'||$current=='product-detail.php'?'active':''; ?>">Products</a></li>
      <li><a href="<?php echo SITE_URL; ?>contact.php" class="<?php echo $current=='contact.php'?'active':''; ?>">Contact</a></li>
    </ul>
    <div class="nav-actions">
      <a href="<?php echo SITE_URL; ?>cart.php" class="icon-link" title="Cart">🛒<?php if ($cartCount > 0): ?><span class="badge"><?php echo $cartCount; ?></span><?php endif; ?></a>
      <?php if ($isLoggedIn): ?>
        <a href="<?php echo SITE_URL; ?>profile.php" class="user-chip">
          <span class="avatar"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'U', 0, 1)); ?></span>
          <span><?php echo htmlspecialchars(explode(' ', $_SESSION['user_name'] ?? 'Account')[0], ENT_QUOTES, 'UTF-8'); ?></span>
        </a>
      <?php else: ?>
        <a href="<?php echo SITE_URL; ?>login.php" class="btn btn-primary btn-sm">Login</a>
      <?php endif; ?>
      <button class="menu-toggle">☰</button>
    </div>
  </div>
</header>
