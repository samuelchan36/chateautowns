<?php
/** CProduct
* @package pages
* @author cgrecu
*/


class CProduct extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("Details", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));
		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory url-source", "data-target" => "Guid", "data-target-option" => "noslash")));
		$this->form->addElement(new CTextInput("Guid", array("label"=>"Link", "class"=>"")));
		$this->form->addElement(new CInputFile("Photo", array("label"=>"Photo (max 1920px width)", "class"=>"mandatory")));
		$this->form->addElement(new CTextArea("Summary", array("label"=>"Summary", "class"=>"mandatory", "rows" => 10)));
		$this->form->addElement(new CRichTextArea("Description", array("label"=>"Description", "class"=>"mandatory", "rows" => 10)));
		$this->form->addElement(new CTextArea("Benefits", array("label"=>"Benefits", "class"=>"mandatory", "rows" => 10), array("help" => "One per line")));
		$this->form->addElement(new CTextArea("Applications", array("label"=>"Applications", "class"=>"mandatory", "rows" => 10), array("help" => "One per line")));
//		$this->form->addElement(new CTextArea("Specs", array("label"=>"Specifications", "class"=>"", "rows" => 10)));
		
//		$this->form->addBlock("DocumentsB", array("label"=>"Specification Documents", "class"=>"block-standard"));
//		$data = $this->mDatabase->getAll("select * from product_documents where type = 'Specifications' and productid = " . intval($this->mRowObj->ID));
//		$txt = "";
//		foreach ($data as $key=>$val) {
//			$txt .= $val["Path"] . "<br>";
//		}
//		$this->form->addElement(new CText("Specs", $txt, array("label"=>"", "class"=> "")));
//		$this->form->addElement(new CInputFile("Path", array("label"=>"Upload Specifications", "class"=>"", "name" => "Path[]", "multiple" => "multiple")));
//
//		$this->form->addBlock("DocumentsC", array("label"=>"Drawings", "class"=>"block-standard"));
//		$data = $this->mDatabase->getAll("select * from product_documents where type = 'Drawings' and productid = " . intval($this->mRowObj->ID));
//		$txt = "";
//		foreach ($data as $key=>$val) {
//			$txt .= $val["Path"] . "<br>";
//		}
//		$this->form->addElement(new CText("Drawings", $txt, array("label"=>"", "class"=> "")));
//		$this->form->addElement(new CInputFile("Path2", array("label"=>"Upload Drawings", "class"=>"", "name" => "Path2[]", "multiple" => "multiple")));

		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();
//		$this->mRowObj->AgentID = intval($this->mRowObj->AgentID);
		$this->uploadImage("Photo", $this->table, "", 1920, 0, "fitwidth", "Photo"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false
		$this->_save();
		
//		die2($_FILES);
//		if ($_FILES["Path"][0]["tmp_name"]) {
//
//		}



	}

	/** comment here */
	function beforeDelete() {
		unlink(".." . $this->mRowObj->Photo);
		Return true;
	}

  }

?>