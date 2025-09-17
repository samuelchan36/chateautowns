<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CActivationAdmin extends CSectionAdmin{

	var $table = "cms_timers";
	var $actions = array("edit", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Timers", "Timer");
	var $mClass = "CActivation";
	


  /** comment here */
  function __construct() {
	parent::__construct();
  }

  /** comment here */
  function display() {

				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	$sql = "SELECT a.*  from ".$this->table." a Where 1=1 ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = false;

	$vSmart->addHeader(array("Publish Date/Time", "Content"));
//	$vSmart->addFuncField($this, "getPublishDateTime");
	$vSmart->addDField("PublishDateTime", "F d, Y H:i");
	$vSmart->addFuncField($this, "getContentTable");

	$vSmart->mDefaultOrder = "a.ID";
	$vSmart->mDefaultOrderDir = "DESC";
	
//	$vSmart->addCompositeFilter("Content", "a.Name,  a.Content, a.Summary", "Search", 1, "input_search w400");
//	$vSmart->addLFilter("a.CommunityID", "Community", $this->mDatabase->getAll2("select distinct a.ID, a.Name from communities a, ".$this->table." b where a.ID = b.CommunityID order by 1 asc"), 1, "w300");
//	$vSmart->addLFilter("a.Status", "Status", $this->mDatabase->getAll2("select distinct Status, Status from ".$this->table." order by 1 asc"), 1, "w150");

//	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));
//	$vSmart->addExtraActions(new CHref(getLink() . "order", "Mass Sort"));


	$vSmart->mColsWidths = array("20px",  "200px", "70%","60px");
	$vSmart->mColsAligns = array("center", "left", "left", "right");
	
				$vSmart->setTemplate("admin");
				Return $vSmart->display();
  }

  /** comment here */
  function getPublishDateTime($data) {
//	Return date("F d, Y H);
  }

  /** comment here */
  function getContentTable($data) {
	 $row = $this->mDatabase->getRow("select * from " . $data["ContentTable"] . " where ID = " . intval($data["ContentID"]));
	 $ret = "[" . $data["ContentTable"] . "] ";
	 $ret = ucwords(str_replace("cms_", "", $ret));
	 if (isset($row["Name"])) Return $ret . $row["Name"];
	 if (isset($row["Title"])) Return $ret . $row["Title"];
	 Return $ret . $row["ID"];
  }

  /** comment here */
  function check($right = "") {
	$data = $this->mDatabase->getAll("select * from cms_timers where PublishYmd = " . date("Ymd") . " and PublishDateTime <= unix_timestamp() order by type desc");

	foreach ($data as $key=>$val) {
		$class = $val["ContentClass"];
		$class = new $class($val["ContentID"], $val["ContentTable"]);
		if ($val["Type"] == "activate") $class->activate();
		if ($val["Type"] == "publish") $class->publish();
	}
	die("complete");
  }




  /** comment here */
  function mainSwitch() {
	switch($this->mOperation) {
		case "check": Return $this->check();

		default:
		  Return CSectionAdmin::mainSwitch();
	}
  }
}

?>