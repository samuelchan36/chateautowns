<?php
/** {ClassName}
* @package pages
* @author cgrecu
*/


class {ClassName} extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));


		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();

		if ($_FILES["Link"]) $this->uploadDocument("Link", $province["Code"] . "/" . $this->table, "", "Link"); // FieldName, FolderName, FileName, TableColumn, Save = false
		if ($_FILES["Image"]) $this->uploadImage("Image", "companies", "", 200,200, "fitwidth", "Image"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false

		$this->_save();

	}

  }

?>