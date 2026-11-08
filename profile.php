<?php
$page_title = 'Profile';
require_once('includes/load.php');
page_require_level(3);
$id   = isset($_GET['id']) ? (int)$_GET['id'] : (int)current_user()['id'];
$prof = find_by_id('users',$id);
if(!$prof){ $session->msg('d','User not found.'); redirect('home.php',false); }
$group = find_by_groupLevel($prof['user_level']);
?>
<?php include_once('layouts/header.php'); ?>
<div class="page-header">
  <h4><i class="bi bi-capsule-pill me-2 text-primary"></i>Profile</h4>
  <a href="edit_account.php" class="btn btn-outline-primary btn-sm"><i class="bi bi-gear me-1"></i>Edit Account</a>
</div>
<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card text-center p-4">
      <img src="uploads/products/download.jpg" alt="logo" class="rounded-circle mx-auto d-block mb-3 border border-3" style="width: 110px; height: 110px; object-fit: cover; border-color: #22c55e;">
      <h4 class="fw-bold"><?php echo htmlspecialchars($prof['name']); ?></h4>
      <p class="text-muted">@<?php echo htmlspecialchars($prof['username']); ?></p>
      <span class="badge bg-<?php echo $prof['user_level']==1?'danger':($prof['user_level']==2?'warning text-dark':'info text-dark'); ?> fs-6 mb-3">
        <?php echo $group ? htmlspecialchars($group['group_name']) : 'User'; ?>
      </span>
      <table class="table table-sm text-start mt-2">
        <tr><th>Email:</th><td><?php echo htmlspecialchars($prof['email']); ?></td></tr>
        <tr><th>Status:</th><td><?php echo $prof['status']=='1'?'<span class="badge bg-success">Active</span>':'<span class="badge bg-secondary">Inactive</span>'; ?></td></tr>
        <tr><th>Last Login:</th><td><?php echo $prof['last_login'] ? date('d M Y, h:i A', strtotime($prof['last_login'])) : 'Never'; ?></td></tr>
        <tr><th>Member Since:</th><td><?php echo date('d M Y', strtotime($prof['created_at'])); ?></td></tr>
      </table>
    </div>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
