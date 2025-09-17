<?php   
/** CGridTable
* @package Layout
* @since March 11
* @author cgrecu
*/


class CGridTable extends CBoxTable{

  var $mColsDataFormat;
  var $mDrawBorders = false;
  var $mColsWidths;
  var $mColsAligns;
  var $mDrawAlternate = false;
  var $mColSpan;
  var $mIgnoreBreakers;
  var $mID;

  function __construct($pBody, $pHeader = "", $pFooter = "", $pStyle = "") {
  	parent::__construct($pBody, $pHeader, $pFooter);
  	if ($pStyle) $this->loadTemplate($pStyle);
  }

  function displayBody() {
  	$tmp = "";
	
	//original styles
	$vStyle = isset($this->mTemplates["body"])?$this->mTemplates["body"]:"";	

	if ($this->mDrawAlternate) {
	  //print_r($this->mTemplates);
	  //$vStyle1 = $this->mTemplates["body1"];
	  //$vStyle2 = $this->mTemplates["body2"];
	  $vStyle1 = $this->constructStyle("body1");
	  $vStyle2 = $this->constructStyle("body2");
	}
	$vStyleBreaker = $this->constructStyle("breaker");

	$vFormatted = false;
	if (!(empty($this->mColsDataFormat))) $vFormatted = true;
	
	$colidx = 0;

//	$vJavaScript = $this->mJavaScript->display();

	settype($this->mBody,'array');
	$rowindex = 0;
	foreach ($this->mBody AS $key=>$vRows) {
	  if ($this->mID) $vID = "id=\"".$this->mID."_rowid_".$rowindex."\"";else $vID = "";
	  $tmp .= "<tr $vID $vJavaScript>"; $idx = 0; $vCount = count($vRows);
	  if ($vCount == 1) { 
		//this is a "breaker" row
		if ($this->mIgnoreBreakers) {
		  if ($colidx % 2 == 0) $vStyleBreaker = $this->constructStyle("body1");  else $vStyleBreaker = $this->constructStyle("body2");
		  $colidx ++;
		}
		if ($vFormatted) $vRows[0] = sprintf($ColsDataFormat[0],$vRows[0]);
		if (!$vRows[0] && !($vRows[0] ===0)) $vRows[0] = "&nbsp;";
		$tmp .= "<td width=\"100%\" " . $vStyleBreaker . " colspan=\"".$this->mColSpan."\">" . $vRows[0] . "</td>";
	  } else {
		// this is a normal line
	  	foreach ($vRows as $key2=>$vCell) {
		  if ($vFormatted) $vCell = sprintf($ColsDataFormat[$idx],$vCell);
		  if ($this->mDrawAlternate) {
			if ($colidx % 2 == 0) $vStyle = $this->mTemplates["body1"];  else $vStyle = $this->mTemplates["body2"];
		  }
		  if (!($this->mDrawBorders)) {
		    if ($idx == 0) $vStyle["border-left-width"] = "0px";
			if (($idx == count($vRows)-1) && $idx) $vStyle["border-right-width"]  = "0px";
		  }
		  if (is_array($this->mColsWidths) && isset($this->mColsWidths[$idx])) $vExtra = "width=\"" . $this->mColsWidths[$idx] . "\""; 
		  else $vExtra = "";
		  
		  if (isset($this->mColsAligns)) {
			$vExtra .= " align=\"". $this->mColsAligns[$idx] .'"';
			$vStyle["text-align"] = "";
		  }
		  $this->mTemplates["body"] = $vStyle;
		  unset($vStyle["border-right-width"]);
		  unset($vStyle["border-left-width"]);
		  if (!$vCell && !($vCell ===0)) $vCell = "&nbsp;";
		  $tmp .= "<td " . $vExtra . " " . $this->constructStyle("body"). ">" . $vCell . "</td>";
		  $idx ++;
		}
		$colidx ++;
	  }
	  $rowindex++;
	$tmp .= "</tr>";
	}
	Return $tmp;
  }


