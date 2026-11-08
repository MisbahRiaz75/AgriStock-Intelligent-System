<?php
$page_title = 'Users';
require_once('includes/load.php');
page_require_level(1);
$users = find_all_users();
?>
<?php include_once('layouts/header.php'); ?>
<div class="page-header">
  <h4><i class="bi bi-people me-2 text-primary"></i>User Management</h4>
  <a href="add_user.php" class="btn btn-primary btn-sm"><i class="bi bi-person-plus me-1"></i>Add User</a>
</div>
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light"><tr><th>#</th><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th class="text-center">Actions</th></tr></thead>
      <tbody>
      <?php if(empty($users)): ?><tr><td colspan="8" class="text-center text-muted py-4">No users found.</td></tr>
      <?php else: $i=1; foreach($users as $u): ?>
      <tr>
        <td><?php echo $i++; ?></td>
        <td><img src="uploads/products/download.jpg" class="rounded-circle me-2" width="32" height="32" style="object-fit: cover; border: 1px solid #22c55e;"><?php echo htmlspecialchars($u['name']); ?></td>
        <td><?php echo htmlspecialchars($u['username']); ?></td>
        <td><?php echo htmlspecialchars($u['email']); ?></td>
        <td><span class="badge bg-<?php echo $u['user_level']==1?'danger':($u['user_level']==2?'warning text-dark':'info text-dark'); ?>"><?php echo htmlspecialchars($u['group_name']); ?></span></td>
        <td><?php echo $u['status']=='1'?'<span class="badge bg-success">Active</span>':'<span class="badge bg-secondary">Inactive</span>'; ?></td>
        <td class="text-muted small"><?php echo $u['last_login'] ? date('d M Y', strtotime($u['last_login'])) : 'Never'; ?></td>
        <td class="text-center">
          <div class="btn-group btn-group-sm">
            <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
            <a href="delete_user.php?id=<?php echo $u['id']; ?>" class="btn btn-outline-danger"
               onclick="return confirm('Delete this user?')"><i class="bi bi-trash"></i></a>
          </div>
        </td>
      </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
