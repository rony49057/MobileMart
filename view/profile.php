<?php
$title='Profile';
include __DIR__ . '/header.php';
$u = current_user();

$role = $u['role'] ?? 'guest';
$defaultBack = base_url('/index.php');
if ($role === 'customer') $defaultBack = base_url('/index.php?page=customer_dashboard');
elseif ($role === 'admin') $defaultBack = base_url('/index.php?page=admin_dashboard');
elseif ($role === 'staff') $defaultBack = base_url('/index.php?page=staff_dashboard');

$backUrl = $_SERVER['HTTP_REFERER'] ?? $defaultBack;
?>

<div class="row between">
  <h2 class="page-title">Profile</h2>
  <div>
    <a class="btn" href="<?= e(base_url('/index.php?page=change_password')) ?>">Change Password</a>
    
  </div>
</div>

<div class="auth-card">
  <h3>View + Update Profile</h3>
  <div class="small">Role: <b><?= e($user['role'] ?? '') ?></b> | Phone: <b><?= e($user['phone'] ?? '') ?></b></div>

  <form method="post" action="<?= e(base_url('/index.php?page=profile')) ?>" onsubmit="return MM.validateProfile()">
    <input type="hidden" name="redirect" value="<?= e($backUrl) ?>">

    <label>Name</label>
    <input type="text" name="name" id="pr_name" value="<?= e($user['name'] ?? '') ?>">

    <label>Gender</label>
    <select name="gender" id="pr_gender">
      <option value="">Select</option>
      <option value="Male" <?= (($user['gender'] ?? '')==='Male'?'selected':'') ?>>Male</option>
      <option value="Female" <?= (($user['gender'] ?? '')==='Female'?'selected':'') ?>>Female</option>
      <option value="Other" <?= (($user['gender'] ?? '')==='Other'?'selected':'') ?>>Other</option>
    </select>

    <label>Date of Birth</label>
    <input type="date" name="dob" id="pr_dob" value="<?= e($user['dob'] ?? '') ?>">

    <label>Address</label>
    <input type="text" name="address" id="pr_address" value="<?= e($user['address'] ?? '') ?>" placeholder="Dhaka, ...">

    <div class="row gap" style="margin-top:12px;">
      <button class="btn" type="submit">Update Profile</button>
      <a class="btn ghost" href="<?= e($backUrl) ?>">Back</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
