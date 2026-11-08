<?php
$page_title = 'Sales Report';
require_once('includes/load.php');
page_require_level(2);

$start = isset($_POST['start_date']) ? $_POST['start_date'] : date('Y-m-01');
$end   = isset($_POST['end_date'])   ? $_POST['end_date']   : date('Y-m-d');
$sales = find_sales_by_dates($start, $end);
$grand = array_sum(array_column($sales, 'grand_total'));
?>
<?php include_once('layouts/header.php'); ?>
<div class="page-header">
  <h4><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Sales Report by Date Range</h4>
</div>
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="post" class="row g-2 align-items-center">
      <div class="col-auto"><label class="form-label mb-0 fw-semibold">From:</label></div>
      <div class="col-md-2"><input type="date" name="start_date" class="form-control" value="<?php echo $start; ?>"></div>
      <div class="col-auto"><label class="form-label mb-0 fw-semibold">To:</label></div>
      <div class="col-md-2"><input type="date" name="end_date" class="form-control" value="<?php echo $end; ?>"></div>
      <div class="col-auto"><button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Generate</button></div>
    </form>
  </div>
</div>
<div class="card mb-3">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>#</th><th>Invoice</th><th>Date</th><th>Customer</th><th>Sold By</th><th class="text-end">Total</th><th class="text-end">GST</th><th class="text-end">Grand Total</th></tr></thead>
        <tbody>
        <?php if(empty($sales)): ?><tr><td colspan="8" class="text-center text-muted py-4">No sales in the selected date range.</td></tr>
        <?php else: $i=1; foreach($sales as $s): ?>
        <tr>
          <td><?php echo $i++; ?></td>
          <td><a href="view_sale.php?id=<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['invoice_no']); ?></a></td>
          <td><?php echo date('d M Y', strtotime($s['sale_date'])); ?></td>
          <td><?php echo htmlspecialchars($s['customer_name']); ?></td>
          <td><?php echo htmlspecialchars($s['sold_by']); ?></td>
          <td class="text-end"><?php echo CURRENCY . number_format($s['total_amount'],2); ?></td>
          <td class="text-end"><?php echo CURRENCY . number_format($s['gst_amount'],2); ?></td>
          <td class="text-end fw-bold"><?php echo CURRENCY . number_format($s['grand_total'],2); ?></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
        <?php if(!empty($sales)): ?>
        <tfoot><tr class="table-success fw-bold"><td colspan="7" class="text-end">Total (<?php echo count($sales); ?> invoices):</td><td class="text-end"><?php echo CURRENCY . number_format($grand,2); ?></td></tr></tfoot>
        <?php endif; ?>
      </table>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
