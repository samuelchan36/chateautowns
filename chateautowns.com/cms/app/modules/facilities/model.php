<?php
/** CTeam
* @package pages
* @author cgrecu
*/


class CFacility extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));
		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory")));
		$this->form->addElement(new CInputFile("Photo", array("label"=>"Photo (1320 x 1110px)", "class"=>"mandatory")));
		$this->form->addElement(new CRichTextArea("Capabilities", array("label"=>"Capabilities", "class"=>"mandatory")));
		$this->form->addElement(new CTextArea("Specs", array("label"=>"Details", "class"=>"mandatory"), array("help" => "Enter each set of stats on a separate line, separate the value from the label with a | character. I.e. 100,000 | Total square footage")));


//		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();
		$this->uploadImage("Photo", $this->table, "", 1320, 1110, "thumbnail", "Photo"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false
		$this->_save();

	}

	/** comment here */
	function beforeDelete() {
		unlink(".." . $this->mRowObj->Photo);
		Return true;
	}


  }

?>