<?php
/** CElevation
* @package pages
* @author cgrecu
*/


class CElevation extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));

		$input = new CSelect("CollectionID", $this->mDatabase->getAll2("select a.ID, concat(b.Name, ' - ' , a.Name) as Name from fp_collections a, communities b where b.ID = a.CommunityID order by a.name asc"), array("label" => "Filter by Collection", "class"=>"w500", "placeholder"=>"Please select")); $this->form->addElement($input);
		$input = new CSelect("ModelID", $this->mDatabase->getAll2("select ID, Name from fp_models order by name asc"), array("label" => "Model", "class"=>"mandatory w300", "placeholder"=>"Please select")); $this->form->addElement($input);
		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("Guid", array("label"=>"Link", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("Price", array("label"=>"Price", "class"=>"")));
		$this->form->addElement(new CTextInput("NoPrice", array("label"=>"Price Message <small>when no price set</small>", "class"=>"")));

		$this->form->addBlock("DEtails2", array("label"=>"Size Information", "class"=>"block-standard"));

		$this->form->addElement(new CTextInput("Sqft", array("label"=>"SQFT", "class"=>"")));
		$this->form->addElement(new CTextInput("AdditionalSqft", array("label"=>"SQFT <small>optional, for terrace, bsmt</small>", "class"=>"")));
		$this->form->addElement(new CTextInput("SqftRange", array("label"=>"SQFT Range <small>optional</small>", "class"=>"")));
		$this->form->addElement(new CTextInput("Bedrooms", array("label"=>"Bedrooms", "class"=>"")));
		$this->form->addElement(new CTextInput("BedroomsTxt", array("label"=>"Bedrooms <small>optional, full text</small>", "class"=>"")));
		$this->form->addElement(new CTextInput("Den", array("label"=>"Den <small>optional, full text</small>", "class"=>"")));
		$this->form->addElement(new CTextInput("Bathrooms", array("label"=>"Bathrooms", "class"=>"")));
		$this->form->addElement(new CTextInput("BathroomsTxt", array("label"=>"Bathrooms <small>optional, full text</small>", "class"=>"")));

		$this->form->addBlock("DEtails4", array("label"=>"FIles", "class"=>"block-standard"));
		$this->form->addElement(new CTextInput("Image", array("label"=>"Rendering", "class"=>"")));
		$this->form->addElement(new CInputFile("ImageNew", array("label"=>"Upload New Rendering", "class"=>"")));
		$this->form->addElement(new CTextInput("Floorplan", array("label"=>"Floorplan", "class"=>"")));
		$this->form->addElement(new CInputFile("FloorplanNew", array("label"=>"Upload New Floorplan", "class"=>"")));
		$this->form->addElement(new CTextInput("PDF", array("label"=>"PDF Download", "class"=>"")));
		$this->form->addElement(new CInputFile("PDFNew", array("label"=>"Upload New PDF", "class"=>"")));

		$this->form->addBlock("DEtails3", array("label"=>"Highrise specific", "class"=>"block-standard"));
		$this->form->addElement(new CTextInput("Floors", array("label"=>"Floors", "class"=>"")));
		$this->form->addElement(new CTextInput("View", array("label"=>"View", "class"=>"")));
		$this->form->addElement(new CTextInput("Maintenance", array("label"=>"View", "class"=>"")));
		$this->form->addElement(new CTextInput("Taxes", array("label"=>"View", "class"=>"")));
//		$this->form->addElement(new CTextInput("Levels", array("label"=>"Levels (plans)", "class"=>"")));
//		$this->form->addElement(new CTextInput("LevelLabels", array("label"=>"Level Labels", "class"=>"")));

		$this->form->addBlock("DEtails5", array("label"=>"Miscellaneous", "class"=>"block-standard"));
		$this->form->addElement(new CCheckbox("IsModelHome", array("yes", "no"), array("label"=>"", "value-label" => "Is Model Home", "class"=>"")));
		$this->form->addElement(new CCheckbox("IsInventory", array("yes", "no"), array("label"=>"", "value-label" => "Is Inventory", "class"=>"")));
		$this->form->addElement(new CTextArea("Notes", array("label"=>"Notes", "class"=>"", "rows" => 2)));


		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();

		$comm = $this->mDatabase->getRow("select a.* from communities a, fp_collections b, fp_models c where a.id = b.communityid and b.id = c.CollectionID and c.ID = " . intval($this->mRowObj->ModelID));

		if ($_FILES["ImageNew"]["tmp_name"]) {
			$this->uploadImage("ImageNew", "comm/" .$comm["Guid"] . "/renderings", "", 1920, 0, "fitwidth", "Image"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false
			$path = "/media/comm/" .$comm["Guid"] . "/renderings/thumbnails"; @mkdir(".." . $path);
			$info = pathinfo($this->mRowObj->Image);
			$path .= "/" . $info["basename"];
			copy(".." . $this->mRowObj->Image, ".." . $path);
			$fm = new CFileManager();
			$fm->thumbnail(".." . $path, 384, 217);
			$this->mRowObj->Thumbnail = $path;
		}
		if ($_FILES["FloorplanNew"]["tmp_name"]) $this->uploadDocument("FloorplanNew", "comm/" . $comm["Guid"] . "/floorplans", "", "Floorplan"); // FieldName, FolderName, FileName, TableColumn, Save = false
		if ($_FILES["PDFNew"]["tmp_name"]) $this->uploadDocument("FloorplanNew", "comm/" . $comm["Guid"] . "/floorplans", "", "PDF"); // FieldName, FolderName, FileName, TableColumn, Save = false

		$this->_save();

	}

  }

?>