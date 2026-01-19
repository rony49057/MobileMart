<?php $title='My Orders'; include __DIR__ . '/header.php'; $u=current_user(); ?>

<div style="margin: 6px 0 12px 0; text-align: right;">
  <button class="btn" type="button" onclick="window.history.back()">Back</button>
</div>

<h2 class="page-title">My Orders</h2>

<?php if (!$orders): ?>
  <p>No orders yet.</p>
<?php else: ?>
  <?php foreach ($orders as $o): ?>
    <div class="order-card">
      <div class="row between">
        <div>
          <b>Order #<?= (int)$o['id'] ?></b>
          <div class="small">Date: <?= e($o['created_at']) ?></div>
        </div>
        <div>
          <span class="badge"><?= e($o['status']) ?></span>
          <?php if ($o['assigned_staff_phone']): ?>
            <span class="badge">Delivery: <?= e($o['assigned_staff_phone']) ?></span>
          <?php endif; ?>
        </div>
      </div>
      <div class="row between">
        <div class="small">Payment: <?= e($o['payment_method']) ?></div>
        <div><b>Total: ৳<?= e($o['total']) ?></b></div>
      </div>

      <?php
        require_once __DIR__ . '/../model/Order.php';
        $items = Order::items((int)$o['id']);
      ?>
      <div class="items">
        <?php foreach ($items as $it): ?>
          <div class="itemrow">
            <img src="<?= e(base_url('/view/images/'.$it['image'])) ?>" alt="img">
            <div>
              <div><?= e($it['model']) ?> (<?= e($it['brand']) ?>)</div>
              <div class="small">Qty: <?= (int)$it['qty'] ?> | Price: ৳<?= e($it['price']) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
