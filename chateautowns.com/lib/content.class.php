<?php 

  class CContent  {
	
	var $mDatabase;
	var $table;
	var $pk = "ID";
	var $mRowObj;
	var $mContentLabField = "Name";
	var $mToggleStates = array("enabled", "disabled");

	/** comment here */
	function __construct($pTable, $pID = "", $pk = "ID") {
	  $this->mDatabase = &$GLOBALS["db"];
	  $this->table = $pTable;
	  $this->pk = $pk;
	  $this->init($pID);
	}

	/** call this for every content class to init mrowobj */
	function init($pContentID) {
	  if (!$pContentID) $pContentID = intval($pContentID);
	  if ($pContentID) {
		  $vSql = "SELECT * FROM ".$this->table." WHERE ".$this->pk."='$pContentID'";
		  $this->mRowObj = $this->mDatabase->getRowObj($vSql);
	  } else {
		  $this->mRowObj = $this->mDatabase->getFieldsObject($this->table);
	  }
	}

	/** comment here */
	function initData($pVal) {
		foreach ($this->mRowObj as $key=>$val) {
			$this->mRowObj->$key = "";
			if (isset($this->mRowObj->$key)) $this->mRowObj->$key = $pVal[$key];

		}

	}

	/** comment here */
	function resetData() {
		foreach ($this->mRowObj as $key=>$val) {
			$this->mRowObj->$key = "";
		}
	}

	/** call this to register POST variables that match the RowObj object's properties */
	function registerForm($pArray = array()) {
	  if (empty($pArray)) $pArray = $_POST;
	  foreach (get_object_vars($this->mRowObj) AS $key=>$val) {
		  if (isset($pArray["$key"]))
			/* if variable is an array( coming from a select element) then
			**  if the array only has one element then the array is destroyed and the element is registered directly
			**	else, if the array has more than one element then the whole array will be passed as a variable
			**  WARNING: in the last case, the RowObj object cannont be saved directly to the database !! */
			if (is_array($pArray["$key"])) {
			  if (count($pArray["$key"])==1) {
				$this->mRowObj->{$key} = $pArray["$key"][0];
			  } else {
				$this->mRowObj->{$key} = $pArray["$key"];
			  }

			} else {
			  $this->mRowObj->{$key} = $pArray["$key"];
			}
	  }
//	  foreach ($this->mRowObj as $key=>$val) {
//		if (is_numeric($val)) $this->mRowObj->$key=$val * 1;
//	  }

	}


	/** call this to register POST variables that match the RowObj object's properties */
	function unregisterForm() {
	  foreach (get_object_vars($this->mRowObj) AS $key=>$val) {
		$_POST["$key"] = $this->mRowObj->{$key};
	  }
	}

	function toArray() {
		$ret = array();
		foreach (get_object_vars($this->mRowObj) AS $key=>$val) {
			$ret["$key"] = $this->mRowObj->{$key};
		}
		Return $ret;
	}

	/** comment here */
	function loadPostData() {
	  $_POST = $_SESSION["gPOST"];
	  foreach (get_object_vars($this->mRowObj) AS $key=>$val) {
		if (!$_POST[$key] && $val) $_POST[$key] = $val;
	  }
	  unset($_SESSION["gPOST"]);
	}

	/** save function */
	function save() {
		$vFields = get_object_vars($this->mRowObj);
		if ($this->mRowObj->{$this->pk}) {
			// update
			$vUpdateQuery = $this->mDatabase->makeUpdateQuery($vFields);
			$vSql = "UPDATE ".$this->table." SET $vUpdateQuery WHERE ".$this->pk."='".$this->mRowObj->{$this->pk}."'";
			$vResult = $this->mDatabase->query($vSql, $pType);
		} else {
			//insert
		  if(!$vFields["TimeStamp"]&&array_key_exists("TimeStamp",$vFields)) $vFields["TimeStamp"] = time();

		  $vInsertQuery = $this->mDatabase->makeInsertQuery($vFields);
		  $vSql = "INSERT INTO ".$this->table." $vInsertQuery";
		  $vResult = $this->mDatabase->query($vSql, $pType);
		  $this->init($this->mDatabase->getLastID());
		}
		Return $vResult;
	}

	/** save function */
	function easySave() {

		$vFields = get_object_vars($this->mRowObj);
//		die2($this->mRowObj);
		if ($this->mRowObj->{$this->pk}) {
			// update
			$vUpdateQuery = $this->mDatabase->makeUpdateQuery($vFields);
			$vSql = "UPDATE ".$this->table." SET $vUpdateQuery WHERE ".$this->pk."='".$this->mRowObj->{$this->pk}."'";
			$vResult = $this->mDatabase->query($vSql);
//			die($vSql);
		} else {
			//insert
		  if((!isset($vFields["TimeStamp"]) || !$vFields["TimeStamp"] ) && array_key_exists("TimeStamp",$vFields)) $vFields["TimeStamp"] = time();
		  $vInsertQuery = $this->mDatabase->makeInsertQuery($vFields);
		  $vSql = "INSERT INTO ".$this->table." $vInsertQuery";
//		  die($vSql);
		  $vResult = $this->mDatabase->query($vSql);
		  $this->init($this->mDatabase->getLastID());
		}
		Return $vResult;
	}

	function delete() {
	  $vSql = "DELETE FROM ".$this->table." WHERE ".$this->pk."='".$this->mRowObj->{$this->pk}."'";
	  Return $this->mDatabase->query($vSql, $pType);
	}

	/** check name for uniqueness */
	function checkName() {
	  $vCount = $this->mDatabase->getValue($this->table,"count(*)","upper(".$this->mContentLabField.") = upper('".$this->mRowObj->{$this->mContentLabField}."') and " . $this->pk . " <> '" . $this->mRowObj->{$this->pk} . "'");
	  if ($vCount > 0) Return false;
	  Return true;
	}



  }
?>