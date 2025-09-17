<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CTeamAdmin extends CSectionAdmin{

	var $table = "staff";
	var $actions = array("edit", "up", "down", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Team", "Team");
	var $mClass = "CTeam";
	


  /** comment here */
  function __construct() {
	parent::__construct();
  }


  /** comment here */
  function display() {
				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	$sql = "SELECT distinct a.* from ".$this->table." a  Where 1=1 ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = true;

	$vSmart->addHeader(array("Order ID", "Photo", "Name", "Title", "Email"));
	$vSmart->addEditField("OrderID");
	$vSmart->addImageField("Photo", 100);
	$vSmart->addEditField("Name");
	$vSmart->addEditField("Title");
	$vSmart->addEditField("Email");

	$vSmart->mDefaultOrder = "a.OrderID";
	$vSmart->mDefaultOrderDir = "ASC";
	
	$vSmart->addCompositeFilter("Content", "a.Name, a.Email", "Search", 1, "input_search w400");
//	$vSmart->addLFilter("b.ID", "Team", $this->mDatabase->getAll2("select distinct b.ID, b.Name from teams b, ".$this->table." a, team_staff c where a.ID = c.StaffID and b.ID = c.TeamID order by 2 asc"), 1, "w300");
	$vSmart->addLFilter("a.Status", "Status", $this->mDatabase->getAll2("select distinct Status, Status from ".$this->table." order by 1 asc"), 1, "w150");

	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));
	$vSmart->addExtraActions(new CHref(getLink() . "order", "Sort"));
//	$vSmart->addExtraActions(new CHref(getLink() . "export", "Export"));


	$vSmart->mColsWidths = array("20px", "20px", "100px", "100px", "25%", "25%", "25%", "60px");
	$vSmart->mColsAligns = array("center", "center", "left", "left", "left", "left", "left", "right");
	
				$vSmart->setTemplate("admin");
				Return $vSmart->display();
  }

  /** comment here */
  function export() {
		$filename = 'JobPostings-' . date("Ymd");
		$items = array();

				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	$sql = "SELECT a.* from ".$this->table." a Where 1=1 ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = true;

	$vSmart->addHeader(array("Position", "Start Date", "Expiry Date"));
	$vSmart->addField("Name");
	$vSmart->addDField("StartDate");
	$vSmart->addDField("ExpiryDate");

	$vSmart->mDefaultOrder = "a.StartDate";
	$vSmart->mDefaultOrderDir = "DESC";
	
	$vSmart->addCompositeFilter("Content", "a.Name, a.jobdescription", "Search", 1, "input_search w400");
	$vSmart->addLFilter("a.Status", "Status", $this->mDatabase->getAll2("select distinct Status, Status from ".$this->table." order by 1 asc"), 1, "w150");
		$vSmart->display(true);
		$data = $this->mDatabase->getAll($vSmart->mSql);
		if ($data) exportToExcel($filename, $data);
		else {
			Return "No data to export";
		}
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
