<?php
require_once('includes/load.php');
page_require_level(1);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// Guard: check if products use this category
$chk = $db->query("SELECT id FROM products WHERE category_id='$id' LIMIT 1");
if($db->num_rows($chk) > 0){
  $session->msg('d','Cannot delete: products are using this category.');
  redirect('categorie.php',false);
}
if(delete_by_id('categories',$id)){ $session->msg('s','Category deleted.'); }
else { $session->msg('d','Failed to delete category.'); }
redirect('categorie.php',false);
