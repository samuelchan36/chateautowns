<?php

	class CManager {
		
		/** comment here */
		function __construct() {
			
		}

		/** comment here */
		function mainSwitch() {

			global $modules;

			$section = "main";
			if (isset($_GET["s"])) $section = $_GET["s"]; 
			$obj = new $modules[$section][0]();
			$operation = "main";
			if (isset($_GET["o"])) $operation = $_GET["o"]; 
			$obj->mOperation = $operation;

			Return $obj->mainSwitch();
		}
	}
?>