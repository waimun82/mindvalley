<?php
include ("../library/base.inc.php");

// Check user login
$Member->checkLogin();

$smarty->assign("email", $_SESSION['member']['email']);
$smarty->display('templates/main.html');

?>