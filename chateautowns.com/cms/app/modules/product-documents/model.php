<?php
/** CProductDocument
* @package pages
* @author cgrecu
*/


class CProductDocument extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {

		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));

		$input = new CSelect("ProductID", $this->mDatabase->getAll2("select ID, Name from products order by name asc"), array("label" => "Product", "class"=>"w300", "placeholder"=>"Please select")); $this->form->addElement($input);
		$input = new CSelect("Type", array(), array("label" => "Type", "class"=>"w300", "placeholder"=>"Please select"), array("options-source" => array("field", $this->table, "Type"))); $this->form->addElement($input);
		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory")));
		$this->form->addElement(new CInputFile("Path", array("label"=>"Document Path (optional)", "class"=>"")));

//		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();

		$this->uploadDocument("Path", $this->table,  "", "Path"); // FieldName, FolderName, FileName, TableColumn, Save = false

		$this->_save();




	}

  }

?>