<?php
/** CModelType
* @package pages
* @author cgrecu
*/


class CModelType extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));

		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory url-source", "data-target" => "Guid", "data-target-option" => "noslash")));
		$this->form->addElement(new CTextInput("Guid", array("label"=>"Link", "class"=>"")));

//		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->checkUniqueUrl();

        $this->setCommonFields();

		$this->_save();

	}

  }

?>