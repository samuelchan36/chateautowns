<?php

	class STitle {
		/** comment here */
		function get($type, $css = "section_title") {
////			$txt = 	"<img border=\"0\" src=\"images/admin/{$type}.jpg\" >";
			$txt = 	"<h1>$type<h1>";
			Return $txt;
		}

		function set($txt) {
			$GLOBALS["doc"]->mTemplateObj->assign("Title", "<h1>" .  $txt . "</h1>");
		}
	}
?>