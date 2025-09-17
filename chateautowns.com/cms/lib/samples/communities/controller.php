<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CCommunityAdmin extends CSectionAdmin{

	var $table = "communities";
	var $actions = array("edit", "up", "down",  "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Communities", "Community");
	var $mClass = "CCommunity";
	


  /** comment here */
  function __construct() {
	parent::__construct();
  }


  /** comment here */
  function display() {
				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	$sql = "SELECT a.*, b.Name as City from ".$this->table." a, cities b Where a.CityID = b.ID ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = true;

	$vSmart->addHeader(array("Order", "City", "Name", "Active", "Upcoming", "Past"));
	$vSmart->addEditField("OrderID");
	$vSmart->addField("City");
	$vSmart->addField("Name");
	$vSmart->addToggleField("IsActive", array("yes", "no"));
	$vSmart->addToggleField("IsUpcoming", array("yes", "no"));
	$vSmart->addToggleField("IsPast", array("yes", "no"));

	$vSmart->mDefaultOrder = "a.Status, a.Name";
	$vSmart->mDefaultOrderDir = "ASC";
	
//	$vSmart->addCompositeFilter("Content", "a.Name, a.jobdescription", "Search", 1, "input_search w400");
	$vSmart->addLFilter("a.TypeID", "Type", $this->mDatabase->getAll2("select distinct TypeID, TypeID from ".$this->table." order by 1 asc"), 1, "w150");
	$vSmart->addLFilter("a.Status", "Status", $this->mDatabase->getAll2("select distinct Status, Status from ".$this->table." order by 1 asc"), 1, "w150");
	$vSmart->addLFilter("a.CityID", "City", $this->mDatabase->getAll2("select distinct b.ID, b.Name from ".$this->table." a, cities b where a.CityID = b.ID order by 1 asc"), 1, "w200");
	$vSmart->addLFilter("a.IsActive", "Active", $this->mDatabase->getAll2("select distinct IsActive, IsActive from ".$this->table." order by 1 asc"), 1, "w150");
	$vSmart->addLFilter("a.IsUpcoming", "Coming Soon", $this->mDatabase->getAll2("select distinct IsUpcoming, IsUpcoming from ".$this->table." order by 1 asc"), 1, "w200");
//	$vSmart->addLFilter("a.IsRegistration", "Has Registration", $this->mDatabase->getAll2("select distinct IsRegistration, IsRegistration from ".$this->table." order by 1 asc"), 1, "w200");
//	$vSmart->addLFilter("a.IsInventory", "Move in Now", $this->mDatabase->getAll2("select distinct IsInventory, IsInventory from ".$this->table." order by 1 asc"), 1, "w200");
//	$vSmart->addLFilter("a.IsConstruction", "Under Construction", $this->mDatabase->getAll2("select distinct IsConstruction, IsConstruction from ".$this->table." order by 1 asc"), 2, "w300");
//	$vSmart->addLFilter("a.IsFuture", "Future Community", $this->mDatabase->getAll2("select distinct IsFuture, IsFuture from ".$this->table." order by 1 asc"), 2, "w300");
//	$vSmart->addLFilter("a.IsSoldOut", "Sold Out", $this->mDatabase->getAll2("select distinct IsSoldOut, IsSoldOut from ".$this->table." order by 1 asc"), 2, "w200");
	$vSmart->addLFilter("a.IsPast", "Past Community", $this->mDatabase->getAll2("select distinct IsPast, IsPast from ".$this->table." order by 1 asc"), 1, "w300");
//	$vSmart->addLFilter("a.HasLandingPage", "Has Landing Page", $this->mDatabase->getAll2("select distinct HasLandingPage, HasLandingPage from ".$this->table." order by 1 asc"), 2, "w200");

	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));
	$vSmart->addExtraActions(new CHref(getLink() . "order", "Sort"));
//	$vSmart->addExtraActions(new CHref(getLink() . "export", "Export"));


	$vSmart->mColsWidths = array("20px", "20px", "80px", "80px", "25%","60px","60px","60px", "60px");
	$vSmart->mColsAligns = array("center", "center", "left",  "left",  "left", "left", "left", "left", "right");
	
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
