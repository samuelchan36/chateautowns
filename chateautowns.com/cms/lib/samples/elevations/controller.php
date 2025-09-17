<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CElevationAdmin extends CSectionAdmin{

	var $table = "fp_elevations";
	var $actions = array("edit", "view", "clone", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Elevations", "Elevation");
	var $mClass = "CElevation";
	


  /** comment here */
  function __construct() {
	parent::__construct();
  }


  /** comment here */
  function display() {
				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	$sql = "SELECT a.*, b.Name as Model, c.Name as Collection, d.Name as Community 
				FROM ".$this->table." a, fp_models b, fp_collections c, communities d Where a.ModelID = b.ID and b.CollectionID = c.ID and c.CommunityID = d.ID  ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = true;

	$vSmart->addHeader(array("Collection", "Model", "Name", "Price", "No Price", "Sqft"));
	$vSmart->addField("Collection");
	$vSmart->addField("Model");
	$vSmart->addEditField("Name");
	$vSmart->addEditField("Price");
	$vSmart->addEditField("NoPrice");
	$vSmart->addEditField("Sqft");

	$vSmart->mDefaultOrder = "b.OrderID";
	$vSmart->mDefaultOrderDir = "ASC";
	
	$vSmart->addCompositeFilter("Content", "b.Name, c.name", "Search", 1, "input_search w400");
	$vSmart->addLFilter("a.Status", "Status", $this->mDatabase->getAll2("select distinct Status, Status from ".$this->table." order by 1 asc"), 1, "w150");
	$vSmart->addLFilter("c.CommunityID", "Community", $this->mDatabase->getAll2("select distinct a.ID, a.Name from communities a, fp_collections b, fp_models c, fp_elevations d where a.ID = b.CommunityID and b.ID = c.CollectionID and c.ID = d.ModelID order by 2 asc"), 1, "w300");
	if ($_GET["srcequal_CommunityID"]) {
		$vSmart->addLFilter("b.CollectionID", "Collection", $this->mDatabase->getAll2("select distinct b.ID, b.Name from fp_collections b, fp_models c, fp_elevations d where b.CommunityID = '".$_GET["srcequal_CommunityID"]."' and b.ID = c.CollectionID and c.ID = d.ModelID order by 2 asc"), 1, "w200");
	}
	if ($_GET["srcequal_CollectionID"]) {
		$vSmart->addLFilter("b.ModelID", "Model", $this->mDatabase->getAll2("select distinct c.ID, c.Name from fp_models c, fp_elevations d where c.CollectionID = '".$_GET["srcequal_CollectionID"]."' and c.ID = d.ModelID order by 2 asc"), 1, "w150");
	}

	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));
//	$vSmart->addExtraActions(new CHref(getLink() . "order", "Sort"));
//	$vSmart->addExtraActions(new CHref(getLink() . "export", "Export"));


	$vSmart->mColsWidths = array("20px", "20px", "15%", "10%", "10%","10%","10%","10%", "60px");
	$vSmart->mColsAligns = array("center", "center", "left",  "left", "left", "left", "left", "left", "right");
	
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
  function filterModels($id) {
	$data = $this->mDatabase->getAll("select ID, Name from fp_models where collectionid = " . intval($id));
	die2(json_encode($data));
  }

  /** comment here */
  function safeUrl($id, $name) {
	$comm = $this->mDatabase->getRow("select a.Guid, c.Name from communities a, fp_collections b, fp_models c where a.id = b.communityid and b.id = c.CollectionID and c.ID = " . intval($id));
	$address = $comm["Name"];
	if (substr($address, 0, 1) != "/") $address = "/" . $address;

	# ensure page is not a duplicate of an existing page
	$parts = explode("/", $address);
	foreach ($parts as $key=>$val) {
		$parts[$key] = trim(sanitize_text_for_urls(str_replace("&", "and", $val)));
	}
	$address = implode("/", $parts);
	
	$address = substr($address, 1);
	if ($options == "addslash") $address = "/" . $address; 

	$url = $comm["Guid"] . "-" . $address . "-" . strtolower($name);
	die($url);

  }


  /** comment here */
  function mainSwitch() {
	switch($this->mOperation) {
		case "filter-models": Return $this->filterModels($_GET["id"]);
		case "safe-url": Return $this->safeUrl($_GET["id"], $_GET["name"]);
		default:
		  Return CSectionAdmin::mainSwitch();
	}
  }
}

?>
