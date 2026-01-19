<?php
$title='Registration';
include __DIR__ . '/header.php';

$old = $_SESSION['old_register'] ?? [];
unset($_SESSION['old_register']);
?>

<div class="auth-card">
  <h2>Registration</h2>
  <form method="post" action="<?= e(base_url('/index.php?page=register')) ?>" onsubmit="return MM.validateRegister()" autocomplete="off">
    <label>Name</label>
    <input type="text" name="name" id="reg_name" autocomplete="off" value="<?= e($old['name'] ?? '') ?>" placeholder="Your name">

    <label>Mobile Number (Unique)</label>
    <input type="text" name="phone" id="reg_phone" autocomplete="off" value="<?= e($old['phone'] ?? '') ?>" placeholder="01XXXXXXXXX">

    <label>Gender</label>
    <select name="gender" id="reg_gender" autocomplete="off">
      <option value="">Select</option>
      <option value="Male" <?= (($old['gender'] ?? '')==='Male'?'selected':'') ?>>Male</option>
      <option value="Female" <?= (($old['gender'] ?? '')==='Female'?'selected':'') ?>>Female</option>
      <option value="Other" <?= (($old['gender'] ?? '')==='Other'?'selected':'') ?>>Other</option>
    </select>

    <label>DOB</label>
    <input type="date" name="dob" id="reg_dob" autocomplete="off" value="<?= e($old['dob'] ?? '') ?>">

    <label>Address</label>
    <input type="text" name="address" id="reg_address" autocomplete="off" value="<?= e($old['address'] ?? '') ?>" placeholder="Address">

    <label>Role</label>
    <select name="role" id="reg_role" autocomplete="off">
      <option value="customer" <?= (($old['role'] ?? 'customer')==='customer'?'selected':'') ?>>Customer</option>
      <option value="staff" <?= (($old['role'] ?? '')==='staff'?'selected':'') ?>>Staff</option>
      <option value="admin" <?= (($old['role'] ?? '')==='admin'?'selected':'') ?>>Admin</option>
    </select>
    

    <label>Password</label>
    <input type="password" name="password" id="reg_pass" autocomplete="new-password" value="">

    <label>Confirm Password</label>
    <input type="password" name="confirm" id="reg_confirm" autocomplete="new-password" value="">

    <button class="btn full" type="submit">Register</button>

    <div class="links">
      <a href="<?= e(base_url('/index.php?page=login')) ?>">Already have account? Login</a>
    </div>
  </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
