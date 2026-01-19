<?php $title='Admin - Product Management'; include __DIR__ . '/header.php'; ?>

<h2 class="page-title">Product Management</h2>

<div class="row gap">
  <a class="btn" href="<?= e(base_url('/index.php?page=admin_dashboard')) ?>">Back</a>
</div>

<div class="split">
  <div class="panel">
    <h3><?= $edit ? 'Update Product' : 'Add New Product' ?></h3>
    <form method="post" action="<?= e(base_url('/index.php?page=admin_products')) ?>" enctype="multipart/form-data" onsubmit="return MM.validateProductForm()">
      <input type="hidden" name="action" value="<?= $edit ? 'update' : 'add' ?>">
      <?php if ($edit): ?>
        <input type="hidden" name="id" value="<?= (int)$edit['id'] ?>">
      <?php endif; ?>

      <label>Model</label>
      <input type="text" name="model" id="p_model" value="<?= e($edit['model'] ?? '') ?>">

      <label>Brand</label>
      <input type="text" name="brand" id="p_brand" value="<?= e($edit['brand'] ?? '') ?>">

      <label>RAM (e.g., 8GB)</label>
      <input type="text" name="ram" value="<?= e($edit['ram'] ?? '') ?>">

      <label>ROM (e.g., 128GB)</label>
      <input type="text" name="rom" value="<?= e($edit['rom'] ?? '') ?>">

      <label>Price</label>
      <input type="text" name="price" id="p_price" value="<?= e($edit['price'] ?? '') ?>">

      <label>Quantity</label>
      <input type="number" name="qty" min="0" value="<?= isset($edit) && $edit && isset($edit['qty']) ? e($edit['qty']) : '' ?>">


      <label>Offer (%)</label>
      <input type="text" name="offer_percent" value="<?= e($edit['offer_percent'] ?? '0') ?>">

      <label>Product Image</label>

<?php
  // Keep previous image when updating if no new file is uploaded
  $currentImg = $edit['image'] ?? 'default-phone.png';
?>
<input type="hidden" name="old_image" value="<?= e($currentImg) ?>">

<input type="file" name="image_file" accept=".png,.jpg,.jpeg,image/png,image/jpeg">
<small>Allowed:( PNG / JPG / JPEG. )</small><br>


      <button class="btn" type="submit"><?= $edit ? 'Update' : 'Add' ?></button>
      <?php if ($edit): ?>
        <a class="btn ghost" href="<?= e(base_url('/index.php?page=admin_products')) ?>">Cancel</a>
      <?php endif; ?>
    </form>
  </div>

  <div class="panel">
    <h3>All Products</h3>
    <?php if (!$products): ?>
      <p>No products.</p>
    <?php else: ?>
      <div class="table">
        <div class="tr head">
          <div>ID</div><div>Model</div><div>Brand</div><div>Price</div><div>Qty</div><div>Action</div>
        </div>
        <?php foreach ($products as $p): ?>
          <div class="tr">
            <div><?= (int)$p['id'] ?></div>
            <div><?= e($p['model']) ?></div>
            <div><?= e($p['brand']) ?></div>
            <div>৳<?= e($p['price']) ?></div>
            <div><?= (int)$p['qty'] ?></div>
            <div class="row gap">
              <a class="btn ghost" href="<?= e(base_url('/index.php?page=admin_products&edit_id='.(int)$p['id'])) ?>">Edit</a>
              <form method="post" action="<?= e(base_url('/index.php?page=admin_products')) ?>" onsubmit="return confirm('Delete this product?')">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <button class="btn danger" type="submit">Delete</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
