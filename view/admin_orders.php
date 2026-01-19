<?php $title='Admin - Orders'; include __DIR__ . '/header.php'; ?>

<h2 class="page-title">Order Management</h2>

<div class="row gap">
  <a class="btn" href="<?= e(base_url('/index.php?page=admin_dashboard')) ?>">Back</a>
</div>

<div class="panel">
  <form method="get" action="<?= e(base_url('/index.php')) ?>" class="searchform">
    <input type="hidden" name="page" value="admin_orders">
    <input type="text" name="q" value="<?= e(trim($_GET['q'] ?? '')) ?>" placeholder="Search order id or user phone">
    <button class="btn" type="submit">Search</button>
  </form>
</div>

<?php if (!$orders): ?>
  <p>No orders.</p>
<?php else: ?>
  <?php foreach ($orders as $o): ?>
    <?php
      $cname = trim($o['user_name'] ?? '');
      $caddr = trim($o['user_address'] ?? '');
      $sname = trim($o['staff_name'] ?? '');

      if ($cname === '') $cname = 'N/A';
      if ($caddr === '') $caddr = 'N/A';
      if ($sname === '') $sname = 'N/A';
    ?>
    <div class="order-card">
      <div class="row between">
        <div>
          <b>Order #<?= (int)$o['id'] ?></b>

          <div class="small">
            Customer: <?= e($o['user_phone']) ?>
            | Name: <?= e($cname) ?>
            | Address: <?= e($caddr) ?>
            | Payment: <?= e($o['payment_method']) ?>
            | Total: ৳<?= e($o['total']) ?>
          </div>

          <div class="small">
            Staff:
            <span id="sf_<?= (int)$o['id'] ?>">
              <?php if (!empty($o['assigned_staff_phone'])): ?>
                <?= e($o['assigned_staff_phone']) ?> | <?= e($sname) ?>
              <?php else: ?>
                Not Assigned
              <?php endif; ?>
            </span>
            | Status:
            <span id="st_<?= (int)$o['id'] ?>"><?= e($o['status']) ?></span>
          </div>

        </div>

        <div class="small"><?= e($o['created_at']) ?></div>
      </div>

      <div class="row gap wrap" style="margin-top:10px;">
        <div>
          <select id="staff_<?= (int)$o['id'] ?>">
            <option value="">Select Staff</option>
            <?php foreach ($staffs as $s): ?>
              <option value="<?= e($s['phone']) ?>"><?= e($s['name']) ?> (<?= e($s['phone']) ?>)</option>
            <?php endforeach; ?>
          </select>
          <button class="btn" type="button" onclick="MM.assignStaff(<?= (int)$o['id'] ?>)">Assign</button>
        </div>

        <div>
          <select id="status_<?= (int)$o['id'] ?>">
            <?php $cur = $o['status']; ?>
            <?php foreach (['Pending','Processing','On the Way','Delivered','Cancelled'] as $st): ?>
              <option value="<?= e($st) ?>" <?= ($st===$cur?'selected':'') ?>><?= e($st) ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn" type="button" onclick="MM.updateOrderStatus(<?= (int)$o['id'] ?>)">Update Status</button>
        </div>

        <div class="small" id="orderMsg_<?= (int)$o['id'] ?>"></div>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
