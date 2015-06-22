<?php
include ("library/base.inc.php");

// Incoming tiny URL
if ($_REQUEST['id']) {

	// Get bookmark record
	if ($bookmark = $Bookmark->getCache(".cache", $_REQUEST['id'])) {
		
		// Update view count if request is not from a preview URL
		if ("http://".$_SERVER['HTTP_HOST'] != SYSTEM_PREVIEW_URL_HOST) {
			$Bookmark->updateBookmarkViewCount(NULL, $bookmark['hashkey']);
		}
		
		// Redirect to full URL
		header('Location: '.$bookmark['url']);

	} else if ($bookmark = $Bookmark->getBookmark(NULL, NULL, NULL, $_REQUEST['id'])) {

		// Update cache file if it does not exist
		$Bookmark->updateCache(".cache", $bookmark[0]['hashkey'], $bookmark[0]['url']);

		// Update view count if request is not from a preview URL
		if ("http://".$_SERVER['HTTP_HOST'] != SYSTEM_PREVIEW_URL_HOST) {
			$Bookmark->updateBookmarkViewCount($bookmark[0]['id']);
		}
		
		// Redirect to full URL
		header('Location: '.$bookmark[0]['url']);

	} else {
		header('HTTP/1.0 404 Not Found');
	}
} else {
	header('HTTP/1.0 404 Not Found');
}
print_r($bookmark);
?>