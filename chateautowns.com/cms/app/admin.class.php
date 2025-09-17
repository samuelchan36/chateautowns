<?php

	class CMain extends CSectionAdmin {

	  var $section = "Home";
	  var $parent = "";

	  /** comment here */
	  function __construct() {
		parent::__construct();
	  }

	  function display() {

		 Return "<p class='welcome'>Welcome to the admin interface</p>";
	  }

	  /** comment here */
	  function getAddress($name) {
			echo sanitize(sanitize_text_for_urls(str_replace("&", "and", $name)));die();
	  }

	  /** comment here */
	  function checkTimers() {
		$data = $this->mDatabase->getAll("select * from cms_timers where PublishDateTime <= unix_timestamp()");
		foreach ($data as $key=>$val) {
			$cls = $val["ContentClass"];
			$section = new $cls();
			if ($val["Type"] == "activate") {
				$section->toggle($val["ContentID"]);
			} 
			if ($val["Type"] == "publish") {
				$section->publish($val["ContentID"]);
			} 
//			$this->mDatabase->query("delete from cms_timers where ID = " . intval($val["ID"]));
		}
		die2("done");
	  }


	  /** comment here */
	  function mainSwitch() {

			switch($this->mOperation) {
				case "brand": Return "brand";
				case "check-timers": Return $this->checkTimers();
				case "get-address": Return $this->getAddress($_GET["name"]);
			default:
			  Return CSectionAdmin::mainSwitch();
			}
	  }
	}

?>