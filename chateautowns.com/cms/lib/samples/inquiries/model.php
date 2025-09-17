<?php
/** CInquiry
* @package pages
* @author cgrecu
*/


class CInquiry extends CDBContent {


	function __construct($id = 0, $table = "inquiries") {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}

/** comment here */
function display() {
		$obj = new STitle();
		$obj->set("Inquiry");

		$tpl = template2("lib/html/views/default2.html")	;	
		$tpl->newBlock("ROW"); $tpl->assign("Label", "Inquiry Date"); $tpl->assign("Value", date("F d, Y H:i", $this->mRowObj->TimeStamp));
		$tpl->newBlock("ROW"); $tpl->assign("Label", "Name"); $tpl->assign("Value", $this->mRowObj->FirstName . " " . $this->mRowObj->LastName);
		$tpl->newBlock("ROW"); $tpl->assign("Label", "Email"); $tpl->assign("Value", $this->mRowObj->Email);
		$tpl->newBlock("ROW"); $tpl->assign("Label", "Message"); $tpl->assign("Value", nl2br($this->mRowObj->Comments));
		


		Return "<br>" . $tpl->output();

	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));


		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();

		if ($_FILES["Link"]) $this->uploadDocument("Link", $province["Code"] . "/" . $this->table, "", "Link"); // FieldName, FolderName, FileName, TableColumn, Save = false
		if ($_FILES["Image"]) $this->uploadImage("Image", "companies", "", 200,200, "fitwidth", "Image"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false

		$this->_save();

	}

  }

?>