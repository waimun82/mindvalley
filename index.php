<?php
include ("library/base.inc.php");

if ($_REQUEST['id']) {
	if ($bookmark = $Bookmark->getBookmark(NULL, NULL, NULL, $_REQUEST['id'])) {
		if ("http://".$_SERVER['HTTP_HOST'] != SYSTEM_PREVIEW_URL_HOST) {
			$Bookmark->updateBookmarkViewCount($bookmark[0]['id']);
		}
		header('Location: '.$bookmark[0]['url']);
	} else {
		header('HTTP/1.0 404 Not Found');
	}
} else {
	header('HTTP/1.0 404 Not Found');
}

?>