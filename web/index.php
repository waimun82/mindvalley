<?php
include ("../library/base.inc.php");

if ($_COOKIE['email']) {
	$smarty->assign("email", $_COOKIE['email']);
	$smarty->assign("remember", true);
}

if ($_REQUEST['login']) {	
	if (!$Member->validateEmailFormat($_REQUEST['email'])) {
		$smarty->assign("error", "ERROR: Invalid email address!");
		$smarty->assign("email", $_REQUEST['email']);
	} else {
		if (!$member = $Member->getMember(NULL, $_REQUEST['email'], NULL, GBL_PUBLISH_STATUS_ACTIVE)) {
			$smarty->assign("error", "ERROR: Email address does not exist!");
			$smarty->assign("email", $_REQUEST['email']);
		} else {
			if (!$Member->validatePassword($_REQUEST['password'], $member['password'])) {
				$smarty->assign("error", "ERROR: Password is wrong!");
				$smarty->assign("email", $_REQUEST['email']);
			} else {
				$Member->doLogin($member);
				if ($_REQUEST['remember']) {
					setcookie("email", $_REQUEST['email']);
				} else {
					setcookie("email", "", time() - 3600);
				}
				if ($_REQUEST['redirect']) {
					header("Location: ".urldecode($_REQUEST['redirect']));
				} else {
					header("Location: main.php");
				}
			}
		}
	}
} else {
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
