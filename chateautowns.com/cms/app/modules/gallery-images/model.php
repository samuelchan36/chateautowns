<?php
/** CTestimonial
* @package pages
* @author cgrecu
*/


class CGalleryImage extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));
		$this->form->addElement(new CSelect("GalleryID", $this->mDatabase->getAll2("select ID, Name from galleries"), array("label"=>"Gallery", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("Tag", array("label"=>"Tag", "class"=>"")));
		$this->form->addElement(new CInputFile("Path", array("label"=>"Image", "class"=>"")));


		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();

        $this->setCommonFields();
		@mkdir("../media/galleries/" . $this->mRowObj->GalleryID . "");
		@mkdir("../media/galleries/" . $this->mRowObj->GalleryID . "/thumbnails");


		$this->uploadImage("Path", "galleries/" . $this->mRowObj->GalleryID, "", 1920, 0, "fitwidth", "Path"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false
		if ($_FILES["Path"]["tmp_name"]) {
				$this->mRowObj->Thumbnail = "/media/galleries/" . $this->mRowObj->GalleryID. "/thumbnails/" . $_FILES['file']['name'];
				copy(".." . $this->mRowObj->Image, ".." . $this->mRowObj->Thumbnail);
				$this->fm->fitWidth(".." . $this->mRowObj->Thumbnail, 960, false);

		}

		$size = GetImageSize(".." . $this->mRowObj->Path);
		if ($size) {
			$this->mRowObj->Width = $size[0];
			$this->mRowObj->Height = $size[1];
		}
		$this->_save();

	}

	/** comment here */
	function beforeDelete() {
		@unlink(".." . $this->mRowObj->Path);
		@unlink(".." . $this->mRowObj->Thumbnail);
		Return true;
	}

  }

?>