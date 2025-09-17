<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CCollectionAdmin extends CSectionAdmin{

	var $table = "fp_collections";
	var $actions = array("edit", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Collections", "Collection");
	var $mClass = "CCollection";
	


  /** comment here */
  function __construct() {
	parent::__construct();
  }


  /** comment here */
  function display() {
				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	$sql = "SELECT a.*, b.Name as Community from ".$this->table." a, communities b Where a.CommunityID = b.ID ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = true;

	$vSmart->addHeader(array("Community", "Order", "Name", "EmbedCode"));
	$vSmart->addField("Community");
	$vSmart->addEditField("OrderID");
	$vSmart->addEditField("Name");
	$vSmart->addFuncField($this, "getEmbedCode");


	$vSmart->mDefaultOrder = "b.OrderID, a.OrderID";
	$vSmart->mDefaultOrderDir = "ASC";
	
//	$vSmart->addCompositeFilter("Content", "a.Name, a.jobdescription", "Search", 1, "input_search w400");
	$vSmart->addLFilter("a.Status", "Status", $this->mDatabase->getAll2("select distinct Status, Status from ".$this->table." order by 1 asc"), 1, "w150");
	$vSmart->addLFilter("a.CommunityID", "Community", $this->mDatabase->getAll2("select distinct a.ID, a.Name from communities a, ".$this->table." b where b.CommunityID = a.ID order by 2 asc"), 1, "w300");

	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));
	$vSmart->addExtraActions(new CHref(getLink() . "order", "Sort"));


	$vSmart->mColsWidths = array("20px", "20px", "180px", "80px", "25%", "25%", "60px");
	$vSmart->mColsAligns = array("center", "center", "left",  "left", "left","left", "right");
	
				$vSmart->setTemplate("admin");
				Return $vSmart->display();
  }

    /** comment here */
  function getEmbedCode($data) {
	Return "###Models_" . $data["ID"] . "###";
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
