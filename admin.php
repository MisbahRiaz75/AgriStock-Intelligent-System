<?php
$page_title = 'Dashboard';
require_once('includes/load.php');
page_require_level(3);

$total_products  = count_by_id('products')['total'] ?? 0;
$total_categories= count_by_id('categories')['total'] ?? 0;
$total_users     = count_by_id('users')['total'] ?? 0;
$stock_value     = total_stock_value();
$mrp_value       = total_mrp_value();
$today_rev       = today_revenue();
$month_rev       = monthly_revenue();
$week_rev        = weekly_revenue();
$expired_count   = count_expired_products();
$expiring_count  = count_expiring_soon(EXPIRY_SOON_DAYS);
$low_stock_count = count_low_stock();
$recent_sales    = find_recent_sales(8);
$top_products    = top_selling_products(5);

// Charts data
$chart_year   = date('Y');
$monthly_data = monthly_revenue_chart($chart_year);
$cat_data     = category_sales_chart();

$months_labels  = json_encode(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec']);
$months_revenue = array_fill(0, 12, 0);
foreach ($monthly_data as $row) { $months_revenue[(int)$row['month'] - 1] = (float)$row['revenue']; }
$months_revenue_json = json_encode($months_revenue);

$cat_labels  = json_encode(array_column($cat_data, 'category_name'));
$cat_revenue = json_encode(array_map('floatval', array_column($cat_data, 'revenue')));
?>
<?php include_once('layouts/header.php'); ?>

<!-- Hero Banner with Farmer Image -->
<div class="mb-4">
  <div class="hero-banner" style="position: relative; border-radius: 12px; overflow: hidden; height: 300px; background: linear-gradient(135deg, #1e7a1a 0%, #2d9d2d 100%); box-shadow: 0 8px 24px rgba(0,0,0,0.15);">
    <img src="uploads/products/steven-weeks-DUPFowqI6oI-unsplash.jpg" alt="Farmer in fields" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.85;">
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, rgba(30, 122, 26, 0.5), rgba(45, 157, 45, 0.5)); display: flex; align-items: center; justify-content: center;">
      <div class="text-center text-white">
        <h1 class="fw-bold mb-2" style="font-size: 2.5rem; text-shadow: 2px 2px 4px rgba(0,0,0,0.3);"><i class="bi bi-sprout me-2"></i><?php echo COMPANY_NAME; ?></h1>
        <p class="fs-5" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.3); letter-spacing: 0.5px;">Growing Together - Inventory Management System</p>
      </div>
    </div>
  </div>
</div>

<div class="page-header">
  <h4><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard</h4>
  <span class="text-muted small"><?php echo date('l, d F Y'); ?></span>
</div>

<!-- Stat Cards Row 1 -->
<div class="row g-3 mb-4">
  <div class="col-6 col-md-3">
    <div class="card stat-card p-3">
      <div class="d-flex align-items-center gap-3">
        <div class="icon-box bg-primary bg-opacity-10 text-primary"><i class="bi bi-box-seam"></i></div>
        <div>
          <div class="text-muted small">Total Products</div>
          <div class="fs-4 fw-bold"><?php echo $total_products; ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card stat-card p-3">
      <div class="d-flex align-items-center gap-3">
        <div class="icon-box bg-success bg-opacity-10 text-success"><i class="bi bi-currency-dollar"></i></div>
        <div>
          <div class="text-muted small">Stock Value (Cost)</div>
          <div class="fs-5 fw-bold"><?php echo CURRENCY . number_format($stock_value, 0); ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card stat-card p-3">
      <div class="d-flex align-items-center gap-3">
        <div class="icon-box bg-info bg-opacity-10 text-info"><i class="bi bi-graph-up-arrow"></i></div>
        <div>
          <div class="text-muted small">Today's Sales</div>
          <div class="fs-5 fw-bold"><?php echo CURRENCY . number_format($today_rev, 0); ?></div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-6 col-md-3">
    <div class="card stat-card p-3">
      <div class="d-flex align-items-center gap-3">
        <div class="icon-box bg-warning bg-opacity-10 text-warning"><i class="bi bi-calendar-month"></i></div>
        <div>
          <div class="text-muted small">Monthly Revenue</div>
          <div class="fs-5 fw-bold"><?php echo CURRENCY . number_format($month_rev, 0); ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Alert Cards -->
