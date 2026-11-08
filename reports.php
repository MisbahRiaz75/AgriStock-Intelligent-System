<?php
$page_title = 'Analytics & Reports';
require_once('includes/load.php');
page_require_level(2);

$year          = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$monthly_data  = monthly_revenue_chart($year);
$cat_data      = category_sales_chart();
$top_products  = top_selling_products(10);
$low_stock     = find_low_stock_products();
$expired       = find_expired_products();
$expiring_soon = find_expiring_soon_products(EXPIRY_SOON_DAYS);

$m_rev = array_fill(0, 12, 0);
foreach($monthly_data as $r) $m_rev[(int)$r['month']-1] = (float)$r['revenue'];

$total_products  = count_by_id('products')['total'] ?? 0;
$stock_val       = total_stock_value();
$mrp_val         = total_mrp_value();
$month_rev       = monthly_revenue();
$week_rev        = weekly_revenue();
$today_rev       = today_revenue();
?>
<?php include_once('layouts/header.php'); ?>
<div class="page-header">
  <h4><i class="bi bi-bar-chart-line me-2 text-primary"></i>Analytics & Reports</h4>
  <div class="d-flex gap-2">
    <a href="reports.php?year=<?php echo $year-1; ?>" class="btn btn-outline-secondary btn-sm"><?php echo $year-1; ?></a>
    <span class="btn btn-primary btn-sm"><?php echo $year; ?></span>
    <a href="reports.php?year=<?php echo $year+1; ?>" class="btn btn-outline-secondary btn-sm"><?php echo $year+1; ?></a>
  </div>
</div>

<!-- KPI Row -->
<div class="row g-3 mb-4">
  <div class="col-md-2 col-6">
    <div class="card stat-card p-3 text-center">
      <div class="fs-3 fw-bold text-primary"><?php echo $total_products; ?></div>
      <div class="text-muted small">Products</div>
    </div>
  </div>
  <div class="col-md-2 col-6">
    <div class="card stat-card p-3 text-center">
      <div class="fs-6 fw-bold text-success"><?php echo CURRENCY . number_format($stock_val,0); ?></div>
      <div class="text-muted small">Stock Value</div>
    </div>
  </div>
  <div class="col-md-2 col-6">
    <div class="card stat-card p-3 text-center">
      <div class="fs-6 fw-bold text-info"><?php echo CURRENCY . number_format($mrp_val,0); ?></div>
      <div class="text-muted small">MRP Value</div>
    </div>
  </div>
  <div class="col-md-2 col-6">
    <div class="card stat-card p-3 text-center">
      <div class="fs-6 fw-bold text-warning"><?php echo CURRENCY . number_format($today_rev,0); ?></div>
      <div class="text-muted small">Today Revenue</div>
    </div>
  </div>
  <div class="col-md-2 col-6">
    <div class="card stat-card p-3 text-center">
      <div class="fs-6 fw-bold text-success"><?php echo CURRENCY . number_format($week_rev,0); ?></div>
      <div class="text-muted small">This Week</div>
    </div>
  </div>
  <div class="col-md-2 col-6">
    <div class="card stat-card p-3 text-center">
      <div class="fs-6 fw-bold text-primary"><?php echo CURRENCY . number_format($month_rev,0); ?></div>
      <div class="text-muted small">This Month</div>
    </div>
  </div>
</div>

<!-- Charts -->
<div class="row g-3 mb-4">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header bg-white fw-semibold"><i class="bi bi-bar-chart me-2 text-primary"></i>Monthly Revenue <?php echo $year; ?></div>
      <div class="card-body"><canvas id="revChart" height="90"></canvas></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header bg-white fw-semibold"><i class="bi bi-pie-chart me-2 text-primary"></i>Sales by Category</div>
      <div class="card-body"><canvas id="catChart" height="220"></canvas></div>
    </div>
  </div>
</div>

