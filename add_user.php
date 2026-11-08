<?php
$page_title = 'Add User';
require_once('includes/load.php');
page_require_level(1);
$groups = find_all('user_groups');

if(isset($_POST['add_user'])){
  $name   = remove_junk($db->escape($_POST['name']));
  $uname  = remove_junk($db->escape($_POST['username']));
  $email  = remove_junk($db->escape($_POST['email']));
  $pass   = $_POST['password'];
  $level  = (int)$_POST['user_level'];
  $status = (int)$_POST['status'];
  if(empty($name)||empty($uname)||empty($pass)){
    $session->msg('d','Name, username and password are required.');
    redirect('add_user.php',false);
  }
  $chk = $db->query("SELECT id FROM users WHERE username='$uname' LIMIT 1");
  if($db->num_rows($chk)>0){ $session->msg('d','Username already taken.'); redirect('add_user.php',false); }
  $hashed = password_hash($pass, PASSWORD_DEFAULT);
  $date   = make_date();
  $sql = "INSERT INTO users (name,username,email,password,user_level,status,created_at) VALUES ('$name','$uname','$email','$hashed','$level','$status','$date')";
  if($db->query($sql)){ $session->msg('s','User added successfully.'); redirect('users.php',false); }
  else { $session->msg('d','Failed to add user.'); redirect('add_user.php',false); }
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="page-header">
  <h4><i class="bi bi-person-plus me-2 text-primary"></i>Add New User</h4>
  <a href="users.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="row"><div class="col-md-6">
<div class="card"><div class="card-body">
  <form method="post">
    <div class="mb-3"><label class="form-label fw-semibold">Full Name *</label><input type="text" name="name" class="form-control" required></div>
    <div class="mb-3"><label class="form-label fw-semibold">Username *</label><input type="text" name="username" class="form-control" required></div>
    <div class="mb-3"><label class="form-label fw-semibold">Email</label><input type="email" name="email" class="form-control"></div>
    <div class="mb-3"><label class="form-label fw-semibold">Password *</label><input type="password" name="password" class="form-control" required></div>
    <div class="row mb-3">
      <div class="col-md-6">
        <label class="form-label fw-semibold">Role</label>
        <select name="user_level" class="form-select">
          <?php foreach($groups as $g): ?><option value="<?php echo $g['group_level']; ?>"><?php echo htmlspecialchars($g['group_name']); ?></option><?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select">
          <option value="1">Active</option>
          <option value="0">Inactive</option>
        </select>
      </div>
    </div>
    <button type="submit" name="add_user" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save User</button>
  </form>
</div></div>
</div></div>
<?php include_once('layouts/footer.php'); ?>
