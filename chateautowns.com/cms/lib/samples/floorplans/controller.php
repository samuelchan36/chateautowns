<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CFloorplanAdmin extends CSectionAdmin{

	var $table = "elevations";
	var $actions = array("edit", "clone", "delete");
	var $mItemsPerPage = 100;
	var $mLabels = array("Floorplans", "Floorplan");
	var $mClass = "CFloorplan";
	


  /** comment here */
  function __construct() {
	parent::__construct();
  }


  /** comment here */
  function display() {
				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	$sql = "SELECT a.*, concat(b.Name, ' ' , a.Name) as FullName, b.Name as Model, b.Collection, c.Name as Community, c.TypeID from ".$this->table." a, models b, communities c Where a.ModelID = b.ID  and b.CommunityID = c.ID ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = true;

	$vSmart->addHeader(array("Community", "Collection", "Name", "Price", "No Price Msg",  "Sqft"));
	$vSmart->addField("Community");
	$vSmart->addField("Collection");
	$vSmart->addField("FullName");
	$vSmart->addEditField("Price");
	$vSmart->addEditField("NoPrice");
	$vSmart->addEditField("SQFT");

	$vSmart->mDefaultOrder = "c.Status, c.OrderID, b.Collection, b.Name, a.Name";
	$vSmart->mDefaultOrderDir = "ASC";
	
	$vSmart->addCompositeFilter("Content", "b.Name, c.Name", "Search", 1, "input_search w400");
	$vSmart->addLFilter("a.Status", "Status", $this->mDatabase->getAll2("select distinct Status, Status from ".$this->table." order by 1 asc"), 1, "w150");
	$vSmart->addLFilter("b.CommunityID", "Community", $this->mDatabase->getAll2("select distinct a.ID, a.Name from communities a, models b where a.ID = b.CommunityID order by 2 asc"), 1, "w150");
	if ($_GET["srcequal_CommunityID"]) {
		$vSmart->addLFilter("b.Collection", "Collection", $this->mDatabase->getAll2("select distinct b.Collection, b.Collection from models b where b.CommunityID = ".intval($_GET["srcequal_CommunityID"])." order by 2 asc"), 1, "w300");
	}

//	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));
//	$vSmart->addExtraActions(new CHref(getLink() . "order", "Sort"));
//	$vSmart->addExtraActions(new CHref(getLink() . "export", "Export"));


	$vSmart->mColsWidths = array("20px", "20px", "150px", "150px", "100px", "100px", "150px", "100px", "60px");
	$vSmart->mColsAligns = array("center", "center", "left",  "left", "left", "left", "left", "left", "right");
	
				$vSmart->setTemplate("admin");
				Return $vSmart->display();
  }



  /** comment here */
  function export() {
		$filename = 'Projects-' . date("Ymd");
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
  function cloneItem($id) {
	$item = $this->prepareClone($id);
	$item->mRowObj->Guid = "";
	$item->mRowObj->Name = "";
	$item->mRowObj->Logo = "";
	$item->mRowObj->CEO = "";
	$item->mRowObj->ImperialLead = "";
	$item->mRowObj->ImperialLeadTitle = "";
	$item->mRowObj->ImperialLeadID = 0;
	$item->mRowObj->CEOPhoto = "";
	$item->mRowObj->OrderID = 0;
	$item->unregisterForm($versionid);
	$item->initForm();
	
	Return $item->displayEdit();	

  }

  /** comment here */
  function filterModels($id) {
	$data = $this->mDatabase->getAll("select ID, Name from models where communityid = " . intval($id));
	die2(json_encode($data));
  }




  /** comment here */
  function mainSwitch() {
	switch($this->mOperation) {
		case "update-price": Return $this->updatePrice($_GET["id"], $_GET["price"]);
		case "filter-models": Return $this->filterModels($_GET["id"]);
		default:
		  Return CSectionAdmin::mainSwitch();
	}
  }
}

?>
