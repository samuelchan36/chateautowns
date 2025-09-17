<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CTemplateAdmin extends CSectionAdmin{

	var $table = "cms_templates";
	var $actions = array("edit", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Master Templates", "Template");
	var $mClass = "CTemplate";

	var $readonly = false;
	

  /** comment here */
  function __construct() {
	parent::__construct();
	$this->checkAccess();
  }

  /** comment here */
  function checkAccess() {
		$check = is_writeable("../html");
		if (!$check) $this->readonly  = true;
  }


  /** comment here */
  function display() {
				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);
	
	if ($this->readonly) error("Warning: Templates folder is not writeable, changes cannot be published.");

	$sql = "SELECT a.* from ".$this->table." a Where 1=1 ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = true;

	$vSmart->addHeader(array("Type", "Name", "Last Updated"));
	$vSmart->addField("Type");
	$vSmart->addField("Title");
	$vSmart->addFuncField($this, "getLastUpdated");

	$vSmart->mDefaultOrder = "a.Title";
	$vSmart->mDefaultOrderDir = "ASC";
	
	$vSmart->addCompositeFilter("Content", "a.title, a.content", "Search", 1, "input_search w400");
	$vSmart->addLFilter("a.Type", "Template Type", $this->mDatabase->getAll2("select distinct Type, Type from cms_templates order by 1 asc"), 1, "w150");
	if (count($GLOBALS["cms_languages"]) > 1)  $vSmart->addLFilter("a.BaseLanguage", "Language", $this->mDatabase->getAll2("select distinct BaseLanguage, BaseLanguage from cms_templates order by 1 asc"), 1, "input_search w200");

//	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));
	$vSmart->addExtraActions(new CHref(getLink() . "refresh-templates", "Update all templates from disk version"), "Refresh");


	$vSmart->mColsWidths = array("20px", "60px", "100px", "40%", "300px", "60px");
	$vSmart->mColsAligns = array("center", "center", "left",  "left", "left", "right");
	
				$vSmart->setTemplate("admin");
				Return $vSmart->display();
  }

  /** comment here */
  function getLastUpdated($data) {
	$txt = "Last CMS Change: <b>" . date("F d, Y H:i", $data["LastCMSChange"]) . "</b>";
	$txt .= "<br>Last Disk Change: <b>" . date("F d, Y H:i", $data["LastDiskChange"]) . "</b>";
	$txt .= "<br>Last Published: <b>" . date("F d, Y H:i", $data["LastPublished"]) . "</b>";
	if ($data["SyncStatus"] == "newer-on-disk") $color = "#ff6633"; else $color = '#000';
	Return "<span style='color: $color'>$txt</span>";

  }


  /** comment here */
  function refreshTemplates() {

		foreach ($GLOBALS["cms_languages"] as $key=>$val) {

		$parser = new CParser();
				$files = $parser->loadFiles("../html/".$val[0]."/dynamic");

		$ret = array();
				foreach ($files as $key2=>$val2) {
					$this->parseFile($val2, "template", $val[0]);
		}

				$files = $parser->loadFiles("../html/".$val[0]."/emails");

		$ret = array();
				foreach ($files as $key2=>$val2) {
					$this->parseFile($val2, "email", $val[0]);
		}

				$files = $parser->loadFiles("../html/".$val[0]."/forms");

		$ret = array();
				foreach ($files as $key2=>$val2) {
					$this->parseFile($val2, "form", $val[0]);
		}

				$this->parseFile("../html/".$val[0]."/master.html", "master", $val[0]);
				$this->parseFile("../html/".$val[0]."/email.master.html", "master", $val[0]);
		}

		redirect(getLink());
  }



/** comment here */
		function parseFile($location, $type, $lang = "en") {
				if ($lang != "en")  $baselang = "/fr"; else $baselang = "";

				$buf = file_get_contents($location);
				$filedata = pathinfo ($location);

					$pageid = $this->mDatabase->getValue("cms_templates", "ID", "Location = '".addslashes2($location). "'");

					$obj = new CTemplate($pageid, "cms_templates");

					$time = filemtime($location);

					$obj->mRowObj->Location = $location;
					$obj->mRowObj->Filename = $filedata["basename"];
					$obj->mRowObj->BaseLanguage = $lang;
					if (!$obj->mRowObj->Title) $obj->mRowObj->Title = ucwords($filedata["filename"]);
					if (!$obj->mRowObj->TimeStamp) $obj->mRowObj->TimeStamp = $time;
					$obj->mRowObj->Type = $type;

					if (!$pageid || !$obj->mRowObj->LastPublished) $obj->mRowObj->LastPublished = $obj->mRowObj->TimeStamp; 
					if (!$pageid || !$obj->mRowObj->LastCMSChange) $obj->mRowObj->LastCMSChange = $obj->mRowObj->TimeStamp; 
					$obj->mRowObj->LastDiskChange = $time; 

					$obj->mRowObj->DiskContent = $buf;
//					if (!$obj->mRowObj->Content) $obj->mRowObj->Content = $buf;
					if (!$obj->mRowObj->Content || $obj->mRowObj->Published == "yes") {
						$obj->mRowObj->Content = $buf;
						$obj->mRowObj->LastCMSChange = $time;
					}

					if ($time == $obj->mRowObj->LastCMSChange)  $obj->mRowObj->SyncStatus = "in-sync";
					if ($time < $obj->mRowObj->LastCMSChange)  $obj->mRowObj->SyncStatus = "newer-on-cms";
					if ($time > $obj->mRowObj->LastCMSChange)  $obj->mRowObj->SyncStatus = "newer-on-disk";

					if (!$obj->mRowObj->UserID) $obj->mRowObj->UserID = $_SESSION["gUserID"];
					if (!$pageid || !$obj->mRowObj->Published) $obj->mRowObj->Published = "yes";

					if (!$obj->mRowObj->Status) $obj->mRowObj->Status = "enabled";
					
					$obj->easySave();			
		}

		/** comment here */
		function refreshTemplate($id) {
			$this->title("Edit ". $this->mLabels[1]);
			$this->enforce();
			
			$class = $this->getClass();
			$vItem = new $class($id, $this->table);

			$vItem->folder = $this->url;
			$vItem->view = $this->folder."/view.html";

			$vItem->mRowObj->Content = file_get_contents($vItem->mRowObj->Location);
			$vItem->mRowObj->LastDiskChange = $vItem->mRowObj->LastCMSChange;
			$vItem->mRowObj->SyncStatus = "in-sync";

			if ($vItem->mDraft) {
				$vItem->mDraft->Content = $vItem->mRowObj->Content;
				$vItem->mDraft->SyncStatus = $vItem->mRowObj->SyncStatus;
				$vItem->mDraft->LastDiskChange = $vItem->mRowObj->LastDiskChange;
			}
			
			$vItem->unregisterForm();
			$vItem->initForm();

			Return $vItem->displayEdit();
			
		}



  /** comment here */
  function mainSwitch() {
	switch($this->mOperation) {
		case "refresh-templates": Return $this->refreshTemplates();
		case "refresh": Return $this->refreshTemplate($_GET["id"]);
		default:
		  Return CSectionAdmin::mainSwitch();
	}
  }
}

?>
