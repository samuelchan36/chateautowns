<?php   
/** CFormTable
* @package Layout
* @since March 11
* @author cgrecu
*/


class CFormTable extends CGridTable {

  var $mForm;//form object, passed by reference
  var $mObjects;

  function __construct($pID = "", $pWidth = "100%", $pHeight = "", $pBorder = "0", $pCellPadding = "0", $pCellSpacing = "0", $pClass = "") {
	parent::__construct($pID, $pWidth, $pHeight, $pBorder, $pCellPadding, $pCellSpacing, $pClass);
	$this->mObjects = array();
  }


  function setObjects($pObjects) {
  	$this->mObjects = $pObjects;
  }

  /** add new ref obj to the end of array */
  function addObject(&$pObj,$pIndex=-1) {
	  if ($pIndex==-1) {
		  $this->mObjects[] = $pObj;
	  } else {
		  $vRight = array_splice($this->mObjects,$pIndex);
		  $this->mObjects[] = $pObj;
		  $this->mObjects = array_merge($this->mObjects,$vRight);
	  } // else
  }

  /** creates html code for a simple table containing a whole form and its elements */
  function displayBody() {
	if (!(empty($pObjects))) $this->mObjects = $pObjects;
	//print_r($pObjects);
	if (empty($this->mColsWidths)) $this->mColsWidths = array("50%", "50%");
	$tmp = "";
	if ($this->mDrawAlternate) {
	  $vStyle1 = $this->constructStyle("body1");
	  $vStyle2 = $this->constructStyle("body2");
	}
	$vStyle = $this->constructStyle("body");
	$vBreakerStyle = $this->mTemplates["breaker"];
	$vLabelStyle = $this->constructStyle("label");
	if (empty($this->mTemplates["label"])) {
	  $vLabelStyle = "style=\"color:".$this->mColText2.";font-size:8pt;font-weight:normal;vertical-align:middle;\"";
	}
	$rowidx = 0;

	foreach ($this->mObjects as $key=>$val) {
	  if (isset($val->mTemplates["mylabel"])) {
		$vMyLabelStyle = $val->constructStyle("mylabel");
	  } else {
		$vMyLabelStyle = $vLabelStyle;	  	
	  }
	  $vMyClass = get_class($val);
	  if ($val->mType != "hidden") {
		$vAttr = array("style"=>";"); // son: ???
		unset($vAttr["colspan"]);
		if ($this->mDrawAlternate) {
		  if ($rowidx % 2 == 0) $vStyle = $vStyle1;
		  else $vStyle = $vStyle2;
		}
		$tmp .= "<tr $vStyle>";
		if (isset($val->mLabel)) {
		  switch($val->mLabelAttach) {
			  case "top":
				$vAttr["colspan"] = 2;
				$vAttr["width"] = "100%";
				$tmp .= "<td $vMyLabelStyle colspan=\"2\" valign=\"top\" align=\"".$val->mLabelAlign."\">".$val->displayLabel()."</td>";
				$tmp .= "</tr><tr $vStyle>";
				$vAttr["align"] = $val->mAlign;
				$vAttr["width"] = "100%";
				$tmp .= $this->drawTD($val->display() . $val->mExample,$vAttr);
				break;
			  case "left":
				if ($val->mType == "radio" || $val->mType == "checkbox" || $vMyClass == "ccheckbox") {
				  $tmp .= "<td colspan=\"2\" $vMyLabelStyle align=\"".$val->mLabelAlign."\">".$val->displayLabel().$val->display()."</td>";
				} else {
				  $vAttr["width"] = $this->mColsWidths[0];
				  $tmp .= "<td $vMyLabelStyle align=\"".$val->mLabelAlign."\" valign=\"top\">".$val->displayLabel()."</td>";
				  $vAttr["align"] = $val->mAlign;
				  $vAttr["width"] = $this->mColsWidths[1];
				  $tmp .= $this->drawTD($val->display() . $val->mExample,$vAttr);
				}
				break;
			  case "right":
				if ($val->mType == "radio" || $val->mType == "checkbox" || $vMyClass == "ccheckbox") {
				  $tmp .= "<td colspan=\"2\" $vMyLabelStyle align=\"".$val->mLabelAlign."\">".$val->display().$val->displayLabel()."</td>";
				} else {
				  $vAttr["align"] = $val->mAlign;
				  $vAttr["width"] = $this->mColsWidths[0];
				  $tmp .= $this->drawTD($val->display(),$vAttr);
				  $vAttr["width"] = $this->mColsWidths[1];
				  $tmp .= "<td $vMyLabelStyle align=\"".$val->mLabelAlign."\" valign=\"top\">".$val->displayLabel()."</td>";
				}
				break;
			  default:
				$vAttr["width"] = $this->mColsWidths[0];
				$tmp .= "<td $vMyLabelStyle align=\"".$val->mLabelAlign."\" valign=\"top\">".$val->displayLabel()."</td>";
				$vAttr["align"] = $val->mAlign;
				$vAttr["width"] = $this->mColsWidths[1];
				$tmp .= $this->drawTD($val->display(),$vAttr);
				break;
		  }
		} else {
		  $vAttr["colspan"] = 2;
		  $vAttr["align"] = $val->mAlign;
		  if (empty($val->mTemplates["div"])) $val->mTemplates["div"] = $vBreakerStyle;
		  $tmp .= $this->drawTD($val->display(),$vAttr);
		}
		$rowidx ++;
		$tmp .= "</tr>";
	  } else {
	  	$tmp .= $val->display();
	  }
	}
	Return $tmp;
  }

}

?>
