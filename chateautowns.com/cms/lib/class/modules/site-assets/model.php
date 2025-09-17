<?php
/** CAsset
* @package pages
* @author cgrecu
*/


class CSiteAsset extends CDBContent {


	function __construct($id, $table) {
		$this->supports_publishing = true;
		$this->supports_draft = true;
		$this->supports_timers = true;
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {


		$this->form->addBlock("Details", array("label"=>"Template Info", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));
		$input = new CSelect("Type", array(), array("label" => "Type", "class"=>"mandatory w200", "placeholder"=>"Please select")); $input->getOptionsFromField("cms_assets", "Type"); $this->form->addElement($input);
		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory")));
		$this->form->addElement(new CText("Location", $this->mRowObj->Path, array("label"=>"Filename", "class"=>"")));

		$this->form->addBlock("Dates", array("label"=>"Status & Dates", "class"=>"block-standard"));
		$this->form->addElement(new CText("LastDiskChange", date("F d, Y H:i", filemtime(".." . $this->mRowObj->Path)), array("label"=>"Last Changed on Disk", "class"=>"")));


		$this->form->addBlock("Edit", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CTextArea("Content", array("label"=>"", "class"=>"mandatory", "value"=>file_get_contents(".." . $this->mRowObj->Path))));


		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {
		@file_put_contents(".." . $this->mRowObj->Path, $_POST["Content"]);
	}

	

  }

?>