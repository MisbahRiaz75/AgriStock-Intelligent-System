<?php
$page_title = 'Home';
require_once('includes/load.php');
if (!$session->isUserLoggedIn()) { redirect('index.php', false); }
redirect('admin.php', false);
