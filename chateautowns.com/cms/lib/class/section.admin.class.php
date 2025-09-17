<?php

class CSectionAdmin {

	var $mOperation = "main";
	var $mClass = "";
	var $mAccess = "";
	var $mSectionName = "";
	var $table = "";
	var $folder = "";
	var $url = "";
	var $mDatabase;

	var $fm;

  /** comment here */
  function __construct() {
		$this->mDatabase = &$GLOBALS["db"];
		if (isset($_GET["o"])) $this->mOperation = $_GET["o"];
		$this->fm = new CFileManager();
  }

    /** comment here */
  function getClass() {
	Return $this->mClass;
  }

  /** comment here */
  function enforce($right = "all") {
		$access = new SAccess();
		Return $access->enforce($right);
  }

  /** comment here */
  function check($right = "all") {
		$access = new SAccess();
		Return $access->check($right);
  }

  /** comment here */
  function checkOther($module, $right = "all") {
		$access = new SAccess();
		Return $access->checkOther($module, $right);
  }

  /** comment here */
  function isAdmin() {
		$access = new SAccess();
		Return $access->isAdmin();
  }

  /** comment here */
  function title($title) {
		$obj = new STitle();
		if (!$title) $title = "Manage ". $this->mLabels[0];
		$obj->set($title);
  }

  /** comment here */
  function displayEdit($pItemID = 0, $versionid = 0) {

	$this->title("Edit ". $this->mLabels[1]);
	if ($pItemID) $this->enforce("edit"); else $this->enforce("create"); 
	
	$class = $this->getClass();

	$vItem = new $class($pItemID, $this->table);

	$vItem->folder = $this->url;
	$vItem->view = $this->folder."/view.html";

	$vItem->unregisterForm($versionid);
	$vItem->initForm();
	Return $vItem->displayEdit();
  }

  /** comment here */
  function displaySave($pItemID) {
	$class = $this->getClass();
	$vItem = new $class($pItemID, $this->table);
	$vItem->mSectionName = $this->mSectionName;
	$vItem->table = $this->table;
//	$vItem->registerForm();
	$vItem->save();
	$this->redirect(getLink() . "");
  }

  /** comment here */
  function toggle($pID) {
	$class = $this->getClass();
	$vItem = new $class($pID, $this->table);
	$vItem->mSectionName = $this->mSectionName;
	$vItem->table = $this->table;
	$vItem->toggle();
	$this->redirect(getLink() . "");
  }

  /** comment here */
  function publish($id) {
	  $this->enforce("publish"); 
	$class = $this->getClass();
	$vItem = new $class($id, $this->table);
	$vItem->mSectionName = $this->mSectionName;
	$vItem->table = $this->table;
	$vItem->publish();
	$this->redirect(getLink() . "");	
  }


  /** comment here */
  function move($pID, $pDirection) {
	  $this->enforce("sort"); 
	$class = $this->getClass();
	$vItem = new $class($pID, $this->table);
	$vItem->mSectionName = $this->mSectionName;
	$vItem->table = $this->table;
	$vItem->move($pDirection);
	$this->redirect(getLink() . "");
  }

  /** comment here */
  function displayItem($pItemID) {
	  $this->enforce("view"); 
	$class = $this->getClass();
	$vItem = new $class($pItemID, $this->table);
	$vItem->mSectionName = $this->mSectionName;
	$vItem->table = $this->table;
	Return $vItem->display();
  }

  /** comment here */
  function displayPreview($pItemID) {
	  $this->enforce("view"); 
	$class = $this->getClass();
	$vItem = new $class($pItemID, $this->table);
	$vItem->mSectionName = $this->mSectionName;
	$vItem->table = $this->table;
	$link = $vItem->mRowObj->Link . "?preview=on";
	redirect($link);
//	Return $vItem->displayPreview();
  }

  /** comment here */
  function delete($pID) {
	  $this->enforce("delete"); 
	$class = $this->getClass();
	$vItem = new $class($pID, $this->table);
	$vItem->mSectionName = $this->mSectionName;
	$vItem->table = $this->table;
	$vItem->delete();
	$this->redirect(getLink() . "main");
  }

  /** comment here */
  function redirect($link) {
	 redirect($link);
  }

