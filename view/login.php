<?php $title='Login'; include __DIR__ . '/header.php'; ?>

<div class="auth-card">
  <h2>Login</h2>
  <form method="post" action="<?= e(base_url('/index.php?page=login')) ?>" onsubmit="return MM.validateLogin()">
    <label>Mobile Number</label>
    <input type="text" name="phone" id="login_phone" placeholder="01XXXXXXXXX">

    <label>Password</label>
    <input type="password" name="password" id="login_pass" placeholder="password">

    <button class="btn full" type="submit">Login</button>

    <div class="small-links">
      <a href="<?= e(base_url('/index.php?page=forgot')) ?>">Forgot Password?</a>
      <span> | </span>
      <a href="<?= e(base_url('/index.php?page=register')) ?>">No account? Register</a>
    </div>

    <div id="login_msg" class="small"></div>
  </form>
</div>

<?php include __DIR__ . '/footer.php'; ?>
