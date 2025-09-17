<?php
/** CLogo
* @package pages
* @author cgrecu
*/


class CActivation extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}

/** comment here */
function displayEdit() {

		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));

		$this->form->addElement(new CText("Content", "[" . $this->mRowObj->ContentTable . "] " . $this->mRowObj->ContentID, array("label"=>"Content")));

		$this->form->addElement(new CText("PublishDateLabel", "Select new date and time"));
		$this->form->addElement(new CInputDate("PublishDate", array("label" => "Published Date")));
		$this->form->addElement(new CInputTime("PublishTime", array("label"=>"Published Time", "class" => "w150")));


//		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
		$this->mRowObj->PublishDateTime = intval(makeDateTime($this->post_data["PublishDate"], $this->post_data["PublishTime"]));
		$this->mRowObj->PublishYMD = date("Ymd", makeDateTime($this->post_data["PublishDate"], $this->post_data["PublishTime"]));
		$this->mRowObj->TimeStamp = time();
		$this->justSave();

	}

  


  }

?>