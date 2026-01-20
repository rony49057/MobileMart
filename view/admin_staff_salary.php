<?php $title='Admin - Staff Salary'; include __DIR__ . '/header.php'; ?>

<h2 class="page-title">Staff Salary</h2>
<div class="row gap">
  <a class="btn" href="<?= e(base_url('/index.php?page=admin_dashboard')) ?>">Back</a>
</div>

<div class="panel">
  <h3>Pay Salary</h3>
  <form method="post" action="<?= e(base_url('/index.php?page=admin_staff_salary')) ?>" onsubmit="return MM.validateSalary()">
    <label>Staff Phone</label>
    <select name="staff_phone">
      <option value="">Select Staff</option>
      <?php foreach ($staffs as $s): ?>
        <option value="<?= e($s['phone']) ?>"><?= e($s['name']) ?> (<?= e($s['phone']) ?>)</option>
      <?php endforeach; ?>
    </select>

    <label>Month (e.g. 01-2026)</label>
    <input type="text" name="month" id="sal_month" placeholder="MM-YYYY">

    <label>Amount</label>
    <input type="text" name="amount" id="sal_amount" placeholder="amount">

    <label>Note</label>
    <input type="text" name="note" placeholder="Optional">

    <button class="btn" type="submit">Pay</button>
  </form>
</div>

<div class="panel" style="margin-top:16px;">
  <h3>Salary History</h3>

  <?php if (empty($history)): ?>
    <p>No salary history.</p>
  <?php else: ?>
    <div class="table">
      <div class="tr head">
        <div>Staff</div>
        <div>Month</div>
        <div>Amount</div>
        <div>Note</div>
        
      </div>

      <?php foreach ($history as $h): ?>
        <div class="tr">
          <div><?= e($h['staff_name'] ?? '') ?> (<?= e($h['staff_phone'] ?? '') ?>)</div>
          <div><?= e($h['month'] ?? '') ?></div>
          <div>৳<?= e($h['amount'] ?? 0) ?></div>
          <div><?= e($h['note'] ?? '') ?></div>
       
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php include __DIR__ . '/footer.php'; ?>
