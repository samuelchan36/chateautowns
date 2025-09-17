<?php
			function fatal_handler() {
					 $error = error_get_last();
					 if (!$error) Return true;
					if (!is_writeable(ROOT_DIR . "/media/logs")) Return true;
					if (!$error) Return true;
					if ($error['type'] === E_ERROR) { 
						file_put_contents(ROOT_DIR . "/media/logs/fatal.txt",  date("F d, Y H:i") . ": [".$error["type"]."]: " . $error["message"] . " (".$error["file"] . ": " . $error["line"].")\n", FILE_APPEND)  ;
						if (DEBUG_MODE == "full") die2($error);
						
						msg("Sorry, we had some trouble processing your request", "error", "redirect");
					}  else {
						if ($error['type'] === E_WARNING || $error['type'] === E_PARSE ) {
							file_put_contents(ROOT_DIR ."/media/logs/errors.txt",  date("F d, Y H:i") . ": [".$error["type"]."]: " . $error["message"] . " (".$error["file"] . ": " . $error["line"].")\n", FILE_APPEND)  ;
						} else {
							if (LOG_ERRORS == "all") {
								if (!strpos($error["message"], "conv_set_encoding")) {
									file_put_contents(ROOT_DIR ."/media/logs/other-errors.txt",  date("F d, Y H:i") . ": [".$error["type"]."]: " . $error["message"] . " (".$error["file"] . ": " . $error["line"].")\n", FILE_APPEND)  ;
								}
							}
						}
					}
					Return true;
			}


			if (DEBUG_MODE == "full") {
				error_reporting(E_ALL);
				ini_set("display_errors", 1);
			} else if (DEBUG_MODE == "on") {
				error_reporting(E_ALL);
				ini_set("display_errors", 0);
			} else {
				error_reporting(0);
				ini_set("display_errors", 0);
			}
			register_shutdown_function( "fatal_handler" );



			/** comment here */
			function redirect($url) {
				ob_end_clean();
				die("<script>window.location='".$url."';</script>");
			}

			/** comment here */
			function redirect301($url) {
				header("HTTP/1.1 301 Moved Permanently"); 
				header("Location: $url");
			}

			/** comment here */
			function redirect2($url) {
				header("Location: $url");
				die();
			}

			/** comment here */
			function json_response($arr) {
				ob_end_clean();
				die(json_encode($arr));
			}

			/** comment here */
			function die2($errs) {
				 print_r($errs);
				 die();
			}

			/** comment here */
			function diefull() {
				 print_r($_REQUEST);
				 print_r($_GET);
				 print_r($_POST);
				 print_r($_FILES);
				 print_r($_SESSION);
				 die();
			}


				/** comment here */
			function debug($on = false) {
				if ($on) {
					ini_set("display_errors", 1);
					error_reporting(E_ALL);
				} else {
					ini_set("display_errors", 0);
					error_reporting(0);
				}
			}

			/** get the history of this call */
			function getFileTrace() {
				$vDebug = debug_backtrace();
				$vFiles = array();
				for ($i=0;$i<count($vDebug);$i++) {
					// skip the first one, since it's always this log func
					if ($i==0) { continue; }
					$aFile = $vDebug[$i];
					$vFiles[] = '('.basename($aFile['file']).':'.$aFile['line'].')';
				} // for
				return implode('<br>',$vFiles);
			}

			/** comment here */
			function error($msg, $action = "none") {
				$_SESSION["error"] = array("message" => $msg, "type" => "error"); 
				switch($action) {
					case "halt": die2($msg);
					case "redirect": redirect("/");
					case "go-back": ob_end_clean(); die("<script>window.go(-1);</script>");
				}
			}


			/** comment here */
			function msg($msg, $type = "standard", $action ="none") {
				$_SESSION["error"] = array("message" => $msg, "type" => "standard"); 
				switch($action) {
					case "halt": die2($msg);
					case "redirect": redirect("/");
					case "go-back": ob_end_clean(); die("<script>window.go(-1);</script>");
				}
			}
			
		/** use this for logging applicatino errors */
			function jserror($x) {
					if (!is_writeable(ROOT_DIR . "/media/logs")) Return true;
					print_r($x);
					file_put_contents(ROOT_DIR ."/media/logs/errors.app.txt", "--------------- " . date("F d, Y H:i") . " ------------------------\n" . ob_get_clean() . "\n--------------------------\n", FILE_APPEND)  ;
					ob_start();
			}

		/** use this for debugging */
			function jsdebug($x, $label = "", $reset = "") {
					if (!is_writeable(ROOT_DIR . "/media/logs")) Return true;
					if ($reset) unlink(ROOT_DIR . "/media/logs/debug.txt");
					if ($label) {
						print_r($label . "\n");
					}
					print_r($x);
					file_put_contents(ROOT_DIR . "/media/logs/debug.txt", "--------------- " . date("F d, Y H:i") . " ------------------------\n" . ob_get_clean() . "\n--------------------------\n", FILE_APPEND)  ;
					ob_start();
			}



?>