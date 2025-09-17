<?php
/** CSmartTable
* @author cgrecu
*/


class CSmartTable extends CHtmlEntity {

  var $mTable;
  var $mSql;
  var $mCountSql;
  var $mFormName = "frmEdit";
  var $mFilters = array();
  var $mHeaders = array();
  var $mHeaderColumns = array();
  var $mIcons = array();
  var $mLegend = array();
  var $mActions = array();
  var $mItemsPerPage = 0;
  var $mExtraActions = array();
  var $mRows = array();
  var $mShowIndex = true;
  var $mShowToggle = true;
  var $mFields = array();
  var $mIconManager;
  var $mColsAligns = array();
  var $mColsWidths = array();
  var $mTemplates = array();
  var $mStatusOn = "enabled";
  var $mTableType = "grid";
  var $mTemplateName = "";
  var $mDrawAlternate = false;
  var $mShowSaveButton = false;
  var $mDefaultOrder = "a.ID";
  var $mDefaultOrderDir = "ASC";
  var $mPublishing = true;

  var $template;

  var $data;

  var $mSection;
  var $mOperation;

  /** comment here */
  function __construct($pTable, $pSql) {
	  $this->mSection = $GLOBALS["doc"]->mModule;
	  if (isset($this->mOperation)) $this->mOperation = $_GET["o"]; if (!$this->mOperation)  $this->mOperation = 'main';
	$this->mDatabase = &$GLOBALS["db"];
	$this->mTable = $pTable;
	$this->mSql = $pSql;
	if (isset($_GET["reset"]) && $_GET["reset"] == "yes") {
		$this->resetSessionVars();
		foreach ($_GET as $key=>$val) {
			if ($key != "s" && $key != "o") {
				$_GET[$key] = "";
			}
		}
	}
	if (isset($_SESSION["gAdminFilters"]) && $_SESSION["gAdminFilters"] && $_SESSION["gAdminSection"] == $this->mSection) {
	  foreach ($_SESSION["gAdminFilters"] as $key=>$val) {
		if (!isset($_GET[$key])) $_GET[$key] = $val;
	  }
	}
	if (isset($_SESSION["gAdminSection"]) && $_SESSION["gAdminSection"] != $this->mSection) $this->resetSessionVars();
	$_SESSION["gAdminSection"] = $this->mSection;
  }

  /** comment here */
  function resetSessionVars() {
	  $_SESSION["gAdminCriteria"] = "";
	  $_SESSION["gAdminOrderBy"] = "";
	  $_SESSION["gAdminOrderDir"] = "";
	  $_SESSION["gAdminCurPage"] = "";
	  $_SESSION["gAdminFilters"] = "";
  }

  /** comment here */
  function setFormName($pName) {
  	$this->mFormName = $pName;
  }

  /** comment here */
  function addTFilter($pFieldComplex, $pLabel, $pRow = 1, $pClass = "") {
	$tmp = explode(".", $pFieldComplex);
	$pField = $tmp[count($tmp)-1];
	$pTable = $tmp[0];if (count($tmp) == 1 || !$pTable) $pTable = $this->mTable;
  	if (!$pClass) $pClass = "admin_search"; 
	$input = new CTextInput("srctxt_$pField", array("class" => $pClass)); if (isset($_GET["srctxt_$pField"])) $input->value = $_GET["srctxt_$pField"];
	$input->attributes["placeholder"] = $pLabel;
	$this->mFilters[$pRow-1][] = array($pLabel . ": " .  $input->display(), $pTable, $pField, "text");
  }

