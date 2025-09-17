<?php
/** CModel
* @package pages
* @author cgrecu
*/


class CModel extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));
		$this->form->addElement(new CSelect("CollectionID", $this->mDatabase->getAll2("select a.ID, concat(b.Name, ' - ', a.Name) as Name from fp_collections a, communities b where a.CommunityID = b.ID order by a.name asc"), array("label" => "Collection", "class"=>"mandatory w500", "placeholder"=>"Please select"))); 
		$this->form->addElement(new CSelect("TypeID", $this->mDatabase->getAll2("select ID, Name from fp_model_types order by 2 asc"), array("label" => "Type", "class"=>"mandatory w200", "placeholder"=>"Please select"))); 
		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("PDF", array("label"=>"Floorplan", "class"=>"")));
		$this->form->addElement(new CInputFile("PDFNew", array("value-label"=>"Upload New Floorplan", "class"=>"")));



//		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();

		$comm = $this->mDatabase->getRow("select b.* from communities b, fp_collections a where a.CommunityID = b.ID and a.ID = " . intval($this->mRowObj->CollectionID));

		if ($_FILES["PDFNew"]["tmp_name"]) {
			$this->uploadDocument("PDFNew", "comm/" . $comm["Guid"] . "/floorplans" , "", "PDF"); // FieldName, FolderName, FileName, TableColumn, Save = false
		}
//		if ($_FILES["Image"]) $this->uploadImage("Image", "companies", "", 200,200, "fitwidth", "Image"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false

		$this->_save();

	}

  }

?>