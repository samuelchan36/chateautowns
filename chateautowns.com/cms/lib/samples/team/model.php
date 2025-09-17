<?php
/** CTeam
* @package pages
* @author cgrecu
*/


class CTeam extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));
		$this->form->addElement(new CSelect("DepartmentID", $this->mDatabase->getAll2("select ID, Name from departments order by 2 asc"), array("label"=>"Team", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("Title", array("label"=>"Title", "class"=>"mandatory")));
		$this->form->addElement(new CInputFile("Image", array("label"=>"Photo (600 x 750px)", "class"=>"")));


//		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();
		$this->uploadImage("Image", $this->table, "", 600, 750, "thumbnail", "Image"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false
		$this->_save();

	}

	/** comment here */
	function beforeDelete() {
		unlink(".." . $this->mRowObj->Image);
		Return true;
	}

  }

?>