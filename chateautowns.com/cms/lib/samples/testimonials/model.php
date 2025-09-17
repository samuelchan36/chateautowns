<?php
/** CTestimonial
* @package pages
* @author cgrecu
*/


class CTestimonial extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));
		$this->form->addElement(new CTextInput("Name", array("label"=>"Title", "class"=>"mandatory")));
		$this->form->addElement(new CTextArea("Message", array("label"=>"Message", "class"=>"mandatory", "rows" => 10)));


		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();

        $this->setCommonFields();

		$this->_save();

	}



  }

?>