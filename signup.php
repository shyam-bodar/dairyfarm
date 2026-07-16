<?php
require_once __DIR__ . '/config/db.php';

if (isset($_SESSION['user_id'])) {
    header('Location: profile.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if ($name == '' || $email == '' || $password == '') {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password != $confirm) {
        $error = 'Passwords do not match.';
    } else {
        $e = mysqli_real_escape_string($conn, $email);
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$e'");
        if (mysqli_num_rows($check) > 0) {
            $error = 'An account with this email already exists.';
        } else {
            $n  = mysqli_real_escape_string($conn, $name);
            $ph = mysqli_real_escape_string($conn, $phone);
            $p  = mysqli_real_escape_string($conn, $password);
            mysqli_query($conn, "INSERT INTO users (name, email, phone, password, role) VALUES ('$n','$e','$ph','$p','user')");
            $userId = mysqli_insert_id($conn);
            $_SESSION['user_id']   = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['role']      = 'user';
            header('Location: profile.php');
            exit;
        }
    }
}

$pageTitle = 'Sign Up';
require_once __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container">
    <div class="form-card">
      <div class="center" style="margin-bottom:20px;">
        <span class="eyebrow">Join Us</span>
        <h2>Create your account</h2>
      </div>
      <?php if ($error != '') echo '<div class="alert alert-error">' . htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8') . '</div>'; ?>
      <form method="post">
        <div class="form-group"><label>Full Name</label><input type="text" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
        <div class="form-group"><label>Email Address</label><input type="email" name="email" required value="<?php echo htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
        <div class="form-group"><label>Phone Number</label><input type="tel" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
        <div class="form-group"><label>Password</label><input type="password" name="password" required></div>
        <div class="form-group"><label>Confirm Password</label><input type="password" name="confirm_password" required></div>
        <button type="submit" class="btn btn-primary btn-block">Create Account</button>
      </form>
      <p class="form-foot">Already have an account? <a href="login.php" style="color:var(--barn);font-weight:600;">Log in</a></p>
    </div>
  </div>
</section>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
