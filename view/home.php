<?php
$title = 'Mobile Mart - Home';
include __DIR__ . '/header.php';
?>

<div class="note">
  <b>Guest:</b> You can add products to cart without login. <b>Order confirm</b> needs login.
</div>

<h2 class="page-title">All Phones</h2>

<div id="productGridMsg"></div>

<div id="productGridWrap">
  <?php if (!$products): ?>
    <p>No products found.</p>
  <?php else: ?>
    <?php include __DIR__ . '/product_grid.php'; ?>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
