<?php
$page_title = 'Add Category';
require_once('includes/load.php');
page_require_level(2);
if(isset($_POST['add_category'])){
  $name = remove_junk($db->escape($_POST['category_name']));
  $desc = remove_junk($db->escape($_POST['description']));
  if(empty($name)){ $session->msg('d','Category name is required.'); redirect('add_categorie.php',false); }
  $chk = $db->query("SELECT id FROM categories WHERE category_name='$name' LIMIT 1");
  if($db->num_rows($chk) > 0){ $session->msg('d','Category already exists.'); redirect('add_categorie.php',false); }
  $sql = "INSERT INTO categories (category_name,description) VALUES ('$name','$desc')";
  if($db->query($sql)){ $session->msg('s','Category added.'); redirect('categorie.php',false); }
  else { $session->msg('d','Failed to add category.'); redirect('add_categorie.php',false); }
}
?>
<?php include_once('layouts/header.php'); ?>
<div class="page-header">
  <h4><i class="bi bi-plus-square me-2 text-primary"></i>Add Category</h4>
  <a href="categorie.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Back</a>
</div>
<div class="row"><div class="col-md-5">
<div class="card"><div class="card-body">
  <form method="post">
    <div class="mb-3">
      <label class="form-label fw-semibold">Category Name *</label>
      <input type="text" name="category_name" class="form-control" placeholder="e.g. Herbicide" required>
    </div>
    <div class="mb-3">
      <label class="form-label fw-semibold">Description</label>
      <textarea name="description" class="form-control" rows="3"></textarea>
    </div>
    <button type="submit" name="add_category" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Save</button>
  </form>
</div></div>
</div></div>
<?php include_once('layouts/footer.php'); ?>