  /** range filter */
  function addRFilter($pFieldComplex, $pLabel1, $pLabel2 = "to", $pRow = 1, $pFieldType = "text") {
	$tmp = explode(".", $pFieldComplex);
	$pField = $tmp[count($tmp)-1];
	$pTable = $tmp[0];if (count($tmp) == 1 || !$pTable) $pTable = $this->mTable;
	if (!isset($_GET["srcfrom_$pField"])) $_GET["srcfrom_$pField"] = "";
	if (!isset($_GET["srcto_$pField"])) $_GET["srcto_$pField"] = "";
	if ($pFieldType == "date") {
	  $input1 = new CInputDate("srcfrom_$pField");$input1->alt->value =  $_GET["srcfrom_$pField"];  $input1->attributes["placeholder"] = $pLabel1;
	  $input2 = new CInputDate("srcto_$pField"); $input2->alt->value =  $_GET["srcto_$pField"];   $input2->attributes["placeholder"] = $pLabel2;
	} else {
	  $input1 = new CTextInput("srcfrom_$pField");$input1->value =  $_GET["srcfrom_$pField"];   $input1->attributes["placeholder"] = $pLabel1;
	  $input2 = new CTextInput("srcto_$pField");$input2->value =  $_GET["srcto_$pField"];  $input2->attributes["placeholder"] = $pLabel2;
	}
	$this->mFilters[$pRow-1][] = array($pLabel1 . ": " . $input1->display() . " ". $pLabel2 . " " . $input2->display(), $pTable, $pField, "range");

  }

  /** comment here */
  function addDFilter($pFieldComplex, $pLabel, $pRow = 1) {
	$tmp = explode(".", $pFieldComplex);
	$pField = $tmp[count($tmp)-1];
	$pTable = $tmp[0];if (count($tmp) == 1 || !$pTable) $pTable = $this->mTable;
   	$input = new CInputDate("srctxt_$pField");$input->value =  $_GET["srctxt_$pField"];
	$input->attributes["placeholder"] = $pLabel;
	$this->mFilters[$pRow-1][] = array($pLabel . ": " .  $input->display(), $pTable, $pField,  "date");

  }

  /** comment here */
  function addLFilter($pFieldComplex, $pLabel, $pOptions, $pRow = 1, $pClass ="") {
	$tmp = explode(".", $pFieldComplex);
	$pField = $tmp[count($tmp)-1];
	$pTable = $tmp[0];if (count($tmp) == 1 || !$pTable) $pTable = $this->mTable;
	if (!$pClass) $pClass = "admin_search";
	$pOptions = array_merge(array(array("", $pLabel)), $pOptions);
	$input = new CSelect("srcequal_$pField", $pOptions, array("class"=>$pClass));
  	if (isset($_GET["srcequal_$pField"])) $input->value = $_GET["srcequal_$pField"];
	$this->mFilters[$pRow-1][] = array($pLabel . ": " .  $input->display(), $pTable, $pField, "list");
  }

  /** comment here */
  function addINFilter($pFieldComplex, $pLabel, $pOptions, $pRow = 1) {
	$tmp = explode(".", $pFieldComplex);
	$pField = $tmp[count($tmp)-1];
	$pTable = $tmp[0];if (count($tmp) == 1 || !$pTable) $pTable = $this->mTable;
  	if (!$pClass) $pClass = "admin_search";
	$input = new CSelect("srcin_$pField", $pOptions, array("placeholder" => "Show all", "class" => $pClass));
  	$input->value = $_GET["srcequal_$pField"];
	$this->mFilters[$pRow-1][] = array($pLabel . ": " .  $input->display(), $pTable, $pField, "list");
  }

  /** comment here */
  function addLDbFilter($pFieldComplex, $pLabel, $pRow = 1) {
  	$options = $this->mDatabase->getAll2("select ID, $pField from " . $this->mTable . " order by 2 ASC");
	$this->addLFilter($pField, $pLabel, $options, $pRow);
  }

  /** comment here */
  function addRadioFilter($pFieldComplex, $pLabel, $pOptions, $pRow = 1) {
	$txt = "";
	$tmp = explode(".", $pFieldComplex);
	$pField = $tmp[count($tmp)-1];
	$pTable = $tmp[0];if (count($tmp) == 1 || !$pTable) $pTable = $this->mTable;
	foreach ($pOptions as $key=>$val) {
   	  $input = new CInputRadio("srctxt_$pField", $val[1]); $input->value = $_GET["srctxt_$pField"];
	  $txt .= " ". $input->html() . "&nbsp;&nbsp;";
	}
	$this->mFilters[$pRow-1][] = array($pLabel . ": " .  $txt,  $pTable, $pField, "radios");
  }


