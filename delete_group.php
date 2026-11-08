<?php
require_once('includes/load.php');
page_require_level(1);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// Prevent deletion of default groups
if($id <= 3){ $session->msg('d','Cannot delete default groups.'); redirect('group.php',false); }
if(delete_by_id('user_groups',$id)){ $session->msg('s','Group deleted.'); }
else { $session->msg('d','Failed to delete group.'); }
redirect('group.php', false);
