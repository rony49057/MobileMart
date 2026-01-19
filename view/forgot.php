<?php $title='Forgot Password'; include __DIR__ . '/header.php'; ?>

<div class="auth-card">
  <h2>Forgot Password</h2>
  <form method="post" action="<?= e(base_url('/index.php?page=forgot')) ?>" onsubmit="return MM.validateForgot()">
    <label>Mobile Number</label>
    <input type="text" name="phone" id="fp_phone" placeholder="01XXXXXXXXX">

    <label>New Password</label>
    <input type="password" name="new_password" id="fp_new">

    <label>Confirm New Password</label>
    <input type="password" name="confirm_password" id="fp_confirm">

    <button class="btn" type="submit">Update Password</button>
  </form>

  <div class="auth-links">
    <a href="<?= e(base_url('/index.php?page=login')) ?>">Back to Login</a>
  </div>
</div>

<?php include __DIR__ . '/footer.php'; ?>
