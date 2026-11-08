<?php
require_once('includes/load.php');
page_require_level(1);

$id   = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$sale = find_sale_by_id($id);
if (!$sale) { $session->msg('d','Invoice not found.'); redirect('sales.php',false); }

// Restore stock for each item
$items = find_sale_items($id);
foreach ($items as $item) {
    restore_product_stock($item['product_id'], $item['quantity']);
}

// Delete sale (cascades to sale_items)
if (delete_by_id('sales', $id)) {
    $session->msg('s', 'Invoice deleted and stock restored.');
} else {
    $session->msg('d', 'Failed to delete invoice.');
}
redirect('sales.php', false);
