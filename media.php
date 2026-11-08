<?php
require_once('includes/load.php');
page_require_level(2);
$session->msg('i', 'Media management has been replaced. Use product images via URL.');
redirect('product.php', false);
