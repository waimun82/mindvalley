<?php
include ("../library/base.inc.php");

// Check user login
$Member->checkLogin();

// Get leaderboard
$member_points = $Member->getMemberPoints();
$smarty->assign("arrResults", $member_points);

$smarty->assign("member_id", $_SESSION['member']['id']);
$smarty->display('templates/game-2048.html');

?>