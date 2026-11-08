<?php
$page_title = 'User Groups';
require_once('includes/load.php');
page_require_level(1);
$groups = find_all('user_groups');
?>
<?php include_once('layouts/header.php'); ?>
<div class="page-header">
  <h4><i class="bi bi-person-badge me-2 text-primary"></i>User Groups</h4>
</div>
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light"><tr><th>#</th><th>Group Name</th><th>Level</th><th>Status</th></tr></thead>
      <tbody>
      <?php if(empty($groups)): ?><tr><td colspan="4" class="text-center text-muted">No groups.</td></tr>
      <?php else: $i=1; foreach($groups as $g): ?>
      <tr>
        <td><?php echo $i++; ?></td>
        <td><?php echo htmlspecialchars($g['group_name']); ?></td>
        <td><?php echo $g['group_level']; ?></td>
        <td><?php echo $g['group_status']=='1'?'<span class="badge bg-success">Active</span>':'<span class="badge bg-secondary">Disabled</span>'; ?></td>
      </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
