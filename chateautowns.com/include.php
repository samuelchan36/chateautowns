<?php

include "vendor/autoload.php";

/* MODULES */
$modules = array();
$modules["main"] = array("CMain", "main", "");
$modules["access"] = array("CAccess", "access", "lib/");


/* INCLUDE FRAMEWORK */
include "lib/functions.php";
include "lib/database.class.php"; if (DB_DB) $db = new CDatabase(true);
include "lib/template.power.class.php";
include "lib/content.class.php";
include "lib/manager.class.php";
include "lib/default.class.php";
include "lib/email.class.php";
//include "lib/plugins/mobile_detect/Mobile_Detect.php";
//require "lib/tracking.class.php";
require "lib/search.class.php";
require "lib/data.class.php"; $datalib = new CDataManager();

/* LOAD DOCUMENT */
include "data.class.php";
include "document.class.php";

/* INCLUDE MODULES */
foreach ($modules as $key=>$val) {
	include $val[2] . "".strtolower($val[1]).".class.php";	
}





?>
