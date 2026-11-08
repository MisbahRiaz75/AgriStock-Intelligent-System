<?php
$page_title = 'Daily Sales';
require_once('includes/load.php');
page_require_level(2);

$selected_date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$sales = find_sales_by_dates($selected_date, $selected_date);
$total = array_sum(array_column($sales, 'grand_total'));
?>
<?php include_once('layouts/header.php'); ?>
<div class="page-header">
  <h4><i class="bi bi-calendar-day me-2 text-primary"></i>Daily Sales</h4>
</div>
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="get" class="row g-2 align-items-center">
      <div class="col-auto"><label class="form-label mb-0 fw-semibold">Date:</label></div>
      <div class="col-md-2"><input type="date" name="date" class="form-control" value="<?php echo $selected_date; ?>"></div>
      <div class="col-auto"><button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>View</button></div>
      <div class="col-auto"><a href="daily_sales.php?date=<?php echo date('Y-m-d'); ?>" class="btn btn-outline-secondary">Today</a></div>
    </form>
  </div>
</div>
<div class="row g-3 mb-3">
  <div class="col-md-3">
    <div class="card stat-card p-3 text-center">
      <div class="text-muted small"><?php echo date('d F Y', strtotime($selected_date)); ?></div>
      <div class="fs-3 fw-bold text-primary"><?php echo count($sales); ?></div>
      <div class="text-muted small">Invoices</div>
    </div>
  </div>
  <div class="col-md-3">
    <div class="card stat-card p-3 text-center">
      <div class="text-muted small">Total Revenue</div>
      <div class="fs-4 fw-bold text-success"><?php echo CURRENCY . number_format($total,2); ?></div>
    </div>
  </div>
</div>
<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>#</th><th>Invoice</th><th>Time</th><th>Customer</th><th>Sold By</th><th class="text-end">Grand Total</th><th></th></tr></thead>
        <tbody>
        <?php if(empty($sales)): ?><tr><td colspan="7" class="text-center text-muted py-4">No sales on this date.</td></tr>
        <?php else: $i=1; foreach($sales as $s): ?>
        <tr>
          <td><?php echo $i++; ?></td>
          <td><a href="view_sale.php?id=<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['invoice_no']); ?></a></td>
          <td><?php echo date('h:i A', strtotime($s['sale_date'])); ?></td>
          <td><?php echo htmlspecialchars($s['customer_name']); ?></td>
          <td><?php echo htmlspecialchars($s['sold_by']); ?></td>
          <td class="text-end fw-bold"><?php echo CURRENCY . number_format($s['grand_total'],2); ?></td>
          <td><a href="print_invoice.php?id=<?php echo $s['id']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-printer"></i></a></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
