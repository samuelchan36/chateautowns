<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CUserGroupAdmin extends CSectionAdmin{

	var $table = "cms_user_groups";
	var $actions = array("edit", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("User Groups", "User Group");
	var $mClass = "CUserGroup";
	

  /** comment here */
  function __construct() {
	parent::__construct();
  }


  /** comment here */
  function display() {
				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	$sql = "SELECT a.* from ".$this->table." a Where 1=1  ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = false;

	$vSmart->addHeader(array("Name", "Admin Group"));
	$vSmart->addField("Name");
	$vSmart->addField("AdminGroup");

	$vSmart->mDefaultOrder = "a.Name";
	$vSmart->mDefaultOrderDir = "ASC";
	
//	$vSmart->addLFilter("a.Status", "Status", $this->mDatabase->getAll2("select distinct Status, Status from ".$this->table." order by 1 asc"), 1, "w150");

	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));

	$vSmart->mColsWidths = array("20px", "70%","20%", "60px");
	$vSmart->mColsAligns = array("center", "left", "left", "right");
	
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