  /** comment here */
  function addCompositeFilter($pField, $pFields, $pLabel, $pRow = 1, $pClass = "") {
//	$tmp = explode(".", $pFieldComplex);
//	$pField = $tmp[count($tmp)-1];
//	$pTable = $tmp[0];if (count($tmp) == 1 || !$pTable) $pTable = $this->mTable;
  	if (!$pClass) $pClass = "admin_search";
  	$input = new CTextInput("srccomp_$pField", array("class" => $pClass)); if (isset($_GET["srccomp_$pField"])) $input->value =  $_GET["srccomp_$pField"];
	$input->attributes["placeholder"] = $pLabel;
	$fields = explode(",", $pFields);
	$this->mFilters[$pRow-1][] = array($pLabel . ": " .  $input->display(), $pField, $fields, "text");
  }

  /** comment here */
  function addHeader($pFields) {
  	$this->mHeaders = $pFields;
  }

  /** comment here */
  function setIcons($pIcons) {
  	$this->mIconManager = new CIcons($pIcons);
  }

  /** comment here */
  function setTemplate($pName) {
  	$this->mTemplateName = $pName;
  }

  /** comment here */
  function setAlternate($alternate = true) {
  	$this->mDrawAlternate = $alternate;
  }

  /** comment here */
  function addIcon($pIcon) {
  	$this->mIconManager->mSelected[] = $pIcon;
  }

  /** comment here */
  function addExtraActions($pAction = "", $pType ="") {
	if (!isset($pAction->attributes["class"])) $pAction->attributes["class"] = "";
	$pAction->attributes["class"] .= " admin table-action";
	$pathinfo = pathinfo($pAction->url);
	switch(strtolower($pathinfo["basename"])) {
		case "edit": 
		case "create": 
			$icon = "fas fa-folder-plus"; 
			$label = $pType ? $pType : "New";
			break;
		case "export": 
				$icon = "fas fa-file-export"; 
				$label = $pType ? $pType : "Export";
				break;
		case "refresh": 
		case "refresh-pages": 
		case "refresh-templates": 
				$icon = "fas fa-sync"; 
				$label = $pType ? $pType : "Refresh";
				break;
		case "sync": 
				$icon = "fas fa-sync"; 
				$label = $pType ? $pType : "Sync";
				break;
		case "sort": 
		case "order": 
				$icon = "fas fa-sort"; 
				$label = $pType ? $pType : "Mass Sort";
				break;
		case "import-emails": 
				$icon = "fas fa-upload"; 
				$label = $pType ? $pType : "Import Emails";
				break;
		case "import-excel": 
				$icon = "fas fa-file-upload"; 
				$label = $pType ? $pType : "Import Excel";
				break;
		default: 
				$icon = "fas fa-ghost"; 
				$label = $pAction->label;
	}

//	die2($pAction);
  	$this->mExtraActions[] = '<a href="' .$pAction->url .'" title="'.$pAction->label.'"><i class="' . $icon . '"></i> '.$label.'</a>';
  }

  /** add column field */
  function addField($pField) {
  	$this->mFields[] = array("", $pField);
  }

  /** add string field */
  function addSField($pField) {
  	$this->mFields[] = array("string", $pField);
  }

  /** add string field */
  function addIField($pField) {
  	$this->mFields[] = array("int", $pField);
  }

  /** add date field */
  function addDField($pField, $pFormat = "F d, Y") {
  	$this->mFields[] = array("date", $pField, $pFormat);
  }

  /** add currency field */
  function addCField($pField, $pDecimals = 2) {
  	$this->mFields[] = array("currency", $pField, $pDecimals);
  }

  /** add currency field */
  function addPField($pField, $pDecimals = 2) {
  	$this->mFields[] = array("percent", $pField, $pDecimals);
  }

  /** add number field */
  function addFField($pField, $pDecimals = 2) {
  	$this->mFields[] = array("float", $pField, $pDecimals);
  }

  /** add number field */
  function addStField($pField = "enabled") {
  	$this->mFields[] = array("status", $pField);
  }

  /** comment here */
  function addFuncField(&$pObject, $pFunction, $param = "") {
  	$this->mFields[] = array("function", $param, $pObject, $pFunction);
  }

