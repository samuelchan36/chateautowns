<?php   
/** Database Operations
* @package System
* @author Lucian Grecu
*/


class CDatabase {

	var $mHost = DB_HOST;
	var $mUser = DB_USER;
	var $mDb = DB_DB;
	var $mPass = DB_PWD;
	var $mConnection;
	var $mQueryCnt = 0;
	var $mLastInserted;
	var $mAffectedRows;
	var $mNewCols;
	var $mLastQueryTime;


	function __construct($connect = false) {
		if ($connect) {
			$this->connect();
			$this->query("SET NAMES 'utf8mb4'");
		}
	}

	/** connect to the DB server */
	function connect() {
		$con = mysqli_connect($this->mHost,$this->mUser,$this->mPass);
		if (!$con) {
			if (DEBUG_MODE != "off") die2($err); else {
				//notifyAdmin($err);
				//showMaintenance("error");
			}

		}
		$this->mConnection = $con;
		if (!mysqli_select_db($this->mConnection, $this->mDb)) {
			$this->dbError(mysqli_error($this->mConnection));
		} // if
		return $this->mConnection;
	}

	/** comment here */
	function dbError($err) {
			file_put_contents(ROOT_DIR ."/media/logs/db-errors.txt", "--------------- " . date("F d, Y H:i") . " ------------------------\n" . $err . "\n--------------------------\n", FILE_APPEND)  ;
			ob_start();

			if (DEBUG_MODE == "on") error($err);
			if (DEBUG_MODE == "full") die2($err);
			if (DEBUG_MODE == "off") Return true;
	
	}

	/** run this query on the server */
	function query($pSql, $log = true) {
		$ret = $this->execute($pSql);
		if (!$ret) {
	  		$vErrMsg = mysqli_error($this->mConnection);
			$msg = "Cannot query the database. <p><b>$pSql </b><p>Error:<br>$vErrMsg<br>Trace: ".getFileTrace();
			$this->dbError($msg);
		} else {
		  return $ret;
		} 
	}

	/** comment here */
	function execute($pSql) {
		$vResult = mysqli_query($this->mConnection, $pSql);
		$this->mLastInserted = mysqli_insert_id($this->mConnection);
		$this->mAffectedRows = mysqli_affected_rows($this->mConnection);
		if (LOG_QUERIES == "yes" && $pSql) mysqli_query($this->mConnection, "insert into cms_log_queries(TimeStamp, Response, Query) values(unix_timestamp(), ".($vResult ? 1 : 0).", '".addslashes2($pSql)."')");
//		$this->mLastQueryTime = $vDiff;
		Return $vResult;
	}

	/** get one row/array from this query */
	function getRow($pSql) {
		$vResult = $this->query($pSql);
		if ($vRow = mysqli_fetch_assoc($vResult)) {
			return $vRow;
		} // if
		return array();
	}

	/** get one row as object from this query */
	function getRowObj($pSql) {
		$vResult = $this->query($pSql);
		if ($vRow = mysqli_fetch_object($vResult)) {
		   $fields = mysqli_num_fields($vResult);
		   for ($i=0; $i < $fields; $i++) {
			   $type  = mysqli_field_type($vResult, $i);
			   $name  = mysqli_field_name($vResult, $i);
			   switch($type) {
				  case "int":
					settype($vRow->{$name}, "int");
					break;
				  case "float":
					settype($vRow->{$name}, "float");
					break;
				  default:
					settype($vRow->{$name}, "string");
					break;
			   }
	  	   }
			return $vRow;
		} // if
		return null;
	}

	/** get all rows, return a 2D array - string-indexed = column names */
	function getAll($pSql) {
		$vReturnHash = array();
		$vResult = $this->query($pSql);
		while ($vRow = mysqli_fetch_assoc($vResult)) {
			if (count($vRow)==1) {
				reset($vRow);
				$vReturnHash[] = current($vRow);
			} else {
				$vReturnHash[] = $vRow;
			} // else
		} // while
		return $vReturnHash;
	}

