<?php
$page_title = 'Products';
require_once('includes/load.php');
page_require_level(3);

$filter  = isset($_GET['filter']) ? $_GET['filter'] : '';
$keyword = isset($_GET['q'])      ? trim($_GET['q']) : '';

if ($keyword !== '') {
    $products = search_products($keyword);
} elseif ($filter === 'low_stock') {
    $products = find_low_stock_products();
} elseif ($filter === 'expired') {
    $products = find_expired_products();
} elseif ($filter === 'expiring') {
    $products = find_expiring_soon_products(EXPIRY_SOON_DAYS);
} else {
    $products = find_all_products();
}
$user = current_user();
?>
<?php include_once('layouts/header.php'); ?>

<div class="page-header">
  <h4><i class="bi bi-box-seam me-2 text-primary"></i>Products
    <?php if ($filter === 'low_stock'): ?><span class="badge bg-warning text-dark ms-2">Low Stock</span><?php endif; ?>
    <?php if ($filter === 'expired'):   ?><span class="badge bg-danger ms-2">Expired</span><?php endif; ?>
    <?php if ($filter === 'expiring'):  ?><span class="badge bg-warning text-dark ms-2">Expiring Soon</span><?php endif; ?>
  </h4>
  <?php if ($user['user_level'] <= 2): ?>
  <a href="add_product.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Product</a>
  <?php endif; ?>
</div>

<!-- Search / Filter Bar -->
<div class="card mb-3">
  <div class="card-body py-2">
    <form method="get" action="product.php" class="row g-2 align-items-center">
      <div class="col-md-5">
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-search"></i></span>
          <input type="text" name="q" class="form-control" placeholder="Search by name, AGI code, category..." value="<?php echo htmlspecialchars($keyword); ?>">
        </div>
      </div>
      <div class="col-auto"><button type="submit" class="btn btn-primary"><i class="bi bi-search me-1"></i>Search</button></div>
      <div class="col-auto"><a href="product.php" class="btn btn-outline-secondary">Reset</a></div>
      <div class="col-auto ms-auto">
        <div class="btn-group">
          <a href="product.php?filter=low_stock" class="btn btn-sm btn-outline-warning <?php echo $filter==='low_stock'?'active':''; ?>">Low Stock</a>
          <a href="product.php?filter=expired"   class="btn btn-sm btn-outline-danger  <?php echo $filter==='expired'?'active':''; ?>">Expired</a>
          <a href="product.php?filter=expiring"  class="btn btn-sm btn-outline-warning <?php echo $filter==='expiring'?'active':''; ?>">Expiring Soon</a>
        </div>
      </div>
    </form>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Product Name</th>
            <th>AGI Code</th>
            <th>Category</th>
            <th>Pack Size</th>
            <th>MRP</th>
            <th class="text-center">Stock</th>
            <th>Expiry Date</th>
            <th>Batch</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if (empty($products)): ?>
          <tr><td colspan="10" class="text-center py-4 text-muted">No products found.</td></tr>
        <?php else: ?>
        <?php $i = 1; foreach ($products as $p): ?>
        <?php
          $today = new DateTime();
          $expiry  = $p['expiry_date'] ? new DateTime($p['expiry_date']) : null;
          $diff    = $expiry ? $today->diff($expiry)->days : 9999;
          $isPast  = $expiry && $expiry < $today;
          $rowClass = '';
          $expiryBadge = '';
          if ($isPast) {
              $rowClass = 'table-danger';
              $expiryBadge = '<span class="badge badge-expired ms-1">EXPIRED</span>';
          } elseif ($expiry && $diff <= EXPIRY_SOON_DAYS) {
              $rowClass = 'table-warning';
              $expiryBadge = '<span class="badge badge-expiring-soon ms-1">'.$diff.'d left</span>';
          }
          $stockClass = ($p['stock_quantity'] <= LOW_STOCK_THRESHOLD) ? 'text-danger fw-bold' : 'text-success fw-semibold';
        ?>
        <tr class="<?php echo $rowClass; ?>">
          <td><?php echo $i++; ?></td>
          <td>
            <strong><?php echo htmlspecialchars($p['product_name']); ?></strong>
            <?php if ($p['gst']): ?><span class="badge bg-info text-dark ms-1" style="font-size:0.7rem;">GST</span><?php endif; ?>
          </td>
          <td><code><?php echo htmlspecialchars($p['agi_code']); ?></code></td>
          <td><?php echo htmlspecialchars($p['category_name']); ?></td>
          <td><?php echo htmlspecialchars($p['pack_size']); ?></td>
          <td><?php echo CURRENCY . number_format($p['mrp'], 2); ?></td>
          <td class="text-center <?php echo $stockClass; ?>"><?php echo $p['stock_quantity']; ?></td>
          <td><?php echo $p['expiry_date'] ? date('d M Y', strtotime($p['expiry_date'])) : '-'; ?><?php echo $expiryBadge; ?></td>
          <td><small><?php echo htmlspecialchars($p['batch_number']); ?></small></td>
          <td class="text-center">
            <div class="btn-group btn-group-sm">
              <a href="edit_product.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
              <?php if ($user['user_level'] == 1): ?>
              <a href="delete_product.php?id=<?php echo $p['id']; ?>" class="btn btn-outline-danger" title="Delete"
                 onclick="return confirm('Delete this product?')"><i class="bi bi-trash"></i></a>
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
  <div class="card-footer text-muted small">
    Showing <?php echo count($products); ?> product(s)
    &nbsp;|&nbsp; Low stock threshold: <?php echo LOW_STOCK_THRESHOLD; ?> units
  </div>
</div>

<?php include_once('layouts/footer.php'); ?>
