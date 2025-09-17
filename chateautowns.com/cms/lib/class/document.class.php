<?php
class CDocument {
	
	var $mTitle;
	var $mCss = array();
	var $mJs = array();
	var $mMeta = array();
	var $mTemplate = "templates/main.html";
	var $mTemplateObj;

	var $mManager = "";

	var $mTestMode = true;
	var $mErrors = array();

	var $mCode = "oaa";

	var $mModules;
	var $mModule;

	var $mStyle = "";
	var $mJavascript = array();

	var $mUserID = 0;
	var $mUser = "";
	var $mUserRights = array();

	var $mGroup = "";

	/** comment here */
	function __construct() {
		$this->loadModules();

		$_SESSION["lang"] = "en";

		$this->mJs[] = "/cms/app/settings.js";

		#$this->mCss[] = "https://fast.fonts.com/cssapi/468e5d4e-20fe-4333-882c-865951e6a2d3.css";

		$this->mJs[] = "https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js";
		$this->mJs[] = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/jquery-ui.min.js";

		$this->mJs[] = "https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.bundle.js";
		$this->mJs[] = "/lib/plugins/animate_number/jquery.animateNumber.min.js";

		$this->mJs[] = "/lib/js/tools.js";
		
		#fancybox
		$this->mCss[] = "/lib/plugins/fancybox/jquery.fancybox.css?v=2.1.5";
		$this->mJs[] = "/lib/plugins/fancybox/jquery.fancybox.pack.js?v=2.1.5";

//		$this->mJs[] = "/lib/plugins/tiny_mce4/tinymce.min.js";
		$this->mJs[] = "/lib/plugins/tinymce5/tinymce.min.js";
		$this->mJs[] = "/lib/plugins/tinymce5/jquery.tinymce.min.js";
		#tinymce
		#$this->mJs[] = "https://cloud.tinymce.com/5/tinymce.min.js?apiKey=2bwv6cdyfr7jcqwd7835ylobxjbo1m4i0cqux6vxyern0x4q";

		# cleave
		$this->mJs[] = "/lib/plugins/cleave/cleave.min.js";
		$this->mJs[] = "/lib/plugins/cleave/addons/cleave-phone.ca.js";

		$this->mCss[] = "/lib/plugins/timepicker/jquery.ptTimeSelect.css";
		$this->mJs[] = "/lib/plugins/timepicker/jquery.ptTimeSelect.js";

		#select2
		$this->mJs[] = "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js";
		$this->mCss[] = "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css";

		#dropzone
		$this->mJs[] = "/lib/plugins/dropzone/dropzone.js";
		$this->mCss[] = "/lib/plugins/dropzone/dropzone.css";
		
		#clipboard
		$this->mJs[] = "/lib/plugins/clipboard/clipboard.min.js";



		$this->mJs[] = "/cms/lib/js/main.js";
		$this->mJs[] = "/cms/app/assets/js/main.js";

		$this->mCss[] = "/cms/app/assets/css/main.css";
		$this->mCss[] = "/cms/lib/css/admin.css";
		$this->mTemplate = "lib/html/admin.html";

//		$this->loadSkin();

		if (isset($_GET["s"])) $this->mModule = $_GET["s"];
		if (!$this->mModule)  $this->mModule = "main";

		$this->login();


		if (!$this->mUserID && $this->mModule != "access" && !($this->mModule == "activation" && isset($_GET["o"]) && $_GET["o"] == "check")) {
			redirect("/cms/access/login");
		}

	}

	/** comment here */
	function loadSkin() {
		$this->mCss[] = "app/assets/css/skin.css";
	}

	/** comment here */
	function loadModules() {

		$this->mModules = array();
		$this->mModules["main"]=array("CMain", "all", 1);
		foreach ($GLOBALS["cms_core_modules"] as $key=>$val) {
			$this->mModules[$key] = $val;
			$this->mModules[$key][2] = true;
		}

		foreach ($GLOBALS["cms_modules"] as $key=>$val) {
			if (isset($this->mModules[$key])) {
				$_SESSION["error"] = "Duplicate module";
				continue;
			}
			$this->mModules[$key] = $val;
			$this->mModules[$key][2] = false;
		}
		
		


	}


	/** comment here */
	function display() {
		if (!isset($_SESSION["error"])) $_SESSION["error"] = array();
		$this->mTemplateObj = template2($this->mTemplate); 
		$body = $this->body();
		$this->mTemplateObj->assign("Head", $this->head());

		$this->mTemplateObj->assign("Body", $body);
		$this->mTemplateObj->assign("Menu", $this->menu());
//		$this->mTemplateObj->assign("Footer", $this->footer());
		$this->mTemplateObj->assign("Scripts", $this->scripts() . "<style>" . $this->mStyle . "</style>");

		if ($_SESSION["error"]) {
			$this->mTemplateObj->assign("Error", $_SESSION["error"]["message"]);
			$this->mTemplateObj->assign("ErrorClass", $_SESSION["error"]["type"]);
		} 

		$_SESSION["error"] = array();

		$this->mTemplateObj->printToScreen();
	}


