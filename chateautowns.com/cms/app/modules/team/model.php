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
		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("Title", array("label"=>"Title", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("Email", array("label"=>"Email", "class"=>"")));
		$this->form->addElement(new CTextInput("Phone", array("label"=>"Phone", "class"=>"")));
		$this->form->addElement(new CInputFile("Photo", array("label"=>"Photo (767 x 767px)", "class"=>"mandatory")));


//		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();
		$this->uploadImage("Photo", $this->table, "", 767, 767, "thumbnail", "Photo"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false
		$this->_save();

	}

	/** comment here */
	function beforeDelete() {
		unlink(".." . $this->mRowObj->Photo);
		Return true;
	}

  }

?>