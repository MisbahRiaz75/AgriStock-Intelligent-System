<?php
$page_title = 'Expiry Alerts';
require_once('includes/load.php');
page_require_level(3);

$expired      = find_expired_products();
$expiring_30  = find_expiring_soon_products(30);
$expiring_60  = find_expiring_soon_products(60);
// Remove 30-day ones from 60-day list to avoid duplicates
$expiring_31_60 = array_filter($expiring_60, function($p) use ($expiring_30) {
    $ids = array_column($expiring_30, 'id');
    return !in_array($p['id'], $ids);
});
?>
<?php include_once('layouts/header.php'); ?>

<div class="page-header">
  <h4><i class="bi bi-exclamation-triangle me-2 text-warning"></i>Expiry Alert System</h4>
</div>

<!-- Summary cards -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card border-success stat-card p-3 text-center">
      <div class="fs-1 text-success fw-bold"><?php echo count($expired); ?></div>
      <div class="fw-semibold text-success"><i class="bi bi-x-circle me-1 text-danger"></i>Expired Products</div>
      <div class="text-muted small">Expiry date has passed</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-success stat-card p-3 text-center">
      <div class="fs-1 text-success fw-bold"><?php echo count($expiring_30); ?></div>
      <div class="fw-semibold text-success"><i class="bi bi-clock me-1" style="color:#fd7e14"></i>Expiring in Next 30 Days</div>
      <div class="text-muted small">Requires immediate attention</div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-success stat-card p-3 text-center">
      <div class="fs-1 text-success fw-bold"><?php echo count($expiring_31_60); ?></div>
      <div class="fw-semibold text-success"><i class="bi bi-calendar-range me-1" style="color:#fd7e14"></i>Expiring in 31-60 Days</div>
      <div class="text-muted small">Plan ahead</div>
    </div>
  </div>
</div>

<!-- Expired Products -->
<div class="card mb-4" id="expired">
  <div class="card-header bg-white text-success fw-semibold">
    <i class="bi bi-x-circle me-2 text-danger"></i>Expired Products (<?php echo count($expired); ?>)
    <span class="badge bg-white text-danger ms-2">SALE BLOCKED</span>
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>Product</th><th>AGI Code</th><th>Category</th><th>Batch</th><th>Expiry Date</th><th>Stock</th><th>Action</th></tr></thead>
      <tbody>
      <?php if (empty($expired)): ?>
        <tr><td colspan="7" class="text-center text-success p-3"><i class="bi bi-check-circle me-2"></i>No expired products.</td></tr>
      <?php else: ?>
      <?php foreach ($expired as $p): ?>
      <tr class="table-danger">
        <td><?php echo htmlspecialchars($p['product_name']); ?></td>
        <td><code><?php echo $p['agi_code']; ?></code></td>
        <td><?php echo htmlspecialchars($p['category_name']); ?></td>
        <td><?php echo htmlspecialchars($p['batch_number']); ?></td>
        <td><span class="badge badge-expired"><?php echo date('d M Y', strtotime($p['expiry_date'])); ?></span></td>
        <td><?php echo $p['stock_quantity']; ?></td>
        <td><a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a></td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Expiring Soon (30 days) -->
<div class="card mb-4" id="soon">
  <div class="card-header bg-white text-success fw-semibold">
    <i class="bi bi-clock-history me-2" style="color:#fd7e14;"></i>Expiring in Next 30 Days (<?php echo count($expiring_30); ?>)
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>Product</th><th>AGI Code</th><th>Category</th><th>Batch</th><th>Expiry Date</th><th>Days Left</th><th>Stock</th><th>Action</th></tr></thead>
      <tbody>
      <?php if (empty($expiring_30)): ?>
        <tr><td colspan="8" class="text-center text-success p-3"><i class="bi bi-check-circle me-2"></i>No products expiring in 30 days.</td></tr>
      <?php else: ?>
      <?php foreach ($expiring_30 as $p):
        $daysLeft = (int)ceil((strtotime($p['expiry_date']) - time()) / 86400);
      ?>
      <tr class="table-warning">
        <td><?php echo htmlspecialchars($p['product_name']); ?></td>
        <td><code><?php echo $p['agi_code']; ?></code></td>
        <td><?php echo htmlspecialchars($p['category_name']); ?></td>
        <td><?php echo htmlspecialchars($p['batch_number']); ?></td>
        <td><?php echo date('d M Y', strtotime($p['expiry_date'])); ?></td>
        <td><span class="badge badge-expiring-soon"><?php echo $daysLeft; ?> days</span></td>
        <td><?php echo $p['stock_quantity']; ?></td>
        <td><a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a></td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Expiring 31-60 days -->
<div class="card mb-4">
  <div class="card-header bg-white text-success fw-semibold">
    <i class="bi bi-calendar-range me-2" style="color:#fd7e14;"></i>Expiring in 31-60 Days (<?php echo count($expiring_31_60); ?>)
  </div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>Product</th><th>AGI Code</th><th>Category</th><th>Batch</th><th>Expiry Date</th><th>Days Left</th><th>Stock</th><th>Action</th></tr></thead>
      <tbody>
      <?php if (empty($expiring_31_60)): ?>
        <tr><td colspan="8" class="text-center text-success p-3"><i class="bi bi-check-circle me-2"></i>No products in this range.</td></tr>
      <?php else: ?>
      <?php foreach ($expiring_31_60 as $p):
        $daysLeft = (int)ceil((strtotime($p['expiry_date']) - time()) / 86400);
      ?>
      <tr>
        <td><?php echo htmlspecialchars($p['product_name']); ?></td>
        <td><code><?php echo $p['agi_code']; ?></code></td>
        <td><?php echo htmlspecialchars($p['category_name']); ?></td>
        <td><?php echo htmlspecialchars($p['batch_number']); ?></td>
        <td><?php echo date('d M Y', strtotime($p['expiry_date'])); ?></td>
        <td><span class="badge bg-secondary"><?php echo $daysLeft; ?> days</span></td>
        <td><?php echo $p['stock_quantity']; ?></td>
        <td><a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a></td>
      </tr>
      <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
