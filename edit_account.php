<?php
$page_title = 'Edit Account';
require_once('includes/load.php');
page_require_level(3);
$current = current_user();

if(isset($_POST['update_account'])){
  $name  = remove_junk($db->escape($_POST['name']));
  $email = remove_junk($db->escape($_POST['email']));
  $id    = (int)$current['id'];
  $sql   = "UPDATE users SET name='$name', email='$email' WHERE id='$id' LIMIT 1";
  if($db->query($sql)){ $session->msg('s','Account updated.'); redirect('profile.php?id='.$id,false); }
  else { $session->msg('d','Update failed.'); redirect('edit_account.php',false); }
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="page-header">
  <h4><i class="bi bi-gear me-2 text-primary"></i>Edit Account</h4>
</div>
<div class="row"><div class="col-md-5">
<div class="card"><div class="card-body">
  <form method="post">
    <div class="mb-3"><label class="form-label fw-semibold">Full Name</label><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($current['name']); ?>"></div>
    <div class="mb-3"><label class="form-label fw-semibold">Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($current['email']); ?>"></div>
    <button type="submit" name="update_account" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Save Changes</button>
    <a href="change_password.php" class="btn btn-outline-secondary ms-2"><i class="bi bi-key me-1"></i>Change Password</a>
  </form>
</div></div>
</div></div>
<?php include_once('layouts/footer.php'); ?>
