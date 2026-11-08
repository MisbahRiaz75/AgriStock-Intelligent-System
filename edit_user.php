<?php
$page_title = 'Edit User';
require_once('includes/load.php');
page_require_level(1);
$id    = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$user_rec = find_by_id('users',$id);
if(!$user_rec){ $session->msg('d','User not found.'); redirect('users.php',false); }
$groups = find_all('user_groups');

if(isset($_POST['update_user'])){
  $name   = remove_junk($db->escape($_POST['name']));
  $email  = remove_junk($db->escape($_POST['email']));
  $level  = (int)$_POST['user_level'];
  $status = (int)$_POST['status'];
  $sql = "UPDATE users SET name='$name',email='$email',user_level='$level',status='$status' WHERE id='$id' LIMIT 1";
  if(!empty($_POST['password'])){
    $hp  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $sql = "UPDATE users SET name='$name',email='$email',user_level='$level',status='$status',password='$hp' WHERE id='$id' LIMIT 1";
  }
  if($db->query($sql)){ $session->msg('s','User updated.'); redirect('users.php',false); }
  else { $session->msg('d','Update failed.'); redirect('edit_user.php?id='.$id,false); }
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="page-header">
  <h4><i class="bi bi-person-gear me-2 text-primary"></i>Edit User</h4>
  <a href="users.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="row"><div class="col-md-6">
<div class="card"><div class="card-body">
  <form method="post">
    <div class="mb-3"><label class="form-label fw-semibold">Full Name *</label><input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user_rec['name']); ?>" required></div>
    <div class="mb-3"><label class="form-label fw-semibold">Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user_rec['email']); ?>"></div>
    <div class="mb-3"><label class="form-label fw-semibold">New Password <small class="text-muted">(leave blank to keep)</small></label><input type="password" name="password" class="form-control"></div>
    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Role</label>
        <select name="user_level" class="form-select">
          <?php foreach($groups as $g): ?><option value="<?php echo $g['group_level']; ?>" <?php echo $g['group_level']==$user_rec['user_level']?'selected':''; ?>><?php echo htmlspecialchars($g['group_name']); ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
          <option value="1" <?php echo $user_rec['status']==1?'selected':''; ?>>Active</option>
          <option value="0" <?php echo $user_rec['status']==0?'selected':''; ?>>Inactive</option>
        </select>
      </div>
    </div>
    <button type="submit" name="update_user" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Update</button>
  </form>
</div></div>
</div></div>
<?php include_once('layouts/footer.php'); ?>