  /** Field1 = URL including tag, Field2 = Tag Name, Field3 = Label including tag, Field4 = Tag name */
  function addLkField($pTxt1, $pField1, $pTxt2, $pField2) {
  	$this->mFields[] = array("link", $pTxt1, $pField1, $pTxt2, $pField2);
  }


  /** add string field */
  function addImageField($pField, $pWidth = 100) {
  	$this->mFields[] = array("image", $pField, $pWidth);
  }

  /** comment here */
  function addToggleField($pField, $pOptions) {
   	$this->mFields[] = array("toggle", $pField, $pOptions);
  }

  /** comment here */
  function addEditField($pField) {
   	$this->mFields[] = array("edit", $pField);
  }

  /** comment here */
  function getFieldValue($pField, &$pValue) {
  	switch($pField[0]) {
  		case "string": Return str_replace("##ID##", $pValue["ID"], $pField[1]);
  		case "date": if (!$pValue[$pField[1]]) Return ""; Return date($pField[2], $pValue[$pField[1]]);
  		case "currency": Return "$" . number_format($pValue[$pField[1]], $pField[2]);
  		case "float": Return number_format($pValue[$pField[1]], $pField[2]);
  		case "int": Return intval($pValue[$pField[1]]);
  		case "percent": Return number_format($pField[1], $pField[2]) . "%";
  		case "image": Return "<img src='".$pValue[$pField[1]]."' width='".$pField[2]."'>";
  		case "link":
		  $link = new CHref(str_replace("##".$pField[2]."##", $pValue[$pField[2]], $pField[1]), str_replace("##".$pField[4]."##", $pValue[$pField[4]], $pField[3]));
		  Return $link->display();
  		case "status":
			  if ($pValue["Status"] == $this->mStatusOn) $link = $this->mIconManager->getIcon("on");
			  else  $link = $this->mIconManager->getIcon("off");
			  if ($link) {$link->url = str_replace("##ID##", $pValue["ID"], $link->url);
				$tmp = $link->display();
				if ($this->mPublishing) {
					if (isset($pValue["Published"])) {
					  if ($pValue["Published"] == "yes") {
						  $tmp .= $this->mIconManager->mIcons["publish"][1];
					  } else  {
						  $link = $this->mIconManager->getIcon("draft");
							$link->url = str_replace("##ID##", $pValue["ID"], $link->url);
							$tmp .= $link->display();
					  }
					}				
				}
				Return $tmp;
		  } else Return "&nbsp;";
  		case "function":
			$funcName = $pField[3];
		  if (!$pField[1]) Return $pField[2]->$funcName($pValue); else Return $pField[2]->$funcName($pValue, $pField[1]);
		 case "toggle":
				Return "<toggle class='inline-update' data-id='".$pValue["ID"]."' data-module='".$this->mSection."' data-field='" . $pField[1]. "' data-on='".$pField[2][0]."' data-off='".$pField[2][1]."' data-value='".$pValue[$pField[1]]."'>";
		 case "edit":
				Return "<input type='text' data-id='".$pValue["ID"]."' class='edit-field' data-module='".$this->mSection."' name='".$pField[1]."".$pValue["ID"]."' id='".$pField[1]."_".$pValue["ID"]."' data-field='" . $pField[1]. "'  value='".$pValue[$pField[1]]."'>";
		default:
		  Return str_replace("##ID##", $pValue["ID"], $pValue[$pField[1]]);
  	}
  }
	

