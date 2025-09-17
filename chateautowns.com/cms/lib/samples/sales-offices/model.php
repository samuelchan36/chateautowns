<?php
/** CSalesOffice
* @package pages
* @author cgrecu
*/


class CSalesOffice extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));

		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory url-source", "data-target" => "Code", "data-target-option" => "noslash")));
		$this->form->addElement(new CTextInput("Code", array("label"=>"Internal Code", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("Title", array("label"=>"Title", "class"=>"")));
		$input = new CSelect("Status", array(), array("label" => "Status", "class"=>"mandatory w300", "placeholder"=>"Please select"), array("options-source" => array("field", "sales_offices", "Status"))); $this->form->addElement($input);

		$this->form->addBlock("InfoB", array("label"=>"Information", "class"=>"block-standard"));
		$this->form->addElement(new CTextArea("Hours", array("label"=>"Hours of Operation <small>use Status=hours</small>", "class"=>"", "rows" => 2)));
		$this->form->addElement(new CTextArea("Message", array("label"=>"Message <small>use Status=message</small>", "class"=>"", "rows" => 2)));
//		$this->form->addElement(new CTextArea("Agents", array("label"=>"Sales Agents", "class"=>"", "rows" => 2)));
		$this->form->addElement(new CTextArea("Address", array("label"=>"Address", "class"=>"", "rows" => 2)));
		$this->form->addElement(new CTextInput("Phone", array("label"=>"Phone", "class"=>"")));
		$this->form->addElement(new CTextInput("Email", array("label"=>"Email", "class"=>"")));
		$this->form->addElement(new CTextInput("Waze", array("label"=>"Waze Link", "class"=>"")));
		$this->form->addElement(new CTextInput("Google", array("label"=>"Google Link", "class"=>"")));
		$this->form->addElement(new CInputFile("Map", array("label"=>"Map", "class"=>"")));

//		$this->form->addBlock("StatusB", array("label"=>"Options", "class"=>"block-standard"));
//		$this->form->addElement(new CCheckbox("HasChat", array("yes", "no"), array("label"=>"", "value-label" => "Has Chat", "class"=>"")));
//		$this->form->addElement(new CCheckbox("HasAppt", array("yes", "no"), array("label"=>"", "value-label" => "Has Appt", "class"=>"")));
//		$this->form->addElement(new CCheckbox("HasRegister", array("yes", "no"), array("label"=>"", "value-label" => "Has Registration", "class"=>"")));
//		$this->form->addElement(new CCheckbox("HasPackage", array("yes", "no"), array("label"=>"", "value-label" => "Has Download Packge", "class"=>"")));


		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();

		$this->_save();

		$this->uploadDocument("Map", $this->table . "/" . $this->mRowObj->Code, "", "Map"); // FieldName, FolderName, FileName, TableColumn, Save = false

		$this->_save();

	}

  }

?>