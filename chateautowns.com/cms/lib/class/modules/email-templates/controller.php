<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CEmailTemplateAdmin extends CSectionAdmin{

	var $table = "jobs";
	var $actions = array("edit", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Job Listings", "Job Listing");
	var $mClass = "CEmailTemplate";
	

  /** comment here */
  function __construct() {
	parent::__construct();
  }


  /** comment here */
  function display() {
				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	$sql = "SELECT a.*, b.name as JobType from ".$this->table." a, job_types b Where a.typeid = b.ID ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = true;

	$vSmart->addHeader(array("Type", "Name"));
	$vSmart->addField("Type");
	$vSmart->addField("Name");

	$vSmart->mDefaultOrder = "a.Name";
	$vSmart->mDefaultOrderDir = "ASC";
	
	$vSmart->addCompositeFilter("Content", "a.name, b.name", "Search", 1, "input_search w400");
	$vSmart->addLFilter("a.Status", "Status", $this->mDatabase->getAll2("select distinct Status, Status from ".$this->table." order by 1 asc"), 1, "w150");
	$vSmart->addLFilter("a.TypeID", "Job Type", $this->mDatabase->getAll2("select distinct ID, Name from job_types order by 1 asc"), 1, "w150");

	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));


	$vSmart->mColsWidths = array("20px", "20px", "22%", "65%", "60px");
	$vSmart->mColsAligns = array("center", "center", "left",  "left", "right");
	
				$vSmart->setTemplate("admin");
				Return $vSmart->display();
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
