<?php
$page_title = 'Change Password';
require_once('includes/load.php');
page_require_level(3);
$current = current_user();

if(isset($_POST['change_password'])){
  $old  = $_POST['current_password'];
  $new  = $_POST['new_password'];
  $conf = $_POST['confirm_password'];
  if(empty($old)||empty($new)||empty($conf)){ $session->msg('d','All fields are required.'); redirect('change_password.php',false); }
  if($new !== $conf){ $session->msg('d','New passwords do not match.'); redirect('change_password.php',false); }
  if(!password_verify($old, $current['password']) && sha1($old) !== $current['password']){
    $session->msg('d','Current password is incorrect.'); redirect('change_password.php',false);
  }
  $hp  = password_hash($new, PASSWORD_DEFAULT);
  $id  = (int)$current['id'];
  $sql = "UPDATE users SET password='$hp' WHERE id='$id' LIMIT 1";
  if($db->query($sql)){ $session->msg('s','Password changed successfully.'); redirect('edit_account.php',false); }
  else { $session->msg('d','Failed to change password.'); redirect('change_password.php',false); }
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="page-header">
  <h4><i class="bi bi-key me-2 text-primary"></i>Change Password</h4>
</div>
<div class="row"><div class="col-md-5">
<div class="card"><div class="card-body">
  <form method="post">
    <div class="mb-3"><label class="form-label fw-semibold">Current Password</label><input type="password" name="current_password" class="form-control" required></div>
    <div class="mb-3"><label class="form-label fw-semibold">New Password</label><input type="password" name="new_password" class="form-control" required></div>
    <div class="mb-3"><label class="form-label fw-semibold">Confirm New Password</label><input type="password" name="confirm_password" class="form-control" required></div>
    <button type="submit" name="change_password" class="btn btn-danger"><i class="bi bi-shield-lock me-1"></i>Update Password</button>
  </form>
</div></div>
</div></div>
<?php include_once('layouts/footer.php'); ?>
