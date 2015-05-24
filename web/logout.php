<?php
include ("../library/base.inc.php");

// Do logout and redirect to login page
if ($Member->doLogout()) {
	header("Location: index.php");
}
?>