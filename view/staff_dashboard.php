<?php $title='Staff Dashboard'; include __DIR__ . '/header.php'; $u=current_user(); ?>

<div class="row between">
  <h2 class="page-title">Staff Dashboard</h2>
  <div class="small">Logged in: <?= e($u['name']) ?> (<?= e($u['phone']) ?>)</div>
</div>

<div class="split">
  <div>
    <h3>Assigned Orders</h3>
    <div class="panel">
      <form method="get" action="<?= e(base_url('/index.php')) ?>" class="searchform">
        <input type="hidden" name="page" value="staff_dashboard">
        <input type="text" name="oq" value="<?= e(trim($_GET['oq'] ?? '')) ?>" placeholder="Search order id / user phone">
        <button class="btn" type="submit">Search</button>
      </form>

      <?php if (!$orders): ?>
        <p>No assigned orders.</p>
      <?php else: ?>
        <?php foreach ($orders as $o): ?>
          <div class="order-mini">
            <div><b>Order #<?= (int)$o['id'] ?></b> | User: <?= e($o['user_phone']) ?> | Total: ৳<?= e($o['total']) ?></div>
            <div class="row gap">
              <div class="small">Status: <span id="st_<?= (int)$o['id'] ?>"><?= e($o['status']) ?></span></div>
              <select id="status_<?= (int)$o['id'] ?>">
                <?php foreach (['Pending','Processing','On the Way','Delivered','Cancelled'] as $st): ?>
                  <option value="<?= e($st) ?>" <?= ($st===$o['status']?'selected':'') ?>><?= e($st) ?></option>
                <?php endforeach; ?>
              </select>
              <button class="btn" onclick="MM.updateOrderStatus(<?= (int)$o['id'] ?>)">Update</button>
            </div>
            <div class="small" id="orderMsg_<?= (int)$o['id'] ?>"></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <h3 class="mt">Product Stock View</h3>

    <div id="productGridMsg"></div>

    <div id="productGridWrap">
      <?php if (!$products): ?>
        <p>No products.</p>
      <?php else: ?>
        <?php include __DIR__ . '/product_grid.php'; ?>
      <?php endif; ?>
    </div>
  </div>

  <div>
    <h3>Salary History</h3>
    <div class="panel">
      <?php if (!$salary): ?>
        <p>No salary paid yet.</p>
      <?php else: ?>
        <?php foreach ($salary as $s): ?>
          <div class="salary-item">
            <b><?= e($s['month']) ?></b> - ৳<?= e($s['amount']) ?>
            <div class="small"><?= e($s['note']) ?> | <?= e($s['created_at']) ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
