<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CSiteAssetAdmin extends CSectionAdmin{

	var $table = "cms_assets";
	var $actions = array("edit", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Master Assets", "Asset File");
	var $mClass = "CSiteAsset";

	var $readonly = false;
	

  /** comment here */
  function __construct() {
	parent::__construct();
	$this->checkAccess();
  }

  /** comment here */
  function checkAccess() {
		$check = is_writeable("../css");
		if (!$check) $this->readonly  = true;
  }


  /** comment here */
  function display() {
				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);
	
	if ($this->readonly) error("Warning: Assets folder is not writeable, changes cannot be published.");

	$sql = "SELECT a.* from ".$this->table." a Where 1=1 ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = false;

	$vSmart->addHeader(array("Type", "Name", "Last Updated"));
	$vSmart->addField("Type");
	$vSmart->addField("Name");
	$vSmart->addFuncField($this, "getLastUpdated");

	$vSmart->mDefaultOrder = "a.Name";
	$vSmart->mDefaultOrderDir = "ASC";
	
//	$vSmart->addCompositeFilter("Content", "a.title, a.content", "Search", 1, "input_search w400");
	$vSmart->addLFilter("a.Type", "Template Type", $this->mDatabase->getAll2("select distinct Type, Type from cms_assets order by 1 asc"), 1, "w150");

//	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));
//	$vSmart->addExtraActions(new CHref(getLink() . "refresh-templates", "Update all templates from disk version"), "Refresh");


	$vSmart->mColsWidths = array("20px", "100px", "40%", "300px", "60px");
	$vSmart->mColsAligns = array("center", "left",  "left", "left", "right");
	
				$vSmart->setTemplate("admin");
				Return $vSmart->display();
  }

  /** comment here */
  function getLastUpdated($data) {
	  $color = "green";
	Return "<span style='color: $color'>".date("F d, Y H:i", filemtime(".." . $data["Path"]))."</span>";

  }

  /** comment here */
  function mainSwitch() {
	switch($this->mOperation) {
		default:
		  Return CSectionAdmin::mainSwitch();
	}
  }
}

?>
