<?php $title='Admin Dashboard'; include __DIR__ . '/header.php'; ?>

<div class="row between">
  <h2 class="page-title">Admin Dashboard</h2>
  <div class="row gap">
    <div class="stats">Total Sell: ৳<?= e($totalSell) ?></div>
    <div class="stats">Salary Paid: ৳<?= e($salaryPaid) ?></div>
    <div class="stats">Net Sell: ৳<?= e($netSell) ?></div>
  </div>
</div>


<div class="row gap">
  <a class="btn" href="<?= e(base_url('/index.php?page=admin_products')) ?>">Product Management</a>
  <a class="btn" href="<?= e(base_url('/index.php?page=admin_orders')) ?>">Order Management</a>
  <a class="btn" href="<?= e(base_url('/index.php?page=admin_users')) ?>">User Management</a>
  <a class="btn" href="<?= e(base_url('/index.php?page=admin_staff_salary')) ?>">Staff Salary</a>
</div>

<h3>Product List (view)</h3>

<div id="productGridMsg"></div>

<div id="productGridWrap">
  <?php if ($products): ?>
    <?php include __DIR__.'/product_grid.php'; ?>
  <?php else: ?>
    <p>No products.</p>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
