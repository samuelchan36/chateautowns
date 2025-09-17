<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CInquiryAdmin extends CSectionAdmin{

	var $table = "inquiries";
	var $actions = array("view", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Inquirys", "Inquiry");
	var $mClass = "CInquiry";
	


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
				$vSmart->mShowToggle = false;

	$vSmart->addHeader(array("First Name", "Last Name", "Email", "Date"));
	$vSmart->addField("FirstName");
	$vSmart->addField("LastName");
	$vSmart->addField("Email");
	$vSmart->addDField("TimeStamp");

	$vSmart->mDefaultOrder = "a.TimeStamp";
	$vSmart->mDefaultOrderDir = "DESC";
	
	$vSmart->addCompositeFilter("Content", "a.FirstName, a.LastName, a.Email, a.Comments", "Search", 1, "input_search w400");
//	$vSmart->addLFilter("a.Status", "Status", $this->mDatabase->getAll2("select distinct Status, Status from ".$this->table." order by 1 asc"), 1, "w150");

//	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));
//	$vSmart->addExtraActions(new CHref(getLink() . "order", "Sort"));
//	$vSmart->addExtraActions(new CHref(getLink() . "export", "Export"));


	$vSmart->mColsWidths = array("20px", "15%", "15%", "25%", "25%", "60px");
	$vSmart->mColsAligns = array("center", "left",  "left",  "left", "left", "right");
	
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
