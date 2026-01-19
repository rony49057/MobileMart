<?php $title='Cart'; include __DIR__ . '/header.php'; ?>

<div id="cartMsg" class="small"></div>

<div style="margin: 6px 0 12px 0; text-align: right;">
  <button class="btn" type="button" onclick="window.history.back()">Back</button>

  <?php if (current_user()): ?>
  <a class="btn orders" href="<?= e(base_url('/index.php?page=my_orders')) ?>">My Orders</a>


  <?php endif; ?>
</div>

<h2 class="page-title">Your Cart</h2>

<?php if (!$items): ?>
  <p>Cart is empty.</p>
<?php else: ?>
  <div class="table">
    <div class="tr head">
      <div>Product</div>
      <div>Price</div>
      <div>Qty</div>
      <div>Subtotal</div>
      <div>Action</div>
    </div>

    <?php $total=0; foreach ($items as $it):
      $price = (float)$it['price'];
      $offer = (int)$it['offer_percent'];
      if ($offer>0) $price = $price - ($price * ($offer/100));
      $sub = $price * (int)$it['cart_qty'];
      $total += $sub;
    ?>
      <div class="tr">
        <div class="prodcell">
          <img class="thumb" src="<?= e(base_url('/view/images/'.$it['image'])) ?>" alt="img">
          <div>
            <div><b><?= e($it['model']) ?></b></div>
            <div class="small"><?= e($it['brand']) ?> | RAM: <?= e($it['ram']) ?> | ROM: <?= e($it['rom']) ?></div>
            <div class="small">Stock: <?= (int)$it['qty'] ?></div>
          </div>
        </div>
        <div>৳<?= e(number_format($price,2)) ?></div>
        <div>
          <input class="qty" type="number" min="1" value="<?= (int)$it['cart_qty'] ?>"
            onchange="MM.updateCartQty(<?= (int)$it['cart_id'] ?>, this.value)">
        </div>
        <div>৳<?= e(number_format($sub,2)) ?></div>
        <div>
          <button class="btn danger" onclick="MM.removeFromCart(<?= (int)$it['cart_id'] ?>)">Remove</button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

  <div class="totalbox">
    <div><b>Total:</b> ৳<?= e(number_format($total,2)) ?></div>
  </div>

  <?php if (!current_user()): ?>
    <div class="note">
      <b>To confirm order:</b> Please login first.
      <a class="btn" href="<?= e(base_url('/index.php?page=login')) ?>">Login</a>
    </div>
  <?php else: ?>
    <div class="checkout">
      <form method="post" action="<?= e(base_url('/index.php?page=confirm_order')) ?>" onsubmit="return MM.validateCheckout()">
        <label>Payment Method</label>
        <select name="payment_method" id="pay_method">
          <option value="cash">Cash</option>
          <option value="card">Card</option>
        </select>
        <button class="btn" type="submit">Confirm Order</button>
      </form>
    </div>
  <?php endif; ?>

<?php endif; ?>

<?php include __DIR__ . '/footer.php'; ?>
