<?PHP
/**
 * Class db pack ADODB
 * Version: 1.1 Date: 20/10/2010
*/
/*
Modification logs:
20/10/2010 - Direct insert record
	Integer db::intRecordId keeps current record id
	function db::getRecordId return current record id.
	function db::insertRcd insert record by pass-in table name and field value with field name.
	function db::cpsFldUpdtSQLSeg return field updating segment of SQL statement.
*/
define('ADODB_FORCE_TYPE', 3);
	class db 
	{
		var $idbconn;
		var $totalquerytime;
		var $totalquerycount;
		protected $intRecordId = -1;// keeps current record id.
		
		function db()	// db constructor
		{
			$this->totalquerytime = $this->totalquerycount = 0;
			require_once (dirname(__DIR__)."/plugins/adodb/adodb.inc.php");
		
			if ($this->idbconn = &ADONewConnection(DB_TYPE))
		    {
		    	if ($this->idbconn->Connect(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME))
		    	{
		    		$this->idbconn->debug = DB_DEBUGMODE;
		    		$this->idbconn->SetFetchMode(ADODB_FETCH_ASSOC);
		    		
		    	}
		    	else
		    	{
		    		trigger_error("Unable to connect to the database. ** Error message: " . $this->idbconn->ErrorMsg() , E_USER_ERROR);
			    	exit();
			    }
		    }
		    else
		    {
		    	trigger_error("Unable to connect to the database. ** Error message: " . $this->idbconn->ErrorMsg() , E_USER_ERROR);
		    	exit();
		    }
		}
		
		function Execute($sql)
		{
			$timestart = microtime();
			//$sql = $this->db_prefix_tables($sql);
			$rs = $this->idbconn->Execute($sql);
			// echo "<hr>" . $sql . "<hr>";
			// if (trim($this->idbconn->ErrorMsg()) != "")
			//	trigger_error("Caught SQL Error. ** SQL Statement: " . $sql . "** Error message: " . $this->idbconn->ErrorMsg() , E_USER_ERROR);
			
			$this->totalquerytime += (microtime() - $timestart);
			$this->totalquerycount++;
			
			return $rs;
		}
		
		function qstr($str)
		{
			return $this->idbconn->qstr($str);
		}
		
		function db_prefix_tables($sql)
		{
			global $_conf;
			
			return strtr($sql, array('{' => _DB_TABLEPREFIX, '}' => ''));
			
		}
		
		function GetInsertSQL(&$rs, $arrFields,$magicq=false,$forcenulls=false)
		{
			return $this->idbconn->GetinsertSQL($rs, $arrFields, $magicq, $forcenulls);
		}
		
		function GetUpdateSQL(&$rs, $arrFields, $forceUpdate=false,$magicq=false,$forcenulls=false)
		{
			return $this->idbconn->GetUpdateSQL($rs, $arrFields, $forceUpdate, $magicq, $forcenulls);
		}
		
		function gettotalquerytime()
		{
			return $this->totalquerytime;
		}
		
		function gettotalquerycount()
		{
			return $this->totalquerycount;
		}
		
		function BeginTrans()
		{
			return $this->idbconn->BeginTrans();
		}
		
		function CommitTrans()
		{
			return $this->idbconn->CommitTrans();
		}
		
		function ErrorMsg()
		{
			return $this->idbconn->ErrorMsg();
		}
		
		function RollbackTrans()
		{
			return $this->idbconn->RollbackTrans();
		}
		/**
		 * function db::getRecordId return current record id.
		 * @return - Integer - Current record id.
		*/
		function getRecordId()
		{ // return current record id.
			return $this->intRecordId;
		} // End of function getRecordId
		/**
		 * function db::insertRcd insert record by pass-in table name and field value with field name.
		 * @param - String - Table name.
		 * @param - Array - Field value with field name.
		 * @return - Boolean - Flag tells process done
		*/
		function insertRcd($strTblName, $arrFld)
		{ // insert record by pass-in table name and field value with field name.
			$bResult = false;
			if(!is_string($strTblName))
			{
				$strMsg = 'Invalid table name!';
				$strMsg .= ' No record inserted.';
				trigger_error($strMsg , E_USER_ERROR);
				return $bResult;
			}// End of if(!is_string($strTblName))
			if(empty($strTblName))
			{
				$strMsg = 'Empty table name!';
				$strMsg .= ' No record inserted.';
				trigger_error($strMsg , E_USER_ERROR);
				return $bResult;
			}// End of if(empty($strTblName))
			if(!is_array($arrFld))
			{
				$strMsg = 'Required pass-in fields\' values as array.';
				$strMsg .= ' No record inserted.';
				trigger_error($strMsg , E_USER_ERROR);
				return $bResult;
			}// End of if(!is_array($arrFld))
			if(empty($arrFld))
			{
				$strMsg = 'Empty field array!';
				$strMsg .= ' No record inserted.';
				trigger_error($strMsg , E_USER_ERROR);
				return $bResult;
			}// End of if(empty($arrFld))
			// Compose field name and value list.
			$strName = '';
			$strVal = '';
			reset($arrFld);
			while(list($strFName, $vVal) = each($arrFld))
			{
				// Validation.
				if(empty($strFName)) continue;
				if(is_array($vVal)) continue;
				if(is_object($vVal)) continue;
				// Add field name.
				if($strName != '') $strName .= ',';
				$strName .= $strFName;

				if(is_null($vVal)) $sVal = ' NULL ';
				else
				{
					$vVal = strval($vVal);
					$vVal = addslashes($vVal);
					$sVal = '\'' . $vVal .  '\'';
				}// End of if(is_null($vVal))
				// Add values list.
				if($strVal != '') $strVal .= ',';
				$strVal .= $sVal;
			}// End of while(list($strFName, $vVal) = each($arrFld))
			// Nothing composed
			if(empty($strName)) return $bResult;
			
			// Compose SQL statement.
			$strSQL = 'INSERT INTO ';
			$strSQL .= $strTblName;
			$strSQL .= "($strName)";
			$strSQL .= " VALUES ($strVal) ";
			$ok = $this->Execute($strSQL);
			//if(empty($ok)) return $bResult;
			if(empty($ok))
			{		
				$errMsg = trigger_error("Error: Execute SQL: $strSQL. ** Error message: " . $this->ErrorMsg() , E_USER_ERROR);
				return $errMsg;
			}
			$iRef = $this->idbconn->Insert_ID();
			if($iRef === false) return $bResult;
			$this->intRecordId = $iRef;
			$bResult = true;
			return $bResult;
		} // End of function insertRcd
		/**
		 * function db::cpsFldUpdtSQLSeg return field updating segment of SQL statement.
		 * @param - Array - Field list in array.
		 * @return - String - Field update segment of SQL statement.
		*/
		function cpsFldUpdtSQLSeg($arrFld)
		{// return field updating segment of SQL statement.
			$strResult = '';
			if(!is_array($arrFld))
			{
				// Compose message.
				$strMsg = 'Pass-in required array.';
				$strMsg .= 'Fields updating segment composing terminated!';
				trigger_error($strMsg , E_USER_ERROR);
				return $strResult;
			}// End of if(!is_array($arrFld))
			if(empty($arrFld))
			{
				// Compose message.
				$strMsg = 'Field list is empty.';
				$strMsg .= 'Fields updating segment composing terminated!';
				trigger_error($strMsg , E_USER_ERROR);
				return $strResult;
			}// End of if(empty($arrFld))
			reset($arrFld);
			while(list($sName, $val) = each($arrFld))
			{
				if(is_array($sName)) continue;
				if(is_object($sName)) continue;
				if(is_array($val)) continue;
				if(is_object($val)) continue;
				if(!empty($strResult)) $strResult .= ', ';
				if(is_null($val)) $sVal = ' NULL ';
				else
				{
					$val = strval($val);
					// Prevent quotes' problem.
					$val = addslashes($val);
					$sVal = '\'' . $val .  '\'';
				}// End of if(!is_null($val))
				$strResult .= " $sName = $sVal ";
			}// End of while(list($sName, $val) = each($arrFld))
			return $strResult;
		} // End of function cpsFldUpdtSQLSeg
	}// End of Class db

?>