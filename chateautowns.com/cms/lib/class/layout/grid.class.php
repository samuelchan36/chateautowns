<?php   
/** CGrid
* @package Layout
* @author cgrecu
*/


class CGrid extends CBoxTable{

  var $mID;
  var $mColsDataFormat;
  var $mColsWidths = array();
  var $mColsAligns = array();
  var $mDrawAlternate = false;
  var $mDetailedClasses = array();
  var $mColSpan;
  var $mIgnoreBreakers;
  var $mClasses = array("table" => "", "header" => "", "body" => "", "bodyalt" => "", "breaker"=> "", "footer" => "");

  function CGridTable($pBody, $pHeader = "", $pFooter = "", $pTemplate = "") {
  	parent::__construct($pBody, $pHeader, $pFooter);
	if ($pTemplate) $this->setTemplate($pTemplate);
  }

  function displayBody() {
  	$tmp = "";

	$formatFields = false; if (!(empty($this->mColsDataFormat))) $formatFields = true;
	$colidx = 0;
	$rowindex = 0;
	foreach ($this->mBody AS $key=>$vRows) {

	  if ($this->mID) $vID = "id=\"".$this->mID."_rowid_".$rowindex."\"";else $vID = "";
	  $tmp .= "<tr $vID>\n"; $idx = 0; 
	  $vCount = count($vRows);
	  $idx = 0;
	  foreach ($vRows as $key2=>$vCell) {
		$attr = "";
		if ($vCount != $this->mColSpan) $attr .= " colspan=\"".$this->mColSpan."\"";
		if ($formatFields) $vCell = sprintf($ColsDataFormat[$idx],$vCell);
		if ($vCount == 1 && !$this->mIgnoreBreakers) $vStyle = $this->mClasses["breaker"]; 
		else {
		  if (empty($this->mDetailedClasses)) 
			  if ($this->mDrawAlternate) {
				if ($colidx % 2 == 0) $vStyle = $this->mClasses["body"];  else $vStyle = $this->mClasses["bodyalt"];
			  } else {
				  $vStyle = $this->mClasses["body"];
			  }
		  else $vStyle = $this->mDetailedClasses[$idx];
		}

		if ($vCount == $this->mColSpan && is_array($this->mColsWidths) && isset($this->mColsWidths[$idx])) $attr .= "width=\"" . $this->mColsWidths[$idx] . "\""; 
		if ($vCount == $this->mColSpan && isset($this->mColsAligns) && isset($this->mColsAligns[$idx])) $attr .= " align=\"". $this->mColsAligns[$idx] .'"';

		if (!$vCell && !($vCell ===0)) $vCell = "&nbsp;";
		$tmp .= "<td class=\"$vStyle\" " . $attr . " >" . $vCell . "</td>\n";
		$idx ++;
	  }
	  $colidx ++;
	  $rowindex++;
  	  $tmp .= "</tr>\n";
	}

	Return $tmp;
  }


  function displayHeader() {

	$vStyle = $this->mClasses["header"];
	$vCellsCount = count($this->mHeader);

	$tmp = "<tr>\n";
	$idx = 0;	$attr = "";
	foreach ($this->mHeader as $key=>$val) {
	  $attr = "";
	  if ($this->mColSpan == $vCellsCount && !empty($this->mColsWidths)) $attr .= "width=\"" . $this->mColsWidths[$idx] . "\"";
	  if ($vCellsCount != $this->mColSpan && !$idx) $attr .= " colspan=\"" .(1 + $this->mColSpan - $vCellsCount). "\"";
	  if (!empty($this->mColsAligns)) $attr .= " align=\"". $this->mColsAligns[$idx] ."\" ";
	  $tmp .= "<td class=\"$vStyle\" $attr >$val</td>\n";
	  $idx ++;
	}
	$tmp .= "</tr>\n";
	Return $tmp;
  }

  function displayFooter() {
	$vStyle = $this->mClasses["footer"];
	$vCellsCount = count($this->mFooter);

	$tmp = "<tr>\n";
	$idx = 0;	$attr = "";
	foreach ($this->mFooter as $key=>$val) {
	  $attr = "";
	  if ($this->mColSpan == $vCellsCount && !empty($this->mColsWidths)) $attr .= "width=\"" . $this->mColsWidths[$idx] . "\"";
	  if ($vCellsCount != $this->mColSpan && !$idx) $attr .= " colspan=\"" .(1 + $this->mColSpan - $vCellsCount). "\"";
	  if (isset($this->mColsAligns)) $attr .= " align=\"". $this->mColsAligns[$idx] ."\" ";
	  $tmp .= "<td class=\"$vStyle\" $attr >$val</td>";
	  $idx ++;
	}
	$tmp .= "</tr>\n";
	Return $tmp;
  }
  
  function setColsDataFormat($pDataFormats) {
	$this->mColsDataFormat = array($pDataFormats);
  }

  function setColsWidths($pWidths) {
	$this->mColsWidths = $pWidths;
  }

  function setColsAligns($pAligns) {
	$this->mColsAligns = $pAligns;
  }

  function setColsClass($pClasses) {
	$this->mDetailedClasses = $pClasses;
  }

  function setClass($pClasses) {
	$this->mClasses = $pClasses;
  }

  /** comment here */
  function setTemplate($pName) {
  	  $this->mClasses = array("table"=>$pName, "header"=>$pName."_header", "body"=>$pName."_body", "bodyalt"=>$pName."_bodyalt", "breaker"=>$pName."_breaker", "footer"=>$pName."_footer");
  }

  function drawAlternate($pIgnoreBreakers = false) {
	$this->mDrawAlternate = !($this->mDrawAlternate);
	$this->mIgnoreBreakers = $pIgnoreBreakers;
  }

  function display() {
	if ($this->mClasses["table"]) $this->mClass = $this->mClasses["table"];
	$this->getColsCount();
	if ($this->mClass) $class =  " class=\"".$this->mClass."\" "; else $class = "";
	$tmp = "<table id=\"".$this->mID."\" cellpadding=\"".$this->mCellPadding."\" cellspacing=\"".$this->mCellSpacing."\" border=\"".$this->mBorder."\" $class  ".$this->constructStyle("table").">";
	if ($this->mHeader != "") $tmp .= $this->displayHeader();
	$tmp .= $this->displayBody();
	if ($this->mFooter != "") $tmp .= $this->displayFooter();
	$tmp .= "</table>";
//	die($tmp);
	Return $tmp;
  }

  /** comment here */
  function getColsCount() {
	if ($this->mColSpan) Return; 
	$vColSpan = 0;
	if (is_array($this->mHeader)) 	
	  $vColSpan = count($this->mHeader);
	if (is_array($this->mFooter)) 	
	  $vColSpan = max($vColSpan, count($this->mFooter));
	if (is_array($this->mBody)) {
	  foreach ($this->mBody as $key=>$val) {
		  if (!is_array($val)) break;
		  $vColSpan = max($vColSpan, count($val));
	  }
	}
	$this->mColSpan = $vColSpan;
  }


}

?>