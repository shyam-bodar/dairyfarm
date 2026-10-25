<?php
$pageTitle = 'Edit Product';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/includes_header.php';

$id = (int)($_GET['id'] ?? 0);
$result  = mysqli_query($conn, "
  SELECT p.*, c.name AS category 
  FROM products p
  LEFT JOIN categories c ON p.category_id = c.id
  WHERE p.id=$id
");
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header('Location: products.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name        = trim($_POST['name'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = (float)($_POST['price'] ?? 0);
    $unit        = trim($_POST['unit'] ?? '');
    $stock       = (int)($_POST['stock'] ?? 0);
    $status      = $_POST['status'] ?? 'active';
    $imageName   = $product['image']; // keep existing image by default

    // Handle new image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowedTypes = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp');
        $fileType     = $_FILES['image']['type'];
        $fileSize     = $_FILES['image']['size'];
        $maxSize      = 2 * 1024 * 1024; // 2MB

        if (!in_array($fileType, $allowedTypes)) {
            $error = 'Only JPG, PNG, GIF and WEBP images are allowed.';
        } elseif ($fileSize > $maxSize) {
            $error = 'Image must be smaller than 2MB.';
        } else {
            $ext       = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $newName   = 'product_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $uploadDir = __DIR__ . '/../assets/images/products/';
            if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $newName)) {
                // Delete old image
                if ($product['image'] != '' && $product['image'] != 'default.jpg' && file_exists($uploadDir . $product['image'])) {
                    unlink($uploadDir . $product['image']);
                }
                $imageName = $newName;
            } else {
                $error = 'Failed to upload image. Check folder permissions.';
            }
        }
    }

    if ($error == '') {
        if ($name == '' || $category == '' || $price <= 0) {
            $error = 'Name, category and price are required.';
        } else {
            $n   = mysqli_real_escape_string($conn, $name);
            $c   = mysqli_real_escape_string($conn, $category);
            $d   = mysqli_real_escape_string($conn, $description);
            $u   = mysqli_real_escape_string($conn, $unit);
            $s   = mysqli_real_escape_string($conn, $status);
            $img = mysqli_real_escape_string($conn, $imageName);
            mysqli_query($conn, "
              UPDATE products 
              SET name='$n', category_id='$c', description='$d', price='$price', unit='$u', stock='$stock', image='$img', status='$s' 
              WHERE id=$id
            ");

            $success = 'Product updated successfully!';
            // Refresh product data
            $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE id=$id"));
        }
    }
}

$hasImage = !empty($product['image']) && file_exists(__DIR__ . '/../assets/images/products/' . $product['image']);
?>

<div class="profile-card" style="max-width:760px;">
  <?php if ($error != '') echo '<div class="alert alert-error">' . htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8') . '</div>'; ?>
  <?php if ($success != '') echo '<div class="alert alert-success">' . htmlspecialchars($success ?? '', ENT_QUOTES, 'UTF-8') . ' <a href="products.php">Back to products</a></div>'; ?>

  <form method="post" enctype="multipart/form-data">
    <!-- Product Image -->
    <div class="form-group">
      <label>Product Image</label>
      <div style="border:2px dashed var(--line);border-radius:12px;padding:24px;text-align:center;background:var(--cream);">
        <?php if ($hasImage): ?>
          <img id="preview" src="<?php echo SITE_URL . 'assets/images/products/' . htmlspecialchars($product['image'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" alt="Current Image" style="max-height:180px;margin:0 auto 14px;border-radius:10px;object-fit:cover;display:block;">
        <?php else: ?>
          <img id="preview" src="" alt="" style="display:none;max-height:180px;margin:0 auto 14px;border-radius:10px;object-fit:cover;">
        <?php endif; ?>
        <p style="color:var(--muted);margin:0 0 12px;font-size:.9rem;"><?php echo $hasImage ? '✓ Current image — choose a new one to replace it' : '📷 No image yet — choose a photo'; ?></p>
        <input type="file" name="image" id="imageInput" accept="image/*" style="display:none;" onchange="previewImage(this)">
        <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('imageInput').click()">
          <?php echo $hasImage ? 'Change Image' : 'Choose Image'; ?>
        </button>
        <p style="color:var(--muted);font-size:.78rem;margin:10px 0 0;">JPG, PNG, WEBP · Max 2MB</p>
      </div>
    </div>

    <div class="grid grid-2">
      <div class="form-group"><label>Product Name *</label><input type="text" name="name" required value="<?php echo htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
      <div class="form-group">
        <label>Category *</label>
        <select name="category">
          <?php
          $cats = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");
            while ($cat = mysqli_fetch_assoc($cats)) {
                $sel = ($product['category_id'] == $cat['id']) ? 'selected' : '';
                echo '<option value="' . $cat['id'] . '" ' . $sel . '>' . htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') . '</option>';
            }

          while ($cat = mysqli_fetch_assoc($cats)) {
              $sel = ($product['category'] == $cat['category']) ? 'selected' : '';
              echo '<option value="' . htmlspecialchars($cat['category'] ?? '', ENT_QUOTES, 'UTF-8') . '" ' . $sel . '>' . htmlspecialchars($cat['category'] ?? '', ENT_QUOTES, 'UTF-8') . '</option>';
          }
          ?>
          <option value="__new__">+ New Category (type below)</option>
        </select>
        <input type="text" name="category_new" placeholder="Type new category name" style="margin-top:8px;">
      </div>
    </div>

    <div class="form-group"><label>Description</label><textarea name="description"><?php echo htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></div>

    <div class="grid grid-3">
      <div class="form-group"><label>Price (<?php echo CURRENCY; ?>) *</label><input type="number" step="0.01" min="0" name="price" required value="<?php echo htmlspecialchars($product['price'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
      <div class="form-group"><label>Unit</label><input type="text" name="unit" value="<?php echo htmlspecialchars($product['unit'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
      <div class="form-group"><label>Stock Quantity</label><input type="number" min="0" name="stock" value="<?php echo htmlspecialchars($product['stock'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
    </div>

    <div class="form-group">
      <label>Status</label>
      <select name="status">
        <option value="active" <?php echo $product['status']=='active' ? 'selected' : ''; ?>>Active (visible to customers)</option>
        <option value="inactive" <?php echo $product['status']=='inactive' ? 'selected' : ''; ?>>Inactive (hidden)</option>
      </select>
    </div>

    <div style="display:flex;gap:12px;margin-top:10px;">
      <button type="submit" class="btn btn-primary">Update Product</button>
      <a href="products.php" class="btn btn-outline">Cancel</a>
    </div>
  </form>
</div>

<script>
function previewImage(input) {
    var preview = document.getElementById('preview');
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}
document.querySelector('form').addEventListener('submit', function() {
    var sel = document.querySelector('select[name="category"]');
    var newVal = document.querySelector('input[name="category_new"]').value.trim();
    if (sel.value == '__new__' && newVal != '') {
        var opt = document.createElement('option');
        opt.value = newVal;
        opt.text = newVal;
        opt.selected = true;
        sel.appendChild(opt);
    }
});
</script>

<?php require_once __DIR__ . '/includes_footer.php'; ?>