<?php
$page_title = 'Edit Category';
require_once('includes/load.php');
page_require_level(2);
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$cat = find_by_id('categories', $id);
if(!$cat){ $session->msg('d','Category not found.'); redirect('categorie.php',false); }
if(isset($_POST['update_category'])){
  $name = remove_junk($db->escape($_POST['category_name']));
  $desc = remove_junk($db->escape($_POST['description']));
  $sql  = "UPDATE categories SET category_name='$name', description='$desc' WHERE id='$id' LIMIT 1";
  if($db->query($sql)){ $session->msg('s','Category updated.'); redirect('categorie.php',false); }
  else { $session->msg('d','Update failed.'); redirect('edit_categorie.php?id='.$id,false); }
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="page-header">
  <h4><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Category</h4>
  <a href="categorie.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="row"><div class="col-md-5">
<div class="card"><div class="card-body">
  <form method="post">
    <div class="mb-3">
      <label class="form-label fw-semibold">Category Name *</label>
      <input type="text" name="category_name" class="form-control" value="<?php echo htmlspecialchars($cat['category_name']); ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label fw-semibold">Description</label>
      <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($cat['description']); ?></textarea>
    </div>
    <button type="submit" name="update_category" class="btn btn-success"><i class="bi bi-check-lg me-1"></i>Update</button>
  </form>
</div></div>
</div></div>
<?php include_once('layouts/footer.php'); ?>