	/** get all rows, ALWAYS return a 2D array - string-indexed = column names */
	function getAllTrue($pSql) {
		$vReturnHash = array();
		$vResult = $this->query($pSql);
		while ($vRow = mysqli_fetch_assoc($vResult)) {
			$vReturnHash[] = $vRow;
		} // while
		return $vReturnHash;
	}

	/** comment here */
	function getAllAssoc($pSql) {
		$vReturnHash = array();
		$vResult = $this->query($pSql);
		while ($vRow = mysqli_fetch_assoc($vResult)) {
			if (count($vRow)==1) {
				reset($vRow);
				$vReturnHash[$vRow["ID"]] = current($vRow);
			} else {
				$vReturnHash[$vRow["ID"]] = $vRow;
			} // else
		} // while
		return $vReturnHash;
	}

	function getAllExtended($pSql) {
		$vReturnHash = array();
		$vResult = $this->query($pSql);

		$mNewCols=array();
		for($i=0;$i<mysqli_num_fields($vResult);$i++) {
			$mNewCols+=array($i => mysqli_field_table($vResult, $i).".".mysqli_field_name($vResult, $i));

		}

		while ($vRow = mysqli_fetch_assoc($vResult)) {
			if (count($vRow)==1) {
				reset($vRow);
				$vReturnHash[] = current($vRow);
			} else {
				$vReturnHash[] = $vRow;
			} // else
		} // while

		return $mNewCols;
	}

	/** get all rows, return a 2D array - number-indexed starting from 0 */
	function getAll2($pSql) {
		$vReturnHash = array();
		$vResult = $this->query($pSql);
		while ($vRow = mysqli_fetch_row($vResult)) {
			if (count($vRow)==1) {
				reset($vRow);
				$vReturnHash[] = current($vRow);
			} else {
				$vReturnHash[] = $vRow;
			} // else
		} // while
		return $vReturnHash;
	}

	function getAll3($pTable, $pLabelCol, $pValCol = "") {
		$vSql = "SELECT $pLabelCol as lab";
		if ($pValCol) $vSql .= ",$pValCol as val";
		$vSql .= " FROM $pTable ORDER BY 1 ASC";
		Return $this->getAll($vSql);
	}

	/** 2 fields only, get all rows, simpler, return an array_key is ids, array_value is value */
	function getAll4($pSql) {
		$vReturnAry = array();
		$rows = $this->getAll2($pSql);
		foreach ($rows AS $row) {
			$vReturnAry[$row[0]] = $row[1];
		} // for
		return $vReturnAry;
	}


	/** if there is any row, return bool */
	function getRowExisted($pTableName,$pCondQuery="") {
		if ($pCondQuery!="") {
			$vCond = "WHERE $pCondQuery";
		} // if
		$vSql = "SELECT * FROM $pTableName $vCond";
		$vRow = $this->getAll($vSql);
		return (count($vRow)>=1);
	}

	/** get one value from the DB */
	function getValue($pTableName,$pField,$pCondQuery="") {
		$vCond = "";
		if ($pCondQuery!="") {
			$vCond = "WHERE $pCondQuery";
		} // if
		$vSql = "SELECT $pField FROM $pTableName $vCond";
		$vResult = $this->query($vSql);
		$vRow = mysqli_fetch_row($vResult);
		if ($vRow) Return $vRow[0]; else Return "";
	}

	/** get count(something) from the DB */
	function getCount($pTableName,$pField="*",$pCondQuery="") {
		if ($pCondQuery!="") {
			$vCond = "WHERE $pCondQuery";
		} // if
		$vSql = "SELECT COUNT($pField) AS cnt FROM $pTableName $vCond";
		$vRow = $this->getRow($vSql);
		if (count($vRow)==0) {
			return 0;
		} // if
		return intval($vRow['cnt']);
	}

