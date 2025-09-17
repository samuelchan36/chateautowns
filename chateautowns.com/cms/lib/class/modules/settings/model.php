<?php
/** CSetting
* @package pages
* @author cgrecu
*/


class CSetting extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));
		$input = new CSelect("TypeID", array(), array("label" => "Type", "class"=>"mandatory w200", "placeholder"=>"Please select")); $input->getOptionsFromDb("job_types", "Name", "ID"); $this->form->addElement($input);
		$this->form->addElement(new CTextInput("Name", array("label"=>"Position Name", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("Subject", array("label"=>"Subject (for notification email)", "class"=>"mandatory")));
		$this->form->addElement(new CInputFile("PDF", array("label"=>"Upload Job Description", "class"=>"mandatory")));

		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {
//				die2($_POST);

        $this->registerForm();
        $this->setCommonFields();


		$this->uploadDocument("PDF", $this->table, "", "PDF"); // FieldName, FolderName, FileName, TableColumn, Save = false

		$this->_save();

	}

  }

?>