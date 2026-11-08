<?php
$page_title = 'Monthly Sales';
require_once('includes/load.php');
page_require_level(2);
$year = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$data = monthly_revenue_chart($year);

$monthly = array_fill(0, 12, 0);
foreach($data as $row) $monthly[(int)$row['month']-1] = (float)$row['revenue'];
$months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
$total_year = array_sum($monthly);
?>
<?php include_once('layouts/header.php'); ?>
<div class="page-header">
  <h4><i class="bi bi-calendar-month me-2 text-primary"></i>Monthly Sales - <?php echo $year; ?></h4>
  <div class="d-flex gap-2">
    <a href="monthly_sales.php?year=<?php echo $year-1; ?>" class="btn btn-outline-secondary btn-sm"><i class="bi bi-chevron-left"></i> <?php echo $year-1; ?></a>
    <a href="monthly_sales.php?year=<?php echo $year+1; ?>" class="btn btn-outline-secondary btn-sm"><?php echo $year+1; ?> <i class="bi bi-chevron-right"></i></a>
  </div>
</div>

<div class="card mb-4">
  <div class="card-header bg-white"><i class="bi bi-bar-chart me-2 text-primary"></i>Monthly Revenue Chart <?php echo $year; ?></div>
  <div class="card-body"><canvas id="monthlyBar" height="80"></canvas></div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover mb-0">
        <thead class="table-light"><tr><th>Month</th><th class="text-end">Revenue</th><th>Bar</th></tr></thead>
        <tbody>
        <?php foreach($months as $i => $m): ?>
        <tr>
          <td><?php echo $m . ' ' . $year; ?></td>
          <td class="text-end fw-semibold <?php echo $monthly[$i]>0?'text-success':'text-muted'; ?>"><?php echo CURRENCY . number_format($monthly[$i],2); ?></td>
          <td style="width:40%">
            <?php if($total_year > 0): $pct = ($monthly[$i]/$total_year)*100; ?>
            <div class="progress" style="height:14px;"><div class="progress-bar bg-primary" style="width:<?php echo $pct; ?>%"></div></div>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot><tr class="table-success fw-bold"><td>Total <?php echo $year; ?></td><td class="text-end"><?php echo CURRENCY . number_format($total_year,2); ?></td><td></td></tr></tfoot>
      </table>
    </div>
  </div>
</div>

<script>
new Chart(document.getElementById('monthlyBar'), {
  type: 'bar',
  data: {
    labels: <?php echo json_encode($months); ?>,
    datasets: [{ label: 'Revenue', data: <?php echo json_encode(array_values($monthly)); ?>,
      backgroundColor: 'rgba(13,110,253,0.7)', borderRadius: 5 }]
  },
  options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});
</script>
<?php include_once('layouts/footer.php'); ?>
