<?php
/** CTestimonial
* @package pages
* @author cgrecu
*/


class CService extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));
		$this->form->addElement(new CTextInput("Name", array("label"=>"Title", "class"=>"mandatory")));
		$this->form->addElement(new CInputFile("Image", array("label"=>"Image (624 x 300px)", "class"=>"mandatory")));
		$this->form->addElement(new CRichTextArea("Summary", array("label"=>"Content", "class"=>"mandatory", "rows" => 10)));


		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
//        $this->checkUniqueUrl();

        $this->setCommonFields();

		$this->uploadImage("Image", $this->table, "", 624, 300, "thumbnail", "Image"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false


		$this->_save();

	}

	/** comment here */
	function beforeDelete() {
		unlink(".." . $this->mRowObj->Image);
		Return true;
	}

  }

?>