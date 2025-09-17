<?php   
/** CGridTable
* @package Layout
* @author cgrecu
*/


class CSimpleGrid extends CBoxTable{

  var $mDrawBorders = false;
  var $mColsWidths;
  var $mColsAligns;
  var $mDrawAlternate = false;
  var $mColSpan;
  var $mIgnoreBreakers;
  var $mID;

  function CGridTable($pBody, $pHeader = "", $pFooter = "", $pStyle = "") {
  	parent::__construct($pBody, $pHeader, $pFooter);
  	if ($pStyle) $this->loadTemplate($pStyle);
  }

  function displayBody() {
	
	$vStyle1 = $this->constructStyle("body1");
	$vStyle2 = $this->constructStyle("body2");

	if (!is_array($this->mBody)) Return CBoxTable::displayBody();
	foreach ($this->mBody as $key=>$val) {
	  $txt .= "<tr>"; 		
	  $txt .= "<td $vStyle1>". $val[0]. "</td><td $vStyle2>" . $val[1]."</td>";
	  $txt .= "</tr>";
	}
	Return $txt;
  }


  function displayHeader() {
	$vStyle1 = $this->constructStyle("header1");
	$vStyle2 = $this->constructStyle("header2");
	if (!is_array($this->mBody)) Return CBoxTable::displayBody();
	foreach ($this->mBody as $key=>$val) {
	  $txt .= "<tr>"; 		
	  $txt .= "<td $vStyle1>". $val[0]. "</td><td $vStyle2>" . $val[1]."</td>";
	  $txt .= "</tr>";
	}
	Return $txt;
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
  

  function display($pDrawBorders = false) {
	if (empty($this->mTemplates)) $this->loadTemplate("emptyGrid");
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

	
	$tmp = "<table id=\"".$this->mID."\" cellpadding=\"".$this->mCellPadding."\" cellspacing=\"".$this->mCellSpacing."\" border=\"".$this->mBorder."\" ".$this->constructStyle("table").">";
	if ($this->mHeader != "") $tmp .= $this->displayHeader();
	$tmp .= $this->displayBody();
	if ($this->mFooter != "") $tmp .= $this->displayFooter();
	$tmp .= "</table>";
	Return $tmp;
  	
  }


  function loadTemplate($pName) {
  	$vTemplate = array();
	$this->mTemplates = array();
	switch ($pName) {
	  case "standard":
		$this->mTemplates['table']['margin'] = '0px 3px 3px 6px';
		$this->mTemplates['table']['border'] = '0';
		$this->mTemplates['body']['vertical-align'] = 'top';
		$this->mTemplates['body']['font-weight'] = 'normal';
		$this->mTemplates['body']['font-size'] = '9pt';
		$this->mTemplates['body']['color'] = $this->mColText;
		$this->mTemplates['body']['padding'] = '2px';
		$this->mTemplates['body1'] = $this->mTemplates['body'];
		$this->mTemplates['body1']["background-color"] = "#aaa";
		$this->mTemplates['body2'] = $this->mTemplates['body'];
		$this->mTemplates['body2']["background-color"] = "#eee";
		break;
	  default:
		  $vTemplate = CBoxTable::loadTemplate($pName);
	}
  }
}

?>