<?php
$page_title = 'View Invoice';
require_once('includes/load.php');
page_require_level(3);

$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sale = find_sale_by_id($id);
if (!$sale) { $session->msg('d','Invoice not found.'); redirect('sales.php',false); }
$items = find_sale_items($id);
?>
<?php include_once('layouts/header.php'); ?>

<div class="page-header">
  <h4><i class="bi bi-file-earmark-text me-2 text-primary"></i>Invoice: <?php echo htmlspecialchars($sale['invoice_no']); ?></h4>
  <div class="d-flex gap-2">
    <a href="print_invoice.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary btn-sm" target="_blank"><i class="bi bi-printer me-1"></i>Print</a>
    <a href="sales.php" class="btn btn-outline-primary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
  </div>
</div>

<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <!-- Header -->
        <div class="d-flex justify-content-between mb-4">
          <div>
            <h5 class="fw-bold text-primary"><?php echo COMPANY_NAME; ?></h5>
            <p class="text-muted mb-0">Medicine Inventory Management System</p>
          </div>
          <div class="text-end">
            <h5 class="fw-bold">INVOICE</h5>
            <p class="mb-0"><strong>#<?php echo htmlspecialchars($sale['invoice_no']); ?></strong></p>
            <p class="text-muted mb-0"><?php echo date('d M Y, h:i A', strtotime($sale['sale_date'])); ?></p>
          </div>
        </div>
        <hr>
        <div class="row mb-4">
          <div class="col-md-6">
            <strong>Customer:</strong> <?php echo htmlspecialchars($sale['customer_name']); ?><br>
          </div>
          <div class="col-md-6 text-end">
            <strong>Sold By:</strong> <?php echo htmlspecialchars($sale['sold_by']); ?>
          </div>
        </div>
        <!-- Items -->
        <table class="table table-bordered">
          <thead class="table-primary">
            <tr><th>#</th><th>Product</th><th>AGI</th><th>Qty</th><th>Price</th><th>GST</th><th class="text-end">Total</th></tr>
          </thead>
          <tbody>
          <?php $n=1; foreach ($items as $it): ?>
          <tr>
            <td><?php echo $n++; ?></td>
            <td><?php echo htmlspecialchars($it['product_name']); ?></td>
            <td><code><?php echo $it['agi_code']; ?></code></td>
            <td><?php echo $it['quantity']; ?></td>
            <td><?php echo CURRENCY . number_format($it['price'],2); ?></td>
            <td><?php echo CURRENCY . number_format($it['gst_amount'],2); ?></td>
            <td class="text-end"><?php echo CURRENCY . number_format($it['subtotal'],2); ?></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr><td colspan="6" class="text-end fw-semibold">Subtotal:</td><td class="text-end"><?php echo CURRENCY . number_format($sale['total_amount'],2); ?></td></tr>
            <tr><td colspan="6" class="text-end fw-semibold">GST:</td><td class="text-end"><?php echo CURRENCY . number_format($sale['gst_amount'],2); ?></td></tr>
            <tr class="table-success"><td colspan="6" class="text-end fw-bold fs-5">Grand Total:</td><td class="text-end fw-bold fs-5"><?php echo CURRENCY . number_format($sale['grand_total'],2); ?></td></tr>
          </tfoot>
        </table>
        <?php if(!empty($sale['notes'])): ?>
        <p class="text-muted small"><strong>Notes:</strong> <?php echo htmlspecialchars($sale['notes']); ?></p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
