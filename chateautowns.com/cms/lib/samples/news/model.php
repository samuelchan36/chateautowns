<?php
/** CTestimonial
* @package pages
* @author cgrecu
*/


class CNews extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));
		$this->form->addElement(new CTextInput("Name", array("label"=>"Title", "class"=>"mandatory url-source", "data-target" => "Guid", "data-target-option" => "noslash")));
		$this->form->addElement(new CTextInput("Guid", array("label"=>"Article Address", "class"=>"")));
		$this->form->addElement(new CTextInput("Link", array("label"=>"Article Link (External articles)", "class"=>"")));
		$this->form->addElement(new CInputDate("ArticleDate", array("label"=>"Date", "class"=>"mandatory w300")));
		$this->form->addElement(new CInputFile("Image", array("label"=>"Image (1920px)", "class"=>"")));
		$this->form->addElement(new CTextArea("Summary", array("label"=>"Summary", "class"=>"mandatory", "rows" => 10)));
		$this->form->addElement(new CRichTextArea("Content", array("label"=>"Content", "class"=>"", "rows" => 10)));


		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->checkUniqueUrl();

        $this->setCommonFields();

		$this->uploadImage("Image", $this->table, "", 1920, 0, "fitwidth", "Image"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false


		$this->_save();

	}

	/** comment here */
	function beforeDelete() {
		@unlink(".." . $this->mRowObj->Image);
		Return true;
	}

  }

?>