  function displayHeader() {

	$vStyle1 = isset($this->mTemplates["header"])?$this->mTemplates["header"]:"";	
	$vCellsCount = count($this->mHeader);

	$tmp = "<tr>";
	$idx = 0;	
	foreach ($this->mHeader as $key=>$val) {
	  $vStyle = $vStyle1;
	  if (!($this->mDrawBorders)) {
		if ($idx == 0) $vStyle["border-left-width"]  = "0px;";
		if ($idx == $vCellsCount-1) $vStyle["border-right-width"]  = "0px;";
	  }
	  $vExtra = "";
	  if ($vCellsCount != $this->mColSpan) $vExtra .= " colspan=\"" .round($this->mColSpan / $vCellsCount). "\"";
	  else {
		if (is_array($this->mColsWidths)) $vExtra = "width=\"" . $this->mColsWidths[$idx] . "\""; 
		else $vExtra = "";
	  }

	  if (isset($this->mColsAligns)) {
		$vExtra .= " align=\"". $this->mColsAligns[$idx] ."\" ";
		$vStyle["text-align"] = "";
	  }

	  $this->mTemplates["header"] = $vStyle;

	  $tmp .= "<td $vExtra " . $this->constructStyle("header"). ">$val</td>";
	  $idx ++;
	}
	$tmp .= "</tr>";
	Return $tmp;
  }

  function displayFooter() {
	$vStyle1 = $this->mTemplates["footer"];
	$vCellsCount = count($this->mFooter);

	$tmp = "<tr>";
	$idx = 0;
	foreach ($this->mFooter as $key=>$val) {
	  $vStyle = $vStyle1;
	  if (!($this->mDrawBorders)) {
		if ($idx == 0) $vStyle["border-left-width"]  = "0px;";
		if ($idx == $vCellsCount-1) $vStyle["border-right-width"]  = "0px;";
	  }
	  $vExtra = "";
	  if ($vCellsCount != $this->mColSpan) $vExtra .= " colspan=\"" .round($this->mColSpan / $vCellsCount). "\"";
	  else {
		if (is_array($this->mColsWidths)) $vExtra = "width=\"" . $this->mColsWidths[$idx] . "\""; 
		else $vExtra = "";
	  }
	  if (isset($this->mColsAligns)) $vExtra .= " align=\"". $this->mColsAligns[$idx] ."\" ";
	  $this->mTemplates["footer"] = $vStyle;
	  $tmp .= "<td $vExtra " . $this->constructStyle("footer"). ">$val</td>";
	  $idx ++;
	}
	$tmp .= "</tr>";
	Return $tmp;


  }
  
  function setColsDataFormat($mColsDataFormat) {
  	$this->mColsDataFormat = $mColsDataFormat;
  }

  function setColsWidths($pWidths) {
  	$this->mColsWidths = $pWidths;
  }

  function setColsAligns($pAligns) {
  	$this->mColsAligns = $pAligns;
  }
  

  function drawAlternate($pIgnoreBreakers = false) {
  	$this->mDrawAlternate = !($this->mDrawAlternate);
	$this->mIgnoreBreakers = $pIgnoreBreakers;
  }

  function display($pDrawBorders = false) {
//	if (empty($this->mTemplates)) $this->loadTemplate("emptyGrid");
	if ($this->mClass) $this->mTemplates = array();

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
	if ($this->mClass) $class =  " class=\"".$this->mClass."\" "; else $class = "";
	$tmp = "<table id=\"".$this->mID."\" cellpadding=\"".$this->mCellPadding."\" cellspacing=\"".$this->mCellSpacing."\" border=\"".$this->mBorder."\" $class  ".$this->constructStyle("table").">";
	if ($this->mHeader != "") $tmp .= $this->displayHeader();
	$tmp .= $this->displayBody();
	if ($this->mFooter != "") $tmp .= $this->displayFooter();
	$tmp .= "</table>";
//	die2($this->mTemplates);
	Return $tmp;
  	
  }


