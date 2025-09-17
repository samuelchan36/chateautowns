<?php
/** CGallery
* @package pages
* @author cgrecu
*/


class CGallery extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("DEtails", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));

//		$this->form->addElement(new CSelect("CommunityID", $this->mDatabase->getAll2("select ID, Name from communities order by name asc"), array("label" => "Community", "class"=>"mandatory w300", "placeholder"=>"Please select"))); 
		$this->form->addElement(new CSelect("Type", array(), array("label" => "Type", "class"=>"mandatory w300", "placeholder"=>"Please select"), array("options-source" => array("field", "galleries", "Type")))); 
		$this->form->addElement(new CTextInput("Name", array("label"=>"Title", "class"=>"mandatory")));
//		$this->form->addElement(new CTextInput("Subtitle", array("label"=>"Subtitle", "class"=>"")));
//		$this->form->addElement(new CTextInput("Path", array("label"=>"Iframe Source<small>video/vr</small>", "class"=>"")));

		$this->form->addBlock("Images", array("label"=>"Photos", "class"=>"block-standard"));
		$data = $this->mDatabase->getAll("select * from gallery_images where galleryid = " . intval($this->mRowObj->ID) ." order by orderid asc");
		$tpl = template2("class/modules/galleries/images.html");
		$tpl->assign("GalleryID", intval($this->mRowObj->ID));
		$_SESSION["tmpfolder"] = uniqid();
		$_SESSION["Slides"] = array();
		foreach ($data as $key=>$val) {
			$tpl->newBlock("IMAGE");
			$_SESSION["Slides"][] = array("id" => $val["ID"], "thumbnail" => $val["Thumbnail"], "image" => $val["Path"], "status" => "existing", "orderid" => $key);
			$tpl->assign("ID", $key);
			$tpl->assign("SlideID", $val["ID"]);
			$tpl->assign("Image", $val["Path"]);
			$tpl->assign("Thumbnail", $val["Thumbnail"]);
			$tpl->assign("OrderID", $val["OrderID"]);
		}
		$this->form->addElement(new CText("Slides", $tpl->output(), array("label"=>"")));
		


		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();
		$this->_save();

		@mkdir("../media/galleries/");
		@mkdir("../media/galleries/" . $this->mRowObj->ID);
		@mkdir("../media/galleries/" . $this->mRowObj->ID . "/thumbnails");
		foreach ($_SESSION["Slides"] as $key=>$val) {
			if ($val["status"] == "existing") $this->mDatabase->query("update gallery_images set orderid = " . intval($val["orderid"]) . " where id = " . $val["id"]);
			if ($val["status"] == "deleted") $this->mDatabase->query("delete from gallery_images where id = " . $val["id"]);
			if ($val["status"] == "new") {
				$fields = array();
				$fields["GalleryID"] = $this->mRowObj->ID;
				$fields["Path"] = str_replace("pending/" . $_SESSION["tmpfolder"], "galleries/" . $this->mRowObj->ID, $val["image"]);
				$fields["Thumbnail"] = str_replace("pending/" . $_SESSION["tmpfolder"], "galleries/" . $this->mRowObj->ID, $val["thumbnail"]);
				rename(".." . $val["image"], ".." . $fields["Path"]);
				rename(".." . $val["thumbnail"], ".." . $fields["Thumbnail"]);
				$fields["OrderID"] = $val["orderid"];
				$x = getimagesize(".." . $fields["Thumbnail"]);
				$fields["Width"] = intval($x[0]);
				$fields["Height"] = intval($x[1]);

				$this->mDatabase->query("insert into gallery_images" . $this->mDatabase->makeInsertQuery($fields));
			}
		}

	}

  }

?>