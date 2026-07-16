<?php
$pageTitle = 'Add Product';
require_once __DIR__ . '/includes_header.php';

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
    $imageName   = '';

    // Handle image upload
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
            $imageName = 'product_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $uploadDir = __DIR__ . '/../assets/images/products/';
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName)) {
                $error     = 'Failed to upload image. Check folder permissions.';
                $imageName = '';
            }
        }
    }

    if ($error == '') {
        if ($name == '' || $category == '' || $price <= 0) {
            $error = 'Name, category and price are required.';
        } else {
            $n  = mysqli_real_escape_string($conn, $name);
            $c  = mysqli_real_escape_string($conn, $category);
            $d  = mysqli_real_escape_string($conn, $description);
            $u  = mysqli_real_escape_string($conn, $unit);
            $s  = mysqli_real_escape_string($conn, $status);
            $img = mysqli_real_escape_string($conn, $imageName);
            mysqli_query($conn, "INSERT INTO products (name, category, description, price, unit, stock, image, status) VALUES ('$n','$c','$d','$price','$u','$stock','$img','$s')");
            $success = 'Product added successfully!';
        }
    }
}
?>

<div class="profile-card" style="max-width:760px;">
  <?php if ($error != '') echo '<div class="alert alert-error">' . htmlspecialchars($error ?? '', ENT_QUOTES, 'UTF-8') . '</div>'; ?>
  <?php if ($success != '') echo '<div class="alert alert-success">' . htmlspecialchars($success ?? '', ENT_QUOTES, 'UTF-8') . ' <a href="products.php">Back to products</a></div>'; ?>

  <form method="post" enctype="multipart/form-data">
    <!-- Product Image Upload -->
    <div class="form-group">
      <label>Product Image</label>
      <div style="border:2px dashed var(--line);border-radius:12px;padding:24px;text-align:center;background:var(--cream);">
        <img id="preview" src="" alt="" style="display:none;max-height:180px;margin:0 auto 14px;border-radius:10px;object-fit:cover;">
        <p style="color:var(--muted);margin:0 0 12px;font-size:.9rem;">📷 Click to choose a product photo</p>
        <input type="file" name="image" id="imageInput" accept="image/*" style="display:none;" onchange="previewImage(this)">
        <button type="button" class="btn btn-outline btn-sm" onclick="document.getElementById('imageInput').click()">Choose Image</button>
        <p style="color:var(--muted);font-size:.78rem;margin:10px 0 0;">JPG, PNG, WEBP · Max 2MB</p>
      </div>
    </div>

    <div class="grid grid-2">
      <div class="form-group"><label>Product Name *</label><input type="text" name="name" required value="<?php echo htmlspecialchars($_POST['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
      <div class="form-group">
        <label>Category *</label>
        <select name="category">
          <option value="">-- Select Category --</option>
          <?php
          $cats = mysqli_query($conn, "SELECT DISTINCT category FROM products ORDER BY category");
          while ($cat = mysqli_fetch_assoc($cats)) {
              $sel = (($_POST['category'] ?? '') == $cat['category']) ? 'selected' : '';
              echo '<option value="' . htmlspecialchars($cat['category'] ?? '', ENT_QUOTES, 'UTF-8') . '" ' . $sel . '>' . htmlspecialchars($cat['category'] ?? '', ENT_QUOTES, 'UTF-8') . '</option>';
          }
          ?>
          <option value="__new__">+ New Category (type below)</option>
        </select>
        <input type="text" name="category_new" placeholder="Type new category name" style="margin-top:8px;" value="<?php echo htmlspecialchars($_POST['category_new'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <p style="font-size:.75rem;color:var(--muted);margin:4px 0 0;">If typing a new category above, select "+ New Category" in the dropdown.</p>
      </div>
    </div>

    <div class="form-group"><label>Description</label><textarea name="description"><?php echo htmlspecialchars($_POST['description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea></div>

    <div class="grid grid-3">
      <div class="form-group"><label>Price (<?php echo CURRENCY; ?>) *</label><input type="number" step="0.01" min="0" name="price" required value="<?php echo htmlspecialchars($_POST['price'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
      <div class="form-group"><label>Unit (e.g. Litre, Kg)</label><input type="text" name="unit" placeholder="Litre" value="<?php echo htmlspecialchars($_POST['unit'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div>
      <div class="form-group"><label>Stock Quantity</label><input type="number" min="0" name="stock" value="<?php echo htmlspecialchars($_POST['stock'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>"></div>
    </div>

    <div class="form-group">
      <label>Status</label>
      <select name="status">
        <option value="active">Active (visible to customers)</option>
        <option value="inactive">Inactive (hidden)</option>
      </select>
    </div>

    <div style="display:flex;gap:12px;margin-top:10px;">
      <button type="submit" class="btn btn-primary">Save Product</button>
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

// Handle "new category" selection
document.querySelector('select[name="category"]').addEventListener('change', function() {
    var newInput = document.querySelector('input[name="category_new"]');
    if (this.value == '__new__') {
        newInput.style.borderColor = 'var(--pasture)';
        newInput.focus();
    }
});

// Before submit: if new category chosen, write it into the select
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
