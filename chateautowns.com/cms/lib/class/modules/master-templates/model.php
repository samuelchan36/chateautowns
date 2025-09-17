<?php
/** CTemplate
* @package pages
* @author cgrecu
*/


class CTemplate extends CDBContent {

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
		if ($this->mRowObj->Location) $time = filemtime($this->mRowObj->Location); else $time = $this->mRowObj->LastDiskChange;
		if ($time == $this->mRowObj->LastCMSChange) $this->mRowObj->SyncStatus = "in-sync";
		if ($time < $this->mRowObj->LastCMSChange) $this->mRowObj->SyncStatus = "newer-on-cms";
		if ($time >  $this->mRowObj->LastCMSChange) $this->mRowObj->SyncStatus = "newer-on-disk";


		$this->form->addBlock("Details", array("label"=>"Template Info", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));
		$input = new CSelect("Type", array(), array("label" => "Type", "class"=>"mandatory w200", "placeholder"=>"Please select")); $input->getOptionsFromField("cms_templates", "Type"); $this->form->addElement($input);
		$this->form->addElement(new CTextInput("Title", array("label"=>"Name", "class"=>"mandatory")));
		$this->form->addElement(new CText("Filename", $this->mRowObj->Filename, array("label"=>"Filename", "class"=>"")));
		$this->form->addElement(new CText("Location", $this->mRowObj->Location, array("label"=>"Path", "class"=>"")));

		$this->form->addBlock("Dates", array("label"=>"Status & Dates", "class"=>"block-standard"));
		$this->form->addElement(new CText("LastPublished", date("F d, Y H:i", $this->mRowObj->LastPublished), array("label"=>"Last Published", "class"=>"")));
		$this->form->addElement(new CText("LastCMSChange", date("F d, Y H:i", $this->mRowObj->LastCMSChange), array("label"=>"Last Changed in the CMS", "class"=>"")));
		$this->form->addElement(new CText("LastDiskChange", date("F d, Y H:i", $time), array("label"=>"Last Changed on Disk", "class"=>"")));

		$status = $this->mRowObj->SyncStatus;
		$class = "";
		if ($status == "newer-on-disk") {
			$class="red-alert";
			$status .= "<a href='/cms/master-templates/refresh?id=".$this->mRowObj->ID."'>Load disk version</a>";
		}
		if ($status == "newer-on-cms")  $class="green-alert";
		$this->form->addElement(new CText("SyncStatus", $status, array("label"=>"Sync Status", "class"=>$class)));


		$this->form->addBlock("Edit", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CTextArea("Content", array("label"=>"", "class"=>"mandatory")));


		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

		$this->registerForm();
        $this->setCommonFields();

		$this->_save();
		$this->mRowObj->LastCMSChange = time();
		$this->justSave();
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

	/** comment here */
	function beforePublish() {
		if (!is_writeable("../html")) {
			error("Unable to publish, template folder is not writeable");
			Return false;;
		}

		Return true;
	}

	/** comment here */
	function afterPublish() {
//		die2($this->mRowObj);
		$fh = fopen($this->mRowObj->Location, "w");
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