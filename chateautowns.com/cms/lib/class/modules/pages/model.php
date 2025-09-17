<?php
/** CPage
* @package pages
* @author cgrecu
*/

class CPage extends CDBContent {


	var $basefolder = "../html/en/pages";

	function __construct($id, $table) {
		$this->supports_publishing = true;
		$this->supports_draft = true;
		$this->supports_timers = true;
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {



		if (!$this->mRowObj->Content) {
			$_POST["Content"] = file_get_contents("lib/class/modules/pages/template.html");
		} 
		
		if ($this->mRowObj->Location) {
			if ($this->mRowObj->Location && file_exists($this->mRowObj->Location)) $time = filemtime($this->mRowObj->Location); else $time = $this->mRowObj->LastDiskChange;
			if ($time == $this->mRowObj->LastCMSChange) $this->mRowObj->SyncStatus = "in-sync";
			if ($time < $this->mRowObj->LastCMSChange) $this->mRowObj->SyncStatus = "newer-on-cms";
			if ($time >  $this->mRowObj->LastCMSChange) $this->mRowObj->SyncStatus = "newer-on-disk";
		} else {
			$this->mRowObj->SyncStatus = "in-sync";
		}

		$this->form->addBlock("Details", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));

		$this->form->addElement(new CTextInput("Title", array("label"=>"Page Name", "class"=>"mandatory")));
		if ($this->mRowObj->Location) $this->form->addElement(new CText("Location", $this->mRowObj->Location, array("label"=>"Disk Address", "class"=>"")));
		if (!$this->mRowObj->Location) {
		$this->form->addElement(new CTextInput("Address", array("label"=>"Page Address", "class"=>"mandatory")));
//		die2($this->mRowObj);
//		if ($this->mRowObj->ID && ($this->mRowObj->BaseURL == $this->mRowObj->Address)) $_POST["IsSubfolder"] = 'yes'; else $_POST["IsSubfolder"] = 'no'; 
		$this->form->addElement(new CCheckbox("IsRoot", array("yes", "no"), array("label"=>"", "value-label" => "Is index page", "class"=>"")));
		} else {
			$this->form->addElement(new CText("Address", "<a href='".$this->mRowObj->Address."' target='_blank'>" . $this->mRowObj->Address . "</a>", array("label"=>"Web Address", "class"=>"")));
		}
		
		$languages = array();
		foreach ($GLOBALS["cms_languages"] as $key=>$val) {
			$languages[] = $val;
		}

		$this->form->addElement(new CSelect("BaseLanguage", $languages, array("label"=>"Language", "class"=>"")));


		$this->form->addBlock("seo", array("label"=>"SEO", "class"=>"block-standard"));
		$this->form->addElement(new CTextInput("SEOTitle", array("label"=>"SEO Title", "class"=>"mandatory")));
		$this->form->addElement(new CTextArea("Description", array("label"=>"SEO Description", "class"=>"")));
		$this->form->addElement(new CInputFile("PageImage", array("label"=>"Page Image", "class"=>"")));

		$this->form->addBlock("options", array("label"=>"Page Options", "class"=>"block-standard"));
		$this->form->addElement(new CCheckbox("Sitemap", array("yes", "no"), array("label"=>"", "value-label" => "Include in Google Sitemap", "class"=>"")));
		$this->form->addElement(new CCheckbox("ShowInSearch", array("yes", "no"), array("label"=>"", "value-label" => "Include in Site Search", "class"=>"")));
		$this->form->addElement(new CCheckbox("AccessLevel", array("public", "private"), array("label"=>"", "value-label" => "Public Page", "class"=>"")));

		
		$this->form->addBlock("Dates", array("label"=>"Status & Dates", "class"=>"block-standard"));
		$this->form->addElement(new CText("LastPublished", $this->mRowObj->LastPublished ? date("F d, Y H:i", $this->mRowObj->LastPublished) : "N/A", array("label"=>"Last Published", "class"=>"")));
		$this->form->addElement(new CText("LastCMSChange", $this->mRowObj->LastCMSChange ? date("F d, Y H:i", $this->mRowObj->LastCMSChange) : "N/A", array("label"=>"Last Changed in the CMS", "class"=>"")));
		$this->form->addElement(new CText("LastDiskChange", $this->mRowObj->ID ? date("F d, Y H:i", $time) : "N/A", array("label"=>"Last Changed on Disk", "class"=>"")));

		$status = $this->mRowObj->SyncStatus;
		$class = "";
		if ($status == "newer-on-disk") {
			$class="red-alert";
			$status .= "<a href='/cms/pages/refresh?id=".$this->mRowObj->ID."'>Load disk version</a>";
		}
		if ($status == "newer-on-cms")  {
			$class="green-alert";
			$status .= "<a href='/cms/pages/refresh?id=".$this->mRowObj->ID."'>Load disk version</a>";
		}
		$this->form->addElement(new CText("SyncStatus", $status, array("label"=>"Sync Status", "class"=>$class)));

		

		$this->form->addBlock("pagecontent", array("label"=>"Content", "class"=>"block-standard"));
//		$input = new CTextArea("Content", array("label"=>"", "class"=>"page-editor editorArea"));
		$frameurl = "/cms/pages/template?id=".$this->mRowObj->ID . "&versionid=0";
		if ($_GET["o"] == "load-version" && $_GET["versionid"]) $frameurl .= "&versionid=" . $_GET["versionid"];
		$this->form->addElement(new CText("EditorArea", "<div class='editor-actions'><a id='activate-editor'>VISUAL EDITOR</a><a id='edit-source-code'>SOURCE CODE EDITOR</a></div><div id='iframe-content-holder'><iframe id='iframe-content' src='".$frameurl."'></iframe></div>", array("label"=>"", "class"=>$class)));
		$this->form->addElement(new CTextArea("Content", array("label"=>"", "class"=>"")));
		$this->form->addElement(new CHidden("Dirty", ($_GET["o"] =="refresh" ? "yes" : "no"), array("label"=>"", "class"=>"")));

//		$this->form->addElement($input);

		$_SESSION["WorkingContent"] = $_POST["Content"];

		$this->publishing();
		Return $this->html();

	}



