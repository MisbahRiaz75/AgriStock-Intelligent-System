<?php
require_once('includes/load.php');
page_require_level(3);
$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sale = find_sale_by_id($id);
if (!$sale) die('Invoice not found.');
$items = find_sale_items($id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Invoice <?php echo htmlspecialchars($sale['invoice_no']); ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
    body { font-size: 0.88rem; }
    @media print { .no-print { display: none; } }
  </style>
</head>
<body class="p-4">
  <div class="no-print mb-3">
    <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="bi bi-printer me-1"></i>Print Invoice</button>
    <a href="view_sale.php?id=<?php echo $id; ?>" class="btn btn-outline-secondary btn-sm ms-2">Back</a>
  </div>

  <div class="border rounded p-4">
    <div class="d-flex justify-content-between mb-3">
      <div>
        <h4 class="fw-bold text-primary mb-0"><?php echo COMPANY_NAME; ?></h4>
        <small class="text-muted">Medicine Inventory Management System</small>
      </div>
      <div class="text-end">
        <h5 class="fw-bold mb-0">TAX INVOICE</h5>
        <div><strong><?php echo htmlspecialchars($sale['invoice_no']); ?></strong></div>
        <div class="text-muted"><?php echo date('d M Y', strtotime($sale['sale_date'])); ?></div>
      </div>
    </div>
    <hr>
    <div class="row mb-3">
      <div class="col-6">
        <strong>Customer:</strong> <?php echo htmlspecialchars($sale['customer_name']); ?>
      </div>
      <div class="col-6 text-end">
        <strong>Sold By:</strong> <?php echo htmlspecialchars($sale['sold_by']); ?>
      </div>
    </div>
    <table class="table table-bordered table-sm">
      <thead class="table-dark">
        <tr><th>#</th><th>Product</th><th>AGI Code</th><th class="text-center">Qty</th><th class="text-end">Unit Price</th><th class="text-end">GST</th><th class="text-end">Amount</th></tr>
      </thead>
      <tbody>
      <?php $n = 1; foreach ($items as $it): ?>
      <tr>
        <td><?php echo $n++; ?></td>
        <td><?php echo htmlspecialchars($it['product_name']); ?></td>
        <td><?php echo htmlspecialchars($it['agi_code']); ?></td>
        <td class="text-center"><?php echo $it['quantity']; ?></td>
        <td class="text-end"><?php echo CURRENCY . number_format($it['price'],2); ?></td>
        <td class="text-end"><?php echo CURRENCY . number_format($it['gst_amount'],2); ?></td>
        <td class="text-end"><?php echo CURRENCY . number_format($it['subtotal'],2); ?></td>
      </tr>
      <?php endforeach; ?>
      </tbody>
      <tfoot>
        <tr><td colspan="6" class="text-end fw-bold">Subtotal</td><td class="text-end"><?php echo CURRENCY . number_format($sale['total_amount'],2); ?></td></tr>
        <tr><td colspan="6" class="text-end fw-bold">GST</td><td class="text-end"><?php echo CURRENCY . number_format($sale['gst_amount'],2); ?></td></tr>
        <tr class="table-primary"><td colspan="6" class="text-end fw-bold fs-6">GRAND TOTAL</td><td class="text-end fw-bold fs-6"><?php echo CURRENCY . number_format($sale['grand_total'],2); ?></td></tr>
      </tfoot>
    </table>
    <?php if(!empty($sale['notes'])): ?>
    <p class="text-muted small mt-2"><strong>Notes:</strong> <?php echo htmlspecialchars($sale['notes']); ?></p>
    <?php endif; ?>
    <div class="text-center mt-4 text-muted small">Thank you for your business! &mdash; <?php echo COMPANY_NAME; ?></div>
  </div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php if (isset($db)) { $db->db_disconnect(); } ?>
