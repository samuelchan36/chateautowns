<?php
/** CAsset
* @package pages
* @author cgrecu
*/


class CAsset extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("Details", array("label"=>"Edit Content", "class"=>"block-standard"));
		$input = new CSelect("SlideshowID", array(), array("label"=>"Slideshow Type", "class"=>"mandatory")); $input->getOptionsFromField("promos", "Type"); $this->form->addElement($input);
		$this->form->addElement(new CTextInput("Name", array("label"=>"Slideshow Name", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("Width", array("label"=>"Width", "class"=>"w100")));
		$this->form->addElement(new CTextInput("Height", array("label"=>"Height", "class"=>"w100")));
		$this->form->addElement(new CTextArea("EmbedCode", array("label"=>"Embed Code", "class"=>"w500")));
		Return $this->html();

	}

	/** comment here */
	function save() {
//				die2($_POST);

        $this->registerForm();
        $this->setCommonFields();
		$this->mRowObj->Width = intval($this->mRowObj->Width);
		$this->mRowObj->Height = intval($this->mRowObj->Height);
		$this->_save();
		if (!$this->mRowObj->EmbedCode) {
			$this->mRowObj->EmbedCode = "###Promos_" .$this->mRowObj->ID . "###";
			$this->easySave();
		}

	}

  }

?>