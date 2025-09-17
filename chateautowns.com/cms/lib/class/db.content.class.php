<?php 

  class CDBContent  {

	var $table;
	var $pk = "ID";
	var $mRowObj;
	var $mDraft;
	var $mInitial;

	var $mDatabase;
	var $mUserID;
	var $mSectionName;

	var $label = "Name";
	var $mToggleStates = array("enabled", "disabled");

	var $folder;
	var $view;
	var $form;
	var $template;

	var $timers = array();
	var $archive = array();

//	var $mRules = array();
	var $has_publishing = false;
	var $has_timers = false;

	var $is_new = false;
	var $is_published  = true;
	var $is_active  = true;

	var $mSaveMode = "direct";

	var $publishing_status = "n/a";
	var $activation_status = "new";
	var $status = "new";
	var $status_code = "new";

	var $order_new_objects = "first";

	var $supports_draft = false;
	var $supports_timers = false;
	var $supports_publishing = false;
	
	var $post_data;

	/** comment here */
	function __construct($pID = "") {
		$this->mDatabase = $GLOBALS["db"];

		$this->mUserID = $_SESSION["gUserID"];
		$this->post_data = $_POST;
//die2($_POST);
		
		if (!$pID || is_numeric($pID)) $this->init($pID);

		if (!property_exists($this->mRowObj, "Published")) {
			$this->supports_publishing = false;
			$this->supports_draft = false;
		} else {
			$this->supports_publishing = true;
			$this->supports_draft = true;
		}

		if ($pID) {

			$this->initDraft();
			$this->initTimer();
			$this->initArchive();
		}


		if ($this->mDraft) $this->is_published = false; else $this->is_published = true; 
		$this->is_active = true; if (isset($this->mRowObj->Status) && $this->mToggleStates && $this->mRowObj->Status == $this->mToggleStates[1])  $this->is_active = false;



		if ($this->supports_publishing) {
			if (!$this->is_new) {
				$this->status_code = "published";
				if ($this->is_published) $this->publishing_status = "published"; else {
					$this->status_code = "pending";
					if (isset($this->timers["publish"]) && $this->timers["publish"]) $this->publishing_status = "pending publishing"; else $this->publishing_status = "draft"; 
				}

				if ($this->is_active) $this->activation_status = "active"; else {
					if (!isset($this->post_data["ActivationType"])) $this->post_data["ActivationType"] = "donot";
					$this->status_code = "inactive";
					if (isset($this->timers["activation"]) && $this->timers["activation"]) $this->activation_status = "pending activation"; else $this->activation_status = "inactive"; 				
				}

				$this->status = $this->activation_status . ", " . $this->publishing_status;
			}
		} else {
			$this->post_data["ActivationType"] = "instant";
		}
		

//die2($this->post_data);
		if (!isset($this->post_data["PublishingType"])) $this->post_data["PublishingType"] = "instant";
		if (isset($this->post_data["PublishingType"]) && $this->post_data["PublishingType"] && $this->post_data["PublishingType"] != "instant") $this->mSaveMode = "draft";

//		switch($_POST["PublishingType"]) {
//			case "delayed":
//				$this->mTimer = array("publish");
//				$this->mSaveMode = "draft";
//				break;
//			case "draft":
//				$this->mSaveMode = "draft";
//				break;
//		}
//
//		switch($_POST["ActivationType"]) {
//			case "delayed":
//				$this->mTimer = array("activate");
//				break;
//		}

	}

	/** call this for every content class to init mrowobj */
	function init($pContentID) {
		if (!$pContentID) $pContentID = intval($pContentID);
		if ($pContentID) {
			$vSql = "SELECT * FROM ".$this->table." WHERE ".$this->pk."='$pContentID'";

			$this->mRowObj = $this->mDatabase->getRowObj($vSql);
			if (!$this->mRowObj) {
			$this->mRowObj = $this->mDatabase->getFieldsObject($this->table);
			$this->is_new = true;
			} else {
				$this->mInitial = clone $this->mRowObj;
			}
		} else {
			$this->mRowObj = $this->mDatabase->getFieldsObject($this->table);
			$this->is_new = true;
		}
	}

	/** comment here */
	function initDraft() {
//		$_POST["PublishingType"] = "instant";

		$this->mDraft = $this->getDraft();
		//if ($this->mDraft && $this->post_data["PublishingType"] != "instant") $this->post_data["PublishingType"] = "draft";
	}

	/** comment here */
	function initArchive() {
		if (!$this->supports_draft) Return true;
		$this->archive = $this->mDatabase->getAll("select ID, Action, TimeStamp, UserID from cms_tracking where ContentTable = '".$this->table."' and ContentID = " . intval($this->mRowObj->{$this->pk}) . " order by timestamp asc");
	}

	/** comment here */
	function initTimer() {
		if (!$this->supports_timers) Return true;
		 $timers = $this->mDatabase->getAll("select * from cms_timers where ContentTable = '".$this->table."' and ContentID = " . intval($this->mRowObj->{$this->pk}));
		 $this->timers = array();
		 foreach ($timers as $key=>$val) {
			$this->timers[$val["Type"]] = $val;
			switch($val["Type"]) {
				case "publish":
					$this->post_data["PublishDate"] = $val["PublishDate"];
					$this->post_data["PublishTime"] = $val["PublishTime"];
					$this->post_data["PublishingType"] = "delayed";
					break;
				case "activate":
					$this->post_data["ActivationDate"] = $val["PublishDate"];
					$this->post_data["ActivationTime"] = $val["PublishTime"];
					$this->post_data["ActivationType"] = "delayed";
					break;			
			}
		 }
	}

	/** call this to register POST variables that match the RowObj object's properties */
	function registerForm($pArray = array()) {
	  if (empty($pArray)) $pArray = $this->post_data;
	  foreach (get_object_vars($this->mRowObj) AS $key=>$val) {
		  if (isset($pArray["$key"]))
			/* if variable is an array( coming from a select element) then
			**  if the array only has one element then the array is destroyed and the element is registered directly
			**	else, if the array has more than one element then the whole array will be passed as a variable
			**  WARNING: in the last case, the RowObj object cannont be saved directly to the database !! */
			if (is_array($pArray["$key"])) {
			  if (count($pArray["$key"])==1) {
				$this->mRowObj->{$key} = $pArray["$key"][0];
			  } else {
				$this->mRowObj->{$key} = $pArray["$key"];
			  }

			} else {
				if (!$pArray["$key"]) {
					if (is_numeric($this->mRowObj->{$key})) $this->mRowObj->{$key} = 0;
					else {
						$this->mRowObj->{$key} = $pArray["$key"];	
					}
				} else
					  $this->mRowObj->{$key} = $pArray["$key"];
			}
	  }



//	  		$vFields = get_object_vars($this->mRowObj);
//		foreach ($vFields as $key=>$val) {
//			echo $key . ": $val => " . gettype($val). "<br>";
//		};
//		die();

//	  foreach ($this->mRowObj as $key=>$val) {
//		if (is_numeric($val)) $this->mRowObj->$key=$val * 1;
//	  }

	}


	/** call this to register POST variables that match the RowObj object's properties */
	function unregisterForm($versionid) {
		$data = array();
		if ($versionid) {
			$version = $this->mDatabase->getRow("select ContentData from cms_tracking where ID = ".intval($versionid)." and ContentTable = '".$this->table."' and ContentID = " . intval($this->mRowObj->{$this->pk}) . " order by timestamp asc");
			if ($version["ContentData"]) $data = unserialize($version["ContentData"]); 
		}

		  foreach (get_object_vars($this->mRowObj) as $key=>$val) {
			  if ($versionid && $data) $_POST["$key"] = $data->$key;
			  else {
				  if (!$this->is_published) $_POST["$key"] = $this->mDraft->$key; else $_POST["$key"] = $this->mRowObj->{$key};
			  }
		  }
	}

	/** comment here */
	function loadVersion($versionid) {
			if ($versionid) {
				$version = $this->mDatabase->getRow("select ContentData from cms_tracking where ID = ".intval($versionid)." and ContentTable = '".$this->table."' and ContentID = " . intval($this->mRowObj->{$this->pk}) . " order by timestamp asc");
				if ($version["ContentData"]) {
					$this->mRowObj = unserialize($version["ContentData"]); 
				}
			}
	}


	/** save function */
	function _save() {

		if ($this->mSaveMode == "draft") {
			if (!$this->mRowObj->{$this->pk}) {
				$this->justSave();
				$this->mRowObj->{$this->pk} = $this->mDatabase->getLastID();
			} 
			$this->saveDraft();
			
		} else {

			if ($this->supports_publishing && $this->mRowObj->{$this->pk}) {
				$this->saveDraft();
				$this->mDraft = $this->mRowObj;
				$this->publish();
			} else {
				if ($this->supports_publishing) $this->mRowObj->Published = "yes";
				$this->easySave();
			}
		}
		$this->addTimers();
		Return true;
	}

	/** save function */
	function save() {
		Return $this->easySave();
	}

	/** save function */
	function easySave($tracking_label = "update") {
		$vFields = get_object_vars($this->mRowObj);
//		foreach ($vFields as $key=>$val) {
//			echo $key . ": " . $val . " => " . gettype($val). "<br>";
//		};
//		die();
		if ($this->mRowObj->{$this->pk}) {
			// update
			$vUpdateQuery = $this->mDatabase->makeUpdateQuery($vFields);
			$vSql = "UPDATE ".$this->table." SET $vUpdateQuery WHERE ".$this->pk."='".$this->mRowObj->{$this->pk}."'";
			$vResult = $this->mDatabase->query($vSql);
			if (!$tracking_label) $tracking_label = "update";
			$this->track($tracking_label, $this->mRowObj);
		} else {
			//insert
		  if((!isset($vFields["Timestamp"]) || !$vFields["Timestamp"]) && array_key_exists("Timestamp",$vFields)) $vFields["Timestamp"] = time();
		  $vInsertQuery = $this->mDatabase->makeInsertQuery($vFields);
		  $vSql = "INSERT INTO ".$this->table." $vInsertQuery";
		  $vResult = $this->mDatabase->query($vSql);
		  $this->init($this->mDatabase->getLastID());
			$this->track("create", $this->mRowObj);
		}
		Return $vResult;
	}

	/** comment here */
	function justSave() {
		$vFields = get_object_vars($this->mRowObj);
		if ($this->mRowObj->{$this->pk}) {
			// update
			$vUpdateQuery = $this->mDatabase->makeUpdateQuery($vFields);
			$vSql = "UPDATE ".$this->table." SET $vUpdateQuery WHERE ".$this->pk."='".$this->mRowObj->{$this->pk}."'";
			$vResult = $this->mDatabase->query($vSql);
		} else {
			//insert
		  if(!$vFields["Timestamp"]&&array_key_exists("Timestamp",$vFields)) $vFields["Timestamp"] = time();
		  $vInsertQuery = $this->mDatabase->makeInsertQuery($vFields);
		  $vSql = "INSERT INTO ".$this->table." $vInsertQuery";
		  $vResult = $this->mDatabase->query($vSql);
		}
		Return $vResult;
		
	}

	/** comment here */
	function saveDraft() {
		if ($this->supports_draft) {
			$this->track("draft", $this->mRowObj);
			$this->mDatabase->query("update " . $this->table . " set published = 'no' where  ".$this->pk."='".$this->mRowObj->{$this->pk}."'");

			$this->mDatabase->query("delete from cms_drafts where ContentTable = '".$this->table."' and ContentID = " . intval($this->mRowObj->{$this->pk}));
			$this->mDatabase->query("insert into cms_drafts(ContentTable, ContentID, ContentData, TimeStamp) values('".$this->table."','".intval($this->mRowObj->{$this->pk})."','".addslashes2(serialize($this->mRowObj))."', ".time().")");

		}
	}
	
	/** comment here */
	function getDraft() {


		if ($this->supports_draft) {
			$data = $this->mDatabase->getValue("cms_drafts", "ContentData", "ContentTable = '".$this->table."' and ContentID = " . intval($this->mRowObj->{$this->pk}));
			if ($data) Return unserialize($data);
		}
		Return "";
	}

	/** comment here */
	function addTimers($type = "") {

		if (!$this->supports_timers) Return true;
		$cls = get_class($this);
//		$cls .= "Admin";
		if ((!$type || $type == "publish") && $this->post_data["PublishingType"] == "delayed") {
			$this->track("timer2", array("Type" => "Publish", "Date" => $this->post_data["PublishDate"], "Time" => $this->post_data["PublishTime"], "TimeStamp" => makeDateTime($this->post_data["PublishDate"], $this->post_data["PublishTime"])));
			$this->mDatabase->query("delete from cms_timers where Type='publish' and ContentTable = '".$this->table."' and ContentID = " . intval($this->mRowObj->{$this->pk}));
			$this->mDatabase->query("insert into cms_timers(ContentClass, ContentTable, ContentID, TimeStamp, Type, PublishDate, PublishTime, PublishDateTime, PublishYMD) values('".$cls."', '".$this->table."','".intval($this->mRowObj->{$this->pk})."',".time().",'publish','".addslashes2($this->post_data["PublishDate"])."','".addslashes2($this->post_data["PublishTime"])."',".intval(makeDateTime($this->post_data["PublishDate"], $this->post_data["PublishTime"])).", ".intval(date("Ymd", $this->post_data["PublishDate"])).")");
		}

		if ((!$type || $type == "activation") && $this->post_data["ActivationType"] == "delayed") {
			$this->track("timer1", array("Type" => "Activate", "Date" => $this->post_data["ActivationDate"], "Time" => $this->post_data["ActivationTime"], "TimeStamp" => makeDateTime($this->post_data["ActivationDate"], $this->post_data["ActivationTime"])));
			$this->mDatabase->query("delete from cms_timers where Type='activate' and ContentTable = '".$this->table."' and ContentID = " . intval($this->mRowObj->{$this->pk}));
			$this->mDatabase->query("insert into cms_timers(ContentClass, ContentTable, ContentID, TimeStamp, Type, PublishDate, PublishTime, PublishDateTime, PublishYMD) values('".$cls."', '".$this->table."','".intval($this->mRowObj->{$this->pk})."',".time().",'activate','".addslashes2($this->post_data["ActivationDate"])."','".addslashes2($this->post_data["ActivationTime"])."',".intval(makeDateTime($this->post_data["ActivationDate"], $this->post_data["ActivationTime"])).", ".intval(date("Ymd", $this->post_data["ActivationDate"])).")");
		}
			
	}

	/** comment here */
	function track($type = "", $content = array()) {
		if (!$this->supports_draft) Return true;
		if ($content) $what = serialize($content); else $what = serialize($this->mInitial);
		$this->mDatabase->query("insert into cms_tracking(Action, UserID, ContentTable, ContentID, ContentData, TimeStamp) values('".$type."',".intval($this->mUserID).",'".$this->table."','".intval($this->mRowObj->{$this->pk})."','".addslashes2($what)."',".time().")");
	}

	/** comment here */
	function prepare() {

		$this->template = template2($this->view);
	}

	/** comment here */
	function generate() {
		$this->template->assign("Class", "status-" . $this->status_code);
		$this->template->assign("Form", $this->form->display());
		$this->template->assign("Log", $this->getLog());
	}

	/** comment here */
	function publishing() {
		if (!$this->supports_publishing) Return true;
		if (!$this->is_new) {
			$this->form->addBlock("publishing", array("label"=>"Publishing Options", "class"=>"block-publishing"));
			$input = new CText("PublishingStatus", $this->publishing_status, array("label" => "Current Status")); $this->form->addElement($input);
			$input = new CInputRadioGroup("PublishingType", array(), array("label"=> "Publish Content", "value" => "instant")); 
			if ($this->supports_timers) 
			$input->addButtons(array(array("instant", "Immediately", "instant"), array("draft", "Save Draft", "instant"), array("delayed", "Publish later", "delayed"))); 
			else
				$input->addButtons(array(array("instant", "Immediately", "instant"), array("draft", "Save Draft", "instant"))); 

			$input->value = "instant";
			$this->form->addElement($input);
			if ($this->supports_timers) {
			$this->form->addBlock("activation_dates", array("label"=>"", "class"=>"block-no-label block-publish-dates"));
			$this->form->addElement(new CText("PublishDateLabel", "Select date and time"));
			$this->form->addElement(new CInputDate("PublishDate", array("label" => "Published Date")));
			$this->form->addElement(new CInputTime("PublishTime", array("label"=>"Published Time", "class" => "w150")));
		}
		}

		if ($this->mRowObj->Status == "disabled" || $this->is_new) {
			$this->form->addBlock("activation", array("label"=>"Activation Options", "class"=>"block-activation"));
	//		$input = new CSelect("Status", array(), array("label" => "Current Status", "class"=>"w200", "placeholder"=>"Please select")); $input->getOptionsFromField("news", "Status"); $this->form->addElement($input);
			$input = new CText("Status", $this->activation_status, array("label" => "Current Status")); $this->form->addElement($input);
			$input = new CInputRadioGroup("ActivationType", array(), array("label"=> "Activate Content", "value" => "instant")); 
			if ($this->supports_timers) 
			$input->addButtons(array(array("instant", "Immediately", "instant"), array("donot", "Do not activate", "instant"), array("delayed", "Activate later", "delayed"))); 
			else
				$input->addButtons(array(array("instant", "Immediately", "instant"), array("donot", "Do not activate", "instant"))); 

			$input->value = "instant";
			$this->form->addElement($input);
			if ($this->supports_timers) {
			$this->form->addBlock("activation_dates2", array("label"=>"", "class"=>"block-no-label block-activation-dates"));
			$this->form->addElement(new CText("ActivationDateLabel", "Select date and time"));
			$this->form->addElement(new CInputDate("ActivationDate", array("label" => "Activation Date")));
			$this->form->addElement(new CInputTime("ActivationTime", array("label"=>"Activation Time", "class" => "w150")));
		}
		}
	
	}

	/** comment here */
	function output() {
		Return $this->template->output();
	}

	/** comment here */
	function html() {
		$this->prepare();
		$this->generate();
		Return $this->output();
	}

	/** comment here */
	function initForm() {
		$this->form = new CForm("frmEdit", array("action" => "/cms/" . $this->folder . "/save?id=" . $this->mRowObj->{$this->pk}, "class" => "validateme status-" . $this->status_code, "data-id" => $this->mRowObj->ID));
	}

	/** comment here */
	function getLog() {
		$history = "";
		if ($this->archive) {
			foreach ($this->archive as $key=>$val) {
				$label = ""; $actions = true;
				switch(strtoupper($val["Action"])) {
					case "CREATE": $label = "CREATE"; $actions = true; break;
					case "DRAFT": $label = "SAVE DRAFT"; break;
					case "TIMER": $label = "TIMER(A)"; $actions = false; break;
					case "TIMER1": $label = "TIMER(A)"; $actions = false; break;
					case "TIMER2": $label = "TIMER(P)"; $actions = false; break;
					case "PUBLISH": $label = "PUBLISHED"; $actions = false; break;
					case "ACTIVATE": $label = "ACTIVATED"; $actions = false; break;
					case "DEACTIVATE": $label = "DEACTIVATED"; $actions = false; break;
					case "UPDATE": $label = "UPDATED"; break;
					default: $label = strtoupper($val["Action"]);
				}
				$history.= '<div><span class="log-date">' . date("M d, Y H:i", $val["TimeStamp"]) . '</span><span class="log-event-info">' . $label  . '</span>';
				if ($actions) $history .= '<span class="log-actions">' ." <a href='/cms/". $this->folder ."/load-version?id=".$this->mRowObj->{$this->pk}."&versionid=".$val["ID"]."' id='load-version'>load</a></span>";
				$history .= "</div>";
			}
		}

		Return $history;	
	}

	function delete() {
		if (!$this->beforeDelete()) Return false;

		$this->track("delete");
		if ($this->supports_timers) $this->mDatabase->query("delete from cms_timers where ContentTable = '".$this->table."' and ContentID = " . intval($this->mRowObj->{$this->pk}));
		if ($this->supports_draft) $this->mDatabase->query("delete from cms_drafts where ContentTable = '".$this->table."' and ContentID = " . intval($this->mRowObj->{$this->pk}));
		$vSql = "DELETE FROM ".$this->table." WHERE ".$this->pk."='".$this->mRowObj->{$this->pk}."'";
		$this->mDatabase->query($vSql);

		Return $this->afterDelete();
	}
	
	/** comment here */
	function beforeDelete() {
		Return true;
	}

	/** comment here */
	function afterDelete() {
		Return true;
	}

	/** check name for uniqueness */
	function checkName() {
	  $vCount = $this->mDatabase->getValue($this->table,"count(*)","upper(Name) = upper('".$this->mRowObj->Name."') and " . $this->mRowObj->ID . " <> '" . $this->mRowObj->ID . "'");
	  if ($vCount > 0) Return false;
	  Return true;
	}

	/** comment here */
	function toggle() {
	  if ($this->mToggleStates && ($this->mRowObj->Status == $this->mToggleStates[1] || !$this->mRowObj->Status)) {
		  #disabled, activate now
		  $this->mRowObj->Status = $this->mToggleStates[0]; 
		  #remove any timers
		  $this->mDatabase->query("delete from cms_timers where Type='activate' and ContentTable = '".$this->table."' and ContentID = " . intval($this->mRowObj->{$this->pk}));
		  $this->track("activate", array());
	  } else {
		  #enabled, deactivate now
		  $this->mRowObj->Status = $this->mToggleStates[1];
		  $this->track("deactivate", array());
	  }
	  $this->easySave();
	}

	/** comment here */
	function publish() {
		if (!$this->beforePublish()) Return false;

		if ($this->mRowObj->Status != "published") {
			$this->mRowObj = $this->mDraft;
			$this->mRowObj->Status = $this->mInitial->Status;
			$this->mRowObj->Published = "yes";
			$this->easySave("publish");
			$this->mDatabase->query("delete from cms_drafts where ContentTable = '".$this->table."' and ContentID = " . intval($this->mRowObj->{$this->pk}));
			$this->mDatabase->query("delete from cms_timers where Type='publish' and ContentTable = '".$this->table."' and ContentID = " . intval($this->mRowObj->{$this->pk}));
		}

		Return $this->afterPublish();

	}

	/** comment here */
	function beforePublish() {
		Return true;
	}

	/** comment here */
	function afterPublish() {
		Return true;
	}

	/** comment here */
	function activate() {
		$this->mRowObj->Status = $this->mToggleStates[0];
		$this->easySave();
	  $this->mDatabase->query("delete from cms_timers where Type='activate' and ContentTable = '".$this->table."' and ContentID = " . intval($this->mRowObj->{$this->pk}));
	  $this->track("activate (auto)", array());
	}

	/** comment here */
	function move($pDirection) {
	  $info = $this->mDatabase->getRow("select max(OrderID) as mx, min(OrderID) as mn from ". $this->table ." where OrderID < 999999 and OrderID >0");
	  if ($pDirection == "up" && $info["mn"] == $this->mRowObj->OrderID) {
		$this->mLastError = "Item is already in first position!";
		Return false;
	  }
	  if ($pDirection == "down" && $info["mx"] == $this->mRowObj->OrderID) {
		$this->mLastError = "Item is already in last position!";
		Return false;
	  }
	  if ($pDirection == "up")
		$pos = $this->mDatabase->getValue($this->table, "max(OrderID)", "OrderID < ". $this->mRowObj->OrderID);
	  else
		$pos = $this->mDatabase->getValue($this->table, "min(OrderID)", "OrderID > ". $this->mRowObj->OrderID . " and OrderID < 999999");
	  if ($pos == "999999" && $this->mRowObj->OrderID == 999999) $this->mRowObj->OrderID = 999998;
	  $this->mDatabase->query("update " . $this->table . " set OrderID = '".$this->mRowObj->OrderID."' where OrderID = $pos");
	  $this->mRowObj->OrderID = $pos;
	  $this->justSave();
	}


	/** comment here */
	function displayEdit() {
	}


	/** comment here */
	function setCommonFields() {

        if (property_exists($this->mRowObj, "Guid") && $this->is_new && $this->mRowObj->Guid) $this->checkUniqueURL();

        if (property_exists($this->mRowObj, "TimeStamp") && $this->is_new && !$this->mRowObj->TimeStamp) $this->mRowObj->TimeStamp = time();

		if (property_exists($this->mRowObj, "Status") && !isset($_POST["Status"]) && !$this->mRowObj->Status) {
			if ($this->mToggleStates) {
				if (!$this->post_data["ActivationType"] || $this->post_data["ActivationType"] == "instant") $this->mRowObj->Status = $this->mToggleStates[0]; else $this->mRowObj->Status = $this->mToggleStates[1]; 
			} 
		}

		if (property_exists($this->mRowObj, "Published")) {
			if ($this->post_data["PublishingType"] == "instant") $this->mRowObj->Published = "yes"; else $this->mRowObj->Published = "no"; 
		}
        
		if (property_exists($this->mRowObj, "UserID") && !$this->mRowObj->UserID) $this->mRowObj->UserID = $_SESSION["gUserID"];
        if (property_exists($this->mRowObj, "LastUpdated") && $this->mRowObj->LastUpdated <= 100000) $this->mRowObj->LastUpdated = time();

		if (property_exists($this->mRowObj, "OrderID") && !$this->mRowObj->OrderID) {
			if ($this->order_new_objects == "first") {
				$this->mDatabase->query("update " . $this->table . " set OrderID = OrderID + 1");
				$this->mRowObj->OrderID = 1;
			} else {
				$this->mRowObj->OrderID = intval($this->mDatabase->getValue($this->table, "max(OrderID)", "orderid is not null")) + 1;
			}
		}
		
	}


	/** comment here */
	function uploadImage($source, $folder, $fname, $w, $h, $size_option, $field, $save = false) {

		$fm = new CFileManager();

		if($_FILES[$source]["tmp_name"]) {

	        $basepath = "media/" . $folder;

			$parts = explode("/", $basepath);
			$base = "../";
			while ($slice = array_shift($parts)) {
				$base .= $slice . "/";
				@mkdir($base);
			}
			
			if (!is_array($_FILES[$source]["name"])) $uploaded_name = $_FILES[$source]["name"]; else $uploaded_name = $_FILES[$source]["name"][0];
			$uploaded_name= filter_filename(mb_convert_encoding($uploaded_name, "ISO-8859-1", "UTF-8"));

			if (!is_array($_FILES[$source]["tmp_name"])) $tmp_name = $_FILES[$source]["tmp_name"]; else $tmp_name = $_FILES[$source]["tmp_name"][0];
			$info = pathinfo(strtolower($uploaded_name));
			if (!$fname) {
				$path = $fm->verifyPath($basepath . "/" . $uploaded_name, "../"); # resized file
			} else {
				$path = $fm->verifyPath($basepath . "/" . $fname . "." . $info["extension"], "../"); # resized file
			}


			if (!$field) $field = $source;
            if(move_uploaded_file($tmp_name, "../" .$path)) {
				if ($info["extension"] == "jpg" || $info["extension"] == "png" || $info["extension"] == "gif" || $info["extension"] == "jpeg" || $info["extension"] == "bmp") {
					if ($size_option == "thumbnail") $fm->thumbnail("../" .$path, $w, $h); //448
					if ($size_option == "fitbox") $fm->fitToBox("../" .$path, $w, $h); //448
					if ($size_option == "fitbox-outside") $fm->fitToBox("../" .$path, $w, $h, true, true); //448
					if ($size_option == "fitwidth") $fm->fitWidth("../" .$path, $w); //448
					if ($size_option == "fitheight") $fm->fitHeight("../" .$path, $h); //448
					if ($size_option == "resize-to-box") $fm->fitInBox("../" .$path, $w, $h, false); //448
				}
                $this->mRowObj->$field = "/" . $path;
            } else {
				error("Unable to move uploaded file");
				Return false;
            }
			if ($save) $this->easySave();
			Return true;
        } else {

			if (!$_FILES[$source]["name"] && $_POST[$source . "_delete_me"] == "delete") $this->mRowObj->$field = "";
			Return false;
        }
	}

	/** comment here */
	function uploadDocument($source, $folder, $fname, $field, $save = false) {

		$fm = new CFileManager();

		if($_FILES[$source]["tmp_name"]) {
	        $basepath = "media/" . $folder;

			$parts = explode("/", $basepath);
			$base = "../";
			while ($slice = array_shift($parts)) {
				$base .= $slice . "/";
				@mkdir($base);
			}

			$clean_name = str_replace("%", "-", filter_filename($_FILES[$source]["name"]));
			if (!$fname) $path = $fm->verifyPath($basepath . "/" . $clean_name, "../"); # resized file
			else {
				$fname = filter_filename($fname);
				$tmp = pathinfo($_FILES[$source]["name"]);
				$path = $fm->verifyPath($basepath . "/" . $fname . "." . $tmp["extension"], "../"); # resized file
			}

			if (!$field) $field = $source;
            if(move_uploaded_file($_FILES[$source]["tmp_name"], "../" .$path)) {
                $this->mRowObj->$field = "/" . $path;
            }

			if ($save) $this->easySave();
        } else {
			if (!$_FILES[$source]["name"] && $_POST[$source . "_delete_me"] == "delete") $this->mRowObj->$field = "";
        }
		
	}

	/** comment here */
	function checkUniqueURL() {
		$check = $this->mDatabase->getRow("select count(*) as cnt from " . $this->table . " where Guid = '".addslashes2($this->mRowObj->Guid)."' and ID <> " . intval($this->mRowObj->ID));
		$Guid = $this->mRowObj->Guid;
		$urlindex = 1;
		while ($check["cnt"] > 0) {
			$this->mRowObj->Guid = $Guid . "-" . $urlindex;
			$urlindex ++;
			$check = $this->mDatabase->getRow("select count(*) as cnt from " . $this->table . " where Guid = '".addslashes2($this->mRowObj->Guid)."' and ID <> " . intval($this->mRowObj->ID));
			if ($urlindex > 10) die("oops");
		}
		Return true;
	}

	/** comment here */
	function checkUniqueField($field) {
		$check = $this->mDatabase->getRow("select count(*) as cnt from " . $this->table . " where $field = '".addslashes2($this->mRowObj->$field)."' and ID <> " . intval($this->mRowObj->ID));
		if ($check["cnt"] > 0) Return false;
		Return true;
	}

  }
?>