  /** comment here */
  function display($generateOnly = false) {
	$filter = $this->displayFilter();
	$this->template = template2("lib/html/nav.html");
//	$this->newBlock("MAIN");

	#filters
//	if ($filter) $this->mDocument->setPiece("TOP", $filter);
	$criteria = $this->createFilter();
//	die($criteria);
	$_SESSION["gAdminCriteria"] = $criteria;
	#create sql
	$this->mSql = str_replace("##CRITERIA##",$criteria,$this->mSql);
	$this->mCountSql = str_replace("##CRITERIA##",$criteria,$this->mCountSql ? $this->mCountSql : "" );
	#order by
//	print_r($_SESSION);
	if (!array_key_exists("orderby", $_GET) && array_key_exists("gAdminOrderBy", $_SESSION) && $_SESSION["gAdminOrderBy"]) {
		$_GET["orderby"] = $_SESSION["gAdminOrderBy"];
	}
	if (array_key_exists("orderby", $_GET)) {
		$this->mSql .= "ORDER BY ". $_GET["orderby"] . " ";
		$_SESSION["gAdminOrderBy"] = $_GET["orderby"];
	} else {
		if ($this->mDefaultOrder) $this->mSql .=  " ORDER BY ". $this->mDefaultOrder; else $this->mSql .= "ORDER BY 1  ";
	}
	#order direction
	if (!array_key_exists("orderdir", $_GET) && array_key_exists("gAdminOrderDir", $_SESSION) && $_SESSION["gAdminOrderDir"]) {
		$_GET["orderdir"] = $_SESSION["gAdminOrderDir"];
	}
	if (array_key_exists("orderdir", $_GET)) {
		$this->mSql .= $_GET["orderdir"];
		$_SESSION["gAdminOrderDir"] = $_GET["orderdir"];
	} else {
		if ($this->mDefaultOrderDir) $this->mSql .= " ". $this->mDefaultOrderDir; else $this->mSql .= " ASC";
	}
//	$this->mSql .= ", 1 ASC";

	if ($generateOnly) Return $this->mSql;

	#icons
	if (isset($this->mExtraParam)) $this->mIconManager->mExtraParam = $this->mExtraParam;

	#navigation
	$qs = explode("&",$_SERVER["QUERY_STRING"]);
	$url = $_SERVER["PHP_SELF"];
	$params = array();
	foreach ($qs as $key=>$val) {
	  $tmp = explode("=", $val);
	  if ($tmp[0] != "start") $params[] = $val;
	}
	if (!empty($params)) {
	  $url .= "?";
	  $url .= implode("&", $params);
	}
	if (!isset($_SESSION["gAdminCurPage"])) $_SESSION["gAdminCurPage"] = 0;
	if (isset($_GET["start"])) $_SESSION["gAdminCurPage"] = $_GET["start"];
	else {
		if ($_SESSION["gAdminCurPage"]) {
			$_GET["start"] = $_SESSION["gAdminCurPage"];
		}
	}
	if (!isset($_GET["start"])) $_GET["start"] = 0;
//	die($this->mSql);
	$browse = new CBrowseHelp($this->mSql, $url, $_GET["start"]);
	if (!$criteria) $browse->mCountQuery = $this->mCountSql;
//	die($this->mSql);

	$browse->mResultsPerPage = $this->mItemsPerPage;
	$items = $browse->getElements();
	$browse->loadNavigation($this->template);
	$this->data = $items;
	$rows = array();
	if (!empty($items)) {
//		$this->newBlock("Navigation");
	}
//	$this->selectBlock("MAIN");

	$vActions = $this->mIconManager->getIcons();
	$legend = $this->mIconManager->displayLegend();
	$footer = array();
//	$footer = array($legend);

	//customize this array
	$header = array();
	if ($this->mShowIndex) $header[] = "<span>#</span>";
	if ($this->mShowToggle) $header[] = "<span class='col-status' title='Status'></span>";

	foreach ($this->mHeaders as $key=>$val) {
		$tmp = explode("|", $val);
//die2($val);
		if (count($tmp) > 1 && $tmp[1] === "false") {
			$header[] = "<span>" . $tmp[0] . "</span>";
//		die2($tmp);
//		die();
		} else {
			$dir = "ASC"; $imgSort = ""; 
			if (count($tmp) > 1) $orderby = $tmp[1]; else $orderby = $this->mFields[$key][1];
//			die2($this->mFields);
//			if ($_GET["orderby"]) $_GET["orderby"] = $this->mFields[$key][1];
////			die2($_GET);
//			if ($_GET["orderdir"] && $_GET["orderby"] == $this->mFields[$key][1]) {
////				die();
//				if ($_GET["orderdir"] == "DESC") $dir = "ASC"; else $dir = "DESC";
////				die($_GET["orderdir"]);
//				if ($dir == "DESC") $imgSort = " <img width=\"14\" src=\"/images/common/small/sort_ascending.png\" align=\"top\">"; else {
//					$imgSort = " <img width=\"14\" src=\"/images/common/small/sort_descending.png\" align=\"top\">";
//				}
//			}
			if (isset($_GET["orderby"]) && $orderby == $_GET["orderby"]) {
				if ($_GET["orderdir"] && $_GET["orderdir"] == "ASC") $dir = "DESC";
				if ($_GET["orderdir"]) {
					if ($_GET["orderdir"] == "DESC") $imgSort = '<i class="fas fa-sort-alpha-down"></i>'; else $imgSort = '<i class="fas fa-sort-alpha-up"></i>'; 
				}
			}


//					$imgSort = " <img width=\"14\" src=\"/cms/lib/images/common/small/sort_descending.png\" align=\"top\">";
			$url = array();
			foreach ($_GET as $key2=>$val2) {
//				if ($key2=="orderby") $url .= "orderby=" . $this->mFields[$key][1] . "&";
//				else if ($key2=="orderdir") $url .= "orderdir=" . $dir . "&";
//				else $url .= $key2 . "=" . $val2 . "&";
				if ($key2 == "orderby") continue;
				if ($key2 == "orderdir") continue;
				$url[] = $key2 . "=" . $val2;
			}

			$url[] = "orderby=" . $orderby;
			$url[] = "orderdir=" . $dir;
			$url = "index.php?" . implode("&", $url);
			
//			if ($tmp[0]=="Title") die2($url);
			$href = new CHref($url, "<nobr>".$tmp[0] . $imgSort . "</nobr>");
			$header[] = $href->display();
		}
	}

	if ($this->mIconManager && $this->mIconManager->mSelected && $vActions) $header[] = "<span>Actions</span>";
//die2($_GET);
	foreach ($items as $key=>$val) {
	  if ($this->mShowIndex) $rows[$key+1][] = ($key+1 + intval($_SESSION["gAdminCurPage"])) . ".";
	  if ($this->mShowToggle) $rows[$key+1][] = $this->getFieldValue(array("status", $val["Status"]), $val);
	  foreach ($this->mFields as $key2=>$field) {
		$rows[$key+1][] = $this->getFieldValue($field, $val);
	  }
	  $actions = array();

	  foreach ($vActions as $key2=>$action) {
		if ($key2 == "on" || $key2 == "off") continue;

		$tmp = str_replace("##ID##", $val["ID"], $action->url);
//		if (!strpos($tmp, "delete"))
			$href = new CHref($tmp, $action->label);
			$href->attributes["class"]="table-icon";
//		else {
//			$href = new CHref("#self", $action->label);
//			$href->setJavaScript("onclick", "if (confirm('Are you sure you want to delete this record?')) window.location='".addslashes($tmp)."'");
//		}
	  	$actions[] = $href->display();
	  }

	  if ($actions) $rows[$key+1][] = "<nobr>".implode("", $actions) ."</nobr>";
	}
	if (empty($rows)) $rows[][] = "No items found";

	if ($this->mTableType) {
	  $vTable = new CGrid($rows, $header, $footer, "admin");
	  $vTable->setTemplate($this->mTemplateName);
	  $vTable->mColsAligns = $this->mColsAligns ;
	  $vTable->mColsWidths = $this->mColsWidths;
	} else {
	  $vTable = new CGridTable($rows, $header, $footer, "admin");
	  foreach ($this->mTemplates as $key=>$val) {
		  foreach ($val as $key2=>$val2) {
			$vTable->mTemplates[$key][$key2] = $val2;
		  }
	  }
	  $vTable->mColsAligns = $this->mColsAligns ;
	  $vTable->mColsWidths = $this->mColsWidths;
	}
	if ($this->mDrawAlternate) $vTable->mDrawAlternate = true;
	
	
	
	$txt = $vTable->display();

	//$txt .= implode(" ", $this->mExtraActions);
	$this->template->assign("MainTableActions", implode(" ", $this->mExtraActions));

//			<a href=""><i class="fas fa-folder-plus"></i></a>
//			<a href=""><i class="fas fa-file-export"></i></i></a>
//			<a href=""><i class="fas fa-sync"></i></i></i></a>

	if ($filter) {
		$this->template->newBlock("FILTERB");
		$this->template->assign("Filters", $filter);
		$this->template->gotoBlock("_ROOT");
	}
	$this->template->assign("MainTableLegend", $legend);
	$this->template->assign("MainTable", $txt);

  Return $this->template->output();;
  }

