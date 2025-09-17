<?php
/** CUser
* @package pages
* @author cgrecu
*/


class CUser extends CSimpleContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {

		if(isset($_SESSION["SavedPostData"]) && $_SESSION["SavedPostData"]) {
			$_POST = $_SESSION["SavedPostData"];
			$_SESSION["SavedPostData"] = array();
		}
		$this->form->addBlock("Details", array("label"=>"Edit Content", "class"=>"block-standard"));
		$input = new CSelect("GroupID", array(), array("label" => "Type", "class"=>"mandatory w200", "placeholder"=>"Please select")); $input->getOptionsFromDb("cms_user_groups", "Name", "ID"); $this->form->addElement($input);
		$this->form->addElement(new CTextInput("Email", array("label"=>"Email (Username)", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory")));
		$_POST["Password"] = "";
		$this->form->addElement(new CTextInput("Password", array("label"=>"Password", "class"=>"")));
	
		
		Return $this->html();

	}

	/** comment here */
	function save() {
        $this->registerForm();
		$this->setCommonFields();

		if (!$this->checkUniqueField("Email") || !$this->checkUniqueField("Username")) {
			error("User already exists");
			$_SESSION["SavedPostData"] = $this->post_data;
			redirect("/cms/users/edit");
		}
		if ($_POST["Password"]) {
			$this->mRowObj->PasswordHash = password_hash($_POST["Password"], PASSWORD_DEFAULT );
		}
		$this->mRowObj->Password = "";
		
		$this->mRowObj->Username = $this->mRowObj->Email;

		$this->_save();

	}


	  /** comment here */
	  function login($remember = true) {
	  	$GLOBALS["doc"]->mUserID = $this->mRowObj->ID;
	  	$GLOBALS["doc"]->mUser = $this;
		$_SESSION["gUserID"] = $this->mUserID;

		$this->mRights = array();
		$tmp = $this->mDatabase->getAll("select * from cms_group_rights where groupid = " . intval($this->mRowObj->ID));
		$rights = array();
		foreach ($tmp as $key=>$val) {
			$this->mRights[$val["Section"]][$val["Operation"]] = true;
		}


		if ($remember) setcookie("UserID", $this->mRowObj->ID, time() + 86400 * 91);

	  }

	  /** comment here */
	  function logout() {
	  	$GLOBALS["doc"]->mUserID = "";
	  	$GLOBALS["doc"]->mUser = "";
		$_SESSION["gUserID"] = 0;
		setcookie("UserID", 0, time() - 1000);
	  }

  }

?>