<?php
require_once('includes/load.php');
page_require_level(1);  // Admin only

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = find_by_id('products', $id);

if (!$product) {
    $session->msg('d', 'Product not found.');
    redirect('product.php', false);
}

// Guard: cannot delete if used in sales
if (product_used_in_sales($id)) {
    $session->msg('d', 'Cannot delete: this product has sales records. Consider deactivating it instead.');
    redirect('product.php', false);
}

if (delete_by_id('products', $id)) {
    $session->msg('s', 'Product deleted successfully.');
} else {
    $session->msg('d', 'Failed to delete product.');
}
redirect('product.php', false);
