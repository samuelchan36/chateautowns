<?php
/** CCommunity
* @package pages
* @author cgrecu
*/


class CCommunity extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));

		$input = new CSelect("CityID", $this->mDatabase->getAll2("select ID, Name from cities order by name asc"), array("label" => "City", "class"=>"mandatory w300", "placeholder"=>"Please select")); $this->form->addElement($input);
		$input = new CSelect("TypeID", array(), array("label" => "TypeID", "class"=>"mandatory w300", "placeholder"=>"Please select"), array("options-source" => array("field", "communities", "TypeID"))); $this->form->addElement($input);
		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory url-source", "data-target" => "Guid", "data-target-option" => "noslash")));
		$this->form->addElement(new CTextInput("Guid", array("label"=>"Link", "class"=>"")));
//		$this->form->addElement(new CTextInput("CID", array("label"=>"CRM List ID", "class"=>"mandatory")));

		$this->form->addBlock("StatusB", array("label"=>"Status", "class"=>"block-standard"));
		$input = new CSelect("Status", array(), array("label" => "Status", "class"=>"mandatory w300", "placeholder"=>"Please select"), array("options-source" => array("field", "communities", "Status"))); $this->form->addElement($input);
		$this->form->addElement(new CCheckbox("IsActive", array("yes", "no"), array("label"=>"", "value-label" => "Is Active", "class"=>"")));
		$this->form->addElement(new CCheckbox("IsUpcoming", array("yes", "no"), array("label"=>"", "value-label" => "Is Coming Soon", "class"=>"")));
//		$this->form->addElement(new CCheckbox("IsFuture", array("yes", "no"), array("label"=>"", "value-label" => "Is Future Community", "class"=>"")));
//		$this->form->addElement(new CCheckbox("IsInventory", array("yes", "no"), array("label"=>"", "value-label" => "Is Move In Now", "class"=>"")));
//		$this->form->addElement(new CCheckbox("IsConstruction", array("yes", "no"), array("label"=>"", "value-label" => "Is Under Construction", "class"=>"")));
//		$this->form->addElement(new CCheckbox("IsSoldOut", array("yes", "no"), array("label"=>"", "value-label" => "Sold Out", "class"=>"")));
		$this->form->addElement(new CCheckbox("IsPast", array("yes", "no"), array("label"=>"", "value-label" => "Past Community", "class"=>"")));
//		$this->form->addElement(new CCheckbox("HasLandingPage", array("yes", "no"), array("label"=>"", "value-label" => "Has Landing Page", "class"=>"")));
//		$this->form->addElement(new CCheckbox("HasGallery", array("yes", "no"), array("label"=>"", "value-label" => "Has Gallery<small>past communities page</small>", "class"=>"")));
//		$this->form->addElement(new CCheckbox("IsRegistration", array("yes", "no"), array("label"=>"", "value-label" => "Has Registration Page", "class"=>"")));

		$this->form->addBlock("SalesOffice", array("label"=>"Sales Office", "class"=>"block-standard"));
		$input = new CSelect("SalesOfficeID", $this->mDatabase->getAll2("select ID, Name from sales_offices order by name asc"), array("label" => "Sales Office", "name" => "SalesOfficeID[]", "multiple" => "true", "class"=>"w300", "placeholder"=>"Please select", "placeholder-value" => 0, "value"=>$this->mDatabase->getAll2("select SalesOfficeID from community_sales_offices where CommunityID = " . intval($this->mRowObj->ID)))); $this->form->addElement($input);

		$this->form->addBlock("MoreInfo", array("label"=>"Additional Information", "class"=>"block-standard"));

		$this->form->addElement(new CTextArea("SalesLine", array("label"=>"Sales Line", "class"=>"", "rows" => 2)));
//		$this->form->addElement(new CTextArea("Summary", array("label"=>"Summary", "class"=>"", "rows" => 2)));
//		$this->form->addElement(new CTextArea("Address", array("label"=>"Address", "class"=>"", "rows" => 2)));
//		$this->form->addElement(new CTextInput("Coordinates", array("label"=>"GPS Coordinates<small>Lat,Long</small>", "class"=>"")));
		if ($this->mRowObj->ID) $this->form->addElement(new CText("DownloadsCode", "###CommunityResources_" . $this->mRowObj->ID. "###", array("label"=>"Downloads Embed Code", "class"=>"")));

		$this->form->addBlock("Images", array("label"=>"Logos and images", "class"=>"block-standard"));
		$this->form->addElement(new CInputFile("Logo", array("label"=>"Logo<small>svg recommended</small>", "class"=>"")));
		$this->form->addElement(new CInputFile("Image", array("label"=>"Banner Image <small>1920 x 1080px</small>", "class"=>"")));

//		$this->form->addBlock("PastInfo", array("label"=>"Past Community Information", "class"=>"block-standard"));
//		$this->form->addElement(new CTextInput("CompletionYear", array("label"=>"Year Completed", "class"=>"")));
//		$this->form->addElement(new CTextInput("Total Units", array("label"=>"Total Units", "class"=>"")));
//
//


//		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();

		$this->uploadDocument("Logo", "communities", "", "Logo"); // FieldName, FolderName, FileName, TableColumn, Save = false
		$this->uploadImage("Image", "communities", "", 1920, 1080, "thumbnail", "Image"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false

		$this->_save();

		$this->mDatabase->query("delete from community_sales_offices where communityid = " . $this->mRowObj->ID);
		foreach ($_POST["SalesOfficeID"] as $key=>$val) {
			$this->mDatabase->query("insert into community_sales_offices (SalesOfficeID, CommunityID) values(".intval($val).",".$this->mRowObj->ID.")");
		}

	}

  }

?>