	/** get sum(something) from the DB */
	function getSum($pTableName,$pField,$pCondQuery="") {
		if ($pCondQuery!="") {
			$vCond = "WHERE $pCondQuery";
		} // if
		$vSql = "SELECT SUM($pField) AS total FROM $pTableName $vCond";
		$vRow = $this->getRow($vSql);
		return $vRow['total'];
	}

	/** get a row but with all empty fields */
	function clearValues($vRow) {
		$vReturnHash = array();
		foreach ($vRow AS $vKey=>$vValue) {
			$vReturnHash[$vKey] = "";
		} // foreach
		return $vReturnHash;
	}

	/** generate update query faster */
	function makeUpdateQuery($pDataAry) {
		$vUpdateAry = array();
		foreach ($pDataAry AS $vField=>$vValue) {
			$vUpdateAry[] = addslashes2($vField)."='".addslashes2($vValue)."'";
		} // for
		return implode(",",$vUpdateAry);
	}

	/** generate insert query */
	function makeInsertQuery($pDataAry) {
		$vFieldAry = array();
		$vValueAry = array();
//		die2($pDataAry);
		foreach ($pDataAry AS $vField=>$vValue) {
			if (isset($vValue)) {
			  $vFieldAry[] = addslashes2($vField);
			  $vValueAry[] = "'".addslashes2($vValue)."'";
			}
		} // foreach
		$vFields = implode(",",$vFieldAry);
		$vValues = implode(",",$vValueAry);
		return "($vFields) VALUES ($vValues)";
	}

	/** comment here */
	function makeMultiInsertQuery($fields) {
		$values = array();
//		die2($pDataAry);
		foreach ($fields AS $vField=>$vValue) {
			if (isset($vValue)) {
			  $values[] = "'".addslashes2($vValue)."'";
			}
		} // foreach
		Return "(" . implode(",",$values) . ")";
	}

	/** comment here */
	function getInsertFields($fields) {
		$names = array();
		foreach ($fields as $key=>$val) {
			$names[] = $key;
		}
		Return implode(", ", $names);
	}

	/** make one insert query with multiple sets of value */
	function makeMultInsert($vFieldAry,&$vDataAry) {
		$vTmpAry = array();
		foreach ($vDataAry AS $vValueAry) {
			$vTmpAry2 = array();
			foreach ($vValueAry AS $vValue) {
				$vTmpAry2[] = "'".addslashes2($vValue)."'";
			} // for
			$vTmpAry[] = '('.implode(',',$vTmpAry2).')';
		} // foreach
		$vFields = implode(',',$vFieldAry);
		$vValues = implode(',',$vTmpAry);
		unset($vTmpAry);
		return "($vFields) VALUES $vValues";
	}

	/** add slashes to multiple vars */
	function addSlashAry($pFieldAry) {
		foreach ($pFieldAry AS $vField=>$vValue) {
			$pFieldAry[$vField] = addslashes2($vValue);
		} // foreach
		return $pFieldAry;
	}

	function getMax($pTable, $pField, $pCond){
		$vSql = "select max($pField) as max from $pTable where $pCond";
		$vMax = $this->getRow($vSql);
		return $vMax["max"];
	}

	function getMin($pTable, $pField, $pCond){
		$vSql = "select max($pField) as min from $pTable where $pCond";
		$vMax = $this->getRow($vSql);
		return $vMax["max"];
	}

	function getFieldsObject($pTable) {
		
		$data = $this->getAll("show fields from $pTable");
		$vObject = new stdClass();
		foreach ($data as $key=>$val) {
				$vObject->{$val["Field"]} = "";
				$type = substr($val["Type"], 0, 3);
			   switch($type) {
				  case "int":
					settype($vObject->{$val["Field"]}, "int");
					break;
				  case "flo":
					settype($vObject->{$val["Field"]}, "float");
					break;
				  default:
					settype($vObject->{$val["Field"]}, "string");
					break;
			   }
				if ($val["Default"]) $vObject->{$val["Field"]} = $val["Default"];
//			   if ($val["Null"] == "NO") {
//				   if ($type == "enu") {
//					   if ($val["Default"]) $vObject->{$val["Field"]} = $val["Default"]; else {
//						   $tmp = explode(",", str_replace(array("enum(", ")", "'"), "", $val["Type"]));
//						   $vObject->{$val["Field"]} = $tmp[0];
//					   }
//				   }
//				   if ($type == "int" || $type == "flo") $vObject->{$val["Field"]} = 0;
//			   }

  		}
//		die2($vObject);
		Return $vObject;
	}

