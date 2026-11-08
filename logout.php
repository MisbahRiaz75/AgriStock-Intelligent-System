<?php
require_once('includes/load.php');
$session->logout();
$session->msg('s', 'You have been logged out successfully.');
redirect('index.php', false);
