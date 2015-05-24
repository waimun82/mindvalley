<?php
include ("../library/base.inc.php");

// Register action
if ($_REQUEST['submit']) {

	// Validate email format
	if (!$Member->validateEmailFormat($_REQUEST['email'])) {
		$smarty->assign("error", "ERROR: Invalid email address!");
	} else {

		// Validate email already exist
		$Member->memberEmail = $_REQUEST['email'];
		if ($member = $Member->getMember(NULL, $_REQUEST['email'])) {
			$smarty->assign("error", "ERROR: Email address already exist!");
		} else {

			// Validate password entered
			if (!$_REQUEST['password']) {
				$smarty->assign("error", "ERROR: Password missing!");
			} else {

				// Validate password matches
				if ($_REQUEST['password'] != $_REQUEST['repassword']) {
					$smarty->assign("error", "ERROR: Password does not match!");
					$smarty->assign("password", $_REQUEST['password']);
				} else {

					// Create user record
					$Member->memberEmail = $_REQUEST['email'];
					$Member->memberPassword = $_REQUEST['password'];
					$Member->memberStatus = GBL_PUBLISH_STATUS_ACTIVE;
					if (!$member_id = $Member->createMember()) {
						$smarty->assign("error", "ERROR: Unable to create record!");
					} else {

						// Redirect to main page
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
