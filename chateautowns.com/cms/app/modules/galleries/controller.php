<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CGalleryAdmin extends CSectionAdmin{

	var $table = "galleries";
	var $actions = array("edit", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Galleries", "Gallery");
	var $mClass = "CGallery";
	


  /** comment here */
  function __construct() {
	parent::__construct();
  }


  /** comment here */
  function display() {


				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	$sql = "SELECT a.*  from ".$this->table." a  Where 1=1 ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = true;

	$vSmart->addHeader(array("Title", "Type", "Embed Code"));
	$vSmart->addField("Name");
	$vSmart->addField("Type");
	$vSmart->addFuncField($this, "getEmbedCode");

	$vSmart->mDefaultOrder = "a.ID";
	$vSmart->mDefaultOrderDir = "DESC";
	
//	$vSmart->addCompositeFilter("Content", "a.Name, a.jobdescription", "Search", 1, "input_search w400");
//	$vSmart->addLFilter("a.CommunityID", "Community", $this->mDatabase->getAll2("select distinct a.ID, a.Name from communities a, ".$this->table." b where a.ID = b.CommunityID order by 2 asc"), 1, "w300");
	$vSmart->addLFilter("a.Type", "Type", $this->mDatabase->getAll2("select distinct Type, Type from ".$this->table." order by 1 asc"), 1, "w150");
	$vSmart->addLFilter("a.Status", "Status", $this->mDatabase->getAll2("select distinct Status, Status from ".$this->table." order by 1 asc"), 1, "w150");

	$vSmart->addExtraActions(new CHref(getLink() . "edit", "Create new"));
//	$vSmart->addExtraActions(new CHref(getLink() . "order", "Sort"));
//	$vSmart->addExtraActions(new CHref(getLink() . "export", "Export"));


	$vSmart->mColsWidths = array("20px", "20px", "100px",  "40%", "30%", "60px");
	$vSmart->mColsAligns = array("center", "center", "left","left", "left", "right");
	
				$vSmart->setTemplate("admin");
				Return $vSmart->display();
  }

  /** comment here */
  function getEmbedCode($data) {
	Return "###Gallery_" . $data["ID"] . "###";
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
  function updateSlidesOrder($listingid, $sort) {
	$tmp = explode(";", $sort);
//	die2($tmp);
	print_r($sort);
	foreach ($tmp as $key=>$val) {
		$_SESSION["Slides"][$val]["orderid"] = $key;
//		if ($_SESSION["Slides"][$val]["status"] == "existing") $this->mDatabase->query("update listing_images set orderid = " . intval($key) . " where listingid = " . intval($listingid) . " and id = " . intval($_SESSION["Slides"][$val]["id"]));
	}
die();
	die("done");
  }

  /** comment here */
  function deleteSlide($listingid, $slideid) {
		$_SESSION["Slides"][$slideid]["status"] = "deleted";
//		if ($_SESSION["Slides"][$slideid]["status"] == "existing") $this->mDatabase->query("delete from listing_images where listingid = " . intval($listingid) . " and id = " . intval($_SESSION["Slides"][$slideid]["id"]));
	die2($_SESSION["Slides"]);
	die("done");
  }

  /** comment here */
  function uploadFile($listingid) {
	   $ret = array();
		$ret["ret"] = "notok";
		$ret["html"] = "";

		if ($_FILES["file"]["tmp_name"]) {
			if ($listingid) {
//				$listing = new CListing($id);
//				$guid = $listing->mRowObj->Guid;
				@mkdir("../media/galleries/");
				@mkdir("../media/galleries/" . $listingid);
				@mkdir("../media/galleries/" . $listingid . "/thumbnails");
				$path = "/media/galleries/" . $listingid. "/" . $_FILES['file']['name'];
				$thumbnail = "/media/galleries/" . $listingid. "/thumbnails/" . $_FILES['file']['name'];
			} else {
				@mkdir("../media/pending");
				@mkdir("../media/pending/" . $_SESSION["tmpfolder"]);
				@mkdir("../media/pending/" . $_SESSION["tmpfolder"] . "/thumbnails");
				$path = "/media/pending/" .  $_SESSION["tmpfolder"]. "/" . $_FILES['file']['name'];
				$thumbnail = "/media/pending/" . $_SESSION["tmpfolder"] .  "/thumbnails/" . $_FILES['file']['name'];
			}

			
			if (file_exists(".." . $path)) {
				unlink(".." . $path);
				unlink(".." . $thumbnail);
			}
			

			$ret2 = move_uploaded_file($_FILES['file']['tmp_name'], ".." . $path);
			if ($ret2) {
					copy(".." . $path, ".." . $thumbnail);

					try {
						$this->fm->fitWidth(".." . $path, 1920, false);
					} catch (Exception $x) {
						$path = "";
					}

					try {
//						CFileManager::fitToBox(".." . $thumbnail, 480, 270, true, true);
						$this->fm->fitWidth(".." . $thumbnail, 960, false);
					} catch (Exception $x) {
						$thumbnail = "";
					}

				$slideid = count($_SESSION["Slides"]);
				if ($path && $thumbnail) {
					$_SESSION["Slides"][$slideid] = array("id" => 0, "thumbnail" => $thumbnail, "image" => $path, "status" => "new", "orderid" => $slideid);


					$ret["ret"] = "ok";	
					$ret["html"] = '<div class="status-enabled" data-id="'.$slideid.'"><a href="'.$path.'" class="fancybox"><img  src="'.$thumbnail.'" alt="Image"></a><a href="" class="delete-slide"><i class="fas fa-trash-alt"></i></a></div>';
				} else {
					$ret["ret"] = "notok";	
					$ret["html"] = 'Unable to convert image';
				}
		}

	}
	die(json_encode($ret));

	
  }

  /** comment here */
  function mainSwitch() {
	switch($this->mOperation) {
		case "update-slides-order": $this->updateSlidesOrder($_GET["id"], $_GET["order"]);
		case "delete-slide": $this->deleteSlide($_GET["id"], $_GET["slideid"]);
		case "upload-file": $this->uploadFile($_GET["id"]);

		default:
		  Return CSectionAdmin::mainSwitch();
	}
  }
}

?>
