<?php
require_once __DIR__ . '/config/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email' AND role='user'");
    $user   = mysqli_fetch_assoc($result);

    if ($user && $password == $user['password']) {
        $_SESSION['user_id']   = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role']      = $user['role'];
        header('Location: profile.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}

$pageTitle = 'Login';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container">
    <div class="form-card">
      <div class="center" style="margin-bottom:20px;">
        <span class="eyebrow">Welcome Back</span>
        <h2>Log in to your account</h2>
      </div>
      <?php if ($error != '') echo '<div class="alert alert-error">' . htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8') . '</div>'; ?>
      <form method="post">
        <div class="form-group"><label>Email Address</label><input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
        <button type="submit" class="btn btn-primary btn-block">Log In</button>
      </form>
      <p class="form-foot">Don't have an account? <a href="signup.php" style="color:var(--barn);font-weight:600;">Sign up</a></p>
      <p class="form-foot">Admin? <a href="admin/login.php" style="color:var(--barn);font-weight:600;">Admin Login</a></p>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
