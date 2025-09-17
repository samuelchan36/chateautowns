<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CUserAdmin extends CSectionAdmin{

	var $table = "cms_users";
	var $actions = array("edit", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Users", "User");
	var $mClass = "CUser";
	

  /** comment here */
  function __construct() {
	parent::__construct();
  }


  /** comment here */
  function display() {
				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	$sql = "SELECT a.*, b.Name as UserGroup from ".$this->table." a, cms_user_groups b Where a.GroupID = b.ID  ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = true;

	$vSmart->addHeader(array("Group", "Email", "Name"));
	$vSmart->addField("UserGroup");
	$vSmart->addField("Email");
	$vSmart->addField("Name");

	$vSmart->mDefaultOrder = "a.ID";
	$vSmart->mDefaultOrderDir = "DESC";
	
	$vSmart->addCompositeFilter("Content", "a.Name, a.Email", "Search", 1, "input_search size400");
	$vSmart->addLFilter("a.Status", "Status", $this->mDatabase->getAll2("select distinct Status, Status from ".$this->table." order by 1 asc"), 1, "w150");
	$vSmart->addLFilter("a.GroupID", "Group", $this->mDatabase->getAll2("select distinct a.ID, a.Name from cms_user_groups a, ".$this->table." b where a.ID = b.GroupID order by 1 asc"), 1, "w200");

	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));

	$vSmart->mColsWidths = array("20px", "20px", "20%", "20%", "50%", "60px");
	$vSmart->mColsAligns = array("center", "center", "left",  "left", "left", "right");
	
				$vSmart->setTemplate("admin");
				Return $vSmart->display();
  }



	/** comment here */
	function logout() {
	  	$GLOBALS["doc"]->mUserID = "";
	  	$GLOBALS["doc"]->mUser = "";
		$_SESSION["gUserID"] = 0;
		setcookie("UserID", 0, time() - 1000);
		redirect("/cms/index.php");
	}


  /** comment here */
  function mainSwitch() {
	switch($this->mOperation) {
		case "logout": Return $this->logout();
		default:
		  Return CSectionAdmin::mainSwitch();
	}
  }
}

?>
