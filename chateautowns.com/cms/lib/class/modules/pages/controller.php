<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CPageAdmin extends CSectionAdmin{

	var $table = "cms_pages";
	var $actions = array("edit", "preview", "clone", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Pages", "Page");
	var $mClass = "CPage";

	var $readonly = false;


  /** comment here */
  function __construct() {
	parent::__construct();
	$this->checkAccess();
  }

  /** comment here */
  function checkAccess() {
		$check = is_writeable("../html");
		if (!$check) $this->readonly  = true;
  }



  /** comment here */
  function display() {
				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

	if ($this->readonly) error("Warning: Html folder is not writeable, changes cannot be published.");

	$sql = "SELECT a.* from ".$this->table." a Where 1=1 ##CRITERIA##";
	
				$vSmart = new CSmartTable($this->table, $sql);
				$vSmart->mItemsPerPage = $this->mItemsPerPage;
				$vSmart->setIcons($this->actions);
				$vSmart->mShowToggle = true;


	$vSmart->addHeader(array("Image", "Page", "SEO Title", "SEO Description"));
	$vSmart->addImageField("PageImage", 100);
	$vSmart->addFuncField($this, "getTitle");
	$vSmart->addEditField("SEOTitle");
	$vSmart->addEditField("Description");
//	$vSmart->addFuncField($this, "getAddress", "Address", "Address");

	$vSmart->mDefaultOrder = "a.ID";
	$vSmart->mDefaultOrderDir = "DESC";
	
	$vSmart->addCompositeFilter("Content", "a.Title, a.SEOTitle, a.content", "Search", 1, "input_search size400");
	$vSmart->addTFilter("a.Title", "Search by title", 1, "input_search size400");
	$vSmart->addTFilter("a.Address", "Search by address", 1, "input_search size400");
	$vSmart->addLFilter("a.BaseURL", "Section", $this->mDatabase->getAll2("select distinct BaseUrl, BaseUrl from cms_pages order by 1 asc"), 2, "w500");
	if (count($GLOBALS["cms_languages"]) > 1)  $vSmart->addLFilter("a.BaseLanguage", "Language", $this->mDatabase->getAll2("select distinct BaseLanguage, BaseLanguage from cms_pages order by 1 asc"), 2, "input_search w200");
	$vSmart->addExtraActions(new CHref(getLink() . "create", "Create new page"), "New");
	$vSmart->addExtraActions(new CHref(getLink() . "refresh-pages", "Update all pages from disk version"), "Refresh");

	$vSmart->mColsWidths = array("20px", "80px", "100px", "15%", "25%", "30%", "100px");
	$vSmart->mColsAligns = array("left", "left", "left", "left", "left", "left", "right");	
				$vSmart->setTemplate("admin");
				Return $vSmart->display();
  }


  /** comment here */
  function getAddress($data) {
//	Return ;
  }

  /** comment here */
  function getTitle($data) {
		Return  "<a href='".$data["Address"]."' target='_blank'>" .   $data["Title"] . "</a>";	
  }



  /** comment here */
  function displayPreview($id) {
	  $page = new CPage($id, "cms_pages");
//	  die($page->mRowObj->Address ."?preview=on&id=" . $id);
	 redirect($page->mRowObj->Address ."?preview=on&id=" . $id);

  }



  /** comment here */
  function getPublished($data) {
		if ($data["Published"] == "yes") Return "<img src='/cms/lib/images/common/small/bullet_ball_glass_green.png' title='Published'>";
		else Return "<a href='index.php?s=pages&o=publish&id=".$data["ID"]."'><img src='/cms/lib/images/common/small/bullet_ball_glass_yellow.png' title='Unpublished - click to publish'></a>";
  }


  /** comment here */
  function refreshPages() {
//die2($GLOBALS["cms_languages"]);

//		$parser = new CParser();
//		$files = $parser->loadFiles("../html/en/pages");
//
//		$ret = array();
//		foreach ($files as $key=>$val) {
//			$this->parseFile($val, "../html/en/pages", "en");
//		}

		foreach ($GLOBALS["cms_languages"] as $key=>$val) {
			$parser = new CParser();
			$files = $parser->loadFiles("../html/".$val[0]."/pages");


			foreach ($files as $key2=>$val2) {
				$this->parseFile($val2, "../html/".$val[0]."/pages", $val[0]);
			}

		}

		$system = new CSystem();
		$system->buildIndex();

		redirect(getLink());
  }



/** comment here */
		function parseFile($location, $basefolder, $lang = "en") {
				
				if ($lang != "en")  $baselang = "/fr"; else $baselang = "";
				$buf = file_get_contents($location);
				$filedata = pathinfo ($location);

					$xml_parser = xml_parser_create();
					$tags = array();
					$index = array();
					xml_parse_into_struct($xml_parser, $buf, $tags, $index);
					xml_parser_free($xml_parser);

					$title = ""; $description = ""; $image = "";
					foreach ($tags as $key=>$val) {
						if ($val["tag"] == "H1") {
							$title = ucwords($val["value"]);
							break;
						}
					}
					if (!$title) {
						foreach ($tags as $key=>$val) {
							if ($val["tag"] == "H2" || $val["tag"] == "H3") {
								$title = ucwords($val["value"]);
								break;
							}
						}
					}

					if (!$title) $title = ucwords($filedata["filename"]);

					foreach ($tags as $key=>$val) {
							if ($val["tag"] == "IMG") {
								$image = $val["attributes"]["SRC"];
								break;
							}
					}

					foreach ($tags as $key=>$val) {
							if ($val["tag"] == "P" && strlen($val["value"] ? $val["value"] : "") >= 20) {
								$description = $val["value"];
								break;
							}
					}

					$pageid = $this->mDatabase->getValue("cms_pages", "ID", "Location = '".addslashes2($location). "'");

					$obj = new CPage($pageid, "cms_pages");

					$time = filemtime($location);

					$obj->mRowObj->Location = $location;
					$obj->mRowObj->Filename = $filedata["basename"];
					$obj->mRowObj->URLName = $filedata["filename"];
					$obj->mRowObj->BaseLanguage = $lang;
					$obj->mRowObj->BaseURL = str_replace($basefolder, "", $filedata["dirname"]);
					if (!$obj->mRowObj->BaseURL) $obj->mRowObj->BaseURL = "/"; 

					$obj->mRowObj->Address = $obj->mRowObj->BaseURL;
					if ($obj->mRowObj->URLName != "home") {
						if ($obj->mRowObj->Address != "/") $obj->mRowObj->Address .=  "/";
						$obj->mRowObj->Address .= $obj->mRowObj->URLName;
						$obj->mRowObj->IsRoot = "no";
					} else {
						$obj->mRowObj->IsRoot = "yes";
					}
//					die2($obj->mRowObj);
					$obj->mRowObj->BaseURL = $baselang . $obj->mRowObj->BaseURL;
					$obj->mRowObj->Address = str_replace("/index", "/", $baselang . $obj->mRowObj->Address);

					if (!$obj->mRowObj->Title) $obj->mRowObj->Title = $title;
					if (!$obj->mRowObj->SEOTitle) $obj->mRowObj->SEOTitle = $obj->mRowObj->Title;
					if (!$obj->mRowObj->Description) $obj->mRowObj->Description = $description;
					if (!$obj->mRowObj->PageImage) $obj->mRowObj->PageImage = $image;
					if (!$obj->mRowObj->TimeStamp) $obj->mRowObj->TimeStamp = $time;

					if (!$pageid || !$obj->mRowObj->LastPublished) $obj->mRowObj->LastPublished = $obj->mRowObj->TimeStamp; 
					if (!$pageid || !$obj->mRowObj->LastCMSChange) $obj->mRowObj->LastCMSChange = $obj->mRowObj->TimeStamp; 
					$obj->mRowObj->LastDiskChange = $time; 

					$obj->mRowObj->DiskContent = $buf;
					if (!$obj->mRowObj->Content || $obj->mRowObj->Published == "yes") {
						$obj->mRowObj->Content = $buf;
						$obj->mRowObj->LastCMSChange = $time;
					}

					if ($time == $obj->mRowObj->LastCMSChange)  $obj->mRowObj->SyncStatus = "in-sync";
					if ($time < $obj->mRowObj->LastCMSChange)  $obj->mRowObj->SyncStatus = "newer-on-cms";
					if ($time > $obj->mRowObj->LastCMSChange)  $obj->mRowObj->SyncStatus = "newer-on-disk";

					if (!$obj->mRowObj->UserID) $obj->mRowObj->UserID = $_SESSION["gUserID"];
					if (!$pageid || !$obj->mRowObj->Published) $obj->mRowObj->Published = "yes";

					if (!$obj->mRowObj->Status) $obj->mRowObj->Status = "enabled";
					if (!$obj->mRowObj->AccessLevel) $obj->mRowObj->AccessLevel = "public";
					if (!$obj->mRowObj->ShowInSearch) $obj->mRowObj->ShowInSearch = "yes";
					if (!$obj->mRowObj->Sitemap) $obj->mRowObj->Sitemap = "yes";
					if (!$obj->mRowObj->SitemapPriority) $obj->mRowObj->SitemapPriority = 1;

					$obj->easySave();			
		}

		/** comment here */
		function refreshPage($id) {
			$this->title("Edit ". $this->mLabels[1]);
			$this->enforce();
			
			$class = $this->getClass();
			$vItem = new $class($id, $this->table);

			$vItem->folder = $this->url;
			$vItem->view = $this->folder."/view.html";

			$vItem->mRowObj->Content = file_get_contents($vItem->mRowObj->Location);
			$vItem->mRowObj->LastDiskChange = $vItem->mRowObj->LastCMSChange;
			$vItem->mRowObj->SyncStatus = "in-sync";

			if ($vItem->mDraft) {
				$vItem->mDraft->Content = $vItem->mRowObj->Content;
				$vItem->mDraft->SyncStatus = $vItem->mRowObj->SyncStatus;
				$vItem->mDraft->LastDiskChange = $vItem->mRowObj->LastDiskChange;
			}
			
			$vItem->unregisterForm(0);
			$vItem->initForm();

			Return $vItem->displayEdit();
			
		}

  /** comment here */
  function cloneItem($id) {

		$page = new CPage($id, $this->table);
		$page2 = new CPage(0, $this->table);

		$page2->mRowObj->Title = "";
		$page2->mRowObj->SEOTitle = "";
		$page2->mRowObj->Content = $page->mRowObj->Content;
		$page2->mRowObj->DiskContent = $page->mRowObj->Content;
		$page2->mRowObj->BaseURL = "";
		$page2->mRowObj->URLName = "";
		$page2->mRowObj->Location = "";
		$page2->mRowObj->Filename = "";
		$page2->mRowObj->IsRoot = "no";
		$page2->mRowObj->Address = "";
		$page2->mRowObj->UserID = intval($_SESSION["gUserID"]);
		$page2->mRowObj->TimeStamp = time();
		$page2->mRowObj->LastPublished = time();
		$page2->mRowObj->LastCMSChange = time();
		$page2->mRowObj->LastDiskChange = time();
		$page2->mRowObj->Status = "enabled";
		$page2->mRowObj->SyncStatus = "in-sync";
		$page2->mRowObj->Sitemap = "yes";
		$page2->mRowObj->AccessLevel = "public";
		$page2->mRowObj->ShowInSearch = "yes";
		$page2->mRowObj->Published = "no";
		$page2->mRowObj->SitemapPriority = 1;

		$page2->folder = $this->url;
		$page2->view = $this->folder."/view.html";

		$page2->unregisterForm(0);
		$page2->initForm();
		Return $page2->displayEdit();


  }


  /** comment here */
  function template2($id, $versionid) {
	$tpl = template2("lib/class/modules/pages/editor.html");
	$tpl->assign("Version", rand());

	$css_files = explode(",", MASTER_CSS);
	$css_paths = "";
	foreach ($css_files as $key => $val){
		$css_paths .= '<link type="text/css" rel="stylesheet" href="'.$val.'"/>' . "\n";
	}
	$tpl->assign("SiteCss", $css_paths);
	  if ($_SESSION["WorkingContent"]) {
//		$page = new CPage($id, "cms_pages");
//			  if ($versionid) $page->loadVersion($_GET["versionid"]);
//				$tpl->assign("Content", $page->mRowObj->WorkingContent ? $page->mRowObj->WorkingContent : $page->mRowObj->Content);
			$tpl->assign("Content", $_SESSION["WorkingContent"]);
	  } else {
		$tpl->assign("Content", file_get_contents("lib/class/modules/pages/template.html"));
	  }
	echo $tpl->output();die();
  }

  /** comment here */
  function updateWorkingContent($id) {
	$_SESSION["WorkingContent"] = $_POST["content"];
//	$this->mDatabase->query("update cms_pages set WorkingContent = '".addslashes2($_POST["content"])."' where id = " . intval($id));
	die("ok");
  }

  /** comment here */
  function validateAddress($id, $address) {
	if (substr($address, 0, 1) != "/") $address = "/" . $address;
	if (substr($address, -1) == "/") $address = substr($address, 0, -1);

	# ensure page is not a duplicate of an existing page
	$parts = explode("/", $address);
	foreach ($parts as $key=>$val) {
		$parts[$key] = trim(sanitize_text_for_urls(str_replace("&", "and", $val)));
	}
	$address = implode("/", $parts);

	 $data = $this->mDatabase->getAll("select ID from cms_pages where id <> " . intval($id) . " and Address ='" . addslashes2($address) . "'");
	 if ($data) die(""); else die($address);
	 
  }

/***********************************************************************************************************
****	  END OF TEMPLATE FUNCTIONS - CONTINUE WITH SPECIAL FUNCTIONS									****
***********************************************************************************************************/


  /** comment here */
  function mainSwitch() {
	switch($this->mOperation) {
		case "view": Return $this->displayPreview($_GET["id"]);
		case "get-address": Return $this->getPath($_GET["title"]);
		case "refresh": Return $this->refreshPage($_GET["id"]);
		case "refresh-pages": Return $this->refreshPages();
		case "clone": Return $this->cloneItem($_GET["id"]);
		case "template": Return $this->template2($_GET["id"], $_GET["versionid"]);
		case "update-content": Return $this->updateWorkingContent($_GET["id"]);
		case "validate-address": Return $this->validateAddress($_GET["id"], $_GET["address"]);
		default:
		  Return CSectionAdmin::mainSwitch();
	}
  }
}

?>
