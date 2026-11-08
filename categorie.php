<?php
$page_title = 'Categories';
require_once('includes/load.php');
page_require_level(2);
$categories = find_all('categories');
$user = current_user();
?>
<?php include_once('layouts/header.php'); ?>
<div class="page-header">
  <h4><i class="bi bi-tags me-2 text-primary"></i>Product Categories</h4>
  <a href="add_categorie.php" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg me-1"></i>Add Category</a>
</div>
<div class="card">
  <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light"><tr><th>#</th><th>Category Name</th><th>Description</th><th>Created</th><th class="text-center">Actions</th></tr></thead>
      <tbody>
      <?php if(empty($categories)): ?><tr><td colspan="5" class="text-center text-muted py-4">No categories found.</td></tr>
      <?php else: $i=1; foreach($categories as $c): ?>
      <tr>
        <td><?php echo $i++; ?></td>
        <td><strong><?php echo htmlspecialchars($c['category_name']); ?></strong></td>
        <td class="text-muted"><?php echo htmlspecialchars($c['description']); ?></td>
        <td><?php echo date('d M Y', strtotime($c['created_at'])); ?></td>
        <td class="text-center">
          <div class="btn-group btn-group-sm">
            <a href="edit_categorie.php?id=<?php echo $c['id']; ?>" class="btn btn-outline-primary"><i class="bi bi-pencil"></i></a>
            <?php if($user['user_level']==1): ?>
            <a href="delete_categorie.php?id=<?php echo $c['id']; ?>" class="btn btn-outline-danger"
               onclick="return confirm('Delete this category?')"><i class="bi bi-trash"></i></a>
            <?php endif; ?>
          </div>
        </td>
      </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include_once('layouts/footer.php'); ?>