<!-- Inventory Reports -->
<div class="row g-3 mb-4">
  <div class="col-md-6">
    <div class="card">
      <div class="card-header bg-danger text-white fw-semibold"><i class="bi bi-exclamation-circle me-2"></i>Expired / Expiring Soon</div>
      <div class="table-responsive">
        <table class="table table-sm mb-0">
          <thead class="table-light"><tr><th>Product</th><th>Expiry</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach(array_slice($expired,0,5) as $p): ?>
          <tr class="table-danger"><td><?php echo htmlspecialchars($p['product_name']); ?></td><td><?php echo date('d M Y',strtotime($p['expiry_date'])); ?></td><td><span class="badge badge-expired">EXPIRED</span></td></tr>
          <?php endforeach; ?>
          <?php foreach(array_slice($expiring_soon,0,5) as $p):
            $dl = (int)ceil((strtotime($p['expiry_date'])-time())/86400); ?>
          <tr class="table-warning"><td><?php echo htmlspecialchars($p['product_name']); ?></td><td><?php echo date('d M Y',strtotime($p['expiry_date'])); ?></td><td><span class="badge badge-expiring-soon"><?php echo $dl; ?>d</span></td></tr>
          <?php endforeach; ?>
          <?php if(empty($expired)&&empty($expiring_soon)): ?><tr><td colspan="3" class="text-center text-success">All clear!</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="card-footer"><a href="expiry_alerts.php" class="btn btn-sm btn-outline-danger">View All</a></div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-header fw-semibold" style="background:#fff;border-left:4px solid #fd7e14;"><i class="bi bi-boxes me-2" style="color:#fd7e14;"></i>Low Stock Alert</div>
      <div class="table-responsive">
        <table class="table table-sm mb-0">
          <thead class="table-light"><tr><th>Product</th><th>Category</th><th class="text-center">Stock</th></tr></thead>
          <tbody>
          <?php foreach(array_slice($low_stock,0,8) as $p): ?>
          <tr><td><?php echo htmlspecialchars($p['product_name']); ?></td><td><?php echo htmlspecialchars($p['category_name']); ?></td><td class="text-center text-danger fw-bold"><?php echo $p['stock_quantity']; ?></td></tr>
          <?php endforeach; ?>
          <?php if(empty($low_stock)): ?><tr><td colspan="3" class="text-center text-success">Stock levels OK.</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
      <div class="card-footer"><a href="product.php?filter=low_stock" class="btn btn-sm btn-outline-warning">View All</a></div>
    </div>
  </div>
</div>

<!-- Top Selling Products -->
<div class="card mb-4">
  <div class="card-header bg-white fw-semibold"><i class="bi bi-trophy me-2 text-warning"></i>Top 10 Selling Products</div>
  <div class="table-responsive">
    <table class="table table-hover mb-0">
      <thead class="table-light"><tr><th>#</th><th>Product</th><th>AGI Code</th><th class="text-center">Qty Sold</th><th class="text-end">Revenue</th></tr></thead>
      <tbody>
      <?php if(empty($top_products)): ?><tr><td colspan="5" class="text-center text-muted py-4">No sales data yet.</td></tr>
      <?php else: $i=1; foreach($top_products as $p): ?>
      <tr>
        <td><?php echo $i === 1 ? '<span class="badge bg-warning text-dark">🥇 1</span>' : ($i === 2 ? '<span class="badge bg-secondary">🥈 2</span>' : $i); $i++; ?></td>
        <td><?php echo htmlspecialchars($p['product_name']); ?></td>
        <td><code><?php echo $p['agi_code']; ?></code></td>
        <td class="text-center"><?php echo $p['total_qty']; ?></td>
        <td class="text-end text-success fw-semibold"><?php echo CURRENCY . number_format($p['total_revenue'],2); ?></td>
      </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
new Chart(document.getElementById('revChart'),{type:'bar',data:{labels:<?php echo json_encode(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']); ?>,datasets:[{label:'Revenue',data:<?php echo json_encode(array_values($m_rev)); ?>,backgroundColor:'rgba(13,110,253,0.7)',borderRadius:5}]},options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}});
new Chart(document.getElementById('catChart'),{type:'doughnut',data:{labels:<?php echo json_encode(array_column($cat_data,'category_name')); ?>,datasets:[{data:<?php echo json_encode(array_map('floatval',array_column($cat_data,'revenue'))); ?>,backgroundColor:['#22c55e','#198754','#ffc107','#dc3545','#0dcaf0','#6f42c1']}]},options:{responsive:true,plugins:{legend:{position:'bottom'}}}});
</script>

<?php include_once('layouts/footer.php'); ?>
