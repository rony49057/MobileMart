<?php $title='Change Password'; include __DIR__ . '/header.php'; ?>

<div class="auth-card">
  <h2>Change Password</h2>
  <form method="post" action="<?= e(base_url('/index.php?page=change_password')) ?>" onsubmit="return MM.validateChangePassword()">
    <label>Old Password</label>
    <input type="password" name="old_password" id="cp_old">

    <label>New Password</label>
    <input type="password" name="new_password" id="cp_new">

    <label>Confirm New Password</label>
    <input type="password" name="confirm_password" id="cp_confirm">

    <button class="btn" type="submit">Update Password</button>
  </form>
  <div class="mt">
    <a class="link" href="<?= e(base_url('/index.php?page=profile')) ?>">Back</a>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
