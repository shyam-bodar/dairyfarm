<?php
// require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';

if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'admin') {
    header('Location: dashboard.php');
    exit;
}

// Provide a fallback db_fetch_one if not defined elsewhere
if (!function_exists('db_fetch_one')) {
  function db_fetch_one($conn, $sql, $types = null, $params = []) {
    if (!($stmt = $conn->prepare($sql))) {
      return null;
    }
    if ($types && $params) {
      // bind_param requires references
      $refs = [];
      foreach ($params as $key => $value) {
        $refs[$key] = &$params[$key];
      }
      array_unshift($refs, $types);
      call_user_func_array([$stmt, 'bind_param'], $refs);
    }
    if (!$stmt->execute()) {
      $stmt->close();
      return null;
    }
    $res = $stmt->get_result();
    if ($res) {
      $row = $res->fetch_assoc();
      $res->free();
      $stmt->close();
      return $row ?: null;
    }
    // fallback for drivers without get_result
    $meta = $stmt->result_metadata();
    if (!$meta) { $stmt->close(); return null; }
    $fields = [];
    $row = [];
    while ($field = $meta->fetch_field()) {
      $fields[] = &$row[$field->name];
    }
    call_user_func_array([$stmt, 'bind_result'], $fields);
    if ($stmt->fetch()) {
      $result = [];
      foreach ($row as $k => $v) $result[$k] = $v;
      $stmt->close();
      return $result;
    }
    $stmt->close();
    return null;
  }
}

if (!function_exists('isAdmin')) {
    function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
}

if (isAdmin()) { header('Location: dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $admin = db_fetch_one($conn, "SELECT * FROM users WHERE email = ? AND role = 'admin'", "s", [$email]);
    if ($admin && $password === $admin['password']) {
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_name'] = $admin['name'];
        $_SESSION['role'] = 'admin';
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid admin credentials.';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | Greenpasture Dairy</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="shortcut icon" href="<?php echo SITE_URL; ?>assets/images/logo.png" type="image/png">
<link rel="stylesheet" href="<?php echo SITE_URL; ?>assets/css/style.css">
</head>
<body style="background:var(--pasture-dark);min-height:100vh;display:flex;align-items:center;">
  <div class="container">
    <div class="form-card">
      <div class="center" style="margin-bottom:20px;">
        <span class="eyebrow">Admin Panel</span>
        <h2>Greenpasture Dairy</h2>
        <p style="color:var(--muted);font-size:.85rem;">Sign in to manage products and orders</p>
      </div>
      <?php if ($error): ?><div class="alert alert-error"><?php echo ($error); ?></div><?php endif; ?>
      <form method="post">
        <div class="form-group"><label>Admin Email</label><input type="email" name="email" required value="<?php echo ($_POST['email'] ?? ''); ?>"></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
        <button type="submit" class="btn btn-primary btn-block">Log In</button>
      </form>
      <p class="form-foot"><a href="<?php echo SITE_URL; ?>index.php" style="color:var(--barn);font-weight:600;">← Back to website</a></p>
      <p class="form-foot" style="font-size:.78rem;">Default: admin@dairyfarm.com / admin123</p>
    </div>
  </div>
</body>
</html>