  /** comment here */
  function displayFilter() {
	if (!$this->mFilters) Return "";

	$vForm = new CForm("frmFilter", array("action" => "/cms/" . $this->mSection, "class" => "", "method" => "GET"));
	$vForm->attributes["id"] = "frmFilter";
	$vForm->no_footer = true;
	
	$rows = array();
	$rows2 = array();

	foreach ($this->mFilters as $key=>$val) {
	  foreach ($val as $key2=>$val2) {
			$rows2[$key][] ="<div class='filter-cell filter-".$val2[3]."' style=''>" . $val2[0] . "</div>";
	  }
	}

//	$vTable = new CGrid($rows, array(), "");
//	$vTable->setTemplate("filters");
	$txt = "";
	$rows2[count($rows2)-1][] = $this->addParams($this->mSection, $this->mOperation) . '<div class="filter-actions"><a id="bt-reset-filters" title="Reset Filters"><i class="fas fa-ban"></i></a> <a id="bt-apply-filters"  title="Apply Filters"><i class="fas fa-check-circle"></i></a></div>';
	foreach ($rows2 as $key=>$val) {
		$txt .= "<filter_group>" . implode("", $val) . "</filter_group>";
	}
	$input = new CText("table_filters", $txt, array("Label"=>""));
	$vForm->addBlock("Filters", array("class"=>"block-filters"));
	$vForm->addElement($input);
//	$vForm->button_cancel = new CButton("bt-reset", "Reset");
	Return $vForm->display();


  }