	/** comment here */
	function menu() {
		$tpl = template2("lib/html/menu.html");

		foreach ($GLOBALS["cms_menu"] as $key=>$val) {
			if (!isset($this->mModules[$key]) || 
				!isset($this->mModules[$key][1]) || 
				!isset($this->mUserRights[$this->mModules[$key][1]]) || 
				!$this->mUserRights[$this->mModules[$key][1]]
			) 
				continue;
			$tpl->newBlock("MENU");
			$tpl->assign("Section", $key);
			$tpl->assign("Label", $val[0]);
			if (isset($val[1]) && $val[1]) {
				$tpl->newBlock("SUBMENU");
				foreach ($val[1] as $key2=>$val2) {

					if (!isset($val2[0]) 
						|| !isset($this->mModules[$val2[0]][1]) 
						|| !isset($this->mUserRights[$this->mModules[$val2[0]][1]])
						|| !$this->mUserRights[$this->mModules[$val2[0]][1]]
						) continue;
					$tpl->newBlock("SUBMENUITEM");
					$tpl->assign("Section", $val2[0]);
					$tpl->assign("Label", $val2[1]);
					if (isset($val2[2]) && $val2[2])  $tpl->assign("Action", $val2[2]);
				}
			}
		}
		Return $tpl->output();
	}


	/** comment here */
	function footer() {
		$tpl = template2("lib/html/footer.html");
		Return $tpl->output();
	}

	/** comment here */
	function scripts() {
		$tpl = template2("lib/html/scripts.html");
		$tpl->assign("More", implode($this->mJavascript));
		Return $tpl->output();
	}	

	/** comment here */
	function head() {
		$this->mTitle = SITE_TITLE;
		$txt = "<title>".$this->mTitle."</title>";

		$txt .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> ';
		foreach ($this->mMeta  as $key=>$val) {
			$txt .= $val;
		}

		foreach ($this->mJs as $key=>$val) {
			$txt .= '<script type="text/javascript" src="'.$val.'" ></script>';
		}

		foreach ($this->mCss  as $key=>$val) {
			$txt .= '<link rel="stylesheet" type="text/css" href="'.$val.'"/>';
		}

		if (isset($_SESSION["gNotification"])) {
			$txt .= '<script>$(window).load(function () { setTimeout(function () { alert2("'.str_replace('"','\"',$_SESSION["gNotification"]).'")}, 100)});</script>';
			unset($_SESSION["gNotification"]);
		}
		
		Return $txt;
	}

	/** comment here */
	function body() {
		if (!$this->mModule) Return "";
		$objname = $this->mModules[$this->mModule][0]; 
		if ($objname) {
		$obj = new $objname();
		$obj->mClass = str_replace("Admin", "", $objname);

		if ($this->mModules[$this->mModule][2]) $obj->folder = "lib/class/modules/" . $this->mModule; else $obj->folder = "app/modules/" . $this->mModule;
		
		$obj->url = $this->mModule;
		$obj->mAccess= $this->mModules[$this->mModule][1]; 
		$obj->mSectionName= $this->mModule;
		return $obj->mainSwitch();
	}
		Return "";
	}

	
	/** comment here */
	function error($err, $severity = 2) {
		error($err);
	}

	/** comment here */
	function message($msg) {
		message($msg);
	}

	/** comment here */
	function login() {
		if (!$this->mUserID) {
			if (isset($_SESSION["gUserID"]) && $_SESSION["gUserID"]) $this->mUserID = $_SESSION["gUserID"];
		}


		if ($this->mUserID) {
			$_SESSION["gUserID"] = $this->mUserID;
			$this->mUser  = new CUser($this->mUserID, "cms_users");
			$this->mUserID  = $this->mUser->mRowObj->ID;
			if (!$this->mUserID) {
				error("An error has occured");
				$_SESSION["gUserID"] = 0;
				redirect("/cms/login");
			}
			$this->mGroup = new CUserGroup($this->mUser->mRowObj->GroupID, "cms_user_groups");

			$this->mUserRights = array();
			if ($this->mGroup->mRowObj->AdminGroup != "yes") {
				$tmp = $GLOBALS["db"]->getAll("select * from cms_group_rights where groupid = " . intval($this->mUser->mRowObj->GroupID));
				$rights = array();
				foreach ($tmp as $key=>$val) {
//die2($val);
					$this->mUserRights[$val["Section"]][$val["Operation"]] = true;
				}
			} else {
				foreach ($GLOBALS["access_rules"] as $key=>$val) {
					if (!isset($this->mUserRights[$key])) $this->mUserRights[$key] = array();
					$this->mUserRights[$key]["all"] = true;
					foreach ($val as $key2=>$val2) {
						if (!is_array($val2)) $this->mUserRights[$key][$val2] = true;
					}
				}
			}
		}

	}

}
?>