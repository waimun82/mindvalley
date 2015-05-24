<?php
include ("../library/base.inc.php");

// Check if user has previously checked remember me when login.
if ($_COOKIE['email']) {
	$smarty->assign("email", $_COOKIE['email']);
	$smarty->assign("remember", true);
}

// Login action
if ($_REQUEST['login']) {

	// Validate email format
	if (!$Member->validateEmailFormat($_REQUEST['email'])) {
		$smarty->assign("error", "ERROR: Invalid email address!");
		$smarty->assign("email", $_REQUEST['email']);
	} else {

		// Validate email exist
		if (!$member = $Member->getMember(NULL, $_REQUEST['email'], NULL, GBL_PUBLISH_STATUS_ACTIVE)) {
			$smarty->assign("error", "ERROR: Email address does not exist!");
			$smarty->assign("email", $_REQUEST['email']);
		} else {
			
			// Validate password entered
			if (!$_REQUEST['password']) {
				$smarty->assign("error", "ERROR: Password missing!");
				$smarty->assign("email", $_REQUEST['email']);
			} else {
				
				// Validate password
				if (!$Member->validatePassword($_REQUEST['password'], $member['password'])) {
					$smarty->assign("error", "ERROR: Password is wrong!");
					$smarty->assign("email", $_REQUEST['email']);
				} else {

					// Create user session and cookies
					$Member->doLogin($member);
					if ($_REQUEST['remember']) {
						setcookie("email", $_REQUEST['email']);
					} else {
						setcookie("email", "", time() - 3600);
					}

					// Redirect to the main page
					if ($_REQUEST['redirect']) {
						header("Location: ".urldecode($_REQUEST['redirect']));
					} else {
						header("Location: main.php");
					}
				}
			}
		}
	}
} else {

	// Check if user session still valid
	if ($Member->validateLogin()) {
		if ($_REQUEST['redirect']) {
			header("Location: ".urldecode($_REQUEST['redirect']));
		} else {
			header("Location: main.php");
		}
	}
}
$smarty->assign("redirect", urlencode($_REQUEST['redirect']));
$smarty->display('templates/login.html');
?>
