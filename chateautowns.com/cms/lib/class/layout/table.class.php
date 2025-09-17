<?php   
/** CTable
* @package Layout
* @since March 11
* @author cgrecu
*/


class CTable extends CStyle{

  var $mHeader;
  var $mBody;
  var $mFooter;

  var $mWidth = "100%";
  var $mID;
  var $mName;
  var $mHeight;
  var $mBorder = 0;
  var $mCellPadding = 0;
  var $mCellSpacing = 0;
  var $mSummary;
  var $mClass;
  var $mJavaScript;
  var $mBorderCollapse = "collapse";

  function __construct($pID = "", $pWidth = "100%", $pHeight = "", $pBorder = "0", $pCellPadding = "0", $pCellSpacing = "0", $pClass = "") {
	parent::__construct();
	if ($pID != "") {
	  $this->mID = $pID;
	  $this->mWidth = $pWidth;
	  $this->mHeight = $pHeight;
	  $this->mBorder = $pBorder;
	  $this->mCellPadding = $pCellPadding;
	  $this->mCellSpacing = $pCellSpacing;
	  $this->mClass = $pClass;
	}

  }
  
  function setJavaScript($pKey, $pEvent) {
  	$this->mJavaScript->setJavaScript($pKey, $pEvent);
  }
  /** sets the width property */
  function setWidth($pWidth) {
  	$this->mWidth = $pWidth;
  }

  /** sets the height property */
  function setHeight($pHeight) {
  	$this->mHeight = $pHeight;
  }

  /** sets the border property */
  function setBorder($pBorder) {
  	$this->mBorder = $pBorder;
  }

  /** sets the cellpadding and cellspacing property */
  function setPadding($pCellSpacing, $pCellPadding) {
  	$this->mCellPadding = $pCellPadding;
	$this->mCellSpacing = $pCellSpacing;
  }

  /** sets the summary property */
  function setSummary($pSummary) {
  	$this->mSummary = $pSummary;
  }

  /** sets the summary property */
  function setClass($pClass) {
  	$this->mClass = $pClass;
  }


  /** creates html code for <table> element */
  function openTable() {
	$tmp = "<table";
  	if ($this->mID != "")  $tmp .= " id=\"$this->mID\"";
	if ($this->mWidth != "")  $tmp .= " width=\"$this->mWidth\"";
	$tmp .= " border=\"$this->mBorder\" cellpadding=\"$this->mCellPadding\" cellspacing=\"$this->mCellSpacing\" border-collapse=\"$this->mBorderCollapse\" ";
	if ($this->mWidth != "") $tmp .= " width=\"$this->mWidth\"";
	if ($this->mHeight != "") $tmp .= " height=\"$this->mHeight\"";
	if ($this->mSummary != "") $tmp .= " summary=\"$this->mSummary\"";
	$tmp .= $this->mJavaScript->display();
	if ($this->mClass != "") $tmp .= " class=\"$this->mClass\""; else $tmp .= $this->constructStyle("table");

	$tmp .= ">\n";
	Return $tmp;
  }

  /** creates html code for closing table element */
  function closeTable() {
  	Return "\n</table>\n";
  }

  /** creates html code for <tr> element */
  function openTR($pAttr=array()) {
	$tmp = "<tr";
	foreach ($pAttr as $key=>$val) {
		$tmp .= " $key=\"$val\"";	
	}
	$tmp .= ">";
	Return $tmp;	
  }
  
  /** creates html code for a full <tr> element, $pText is html code for the enclosing <td> */
  function drawTR($pText, $pAttr=array()) {
	$tmp = "<tr";
	foreach ($pAttr as $key=>$val) {
		$tmp .= " $key=\"$val\"";	
	}
	$tmp .= ">$pText</tr>";
	Return $tmp;	
  }

  /** creates html code for a full <tr><td> element, $pText is enclosing text */
  function drawFullTR($pText, $pAttr = array()) {
	$tmp = "<tr";
	foreach ($pAttr as $key=>$val) {
		$tmp .= " $key=\"$val\"";	
	}
	$tmp .= "><td>$pText</td></tr>";
	Return $tmp;	
  }

  /** creates html code for a full <tr> element, $pCells is array of cells */
  function drawTR2($pCells, $pAttr=array()) {
	$tmp = "<tr";
	foreach ($pAttr as $key=>$val) {
		$tmp .= " $key=\"$val\"";	
	}
	$tmp .= "><td>".implode("<td>", $pCells)."</td></tr>";
	Return $tmp;	
  }

  /** creates html code for a full td element */
  function drawTD($pText, $pAttr=array()) {
	$tmp = "<td";
	foreach ($pAttr as $key=>$val) {
		if (!empty($val)) {
			$tmp .= " $key=\"$val\"";		
		} // if
		
	}
	$tmp .= ">$pText</td>";
	Return $tmp;	
  }

  /** creates html element for a closing <tr> */
  function closeTR() {
  	Return "</tr>";
  }

  /** creates html code for a <td> element */
  function openTD($pAttr=array()) {
	$tmp = "<td";
	foreach ($pAttr as $key=>$val) {
		if (!empty($val)) {
			$tmp .= " $key=\"$val\"";		
		} // if
	}
	$tmp .= ">";
	Return $tmp;	
  }

  /** creates html code for closing a <td> element */
  function closeTD() {
  	Return "</td>";
  }


  function displayHeader() {
	$tmp = "";
  	if (empty($this->mHeader)) Return $tmp;
	else {
	  //display the header  	
	  $vHeadStyle = $this->constructStyle("header");
	  foreach ($this->mHeader as $key=>$val) {
	  	if (is_array($val)) {
		  $tmp .= "<td ";
		  foreach ($val as $key2=>$val2) {
		  	if ($key2 != "data") $tmp .= " $key2=\"$val2\" "; 
		  }
		  $tmp .= " $vHeadStyle>" . $val["data"] . "</td>";
		} else {
			$tmp .= "<td $vHeadStyle>$val</td>";
		}
	  	
	  }
	}
	Return $tmp;
  }

  function displayBody() {
	$tmp = "";
  	if (empty($this->mBody)) Return $tmp;
	else {
	  //display the body  	
	  $vHeadStyle = $this->constructStyle("body");
	  foreach ($this->mBody as $key0=>$val0) {
		foreach ($val0 as $key=>$val) {
		  if (is_array($val)) {
			$tmp .= "<td ";
			foreach ($val as $key2=>$val2) {
			  if ($key2 != "data") $tmp .= " $key2=\"$val2\" "; else $tmp .= " $vBodyStyle>" . $val2 . "</td>";
			}
		  } else {
			  $tmp .= "<td $vBodyStyle>$val</td>";
		  }
		}	  	
	  }
	}
	Return $tmp;
  	
  }


  function displayFooter() {
  	
  }


  /** displays a simple table */
  function display() {
	$tmp = $this->openTable();
	$tmp .= $this->displayHeader();
	$tmp .= $this->displayBody();
	$tmp .= $this->closeTable();
	Return $tmp;
  }
}

?>
