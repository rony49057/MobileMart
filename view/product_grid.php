<?php
// expects: $products array
$u = current_user();
$role = $u['role'] ?? 'guest';
?>
<div class="grid">
  <?php foreach ($products as $p): ?>
    <div class="card">
      <a class="imgwrap" href="<?= e(base_url('/index.php?page=product&id='.(int)$p['id'])) ?>">
        <img src="<?= e(base_url('/view/images/'.$p['image'])) ?>" alt="phone">
      </a>
      <div class="card-body">
        <div class="title"><?= e($p['model']) ?></div>
        <div class="meta">Brand: <?= e($p['brand']) ?></div>
        <div class="meta">RAM: <?= e($p['ram']) ?> | ROM: <?= e($p['rom']) ?></div>
        <div class="meta">Stock: <?= (int)$p['qty'] ?></div>
        <div class="price">
          ৳<?= e($p['price']) ?>
          <?php if ((int)$p['offer_percent'] > 0): ?>
            <span class="badge"><?= (int)$p['offer_percent'] ?>% OFF</span>
          <?php endif; ?>
        </div>

        <div class="actions">
          <?php if ($role === 'customer' || $role === 'guest'): ?>
            <button class="btn" onclick="MM.addToCart(<?= (int)$p['id'] ?>, 1)">Add to Cart</button>
          <?php endif; ?>
          <a class="btn ghost" href="<?= e(base_url('/index.php?page=product&id='.(int)$p['id'])) ?>">Details</a>
        </div>

        <div class="small" id="msg_<?= (int)$p['id'] ?>"></div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