	function getLastID() {
		return $this->mLastInserted;
	}

	function getAffectedRows() {
		return mysqli_affected_rows($this->mConnection);
	}

	function getRandValue($pTable, $pField) {
		$vSql = "SELECT $pField FROM $pTable ORDER BY RAND() LIMIT 1";
		$vResult = $this->getRow($vSql);
		Return $vResult[$pField];
	}


	function getFulltextKey($table){
		/* grab all keys of db.table */
		$indices=mysqli_query("SHOW INDEX FROM $table")
			 or die(mysqli_error());
		$indices_rows=mysqli_num_rows($indices);

		/* grab only fulltext keys */
		for($nth=0;$nth<$indices_rows;$nth++){
			$nth_index=mysqli_result($indices,$nth,'Index_type');
			if($nth_index=='FULLTEXT'){
				$match_a[].=mysqli_result($indices,$nth,'Column_name');
			}
		}

		/* delimit with commas */
		$match=implode(',',$match_a);

		return $match;
	}

	function get_field_type($field_type)
	{
		switch ($field_type)
		{
		case MYSQLI_TYPE_DECIMAL:
		case MYSQLI_TYPE_NEWDECIMAL:
		case MYSQLI_TYPE_FLOAT:
		case MYSQLI_TYPE_DOUBLE:
			return 'float';

		case MYSQLI_TYPE_BIT:
		case MYSQLI_TYPE_TINY:
		case MYSQLI_TYPE_SHORT:
		case MYSQLI_TYPE_LONG:
		case MYSQLI_TYPE_LONGLONG:
		case MYSQLI_TYPE_INT24:
		case MYSQLI_TYPE_YEAR:
		case MYSQLI_TYPE_ENUM:
			return 'int';

		case MYSQLI_TYPE_TIMESTAMP:
		case MYSQLI_TYPE_DATE:
		case MYSQLI_TYPE_TIME:
		case MYSQLI_TYPE_DATETIME:
		case MYSQLI_TYPE_NEWDATE:
		case MYSQLI_TYPE_INTERVAL:
		case MYSQLI_TYPE_SET:
		case MYSQLI_TYPE_VAR_STRING:
		case MYSQLI_TYPE_STRING:
		case MYSQLI_TYPE_CHAR:
		case MYSQLI_TYPE_GEOMETRY:
			return 'string';

		case MYSQLI_TYPE_TINY_BLOB:
		case MYSQLI_TYPE_MEDIUM_BLOB:
		case MYSQLI_TYPE_LONG_BLOB:
		case MYSQLI_TYPE_BLOB:
			return 'binary';

		default:
			trigger_error("unknown type: $field_type");
			return 's';
		}
	}
}


function mysqli_field_type( $result , $field_offset ) {
    static $types;

    $type_id = mysqli_fetch_field_direct($result,$field_offset)->type;

    if (!isset($types))
    {
        $types = array();
        $constants = get_defined_constants(true);
        foreach ($constants['mysqli'] as $c => $n) if (preg_match('/^MYSQLI_TYPE_(.*)/', $c, $m)) $types[$n] = $m[1];
    }

    return array_key_exists($type_id, $types)? $types[$type_id] : NULL;
}

function mysqli_field_name($result, $field_offset)
{
    $properties = mysqli_fetch_field_direct($result, $field_offset);
    return is_object($properties) ? $properties->name : null;
}

$db = new CDatabase(true); 

?>