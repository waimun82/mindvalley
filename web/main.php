<?php
include ("../library/base.inc.php");

$Member->checkLogin();

$smarty->assign("email", $_SESSION['member']['email']);
$smarty->display('templates/main.html');

?>