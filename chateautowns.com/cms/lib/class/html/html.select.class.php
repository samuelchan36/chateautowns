<?php   
/** select objects
* @package HTML
* @since February 06
* @author cgrecu
*/

class CSelect extends CFormElement {

	var $mOptions;
	var $mExtraOption = array();
	var $mDatabase;

  /** constructor */
  function __construct($pID, $pOptions, $pAttributes = array(), $help = array()) {
	$this->mDatabase = $GLOBALS["db"];
	if (!isset($pAttributes["class"])) $pAttributes["class"] = "";
	$pAttributes["class"] .= " js-select";
	parent::__construct($pID, $pAttributes, $help);	  
	if (isset($help["options-source"])) {
		switch($help["options-source"][0]) {
			case "field": $this->getOptionsFromField($help["options-source"][1], $help["options-source"][2]);
				
		
		}
	} else
		$this->setOptions($pOptions);
  }

  /** sets the options property (an array where the key maps to select-option-value and the value maps to select-option-text */
  function setOptions($pOptions) {
  	$this->mOptions = $pOptions;
  }

  /** set the option from xxx to xxx, stepping x */
  function setOptionsNumeric($pFrom,$pTo,$pStep=1,$pDefault="",$pFront="",$pEnd="") {
	  $vOptAry = array();

	  if ($pFrom>$pTo) {
		  for ($i=$pFrom;$i>=$pTo;$i-=$pStep) {
			$vOptAry[] = array($i,$pFront.$i.$pEnd);
		  } // for
	  } else {
		  for ($i=$pFrom;$i<=$pTo;$i+=$pStep) {
			$vOptAry[] = array($i,$pFront.$i.$pEnd);
		  } // for
	  } // else
	  $this->setOptions($vOptAry,$pDefault);
  }


  /** connects to the database and retrieves an array of values */
  function getOptionsFromDB($pTable, $pLabelCol, $pValueCol,$pExtra="ORDER BY 1 ASC") {
	$vSQL = "SELECT $pValueCol, $pLabelCol FROM $pTable $pExtra";
//	var_dump($vSQL);
	$this->mOptions = $this->mDatabase->getAll2($vSQL);
	
  }

  /** connects to the database and retrieves an array of values */
  function getOptionsFromQuery($pQuery) {
	$this->mOptions = $this->mDatabase->getAll2($pQuery);
  }

  /** creates the options from the values of given ENUM column*/
  function getOptionsFromField($pTable, $pColumn = "") {
  	$vSql = "SHOW FIELDS FROM $pTable";
	$vFields = $this->mDatabase->getAll($vSql);
	foreach ($vFields as $key=>$val) {
		if (!(strpos($val["Type"],"enum") === false)) {
		  if ($val["Field"] == $pColumn || $pColumn == "") {
			$vValue = $val["Type"];
			break;
		  }
		}
	}
	if ($vValue != "") {
	  $vValue = str_replace("'","",$vValue);
	  $vValue = substr($vValue,5,-1);
	  $vValue = explode(",", $vValue);
	  foreach ($vValue as $key=>$val) {
	  	$this->mOptions[] = array($val,$val);
	  }
	}

  }

  /** comment here */
  function html() {

	if (!isset($this->attributes["name"]) || !$this->attributes["name"]) {
		if (isset($this->attributes["multiple"])) {
			$this->attributes["multiple"] = "multiple";
			$this->attributes["name"] = $this->id . "[]";
		} else {
			$this->attributes["name"] = $this->id;
		}
	}
//die2($this->attributes);
	$txt = '<select id="'.$this->id.'" ';
	foreach ($this->attributes as $key=>$val) {
		if ($key == "label") continue;
		if ($key == "value") continue;
		$txt .= ' ' . $key . '="'. htmlentities($val) .'"';
	}
	$txt .= ">\n";
	if (isset($this->attributes["placeholder"]) && $this->attributes["placeholder"]) $txt .= '<option value="'.(isset($this->attributes["placeholder-value"]) ? $this->attributes["placeholder-value"] : "").'">'.(isset($this->attributes["placeholder-value"]) ? $this->attributes["placeholder"] : "").'</option>';
	if ($this->mExtraOption) $txt .= '<option value="'.htmlentities($this->mExtraOption[0]).'">'.htmlentities($this->mExtraOption[1]).'</option>';
	foreach ($this->mOptions as $key=>$val) {
		$selected = "";
		if (isset($this->attributes["multiple"])){
			if ($this->value && !is_array($this->value)) jserror("Invalid select options ");
			if ($this->value && in_array($val[0], $this->value)) $selected = "selected";
		} else {
			if ($val[0] == $this->value) $selected = "selected";
		}
		$txt .= '<option value="'.htmlentities($val[0]).'" '. $selected . '>'. htmlentities($val[1]).'</option>' . "\n";
	}

	$txt .= "</select>\n";

	Return $txt;
  }



}
?>
