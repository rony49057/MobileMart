<?php
$u = current_user();
$role = $u['role'] ?? 'guest';

$cartCount = 0;
try {
    require_once __DIR__ . '/../model/Cart.php';
    $cartCount = Cart::countItems();
} catch (Exception $e) {
    $cartCount = 0;
}

$searchQ  = trim($_GET['q'] ?? '');
$brandVal = trim($_GET['brand'] ?? '');
$sortVal  = trim($_GET['sort'] ?? '');

$title = $title ?? 'Mobile Mart';

$homePage = 'home';
if ($role === 'customer') $homePage = 'customer_dashboard';
elseif ($role === 'admin') $homePage = 'admin_dashboard';
elseif ($role === 'staff') $homePage = 'staff_dashboard';

$homeUrl = base_url('/index.php' . ($homePage === 'home' ? '' : '?page=' . $homePage));

$displayName = trim($u['name'] ?? '');
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($title) ?></title>
  <link rel="stylesheet" href="<?= e(base_url('/view/css/style.css')) ?>">
  <script src="<?= e(base_url('/view/js/app.js')) ?>" defer></script>
</head>
<body>

<div class="topbar">
  <div class="brand">
    <a href="<?= e($homeUrl) ?>">📱 Mobile Mart</a>
  </div>

  <div class="topbar-right">
    <?php if ($role === 'guest'): ?>
      <a class="btn" href="<?= e(base_url('/index.php?page=login')) ?>">Login</a>
      <a class="btn" href="<?= e(base_url('/index.php?page=cart')) ?>">Cart <span id="cartCount"><?= (int)$cartCount ?></span></a>
    <?php else: ?>
      <span class="small" style="margin:0; font-weight:800;">
        <?= e($displayName !== '' ? $displayName : 'User') ?>
      </span>

      <?php if ($role === 'customer'): ?>
        <a class="btn" href="<?= e(base_url('/index.php?page=cart')) ?>">Cart <span id="cartCount"><?= (int)$cartCount ?></span></a>
      <?php endif; ?>

      <a class="btn" href="<?= e(base_url('/index.php?page=profile')) ?>">Profile</a>
      <a class="btn danger" href="<?= e(base_url('/index.php?page=logout')) ?>">Logout</a>
    <?php endif; ?>
  </div>
</div>

<?php if (isset($brands) && is_array($brands)): ?>
<div class="searchbar">
  <form method="get" action="<?= e(base_url('/index.php')) ?>" class="searchform" id="mmSearchForm">
    <?php $pageKeep = $_GET['page'] ?? $homePage; ?>
    <input type="hidden" name="page" value="<?= e($pageKeep) ?>">

    <input type="text" id="mmSearchQ" name="q" value="<?= e($searchQ) ?>" placeholder="Search model or brand" autocomplete="off">

    <select name="brand" id="mmBrand">
      <option value="">All Brands</option>
      <?php foreach ($brands as $b): $bn = is_array($b) ? ($b['brand'] ?? '') : $b; ?>
        <option value="<?= e($bn) ?>" <?= ($bn === $brandVal ? 'selected' : '') ?>><?= e($bn) ?></option>
      <?php endforeach; ?>
    </select>

    <select name="sort" id="mmSort">
      <option value="">Sort: New</option>
      <option value="price_asc" <?= ($sortVal === 'price_asc' ? 'selected' : '') ?>>Price Low→High</option>
      <option value="price_desc" <?= ($sortVal === 'price_desc' ? 'selected' : '') ?>>Price High→Low</option>
    </select>

    <button class="btn" type="submit">Search</button>
  </form>
</div>
<?php endif; ?>

<div class="container">
  <?php if ($msg = get_flash('error')): ?>
    <div class="alert error"><?= e($msg) ?></div>
  <?php endif; ?>
  <?php if ($msg = get_flash('success')): ?>
    <div class="alert success"><?= e($msg) ?></div>
  <?php endif; ?>
