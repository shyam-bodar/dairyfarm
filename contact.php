<?php
require_once __DIR__ . '/config/db.php';
$pageTitle = "Contact ";
require_once __DIR__ . '/includes/header.php';

$success = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');
    if ($name === '' || $email === '' || $message === '') {
        $error = 'Please fill in all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        
       
        if (function_exists('db_exec')) {
          db_exec($conn, "INSERT INTO contacts (name, email, message) VALUES (?,?,?)", "sss", [$name, $email, $message]);
        } else {
          if (isset($conn) && $conn instanceof mysqli) {
            $stmt = $conn->prepare("INSERT INTO contacts (name, email, message) VALUES (?,?,?)");
            if ($stmt) {
              $stmt->bind_param('sss', $name, $email, $message);
              $stmt->execute();
              $stmt->close();
            } else {
              $error = 'Failed to save message.';
            }
          } else {
           
            $error = 'Database connection not available.';
          }
        }
        $success = "Thanks $name! We've received your message and will get back to you soon.";
    }
}

$pageTitle = 'Contact Us';
require_once __DIR__ . '/includes/header.php';
?>

<section class="section-sm" style="padding-top:50px;">
  <div class="container center">
    <span class="eyebrow">Get In Touch</span>
    <h1>We'd love to hear from you</h1>
  </div>
</section>

<section class="section" style="padding-top:0;">
  <div class="container grid grid-2" style="align-items:flex-start;">
    <div class="form-card" style="margin:0;max-width:none;">
      <?php if ($success): ?><div class="alert alert-success"><?php echo ($success); ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert alert-error"><?php echo ($error); ?></div><?php endif; ?>
      <form method="post">
        <div class="form-group"><label>Your Name</label><input type="text" name="name" required value="<?php echo ($_POST['name'] ?? ''); ?>"></div>
        <div class="form-group"><label>Email Address</label><input type="email" name="email" required value="<?php echo ($_POST['email'] ?? ''); ?>"></div>
        <div class="form-group"><label>Message</label><textarea name="message" required><?php echo ($_POST['message'] ?? ''); ?></textarea></div>
        <button type="submit" class="btn btn-primary btn-block">Send Message</button>
      </form>
    </div>
    <div>
      <h3>Visit Our Farm</h3>
      <p style="color:var(--muted);">We welcome visitors who'd like to see where their dairy comes from. Stop by during our open hours, or reach out using the details below.</p>
      <div class="feature-card" style="margin-top:18px;"><div class="ico">📍</div><h3 style="font-size:1rem;">Address</h3><p style="color:var(--muted);margin:0;font-size:.9rem;">Krishna Dairy Farm, Jamnagar, GJ</p></div>
      <div class="feature-card" style="margin-top:14px;"><div class="ico">📞</div><h3 style="font-size:1rem;">Phone</h3><p style="color:var(--muted);margin:0;font-size:.9rem;">+91 98765 43210</p></div>
      <div class="feature-card" style="margin-top:14px;"><div class="ico">✉️</div><h3 style="font-size:1rem;">Email</h3><p style="color:var(--muted);margin:0;font-size:.9rem;">kdf12@gmail.com</p></div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
