<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CModelAdmin extends CSectionAdmin{

	var $table = "fp_models";
	var $actions = array("edit", "clone", "delete");
	var $mItemsPerPage = 100;
	var $mLabels = array("Models", "Model");
	var $mClass = "CModel";
	


  /** comment here */
  function __construct() {
	parent::__construct();
  }


  /** comment here */
  function display() {
				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	$sql = "SELECT a.*, b.Name as Collection, c.Name as Type from ".$this->table." a, fp_collections b, fp_model_types c Where a.CollectionID = b.ID and a.TypeID = c.ID ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = true;

	$vSmart->addHeader(array("Order", "Name", "Collection", "Type", "PDF"));
	$vSmart->addEditField("OrderID");
	$vSmart->addEditField("Name");
	$vSmart->addField("Collection");
	$vSmart->addField("Type");
	$vSmart->addEditField("PDF");

	$vSmart->mDefaultOrder = "a.Name";
	$vSmart->mDefaultOrderDir = "ASC";
	
	$vSmart->addCompositeFilter("Content", "a.Name", "Search", 1, "input_search w400");
	$vSmart->addLFilter("a.CollectionID", "Collection", $this->mDatabase->getAll2("select distinct a.ID, concat(c.Name, ' - ', a.Name) as Name from ".$this->table." b, fp_collections a, communities c where c.ID = a.CommunityID and a.id = b.CollectionID order by 2 asc"), 1, "w400");
	$vSmart->addLFilter("a.TypeID", "Type", $this->mDatabase->getAll2("select distinct a.ID, a.Name from ".$this->table." b, fp_model_types a where a.id = b.typeid order by 2 asc"), 1, "w200");

	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));
	$vSmart->addExtraActions(new CHref(getLink() . "order", "Sort"));
//	$vSmart->addExtraActions(new CHref(getLink() . "export", "Export"));


	$vSmart->mColsWidths = array("20px", "20px", "100px", "15%", "15%","10%", "30%", "60px");
	$vSmart->mColsAligns = array("center", "center", "left",  "left", "left", "left", "left", "right");
	
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
