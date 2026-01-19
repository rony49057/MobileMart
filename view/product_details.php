<?php
$title='Product Details';
include __DIR__ . '/header.php';
$u = current_user();
$role = $u['role'] ?? 'guest';
?>

<div style="margin: 6px 0 12px 0; text-align: right;">
  <button class="btn" type="button" onclick="window.history.back()">Back</button>
</div>

<div class="details">
  <div class="details-img">
    <img src="<?= e(base_url('/view/images/'.$product['image'])) ?>" alt="phone">
  </div>
  <div class="details-info">
    <h2><?= e($product['model']) ?></h2>
    <p><b>Brand:</b> <?= e($product['brand']) ?></p>
    <p><b>RAM:</b> <?= e($product['ram']) ?> | <b>ROM:</b> <?= e($product['rom']) ?></p>
    <p><b>Price:</b> ৳<?= e($product['price']) ?> <?php if ((int)$product['offer_percent']>0): ?><span class="badge"><?= (int)$product['offer_percent'] ?>% OFF</span><?php endif; ?></p>
    <p><b>Stock:</b> <?= (int)$product['qty'] ?></p>

    <?php if ($role === 'customer' || $role === 'guest'): ?>
      <div class="actions">
        <input class="qty" type="number" id="d_qty" value="1" min="1">
        <button class="btn" onclick="MM.addToCart(<?= (int)$product['id'] ?>, document.getElementById('d_qty').value)">Add to Cart</button>
        <a class="btn ghost" href="<?= e(base_url('/index.php?page=cart')) ?>">Go Cart</a>
      </div>
      <div class="small" id="msg_<?= (int)$product['id'] ?>"></div>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
