<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CResourceAdmin extends CSectionAdmin{

	var $table = "resources";
	var $actions = array("edit", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Resources", "Resource");
	var $mClass = "CResource";
	

  /** comment here */
  function __construct() {
	parent::__construct();
  }


  /** comment here */
  function display() {

				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	$sql = "SELECT a.* from ".$this->table." a Where 1=1 ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = true;

	$vSmart->addHeader(array("Order", "Type", "Name"));
	$vSmart->addEditField("OrderID");
	$vSmart->addField("Type");
	$vSmart->addEditField("Name");

	$vSmart->mDefaultOrder = "a.OrderID";
	$vSmart->mDefaultOrderDir = "ASC";
	
//	$vSmart->addCompositeFilter("Content", "a.Name,  a.Content, a.Summary", "Search", 1, "input_search w400");
	$vSmart->addLFilter("a.Status", "Status", $this->mDatabase->getAll2("select distinct Status, Status from ".$this->table." order by 1 asc"), 1, "w150");

	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));
//	$vSmart->addExtraActions(new CHref(getLink() . "order", "Mass Sort"));


	$vSmart->mColsWidths = array("20px", "60px",  "100px", "100px", "70%", "60px");
	$vSmart->mColsAligns = array("center", "center", "left", "left", "left", "right");
	
				$vSmart->setTemplate("admin");
				Return $vSmart->display();
  }

  /** comment here */
  function export() {
		$filename = 'News-' . date("Ymd");
		$items = array();

				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	$sql = "SELECT a.* from ".$this->table." a Where 1=1 ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = true;

	$vSmart->addHeader(array("Name", "Title", "Company"));
	$vSmart->addField("Name");
	$vSmart->addField("Title");
	$vSmart->addField("Company");

	$vSmart->mDefaultOrder = "a.Name";
	$vSmart->mDefaultOrderDir = "DESC";
	
	$vSmart->addCompositeFilter("Content", "a.Name, a.Title, a.Company", "Search", 1, "input_search w400");
	$vSmart->addLFilter("a.Status", "Status", $this->mDatabase->getAll2("select distinct Status, Status from ".$this->table." order by 1 asc"), 1, "w150");

		$vSmart->display(true);
		$data = $this->mDatabase->getAll($vSmart->mSql);
		if ($data) exportToExcel($filename, $data);
		else {
			Return "No data to export";
		}
  }

  function displayPreview($id){
	  $news = new CResource($id, $this->table);

	redirect("/news/" . $news->mRowObj->Guid . "?preview=on");
  }

  /** comment here */
  function cloneItem($id) {
	$item = $this->prepareClone($id);
	$item->mRowObj->Guid = "";
	$item->mRowObj->Title = "";
	$item->mRowObj->Image = "";
	$item->unregisterForm($versionid);
	$item->initForm();
	
	Return $item->displayEdit();	

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