  /** comment here */
  function addParams($pName, $pOp) {
		$vName = new CHidden("s", $pName);
		$vOp = new CHidden("o", $pOp);
		$hidden = $vName->display() . $vOp->display();
		Return $hidden;
  }

  /** comment here */
  function createFilter() {
	$criteria = array();
	$_SESSION["gAdminFilters"] = array();
//	return;


  	foreach ($_GET as $key=>$val) {
	  if (!$val) continue;

	if (strpos($key, "_alt")) continue;
  	  $cd = substr($key, 0, 3);

	  if ($cd == "src") {

		$fld = substr($key, 3);
		$tmp = explode("_",$fld);
		$type = array_shift($tmp);
		$fldName = implode("_", $tmp);
		$val = addslashes($val);

		$filter = $this->getFilter($fldName);

		$tblName = $filter[1];

		switch($type) {
			case "txt": $criteria[] = $tblName .".".$fldName ." like '%$val%' ";break;
			case "from": $criteria[] = $tblName .".".$fldName ." >= '$val' ";break;
			case "to": $criteria[] = $tblName .".".$fldName ." <= '$val' ";break;
			case "equal": $criteria[] = $tblName .".".$fldName ." = '$val' ";break;
			case "logic": $criteria[] = $tblName .".".$fldName ." > 0 ";break;
			case "in": $criteria[] = $tblName .".".$fldName ." in (select ContactID from contact_homes where TypeID = '$val') ";break;
			case "comp":
				$crit = "(";
				$crits = array();
				foreach ($filter[2] as $key2=>$val2) {
					$crits[] = $val2." like '%$val%' ";
				}
				$crit .= implode(" OR ", $crits);
				$crit .= ")";
				$criteria[] = $crit;
				break;
		}
		$_SESSION["gAdminFilters"][$key] = $val;
	  }
  	}

	if (!empty($criteria)) Return " AND (". implode(" AND ", $criteria) . ") "; else $criteria = "";
	Return $criteria;
  }

  /** comment here */
  function getFilter($pName) {

  	foreach ($this->mFilters as $key=>$val) {
	  foreach ($val as $key2=>$val2) {
		  if (is_array($val2[2])) {
			  		if ($val2[1] == $pName) Return $val2;	
		  } else {
				if ($val2[2] == $pName) Return $val2;	
		  }
  		
	  }
  	}
  }


}

?>