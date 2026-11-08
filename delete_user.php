<?php
require_once('includes/load.php');
page_require_level(1);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$cu = current_user();
if($id == $cu['id']){ $session->msg('d','You cannot delete your own account.'); redirect('users.php',false); }
if(delete_by_id('users',$id)){ $session->msg('s','User deleted.'); }
else { $session->msg('d','Failed to delete user.'); }
redirect('users.php',false);
