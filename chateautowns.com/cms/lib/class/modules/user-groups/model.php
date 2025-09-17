<?php
/** CUserGroup
* @package pages
* @author cgrecu
*/


class CUserGroup extends CSimpleContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("Details", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory")));
		$this->form->addElement(new CCheckbox("AdminGroup", array("yes", "no"), array("label"=>"Admin Group", "value-label" => "Is Admin Group")));
		
		$this->form->addBlock("rights", array("label"=>"Access Rights", "class"=>"block-standard"));
		
		if ($this->mRowObj->AdminGroup != "yes") {
			$tmp = $this->mDatabase->getAll("select * from cms_group_rights where groupid = " . intval($this->mRowObj->ID));
			$rights = array();
			foreach ($tmp as $key=>$val) {
				$rights[$val["Section"]][$val["Operation"]] = true;
			}
	//die2($rights);
	//		die2($GLOBALS["access_rules"]);
			foreach ($GLOBALS["access_rules"] as $key=>$val) {
				$input = new CCheckbox("Right_" . $key . "_0", array($key, "no"), array("label"=>$val[0], "value" => (isset($rights[$key]) && $rights[$key] && $rights[$key]["all"]) ? $key : "no",  "value-label" => "Module Access", "class"=>"user-group-head", "name"=>"Rights[]"));
				$this->form->addElement($input);
				foreach ($val[1] as $key2=>$val2) {
					$input = new CCheckbox("Right_" . $key . "_" . ($key2+1), array($val2, "no"), array("label"=>"&nbsp;", "value" => ($rights[$key] && $rights[$key][$val2]) ? $val2 : "no", "value-label" => $val2, "class"=>"user-group-item", "name"=>"Rights2[".$key."][]"));
					$this->form->addElement($input);
				}
			}
		}
		Return $this->html();

	}

	/** comment here */
	function save() {

		$this->registerForm();
		if (!$this->mRowObj->DeleteFlag) $this->mRowObj->DeleteFlag  = "yes";
		$this->setCommonFields();

		$this->_save();
		if ($this->mRowObj->AdminGroup != "yes") {
			$this->mDatabase->query("delete from cms_group_rights where groupid = " . intval($this->mRowObj->ID));
			foreach ($_POST["Rights"] as $key=>$val) {
				if ($val != "no") { 
					$this->mDatabase->query("insert into cms_group_rights(GroupID, Section, Operation) values(".intval($this->mRowObj->ID).",'".addslashes2($val)."','all')");
					foreach ($_POST["Rights2"][$val] as $key2=>$val2) {
						if ($val2 != "no") {
							$this->mDatabase->query("insert into cms_group_rights(GroupID, Section, Operation) values(".intval($this->mRowObj->ID).",'".addslashes2($val)."','".addslashes2($val2)."')");
						}
					}
				}
			}
		}
	}

	/** comment here */
	function delete() {
		if ($this->mRowObj->DeleteFlag == "yes") $this->mDatabase->query("delete from " . $this->table . " where id = " . intval($this->mRowObj->ID));
		else {
			error("Cannot delete group");
		}
	}



  }

?>