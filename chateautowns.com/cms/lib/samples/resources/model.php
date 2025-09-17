<?php
/** CTestimonial
* @package pages
* @author cgrecu
*/


class CResource extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));
		$this->form->addElement(new CTextInput("Name", array("label"=>"Title", "class"=>"mandatory")));
		$this->form->addElement(new CSelect("Type", array(), array("label"=>"Icon", "class"=>"mandatory"), array("options-source" => array("field", $this->table, "Type"))));
		$this->form->addElement(new CTextInput("CallToAction", array("label"=>"Subtitle", "class"=>"")));
		$this->form->addElement(new CInputFile("Path", array("label"=>"Upload Document", "class"=>"")));
		$this->form->addElement(new CTextInput("Link", array("label"=>"Resource Link (External resources)", "class"=>"")));


		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();

        $this->setCommonFields();

		$this->uploadDocument("Path", $this->table, "", "Path"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false


		$this->_save();

	}

	/** comment here */
	function beforeDelete() {
		@unlink(".." . $this->mRowObj->Path);
		Return true;
	}

  }

?>