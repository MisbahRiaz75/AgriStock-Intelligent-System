<?php
$page_title = 'Sales';
require_once('includes/load.php');
page_require_level(3);
$user = current_user();

// Only admin/manager see all sales; staff see their own
if ($user['user_level'] <= 2) {
    $all_sales = find_all_sales();
} else {
    $all_sales = find_by_sql("SELECT s.*, u.name AS sold_by FROM sales s LEFT JOIN users u ON u.id=s.user_id WHERE s.user_id='{$user['id']}' ORDER BY s.sale_date DESC");
}
?>
<?php include_once('layouts/header.php'); ?>

<div class="page-header">
  <h4><i class="bi bi-receipt me-2 text-primary"></i>Sales Invoices</h4>
  <a href="add_sale.php" class="btn btn-primary btn-sm"><i class="bi bi-cart-plus me-1"></i>New Sale</a>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Invoice No</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Sold By</th>
            <th class="text-end">Subtotal</th>
            <th class="text-end">GST</th>
            <th class="text-end">Grand Total</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($all_sales)): ?>
          <tr><td colspan="9" class="text-center py-4 text-muted">No sales records found.</td></tr>
        <?php else: ?>
        <?php $i=1; foreach ($all_sales as $s): ?>
        <tr>
          <td><?php echo $i++; ?></td>
          <td><span class="fw-semibold text-primary"><?php echo htmlspecialchars($s['invoice_no']); ?></span></td>
          <td><?php echo date('d M Y, h:i A', strtotime($s['sale_date'])); ?></td>
          <td><?php echo htmlspecialchars($s['customer_name']); ?></td>
          <td><?php echo htmlspecialchars($s['sold_by']); ?></td>
          <td class="text-end"><?php echo CURRENCY . number_format($s['total_amount'], 2); ?></td>
          <td class="text-end text-muted"><?php echo CURRENCY . number_format($s['gst_amount'], 2); ?></td>
          <td class="text-end text-success fw-bold"><?php echo CURRENCY . number_format($s['grand_total'], 2); ?></td>
          <td class="text-center">
            <div class="btn-group btn-group-sm">
              <a href="view_sale.php?id=<?php echo $s['id']; ?>" class="btn btn-outline-info" title="View"><i class="bi bi-eye"></i></a>
              <a href="print_invoice.php?id=<?php echo $s['id']; ?>" class="btn btn-outline-secondary" target="_blank" title="Print"><i class="bi bi-printer"></i></a>
              <?php if ($user['user_level'] == 1): ?>
              <a href="delete_sale.php?id=<?php echo $s['id']; ?>" class="btn btn-outline-danger" title="Delete"
                 onclick="return confirm('Delete this invoice? Stock will be restored.')"><i class="bi bi-trash"></i></a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div class="card-footer text-muted small"><?php echo count($all_sales); ?> invoice(s) found.</div>
</div>

<?php include_once('layouts/footer.php'); ?>