  /** comment here */
  function cloneItem($pID) {
	  $this->enforce("create"); 
	$class = $this->getClass();
	$vItem = new $class($pID, $this->table);
	$vItem2 = new $class(0, $this->table);

	foreach ($vItem->mRowObj as $key=>$val) {
		if ($key == "ID" || $key == "Published" || $key == "Status" || $key == "TimeStamp" || $key == "UserID") continue;
		if ($val) $vItem2->mRowObj->$key = $val;
	}
	
	$vItem2->folder = $this->url;
	$vItem2->view = $this->folder."/view.html";

	$vItem2->unregisterForm(0);
	$vItem2->initForm();
	
	Return $vItem2->displayEdit();	
  }

  /** comment here */
  function prepareClone($pID) {
	  $this->enforce("create"); 
	$class = $this->getClass();
	$vItem = new $class($pID, $this->table);
	$vItem2 = new $class(0, $this->table);

	foreach ($vItem->mRowObj as $key=>$val) {
		if ($key == "ID" || $key == "Published" || $key == "Status" || $key == "TimeStamp" || $key == "UserID") continue;
		if ($val) $vItem2->mRowObj->$key = $val;
	}
	
	$vItem2->folder = $this->url;
	$vItem2->view = $this->folder."/view.html";
	Return $vItem2;	
	
  }

  /** comment here */
  function displayOrder() {
	  $this->enforce("sort"); 
	$class = $this->getClass();
	$vItem = new $class(0, $this->table);
	 $sql = "select " . $vItem->pk .", " . $vItem->label . ", OrderID from " . $this->table . " order by OrderID ASC";
	 $data = $this->mDatabase->getAll($sql);
	 $form = new CForm("frmEdit", array("action" => "/cms/" . $this->url . "/do-order", "class" => "re-order"));
	 $form->addBlock("Reorder Fields");
//	 die2($data);
	 foreach ($data as $key=>$val) {
		 $input = new CTextInput("OrderID_" . $val[$vItem->pk], array("label"=>$val[$vItem->label], "class"=>"mandatory w50", "name" => "OrderID[]"));
		 $input->value = $val["OrderID"];
		 $form->addElement($input);
		 $input = new CHidden("ID_" . $val[$vItem->pk], array("name" => "ID[]", "value" => $val[$vItem->pk]));
		 $form->addElement($input);
	 }

	 Return $form->display();
  }

  /** comment here */
  function doOrder() {
	  $this->enforce("sort"); 
	$class = $this->getClass();
	$vItem = new $class(0, $this->table);
	foreach ($_POST[$vItem->pk] as $key=>$val) {
		$this->mDatabase->query("update " . $this->table . " set orderid = " . intval($_POST["OrderID"][$key]). " where " . $vItem->pk . " = " . intval($val));
	}
	redirect(getLink());
  }

  /** comment here */
  function toggleField($id, $fld, $val) {
	$this->mDatabase->query("update " .$this->table." set ".addslashes2($fld)." = '".addslashes2($val)."' where id = " . intval($id));
	die("updated");
  }

  /** comment here */
  function updateField($id, $fld, $value) {
	$this->mDatabase->query("update  " .$this->table."  set ".addslashes2($fld)." = '".addslashes2($value)."' where id = " . intval($id));
	die("updated");
  }

  /** comment here */
  function mainSwitch() {
	if (isset($_GET["id"])) $id = $_GET["id"]; else $id = "";
	if (isset($_GET["type"])) $type = $_GET["type"]; else $type = "";
	if (isset($_GET["versionid"])) $versionid = $_GET["versionid"]; else $versionid = "";
	switch($this->mOperation) {
			case "main":
			case "": Return $this->display();
			case "show":
			case "view": Return $this->displayItem($id);
			case "preview": Return $this->displayPreview($id);
			case "edit":
			case "create": Return $this->displayEdit($id);
			case "save": Return $this->displaySave($id);
			case "toggle": Return $this->toggle($id);
			case "move": Return $this->move($id, $type);
			case "delete": Return $this->delete($id);
			case "clone": Return $this->cloneItem($id);
			case "publish": Return $this->publish($id);
			case "load-version": Return $this->displayEdit($id, $versionid);
			case "order": Return $this->displayOrder();
			case "do-order": Return $this->doOrder();
			case "export": Return $this->export();
			case "toggle-field": Return $this->toggleField($_GET["id"],$_GET["fld"], $_GET["value"]);
			case "update-field": Return $this->updateField($_GET["id"],$_GET["fld"], $_GET["value"]);

			default:
				die("operation not supported");
	}
  }


}

?>