<?php
/** CFloorplan
* @package pages
* @author cgrecu
*/


class CFloorplan extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {

		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));

		$input = new CSelect("CommunityID", $this->mDatabase->getAll2("select ID, Name from communities order by name asc"), array("label" => "Filter by Community", "class"=>"w300", "placeholder"=>"Please select")); $this->form->addElement($input);
		$input = new CSelect("ModelID", $this->mDatabase->getAll2("select ID, Name from models order by name asc"), array("label" => "Model", "class"=>"mandatory w300", "placeholder"=>"Please select")); $this->form->addElement($input);
		$input = new CSelect("LotID", $this->mDatabase->getAll2("select ID, Name from lot_types order by name asc"), array("label" => "Lot Type", "class"=>"w200", "placeholder"=>"Please select")); $this->form->addElement($input);
		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("URLName", array("label"=>"URL Name", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("Price", array("label"=>"Price", "class"=>"")));
		$this->form->addElement(new CTextInput("NoPrice", array("label"=>"NoPrice", "class"=>"")));
		$this->form->addElement(new CTextInput("SQFT", array("label"=>"SQFT", "class"=>"")));
		$this->form->addElement(new CTextInput("Bedrooms", array("label"=>"Bedrooms", "class"=>"")));
		$this->form->addElement(new CTextInput("Bathrooms", array("label"=>"Bathrooms", "class"=>"")));
		$this->form->addElement(new CTextInput("Levels", array("label"=>"Levels (plans)", "class"=>"")));
		$this->form->addElement(new CTextInput("LevelLabels", array("label"=>"Level Labels", "class"=>"")));


		$this->form->addElement(new CTextInput("Image", array("label"=>"Rendering", "class"=>"")));
		$this->form->addElement(new CInputFile("ImageNew", array("label"=>"Upload New Rendering", "class"=>"")));
		$this->form->addElement(new CTextInput("Floorplan", array("label"=>"Floorplan", "class"=>"")));
		$this->form->addElement(new CInputFile("FloorplanNew", array("label"=>"Upload New Floorplan", "class"=>"")));

//		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();

		$comm = $this->mDatabase->getRow("select a.* from communities a, models b where a.id = b.communityid and b.id = " . intval($this->mRowObj->ModelID));

		if ($_FILES["ImageNew"]["tmp_name"]) $this->uploadImage("ImageNew", "comm/" .$comm["PHPCode"] . "/renderings", "", 1920, 0, "fitwidth", "Image"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false
		if ($_FILES["FloorplanNew"]["tmp_name"]) $this->uploadDocument("FloorplanNew", "comm/" . $comm["PHPCode"] . "/floorplans", "", "Floorplan"); // FieldName, FolderName, FileName, TableColumn, Save = false

		$this->_save();




	}

  }

?>