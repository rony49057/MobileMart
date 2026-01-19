<?php
$title='Admin - User Management';
include __DIR__ . '/header.php';

$old = $_SESSION['old_add_staff'] ?? [];
unset($_SESSION['old_add_staff']);
?>

<h2 class="page-title">User Management</h2>
<div class="row gap">
  <a class="btn" href="<?= e(base_url('/index.php?page=admin_dashboard')) ?>">Back</a>
</div>

<div class="split">
  <div class="panel">
    <h3>Add New Staff</h3>
    <form method="post" action="<?= e(base_url('/index.php?page=admin_users')) ?>" autocomplete="off" onsubmit="return MM.validateAddStaff()">
      <input type="hidden" name="action" value="add_staff">

      <label>Name</label>
      <input type="text" name="name" id="st_name" autocomplete="off" value="<?= e($old['name'] ?? '') ?>">

      <label>Mobile Number</label>
      <input type="text" name="phone" id="st_phone" autocomplete="off" value="<?= e($old['phone'] ?? '') ?>">

      <label>Gender</label>
      <select name="gender" id="st_gender" autocomplete="off">
        <option value="">Select</option>
        <option value="Male" <?= (($old['gender'] ?? '')==='Male'?'selected':'') ?>>Male</option>
        <option value="Female" <?= (($old['gender'] ?? '')==='Female'?'selected':'') ?>>Female</option>
        <option value="Other" <?= (($old['gender'] ?? '')==='Other'?'selected':'') ?>>Other</option>
      </select>

      <label>DOB</label>
      <input type="date" name="dob" id="st_dob" autocomplete="off" value="<?= e($old['dob'] ?? '') ?>">

      <label>Address</label>
      <input type="text" name="address" id="st_address" autocomplete="off" value="<?= e($old['address'] ?? '') ?>">

      <label>Password</label>
      <input type="password" name="password" id="st_pass" autocomplete="new-password" value="">

      <label>Confirm Password</label>
      <input type="password" name="confirm" id="st_cpass" autocomplete="new-password" value="">

      <button class="btn" type="submit">Add Staff</button>
      <div class="small" id="as_msg"></div>
    </form>
  </div>

  <div class="panel">
    <h3>Customers</h3>
    <div class="list">
      <?php if (!$customers): ?>
        <p>No customers.</p>
      <?php else: ?>
        <?php foreach ($customers as $c): ?>
          <div class="item">
            <div>
              <b><?= e($c['name']) ?></b> (<?= e($c['phone']) ?>)
              <div class="small"><?= e($c['gender']) ?> | <?= e($c['dob']) ?></div>
              <div class="small">Address: <?= e($c['address']) ?></div>
              <form method="post" action="<?= e(base_url('/index.php?page=admin_users')) ?>" class="inline" autocomplete="off">
                <input type="hidden" name="action" value="update_user">
                <input type="hidden" name="phone" value="<?= e($c['phone']) ?>">
                <input type="text" name="name" value="<?= e($c['name']) ?>" placeholder="Name">
                <input type="text" name="gender" value="<?= e($c['gender']) ?>" placeholder="Gender">
                <input type="date" name="dob" value="<?= e($c['dob']) ?>">
                <input type="text" name="address" value="<?= e($c['address']) ?>" placeholder="Address">
                <button class="btn ghost" type="submit">Update</button>
              </form>
            </div>
            <div class="row gap">
              <form method="post" action="<?= e(base_url('/index.php?page=admin_users')) ?>" class="inline" autocomplete="off">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="phone" value="<?= e($c['phone']) ?>">
                <input type="password" name="new_password" placeholder="New pass" value="">
                <button class="btn" type="submit">Reset</button>
              </form>
              <form method="post" action="<?= e(base_url('/index.php?page=admin_users')) ?>" class="inline" onsubmit="return confirm('Delete this user?')" autocomplete="off">
                <input type="hidden" name="action" value="delete_user">
                <input type="hidden" name="phone" value="<?= e($c['phone']) ?>">
                <button class="btn danger" type="submit">Delete</button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="panel" style="margin-top:16px;">
  <h3>Staff List</h3>
  <div class="list">
    <?php if (!$staffs): ?>
      <p>No staff.</p>
    <?php else: ?>
      <?php foreach ($staffs as $s): ?>
        <div class="item">
          <div>
            <b><?= e($s['name']) ?></b> (<?= e($s['phone']) ?>)
            <div class="small"><?= e($s['gender']) ?> | <?= e($s['dob']) ?></div>
            <div class="small">Address: <?= e($s['address']) ?></div>
            <form method="post" action="<?= e(base_url('/index.php?page=admin_users')) ?>" class="inline" autocomplete="off">
              <input type="hidden" name="action" value="update_user">
              <input type="hidden" name="phone" value="<?= e($s['phone']) ?>">
              <input type="text" name="name" value="<?= e($s['name']) ?>" placeholder="Name">
              <input type="text" name="gender" value="<?= e($s['gender']) ?>" placeholder="Gender">
              <input type="date" name="dob" value="<?= e($s['dob']) ?>">
              <input type="text" name="address" value="<?= e($s['address']) ?>" placeholder="Address">
              <button class="btn ghost" type="submit">Update</button>
            </form>
          </div>
          <div class="row gap">
            <form method="post" action="<?= e(base_url('/index.php?page=admin_users')) ?>" class="inline" autocomplete="off">
              <input type="hidden" name="action" value="reset_password">
              <input type="hidden" name="phone" value="<?= e($s['phone']) ?>">
              <input type="password" name="new_password" placeholder="New pass" value="">
              <button class="btn" type="submit">Reset</button>
            </form>
            <form method="post" action="<?= e(base_url('/index.php?page=admin_users')) ?>" class="inline" onsubmit="return confirm('Delete this staff?')" autocomplete="off">
              <input type="hidden" name="action" value="delete_user">
              <input type="hidden" name="phone" value="<?= e($s['phone']) ?>">
              <button class="btn danger" type="submit">Delete</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
