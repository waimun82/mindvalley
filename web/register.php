<?php
include ("../library/base.inc.php");

if ($_REQUEST['submit']) {
	if (!$Member->validateEmailFormat($_REQUEST['email'])) {
		$smarty->assign("error", "ERROR: Invalid email address!");
	} else {
		$Member->memberEmail = $_REQUEST['email'];
		if ($member = $Member->getMember(NULL, $_REQUEST['email'])) {
			$smarty->assign("error", "ERROR: Email address already exist!");
		} else {
			if (!$_REQUEST['password']) {
				$smarty->assign("error", "ERROR: Password missing!");
			} else {
				if ($_REQUEST['password'] != $_REQUEST['repassword']) {
					$smarty->assign("error", "ERROR: Password does not match!");
					$smarty->assign("password", $_REQUEST['password']);
				} else {
					$Member->memberEmail = $_REQUEST['email'];
					$Member->memberPassword = $_REQUEST['password'];
					$Member->memberStatus = GBL_PUBLISH_STATUS_ACTIVE;
					if (!$member_id = $Member->createMember()) {
						$smarty->assign("error", "ERROR: Unable to create record!");
					} else {
						$member = $Member->getMember($member_id);
						$Member->doLogin($member);
						header("Location: main.php");
					}
				}
			}
		}
	}
}
$smarty->assign("email", $_REQUEST['email']);
$smarty->display('templates/register.html');
?>
