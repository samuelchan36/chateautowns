<?php
/** CSample
* @package pages
* @author cgrecu
*/


class CSample extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));
		
		$input = new CTextInput("Name", //field ID
												array("label"=>"Name", "class"=> "mandatory w600", "config-max-length" => 4),  // array of Attributes
												array("help", "This is the field name") // Optional - tooltip help
												);
		$this->form->addElement($input);
		
		$this->form->addElement(new CInputEmail("Email", array("label"=>"Email", "class"=> "mandatory w600", "config-max-length" => 255)));
		$this->form->addElement(new CInputFloat("FloatValue", array("label"=>"Float Value", "class"=> "mandatory w150")));
		$this->form->addElement(new CInputInt("IntValue", array("label"=>"Int Value", "class"=> "mandatory w150")));
		$this->form->addElement(new CInputDate("DateValue", array("label"=>"Date Value", "class"=> "mandatory w300")));
		$this->form->addElement(new CInputTime("TimeValue", array("label"=>"Time Value", "class"=> "mandatory w200")));
		$this->form->addElement(new CInputRadioGroup("RadioValue", array(array("value1", "Value 1"), array("value2", "Value 2"), array("value3", "Value 3")), array("label"=>"Radio Group", "class"=> "mandatory")));
		$this->form->addElement(new CCheckbox("CheckboxValue", array("yes","no"), array("label"=>"CheckboxValue", "value-label" => "Check for yes")));


		$this->form->addElement(new CInputFile("Path", array("label"=>"Upload Document", "class"=> "w300"), array("help", "Select a document")));
		$this->form->addElement(new CInputFile("ImagePath", array("label"=>"Upload Image", "class"=> "w300"), array("help", "Select an image")));

		$this->form->addElement(new CSelect("GroupID", $this->mDatabase->getAll2("select a.ID, a.Name from cms_user_groups a"), array("label"=>"Controlling List", "placeholder" => "Select Parent", "class"=>"js-drop-controller", "data-target" => "AccountID", "data-source" => "cms_user_groups", "data-controller" => "system")));
		$this->form->addElement(new CSelect("AccountID", array(), array("label"=>"Controlled List", "placeholder" => "Select Parent", "class"=>"")));

		$input = new CSelect("SelectValue", //field ID
												array(), // list of options
												array("label"=>"Select Value", "class"=> "mandatory w300", "placeholder" => "Please select"),  // array of Attributes
												array("help", "Select with few options stored as enum field") // Optional - tooltip help
												);
		$input->getOptionsFromField($this->table, "SelectValue"); // Table, FieldName
		$this->form->addElement($input);

		$input = new CSelect("SelectValue2", //field ID
												array(), // list of options
												array("label"=>"Select Value 2", "class"=> "w300", "placeholder" => "Please select"),  // array of Attributes
												array("help", "Select many options coming from different table") // Optional - tooltip help
												);
		$input->getOptionsFromDb("cms_users", "Username", "ID"); // Table, LabelColumn, IDColumn
		$input->mExtraOption = array("0", "No option selected");//optional: no value selected
		$this->form->addElement($input);

		$input = new CSelect("SelectValue3", //field ID
												array(), // list of options
												array("label"=>"Select Value 3", "class"=> "w300", "placeholder" => "Please select", "multiple" => "multiple"),  // array of Attributes
												array("help", "Select many options coming from a different table, and allows for multiple options to be selected") // Optional - tooltip help
												);
		$input->getOptionsFromDb("cms_users", "Username", "ID"); // Table, LabelColumn, IDColumn
		$this->form->addElement($input);

		$input = new CTextArea("TextValue", //field ID
												array("label"=>"Text Value", "class"=> "mandatory w600", "config-max-length" => 512, "rows" => 8, "cols" => 80),  // array of Attributes
												array("help", "This is the field name") // Optional - tooltip help
												);
		$this->form->addElement($input);

		$input = new CRichTextArea("RichTextValue", //field ID
												array("label"=>"Text Value", "class"=> "mandatory w600", "rows" => 16, "cols" => 80),  // array of Attributes
												array("help", "This is the field name") // Optional - tooltip help
												);
		$this->form->addElement($input);

		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();

        $this->checkUniqueUrl();

		if (!$this->checkUniqueField("Email")) {
			$_SESSION["error"] = "Email address already in use";
			Return false;
		}

		if ($_FILES["Path"]) $this->uploadDocument("Path", $this->table, "", "Path"); // FieldName, FolderName, FileName, TableColumn, Save = false
		if ($_FILES["ImagePath"]) $this->uploadImage("Image", $this->table, "", 200,200, "fitwidth", "ImagePath"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false
		

		$this->_save();

	}

  }

?>