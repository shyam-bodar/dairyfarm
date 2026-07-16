<?php
require_once __DIR__ . '/../config/db.php';
if (!(isset($_SESSION['user_id']) && ($_SESSION['role'] ?? '') == 'admin')) {
    header('Location: ' . SITE_URL . 'admin/login.php');
    exit;
}
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle ?? '', ENT_QUOTES, 'UTF-8') . ' | Admin' : 'Admin Panel'; ?></title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="shortcut icon" href="<?php echo SITE_URL; ?>assets/images/logo.png" type="image/png">
<link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
</head>
<body>
<div class="admin-wrap">
  <aside class="admin-sidebar">
    <div class="logo"><span class="dot"></span> Admin Panel</div>
    <nav>
      <a href="dashboard.php" <?php if($current=='dashboard.php') echo 'class="active"'; ?>>📊 Dashboard</a>
      <a href="products.php" <?php if(in_array($current,['products.php','add-product.php','edit-product.php'])) echo 'class="active"'; ?>>🥛 Products</a>
      <a href="orders.php" <?php if($current=='orders.php') echo 'class="active"'; ?>>📦 Orders</a>
      <a href="<?php echo SITE_URL; ?>index.php">🌐 View Website</a>
      <a href="logout.php">🚪 Logout</a>
    </nav>
  </aside>
  <main class="admin-main">
    <div class="admin-topbar">
      <button class="side-toggle" style="background:none;border:none;font-size:1.5rem;cursor:pointer;">☰</button>
      <h2 style="margin:0;"><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle ?? '', ENT_QUOTES, 'UTF-8') : 'Dashboard'; ?></h2>
      <div class="user-chip">
        <span class="avatar"><?php echo strtoupper(substr($_SESSION['user_name'] ?? 'A', 0, 1)); ?></span>
        <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
    </div>
