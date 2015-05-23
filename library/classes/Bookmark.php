<?php
class Bookmark extends db {
	
	/*
	* URL to be directed
	* @var string
	*/
	public $bookmarkUrl = NULL;
	
	/*
	* Unique base 36 hashkey identifier
	* @var string
	*/
	public $bookmarkHashkey = NULL;
		
	/*
	* Status of URL redirection
	* @var int (0 = Delete, 1 = Active, 2 = Pending, 3 = Inactive)
	*/
	public $bookmarkStatus = NULL;

	/*
	* Generate unique base md5 hashkey
	* @access private
	* @param string $string - URL string
	* @return true if successful, false if failed
	*/
	private function generateHashkey($string) {
		if ($string) {
			return substr(md5($string), 0, 8);
		} else {
			return false;
		}
	}

	/*
	* Validate URL format to be valid
	* @access public
	* @param string $url - URL to validate
	* @return true if valid, false if invalid
	*/
	public function validateURLFormat($url) {
		if (!filter_var($url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED)) {
			return false;
		} else {
			return true;
		}
	}

	/*
	* Validate URL does not already exist
	* @access public
	* @param string $url - URL to validate
	* @return true if valid, false if invalid
	*/
	public function validateURLExist($url) {
		if (!$this->getBookmark(NULL, $_SESSION['member']['id'], $url, NULL, GBL_PUBLISH_STATUS_ACTIVE)) {
			return true;
		} else {
			return false;
		}
	}

	/*
	* Search record in tbl_bookmakr
	* @access public
	* @param integer $bookmark_id - ID filter of bookmark
	* @param string $url - URL filter of bookmark
	* @param string $haskey - Hashkey filter of bookmark
	* @param integer $status - Status filter of bookmark (0 = Delete, 1 = Active, 2 = Pending, 3 = Inactive)
	* @return results array if successful, false if failed
	*/
	public function getBookmark($bookmark_id = NULL, $member_id = NULL, $url = NULL, $hashkey = NULL, $status = NULL) {
		$sql = "
		SELECT id, member_id, url, hashkey, views, status, creation_timestamp, modified_timestamp 
		FROM tbl_bookmark 
		WHERE status != ".GBL_PUBLISH_STATUS_DELETE." ";
		$sql .= $bookmark_id ? "AND id = ".$bookmark_id." " : NULL;
		$sql .= $member_id ? "AND member_id = ".$member_id." " : NULL;
		$sql .= $url ? "AND url LIKE '%".$url."%' " : NULL;
		$sql .= $hashkey ? "AND hashkey = '".$hashkey."' " : NULL;
		$sql .= $status ? "AND status = ".$status." " : NULL;
		if ($result = $this->Execute($sql)) {
			$arrResults = array();
			$counter = 1;
			while ($result_row = $result->FetchRow()) {
				array_push($arrResults, array(
				"number" => $counter, 
				"id" => $result_row['id'], 
				"url" => $result_row['url'], 
				"hashkey" => $result_row['hashkey'], 
				"preview_url" => SYSTEM_PREVIEW_URL_HOST."/".$result_row['hashkey'], 
				"tiny_url" => SYSTEM_TINY_URL_HOST."/".$result_row['hashkey'], 
				"views" => $result_row['views'], 
				"creation_timestamp" => date("d-M-Y g:i A", strtotime($result_row['creation_timestamp'])), 
				"modified_timestamp" => date("d-M-Y g:i A", strtotime($result_row['modified_timestamp']))));
				$counter ++;
			}
			return $arrResults;
		} else {
			return false;
		}
	}

	/*
	* Create record in tbl_bookmark
	* @access public
	* @return record ID if successful, false if failed
	*/
	public function createBookmark() {
		global $Common;
		$record['member_id'] = $_SESSION['member']['id'];
		$record['url'] = $Common->verifyVariable($this->bookmarkUrl);
		$record['hashkey'] = $this->generateHashkey($this->bookmarkUrl);
		$record['status'] = $this->bookmarkStatus;
		$record['creation_timestamp'] = date("Y-m-d H:i:s", time());
		$record['modified_timestamp'] = date("Y-m-d H:i:s", time());
		if ($this->insertRcd("tbl_bookmark", $record)) {
			return $this->getRecordId();
		} else {
			return false;
		}
	}

	/*
	* Update record in tbl_bookmark
	* @access public
	* @param integer $bookmark_id - ID of bookmark record
	* @return true if successful, false if failed
	*/
	public function updateBookmark($bookmark_id) {
		if ($id) {
			$sql = "UPDATE tbl_bookmark SET ";
			$sql .= $this->bookmarkUrl ? "url = ".$this->bookmarkUrl.", hashkey = ".$this->generateHashkey($this->bookmarkUrl).", " : NULL;
			$sql .= $this->bookmarkStatus ? "status = ".$this->bookmarkStatus.", " : NULL;
			$sql .= "modified_timestamp = NOW() ";
			$sql .= "WHERE id = ".$bookmark_id;
			if ($this->Execute($sql)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/*
	* Delete record in tbl_bookmark (soft delete)
	* @access public
	* @param integer $bookmark_id - ID of URL record
	* @return true if successful, false if failed
	*/
	public function deleteBookmark($bookmark_id) {
		if ($bookmark_id) {
			$sql = "UPDATE tbl_bookmark SET status = ".GBL_PUBLISH_STATUS_DELETE.", ";
			$sql .= "modified_timestamp = NOW() ";
			$sql .= "WHERE id = ".$bookmark_id;
			if ($this->Execute($sql)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/*
	* Update view in tbl_bookmark
	* @access public
	* @param integer $bookmark_id - ID of bookmark record
	* @return true if successful, false if failed
	*/
	public function updateBookmarkViewCount($bookmark_id) {
		if ($bookmark_id) {
			$sql = "UPDATE tbl_bookmark SET views = views + 1 WHERE id = ".$bookmark_id;
			if ($this->Execute($sql)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	/*
	* Write .htaccess RewriteRule for root index file to get hashkey
	* @access public
	* @param string $file_path - File path of .htaccess file
	* @param integer $hashkey - Hashkey of URL to redirect
	* @return true if successful, false if failed
	*/
	public function writeHtaccess($file_path) {
		$content = "RewriteEngine on";
		$bookmarks = $this->getBookmark(NULL, NULL, NULL, NULL, GBL_PUBLISH_STATUS_ACTIVE);
		foreach($bookmarks AS $bookmark) {
			$content .= "\nRewriteRule ^".$bookmark['hashkey']."$ index.php?id=".$bookmark['hashkey']." [NC,L]";
		}
		$file = fopen($file_path, 'w') or die("Unable to write to HTACCESS file.");
		fwrite($file, $content);
		fclose($file);
	}

}