<div class="row g-3 mb-4">
  <div class="col-md-4">
    <div class="card border-danger stat-card p-3">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-danger fw-semibold"><i class="bi bi-exclamation-circle me-1"></i>Expired Products</div>
          <div class="fs-3 fw-bold text-danger"><?php echo $expired_count; ?></div>
        </div>
        <a href="expiry_alerts.php#expired" class="btn btn-outline-danger btn-sm">View</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-warning stat-card p-3">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="text-warning fw-semibold"><i class="bi bi-clock-history me-1"></i>Expiring in 30 days</div>
          <div class="fs-3 fw-bold text-warning"><?php echo $expiring_count; ?></div>
        </div>
        <a href="expiry_alerts.php#soon" class="btn btn-outline-warning btn-sm">View</a>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card border-orange stat-card p-3" style="border-color:#fd7e14!important;">
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <div class="fw-semibold" style="color:#fd7e14;"><i class="bi bi-boxes me-1"></i>Low Stock</div>
          <div class="fs-3 fw-bold" style="color:#fd7e14;"><?php echo $low_stock_count; ?></div>
        </div>
        <a href="product.php?filter=low_stock" class="btn btn-sm" style="color:#fd7e14;border-color:#fd7e14;">View</a>
      </div>
    </div>
  </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
  <div class="col-md-8">
    <div class="card">
      <div class="card-header bg-white"><i class="bi bi-bar-chart me-2 text-primary"></i>Monthly Revenue <?php echo $chart_year; ?></div>
      <div class="card-body"><canvas id="monthlyChart" height="100"></canvas></div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-header bg-white"><i class="bi bi-pie-chart me-2 text-primary"></i>Sales by Category</div>
      <div class="card-body"><canvas id="categoryChart" height="200"></canvas></div>
    </div>
  </div>
</div>

<!-- Bottom Row -->
<div class="row g-3">
  <!-- Recent Sales -->
  <div class="col-md-7">
    <div class="card">
      <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span><i class="bi bi-receipt me-2 text-primary"></i>Recent Sales</span>
        <a href="sales.php" class="btn btn-sm btn-outline-primary">View All</a>
      </div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light"><tr><th>Invoice</th><th>Date</th><th>By</th><th>Total</th></tr></thead>
          <tbody>
          <?php foreach ($recent_sales as $s): ?>
          <tr>
            <td><a href="view_sale.php?id=<?php echo $s['id']; ?>"><?php echo htmlspecialchars($s['invoice_no']); ?></a></td>
            <td><?php echo date('d M Y', strtotime($s['sale_date'])); ?></td>
            <td><?php echo htmlspecialchars($s['staff_name'] ?? 'N/A'); ?></td>
            <td class="fw-semibold text-success"><?php echo CURRENCY . number_format($s['grand_total'], 2); ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($recent_sales)): ?><tr><td colspan="4" class="text-center text-muted">No sales yet</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- Top Products -->
  <div class="col-md-5">
    <div class="card">
      <div class="card-header bg-white"><i class="bi bi-trophy me-2 text-warning"></i>Top Selling Products</div>
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light"><tr><th>Product</th><th>Qty</th><th>Revenue</th></tr></thead>
          <tbody>
          <?php foreach ($top_products as $p): ?>
          <tr>
            <td><?php echo htmlspecialchars($p['product_name']); ?><br><small class="text-muted"><?php echo $p['agi_code']; ?></small></td>
            <td><?php echo $p['total_qty']; ?></td>
            <td class="text-success"><?php echo CURRENCY . number_format($p['total_revenue'], 0); ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($top_products)): ?><tr><td colspan="3" class="text-center text-muted">No data</td></tr><?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
var ctx1 = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx1, {
  type: 'bar',
  data: {
    labels: <?php echo $months_labels; ?>,
    datasets: [{ label: 'Revenue (<?php echo CURRENCY; ?>)', data: <?php echo $months_revenue_json; ?>,
      backgroundColor: 'rgba(13,110,253,0.7)', borderRadius: 4 }]
  },
  options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
var ctx2 = document.getElementById('categoryChart').getContext('2d');
new Chart(ctx2, {
  type: 'doughnut',
  data: {
    labels: <?php echo $cat_labels; ?>,
    datasets: [{ data: <?php echo $cat_revenue; ?>,
      backgroundColor: ['#22c55e','#198754','#ffc107','#dc3545','#0dcaf0','#6f42c1'] }]
  },
  options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});
</script>

<?php include_once('layouts/footer.php'); ?>
