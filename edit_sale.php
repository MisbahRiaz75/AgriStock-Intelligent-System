<?php
require_once('includes/load.php');
page_require_level(3);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
redirect('view_sale.php?id='.$id, false);
