<?php
include ("../library/base.inc.php");

$Member->checkLogin();

$smarty->display('templates/game-2048.html');

?>