	/** comment here */
	function save() {

//		die2($_POST);
		
		if ($_POST["Dirty"] == "no") { 
			unset($_POST["Content"]);
		} else {
			$_POST["Content"] = str_replace(array("<!--", "-->"), array("\n<!--", "-->\n"), $_POST["Content"]); // ensure Template Power Blocks will work
		}

		$this->registerForm();


		if (!$this->mRowObj->Location) {

			# ensure page is not a duplicate of an existing page
			if (substr($this->mRowObj->Address, 0, 1) != "/") $this->mRowObj->Address = "/" . $this->mRowObj->Address;
			if (substr($this->mRowObj->Address, -1) == "/") $this->mRowObj->Address = substr($this->mRowObj->Address, 0, -1);

			$parts = explode("/", $this->mRowObj->Address);

			foreach ($parts as $key=>$val) {
				$parts[$key] = trim(sanitize_text_for_urls(str_replace("&", "and", $val)));
			}
			$this->mRowObj->Address = implode("/", $parts);
			
			$check = $this->mDatabase->getValue("cms_pages", "count(*)", "Address = '" . addslashes2($this->mRowObj->Address) . "' and ID <> " . intval($this->mRowObj->ID));
			if ($check) {
				error("Sorry, this page address is currently in use!", "error");
				Return false;
			}
			
		}

		$this->setCommonFields();
		if (!$this->mRowObj->SitemapPriority) $this->mRowObj->SitemapPriority = 1;
		if (!$this->mRowObj->SyncStatus) $this->mRowObj->SyncStatus = "in-sync";
		if (!$this->mRowObj->LastPublished) $this->mRowObj->LastPublished = 0;
		if (!$this->mRowObj->BaseLanguage) $this->mRowObj->BaseLanguage = "en";

		## cleanup content
//		$this->mRowObj->WorkingContent = $this->mRowObj->Content;
//		$this->mInitial->WorkingContent = $this->mInitial->Content;

		$this->_save();
		if ($_FILES["PageImage"]) $this->uploadImage("PageImage", "pages", "", 800,800, "fitwidth", "PageImage"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false
		$this->mRowObj->LastCMSChange = time();
		if (!$this->mRowObj->LastDiskChange) $this->mRowObj->LastDiskChange = $this->mRowObj->LastCMSChange;
		if ($this->is_new || ($_POST["PublishingType"] == "instant" && $_POST["Dirty"] == "yes")) {
			$this->afterPublish();
		} else {
			$this->justSave();
		}

		Return true;
	}


	/** comment here */
	function beforeDelete() {
		if (!is_writeable("../html")) {
			error("Unable to delete, template folder is not writeable");
			Return false;;
		}
		Return true;		
	}

	/** comment here */
	function afterDelete() {
		if ($this->mRowObj->Location) unlink($this->mRowObj->Location);
		Return true;;
	}

//
//	/** comment here */
//	function initLocation() {
//		$basepath = $this->mRowObj->BaseURL;
//
//		$parts = explode("/", $basepath);
//		$base = "../";
//		while ($slice = array_shift($parts)) {
//			$base .= $slice . "/";
//			@mkdir($base);
//		}
//
//		if (!$this->mRowObj->Location) {
//			$this->mRowObj->Location = "html/en/pages" . $this->mRowObj->BaseURL . $this->mRowObj->URLName .".html";
//			$this->easySave();
//		}		
//	}



	/** comment here */
	function beforePublish() {
		if (!is_writeable("../html")) {
			error("Unable to publish, html folder is not writeable");
			Return false;;
		}

		if (!$this->mRowObj->Address) {
			error("Cannot publish this page, please enter an address first", "error");
			Return false;
		}

		Return true;
	}

	/** comment here */
	function afterPublish() {

//		die2($this->mRowObj);
		if (!$this->mRowObj->Location) {

			if (substr($this->mRowObj->Address, 0, 1) != "/") $this->mRowObj->Address = "/" . $this->mRowObj->Address;
			if (substr($this->mRowObj->Address, -1) == "/") $this->mRowObj->Address = substr($this->mRowObj->Address, 0, -1);


			# ensure page is not a duplicate of an existing page
			$parts = explode("/", $this->mRowObj->Address);
			foreach ($parts as $key=>$val) {
				$parts[$key] = trim(sanitize_text_for_urls(str_replace("&", "and", $val)));
			}

			$this->mRowObj->Address = implode("/", $parts);
			
			$check = $this->mDatabase->getValue("cms_pages", "count(*)", "Address = '" . addslashes2($this->mRowObj->Address) . "' and ID <> " . intval($this->mRowObj->ID));
			if ($check) {
				error("Sorry, this page address is currently in use!", "error");
				Return false;
			}
//						die($this->mRowObj->IsRoot);

			if ($this->mRowObj->IsRoot=="no") {
				$this->mRowObj->Location = $this->basefolder . $this->mRowObj->Address . ".html";
				$this->mRowObj->URLName = array_pop($parts);
				
			} else {
				$this->mRowObj->Location = $this->basefolder . $this->mRowObj->Address . "/".DEFAULT_FOLDER_INDEX.".html";
				$this->mRowObj->URLName = DEFAULT_FOLDER_INDEX;
			}
			$this->mRowObj->Address = str_replace("/" . DEFAULT_FOLDER_INDEX, "/", $this->mRowObj->Address);

			
			$path = $this->basefolder;
			foreach ($parts as $key=>$val) {
				$path .= "/" . $val;
				@mkdir($path);
			}

			$this->mRowObj->BaseURL= str_replace("//", "/", "/" . implode("/", $parts));
			$this->mRowObj->Filename = $this->mRowObj->URLName . ".html";
		}
//die('done');

		$fh = fopen(ROOT_DIR . str_replace("..", "", $this->mRowObj->Location), "w");
		fwrite($fh, $this->mRowObj->Content);
		fclose($fh);

		$this->mRowObj->DiskContent = $this->mRowObj->Content;
		$time = filemtime($this->mRowObj->Location);
		$this->mRowObj->LastCMSChange = $time;
		$this->mRowObj->LastPublished = $time;
		$this->mRowObj->LastDiskChange = $time;
		$this->mRowObj->SyncStatus = "in-sync";
		$this->justSave();
	}

  }

?>