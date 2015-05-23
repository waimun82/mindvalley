<?php
include ("../library/base.inc.php");

if ($Member->doLogout()) {
	header("Location: index.php");
}
?>