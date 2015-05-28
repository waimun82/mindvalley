<?php
include ("../library/base.inc.php");

if ($_REQUEST['id']) {
	
	// Update user points
	if ($Member->createMemberPoints($_REQUEST['id'], $_REQUEST['points'])) {
		echo "COMPLETED!\nSCORE UPDATED TO YOUR RECORD: +".$_REQUEST['points'];
	} else {
		echo "SCORE UPDATE FAILED!";
	}
}

?>