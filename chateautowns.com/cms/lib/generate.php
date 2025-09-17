<?php
	include "settings.php";
	foreach ($cms_modules as $key=>$val) {
		if (!file_exists("app/modules/".$key."/controller.php")) {
			@mkdir("class/modules/".$key);
			$txt = file_get_contents("lib/class/modules/_template/controller.php");
			$classname= str_replace("Admin" , "", $val[0]);
			$itemname= substr($classname, 1);
			$itemname2 = $itemname . "s";
			$txt = str_replace(array("{ClassName}", "{TableName}", "{ItemName}", "{ItemNamePlural}"), array($classname, $key, $itemname, $itemname2), $txt);
			$fh = fopen("app/modules/".$key."/controller.php", "a+");
			fwrite($fh, $txt);
			fclose($fh);

			$txt = file_get_contents("lib/class/modules/_template/model.php");
			$txt = str_replace(array("{ClassName}", "{TableName}", "{ItemName}", "{ItemNamePlural}"), array($classname, $key, $itemname, $itemname2), $txt);
			$fh = fopen("app/modules/".$key."/model.php", "a+");
			fwrite($fh, $txt);
			fclose($fh);

			copy("lib/class/modules/_template/view.html", "app/modules/".$key."/view.html");
		}
	}

?>