  function loadTemplate($pName) {
  	$vTemplate = array();
	$this->mTemplates = array();
	switch ($pName) {
	  case "pers_site":
		$this->mTemplates["body"]["padding"] = "1px 2px";
		$this->mTemplates["body"]["font-size"] = "8pt";
		$this->mTemplates["body"]["color"] = "#444";
		$this->mTemplates["body"]["vertical-align"] = "bottom";
		$this->mTemplates["header"]["color"] = "#fff";
		$this->mTemplates["header"]["font-size"] = "8pt";
		$this->mTemplates["header"]["font-weight"] = "bold";
		$this->mTemplates["header"]["padding"] = "1px 2px";
		$this->mTemplates["header"]["background-color"] = $this->mColHead;
		$this->mTemplates["table"]["border"] = "1px solid ". $this->mColHead;
		$this->mTemplates["footer"]["text-align"] = "right";
		$this->mTemplates["footer"]["padding"] = "5px 3px 2px 3px";
		break;
	  case "basicFormTable":
		$this->mTemplates["breaker"]["padding"] = "2px 2px";
		$this->mTemplates["breaker"]["font-size"] = "9pt";
		$this->mTemplates["breaker"]["color"] = $this->mColText2;
		$this->mTemplates["breaker"]["vertical-align"] = "middle";

		$this->mTemplates["body"]["padding"] = "1px 2px";
		$this->mTemplates["body"]["font-size"] = "9pt";
		$this->mTemplates["body"]["color"] = $this->mColText2;
		$this->mTemplates["body"]["vertical-align"] = "middle";
		$this->mTemplates['header']['color'] = $this->mColText2;
		$this->mTemplates["header"]["font-size"] = "9pt";
		$this->mTemplates["header"]["font-weight"] = "bold";
		$this->mTemplates["header"]["padding"] = "4px 2px";
		$this->mTemplates['header']["background-color"] = $this->mColText;
		$this->mTemplates['table']['border'] = "1px solid $this->mColText";
		$this->mTemplates["footer"]["text-align"] = "center";
		$this->mTemplates["footer"]["padding"] = "5px 3px 2px 3px";
		break;
	  case "awards":
		$this->mTemplates['table']['width'] = '160px';
		$this->mTemplates['table']['margin'] = '10px';
		$this->mTemplates['header']['vertical-align'] = 'middle';
		$this->mTemplates['header']['font-weight'] = 'bold';
		$this->mTemplates['header']['font-size'] = '9pt';
		$this->mTemplates['header']['color'] = "#222";
		$this->mTemplates['header']['padding'] = '4px 2px';
		$this->mTemplates['breaker']['vertical-align'] = 'top';
		$this->mTemplates['breaker']['font-weight'] = 'normal';
		$this->mTemplates['breaker']['font-size'] = '9pt';
		$this->mTemplates['breaker']['color'] = "#222";
		$this->mTemplates['breaker']['padding'] = '5px 2px';
		$this->mTemplates['body']['vertical-align'] = 'top';
		$this->mTemplates['body']['font-weight'] = 'normal';
		$this->mTemplates['body']['font-size'] = '9pt';
		$this->mTemplates['body']['color'] = "#222";
		$this->mTemplates['body']['padding'] = '5px 2px';
		break;
	  case "report_column":
		$this->mTemplates['table']['width'] = '200px';
		$this->mTemplates['table']['margin'] = '10px';
		$this->mTemplates['header']['vertical-align'] = 'middle';
		$this->mTemplates['header']['font-weight'] = 'bold';
		$this->mTemplates['header']['font-size'] = '9pt';
		$this->mTemplates['header']['color'] = "#222";
		$this->mTemplates['header']['padding'] = '4px 2px';
		$this->mTemplates['body']['vertical-align'] = 'top';
		$this->mTemplates['body']['font-weight'] = 'normal';
		$this->mTemplates['body']['font-size'] = '9pt';
		$this->mTemplates['body']['color'] = "#222";
		$this->mTemplates['body']['padding'] = '5px 2px';
		break;
	  case "emptyGrid":
	  case "default":
		$this->mTemplates['table']['margin'] = '0px 3px 3px 6px';
		$this->mTemplates['table']['border'] = '0';
		$this->mTemplates['body']['vertical-align'] = 'top';
		$this->mTemplates['body']['font-weight'] = 'normal';
		$this->mTemplates['body']['font-size'] = '9pt';
		$this->mTemplates['body']['color'] = $this->mColText;
		$this->mTemplates['body']['padding'] = '2px';
		break;
	  default:
		  $vTemplate = CBoxTable::loadTemplate($pName);
	}
  }
}

?>