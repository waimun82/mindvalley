<?php
include ("../library/base.inc.php");

// Check user login
$Member->checkLogin();

// Add bookmark action
if ($_REQUEST['add']) {

	// Validate URL format
	if (!$Bookmark->validateURLFormat($_REQUEST['url'])) {	
		$smarty->assign("error", "ERROR: Invalid URL format!");
		$smarty->assign("url", $_REQUEST['url']);
	} else {
		
		// Validate URL host
		if (!$Bookmark->validateURLHost($_REQUEST['url'])) {
			$smarty->assign("error", "ERROR: Invalid URL host!");
			$smarty->assign("url", $_REQUEST['url']);
		} else {
			
			// Validate URL already exist
			if (!$Bookmark->validateURLExist($_REQUEST['url'])) {
				$smarty->assign("error", "ERROR: URL already exist!");
				$smarty->assign("url", $_REQUEST['url']);
			} else {

				// Create bookmark record
				$Bookmark->bookmarkUrl = $_REQUEST['url'];
				$Bookmark->bookmarkStatus = GBL_PUBLISH_STATUS_ACTIVE;
				if (!$hashkey = $Bookmark->createBookmark()) {
					$smarty->assign("error", "ERROR: Unable to create record!");
					$smarty->assign("url", $_REQUEST['url']);
				} else {

					// Update .htaccess
					$Bookmark->writeHtaccess("../.htaccess", $hashkey);

					// Update cache
					$Bookmark->updateCache("../.cache", $hashkey, $_REQUEST['url']);

					// Return message
					$smarty->assign("success", "URL record created!");
				}
			}
		}
	}

// Delete bookmark action
} else if ($_REQUEST['delete']) {
	if (!$Bookmark->deleteBookmark($_REQUEST['id'])) {
		$smarty->assign("error", "ERROR: Unable to update record!");
	} else {
		$smarty->assign("success", "URL record deleted!");
	}
}

// Show bookmark listing
if ($arrResults = $Bookmark->getBookmark(NULL, $_SESSION['member']['id'])) {
	SmartyPaginate::connect();
	SmartyPaginate::setLimit(SYSTEM_RESULTS_PER_PAGE);
	SmartyPaginate::setTotal(count($arrResults));
	SmartyPaginate::setUrl('bookmark.php?');
	SmartyPaginate::setFirstText('<img src="templates/images/table/paging_far_left.gif" ONCLICK="loading();" class="page-far-left">');
	SmartyPaginate::setPrevText('<img src="templates/images/table/paging_left.gif" ONCLICK="loading();" class="page-left">');
	SmartyPaginate::setNextText('<img src="templates/images/table/paging_right.gif" ONCLICK="loading();" class="page-right">');
	SmartyPaginate::setLastText('<img src="templates/images/table/paging_far_right.gif" ONCLICK="loading();" class="page-far-right">');
	SmartyPaginate::assign($smarty);
	$smarty->assign("arrResults", array_slice($arrResults, SmartyPaginate::getCurrentIndex(), SmartyPaginate::getLimit()));
}
$smarty->display('templates/bookmark.html');
SmartyPaginate::reset();

/*
* ----- *
* NOTES
* ----- *
*
* FUTURE ENHANCEMENTS:
* -------------------
* Able to edit bookmarks.
* Implement bookmark web services for easier integration with other websites or applications.
* Implement description indexing using meta tags of the bookmarked URL.
* Add cronjob to clean up cache file.
*/

?>