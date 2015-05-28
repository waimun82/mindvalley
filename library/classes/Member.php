<?php
class Member extends db {
	
	/*
	* Email address of member
	* @var string
	*/
	public $memberEmail = NULL;

	/*
	* Password of member
	* @var string
	*/
	public $memberPassword = NULL;

	/*
	* Token identifier of member
	* @var string
	*/
	public $memberToken = NULL;

	/*
	* Status of member
	* @var string (0 = Delete, 1 = Active, 2 = Pending, 3 = Inactive)
	*/
	public $memberStatus = NULL;

	/*
	* Last login of member
	* @var datetime
	*/
	public $memberLastLogin = NULL;

	/*
	* Create login session of member
	* @access public
	* @param array $member_info - Member information block
	* @return true
	*/
	public function doLogin($member_info = array()) {
		$this->updateMemberLastLogin($member_info['id']);
		$_SESSION['member']['id'] = $member_info['id'];
		$_SESSION['member']['email'] = $member_info['email'];
		$_SESSION['member']['token'] = $member_info['token'];
		return true;
	}
	
	/*
	* Destroy session of member
	* @access public
	* @return true
	*/
	public function doLogout() {
		unset($_SESSION['member']);
		session_destroy();
		return true;
	}

	/*
	* Validate if member session exist
	* @access public
	* @return true if member has login, return false if member has not login
	*/
	public function validateLogin() {
		if(empty($_SESSION['member']['id']) || empty($_SESSION['member']['email']) || empty($_SESSION['member']['token'])) {
			return false;
		} else {
			if ($_SESSION['member']['token'] != md5($_SESSION['member']['email'].SYSTEM_SECRET_KEY)) {
				$this->doLogout();
				return false;
			} else {
				return true;
			}
		}
	}
	
	/*
	* Check if member has login and redirect back to login page
	* @access public
	*/
	public function checkLogin() {
		if(!$this->validateLogin()) {
			header("Location: index.php?redirect=".urlencode($_SERVER['REQUEST_URI']));
		}
	}
	
	/*
	* Generate unique base md5 token with secret key
	* @access private
	* @param string $string - URL string
	* @return true if successful, false if failed
	*/
	private function generateHashkey($string) {
		if ($string) {
			return md5($string.SYSTEM_SECRET_KEY);
		} else {
			return false;
		}
	}

	/*
	* Validate email to be valid
	* @access public
	* @param string $email - Email of member
	* @return true if valid, false if invalid
	*/
	public function validateEmailFormat($email) {
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return false;
		} else {
			return true;
		}
	}

	/*
	* Validate member password
	* @access public
	* @param string $password - Password of member unencoded
	* @param string $md5_password - Password of member in md5 format
	* @return true if password match, false if password mismatched
	*/
	public function validatePassword($password, $md5_password) {
		if ($this->generateHashkey($password) != $md5_password) {
			return false;
		} else {
			return true;
		}
	}

	/*
	* Search record in tbl_member
	* @access public
	* @return results array if successful, false if failed
	*/
	public function getMember($member_id = NULL, $email = NULL, $token = NULL, $status = NULL) {
		$sql = "
		SELECT id, email, password, token, status, last_login, creation_timestamp, modified_timestamp 
		FROM tbl_member 
		WHERE status != ".GBL_PUBLISH_STATUS_DELETE." ";
		$sql .= $member_id ? "AND id = ".$member_id." " : NULL;
		$sql .= $email ? "AND email = '".$email."' " : NULL;
		$sql .= $token ? "AND token = '".$token."' " : NULL;
		$sql .= $status ? "AND status = '".$status."' " : NULL;
		if($result = $this->Execute($sql)) {
			$member = $result->FetchRow();
			return $member;
		} else {
			return false;
		}
	}
	
	/*
	* Create record in tbl_member
	* @access public
	* @return record ID if successful, false if failed
	*/
	public function createMember() {
		global $Common;
		$record['email'] = $Common->verifyVariable($this->memberEmail);
		$record['password'] = $this->generateHashkey($this->memberPassword);
		$record['token'] = $this->generateHashkey($this->memberEmail);
		$record['status'] = $this->memberStatus;
		$record['last_login'] = date("Y-m-d H:i:s", time());
		$record['creation_timestamp'] = date("Y-m-d H:i:s", time());
		$record['modified_timestamp'] = date("Y-m-d H:i:s", time());
		if ($this->insertRcd("tbl_member", $record)) {
			return $this->getRecordId();
		} else {
			return false;
		}
	}

	/*
	* Update member last login
	* @access public
	* @param integer $member_id - ID of member record
	* @return true if successful, false if failed
	*/
	public function updateMember($member_id) {
		if ($member_id) {
			$sql = "UPDATE tbl_member SET ";
			$sql .= $this->memberPassword ? "password = '".$this->generateHashkey($this->memberPassword)."', " : NULL;
			$sql .= $this->memberStatus ? "status = ".$this->memberStatus.", " : NULL;
			$sql .= "modified_timestamp = NOW() ";
			$sql .= "WHERE id = ".$member_id;
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
	* Update member last login
	* @access public
	* @param integer $member_id - ID of member record
	* @return true if successful, false if failed
	*/
	public function updateMemberLastLogin($member_id) {
		if ($member_id) {
			$sql = "UPDATE tbl_member SET last_login = NOW() WHERE id = ".$member_id;
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
	* Get member leaderboard
	* @access public
	* @return true if successful, false if failed
	*/
	public function getMemberPoints() {
		$sql = "SELECT member_id, COUNT(*) AS games, SUM(points) AS points, timestamp FROM tbl_member_points GROUP BY member_id ORDER BY points DESC";
		if ($result = $this->Execute($sql)) {
			$arrResults = array();
			$counter = 1;
			while ($result_row = $result->FetchRow()) {
				$member = $this->getMember($result_row['member_id']);
				array_push($arrResults, array(
				"rank" => $counter, 
				"email" => $member['email'], 
				"games" => $result_row['games'], 
				"points" => number_format($result_row['points'])));
				$counter ++;
			}
			return $arrResults;
		} else {
			return false;
		}
	}

	/*
	* Create record in tbl_member_points
	* @access public
	* @param integer $member_id - ID of member record
	* @param float $points - Points obtained
	* @return true if successful, false if failed
	*/
	public function createMemberPoints($member_id, $points) {
		if ($member_id) {
			$record['member_id'] = $member_id;
			$record['points'] = $points;
			$record['timestamp'] = date("Y-m-d H:i:s", time());
			if ($this->insertRcd("tbl_member_points", $record)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

}

?>