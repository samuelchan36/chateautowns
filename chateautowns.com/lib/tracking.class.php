<?php

	class CTracking extends CDefault {
		
		var $stop = false;
		
		function __construct() {
			parent::__construct();
//			$this->resetSession();
			if (substr($_SERVER["REQUEST_URI"], 1, 8) != "tracking" && substr($_SERVER["REQUEST_URI"], 1, 5) != "cron/") {
				$this->initSession();
				
			}
		}
		

		/** comment here */
		function initSession() {
//			$_SESSION = array();
//				setcookie("jscuqid", base64_encode(serialize($cook)), time() - 86400 * 730, "/");
//die();
//			die2($_SESSION["Session"]);
//			die2($_COOKIE);
//$_SESSION["Session"] = array();
			if (!$_SESSION["Session"]) {
//				die('a');
				$ip =$this->decodeIP();
				#new session
//				if ($cookies["SessionID"]) $_SESSION["Tracking"]["ID"] = $cookies["SessionID"];
//				if ($cookies["UserID"]) $_SESSION["Tracking"]["UserID"] = $cookies["UserID"];
//				if ($cookies["UserID"]) $_SESSION["Tracking"]["CartID"] = $cookies["UserID"];
				#create new session
				$_SESSION["Session"] = array();
				#check cookie
				$savedSession = array();
				if ($_COOKIE["jscuqid"]) {
					$savedSession = unserialize(base64_decode($_COOKIE["jscuqid"]));
//					die2($savedSession);
				}
				$visitor = array();
//die2($savedSession);
				if ($savedSession) {
						#if cookie exists , check if cookie is valid
						$visitor = $this->mDatabase->getRow("select js_tracking_visitors.*, js_accounts.InactiveStatus, js_accounts.Status from js_tracking_visitors left outer join js_accounts on js_tracking_visitors.UserID = js_accounts.ID where js_tracking_visitors.ID = " . intval($savedSession["id"]) . " and md5(js_tracking_visitors.Code) = '" . addslashes2($savedSession["key"]) . "'");

						if ($visitor) {
							$_SESSION["Session"]["VisitorID"] = $visitor["ID"];
							# [PAST VISITOR] #Check if UserID
							if ($visitor["UserID"]) {
								$_SESSION["Session"]["UserID"] = $visitor["UserID"];
								#[PREVIOUS ACCOUNT]  if UserID exists, check if user should be autologged
								if ($visitor["RememberMe"] == "yes" && $visitor["InactiveStatus"] == "active" && $visitor["Status"] == "active") {
									#[AUTOLOG] if yes, autolog user	
									$_SESSION["UserID"] = $visitor["UserID"];
									$_SESSION["PriceLevelID"] = $this->mDatabase->getValue("js_accounts", "PriceLevel", "ID= " . intval($_SESSION["UserID"]));
								} else {
									#[NO AUTOLOG] if not, update session silently								
								}
							} else {
								#[NO USER ID] get visitor unique id								
							}

							# CHECK CART
							$cart = $this->mDatabase->getRow("select * from js_tracking_cart where VisitorID = " . intval($visitor["ID"]) . " order by ID desc limit 1");

							$cartdata = array(); if ($cart) $cartdata = unserialize($cart["CartData"]);
							if ($cart["Status"] == "created" || $cart["Status"] == "checkout" ) {
								#unfinished cart exists
								$_SESSION["cart"] = $cartdata;

//								$catalog = new CCatalogLocal();
//								$catalog->updateCartPrices();
							} else {
								$_SESSION["cart"] = array();
							}
							$_SESSION["favourites"] = array(); if ($cart) $_SESSION["favourites"] = $cartdata["favourites"];
							
							$session = $this->mDatabase->getRow("select * from js_tracking_sessions where VisitorID = " .intval($_SESSION["Session"]["VisitorID"]). " and LastUpdated >= unix_timestamp() - 3600 order by id desc limit 1");
							if ($session) {
								$_SESSION["Session"]["Server"] = array("agent" => $_SERVER["HTTP_USER_AGENT"], "referer" => $_SERVER["HTTP_REFERER"], "language" => $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
								$_SESSION["Session"]["ID"] = $session["ID"];
								$_SESSION["Session"]["UserIP"] = $ip["IP"];
								$_SESSION["Session"]["CountryID"] = $ip["CountryID"];
								$_SESSION["Session"]["StateID"] = $ip["StateID"];
								$_SESSION["Session"]["City"] = $ip["City"];
								$_SESSION["Session"]["Saved"] = true;
								$this->mDatabase->query("update js_tracking_sessions set lastupdated = unix_timestamp() where id = " . intval($_SESSION["Session"]["ID"]));
							}

					}  
				} 

				if (!$visitor) {
					# [FIRST TIME VISIT] create visitor unique id
					$code = substr(uniqid("visitors") . $this->gen_uuid(), 0, 254);
//					die($code);
//die2($_SERVER);
					$this->mDatabase->query("insert into js_tracking_visitors(UserID, Code, TimeStamp, Visits, LastVisit, StartPage, UserIP, CountryID, StateID, City, RememberMe, Status) values(0, '".addslashes2($code)."',  unix_timestamp(), 1, unix_timestamp(), '".addslashes($_SERVER["REQUEST_URI"])."', '".$ip["IP"]."', '".$ip["CountryID"]."', '".$ip["StateID"]."', '".$ip["City"]."', 'no', 'pending')");
					$_SESSION["Session"]["VisitorID"] = $this->mDatabase->getLastID();
					$cook = array("id" => $_SESSION["Session"]["VisitorID"], "key" => md5($code));
					setcookie("jscuqid", base64_encode(serialize($cook)), time() + 86400 * 730, "/");
				} else {
					$this->mDatabase->query("update js_tracking_visitors set Visits = Visits + 1, LastVisit = unix_timestamp() where ID = " . intval($visitor["ID"]));
				}
				
				if (!$_SESSION["Session"]["ID"]) {
						$_SESSION["Session"]["Server"] = array("agent" => $_SERVER["HTTP_USER_AGENT"], "referer" => $_SERVER["HTTP_REFERER"], "language" => $_SERVER["HTTP_ACCEPT_LANGUAGE"]);

						$this->mDatabase->query("insert into js_tracking_sessions(TimeStamp, LastUpdated, VisitorID, UserID, StartPage, UserIP, CountryID, StateID, City, Duration, Language, UserAgent, Referer, Status) values(unix_timestamp(), unix_timestamp(), ".intval($_SESSION["Session"]["VisitorID"]).", '".intval($_SESSION["Session"]["UserID"])."', '".addslashes($_SERVER["REQUEST_URI"])."', '".$ip["IP"]."', '".$ip["CountryID"]."', '".$ip["StateID"]."', '".$ip["City"]."', 0, '".addslashes2(substr($_SESSION["Session"]["Server"]["language"], 0, 254))."', '".addslashes2(substr($_SESSION["Session"]["Server"]["agent"], 0, 254))."', '".addslashes2(substr($_SESSION["Session"]["Server"]["referer"], 0, 254))."', 'pending')");
						$_SESSION["Session"]["ID"] = $this->mDatabase->getLastID();
						$_SESSION["Session"]["UserIP"] = $ip["IP"];
						$_SESSION["Session"]["CountryID"] = $ip["CountryID"];
						$_SESSION["Session"]["StateID"] = $ip["StateID"];
						$_SESSION["Session"]["City"] = $ip["City"];
						$_SESSION["Session"]["Saved"] = false;
				}

			} else {
				#existing session - update LastUpdated field
				$this->mDatabase->query("update js_tracking_sessions set lastupdated = unix_timestamp() where id = " . intval($_SESSION["Session"]["ID"]));
			}

		}

		/** comment here */
		function cleanup() {
			if (!$_SESSION["Session"]["Saved"] && !$this->stop) {
				$this->mDatabase->query("update js_tracking_sessions set status = 'verified' where id = " . intval($_SESSION["Session"]["ID"]));
				$this->mDatabase->query("update js_tracking_visitors set status = 'verified' where id = " . intval($_SESSION["Session"]["VisitorID"]));
				$_SESSION["Session"]["Saved"] = true;
			}
		}

		/** comment here */
		function resetSession() {
				$_SESSION["Session"] = array();
				$cook = array("id" => "", "key" => "");
				setcookie("jscuqid", base64_encode(serialize($cook)), time() - 86400 * 730, "/");
				unset($_COOKIES["jscuqid"]);
		}
		
		# call this function after login
		function login($remember) {
				$visitor = $this->mDatabase->getRow("select * from js_tracking_visitors where UserID = " . intval($_SESSION["UserID"]) . " order by ID desc LIMIT 1");
				if ($visitor && $visitor["ID"] != $_SESSION["Session"]["VisitorID"]) {
					#not a new visitor, let's move all sessions to the existing one and delete the current visitor record
					$this->mDatabase->query("update js_tracking_sessions set UserID = " .intval($_SESSION["UserID"]). ", VisitorID = ".$visitor["ID"]." where visitorid = " . intval($_SESSION["Session"]["VisitorID"]));
					$this->mDatabase->query("update js_tracking_visitors a, js_tracking_visitors b set a.Visits= a.Visits + b.Visits where b.id = " . intval($_SESSION["Session"]["VisitorID"]) . " and a.id = " . intval($visitor["ID"]));
					$this->mDatabase->query("delete from js_tracking_visitors where id = " . intval($_SESSION["Session"]["VisitorID"]));

					$cook = array("id" => $visitor["ID"], "key" => md5($visitor["Code"]));
					setcookie("jscuqid", base64_encode(serialize($cook)), time() + 86400 * 730, "/");

					$_SESSION["Session"]["VisitorID"] = $visitor["ID"];
				} else {
					$this->mDatabase->query("update js_tracking_sessions set UserID = " .intval($_SESSION["UserID"]). "  where visitorid = " . intval($_SESSION["Session"]["VisitorID"]));
					$rememberme = 'no'; if ($remember) $rememberme = 'yes';
					if ($_SESSION["UserID"]) $this->mDatabase->query("update js_tracking_visitors set UserID = " .intval($_SESSION["UserID"]). ", RememberMe = '$rememberme' where id = " . intval($_SESSION["Session"]["VisitorID"]));
				}
				$_SESSION["Session"]["UserID"] = $_SESSION["UserID"];

				#load existing cart if applicable
				if ($visitor["CartID"]) {
					$cart = $this->mDatabase->getRow("select * from js_tracking_cart where id = " . intval($visitor["CartID"]));
					if ($cart["CartData"]) $cart = unserialize($cart["CartData"]);
					if ($cart["products"]) $_SESSION["cart"] = $cart;
				} else {
					#check if the existing session has a cart
					$cart = $this->mDatabase->getRow("select * from js_tracking_cart where sessionid = " . intval($_SESSION["Session"]["ID"]) . " and status <> 'processed'");
					if ($cart)  $this->mDatabase->query("update js_tracking_visitors set cartid = " . intval($cart["ID"]) . "  where id = " . intval($_SESSION["Session"]["VisitorID"]));
					
				}
		}

		# call this function after login
		function logout() {
				$this->mDatabase->query("update js_tracking_visitors set RememberMe = 'no' where id = " . intval($_SESSION["Session"]["VisitorID"]));
				$_SESSION["cart"] = array();
				$this->resetSession();
		}

		/** comment here */
		function updateSession($data) {
			if ($_SESSION["Session"] && !$_SESSION["Session"]["Browser"]) {
				$this->mDatabase->query("update js_tracking_sessions set Browser = '".addslashes2(serialize($data))."' where id = " . intval($_SESSION["Session"]["ID"]));
				$_SESSION["Session"]["Browser"] = $data;
			}
			
		}


		/** comment here */
		function trackPage() {
			if ($this->stop) Return false;
			$this->mDatabase->query("insert into js_tracking_actions(SessionID, CartID, Type, TimeStamp, Value, Status) values(".intval($_SESSION["Session"]["ID"]).", 0, 'page', unix_timestamp(), '".addslashes($_SERVER["REQUEST_URI"])."', 'pending')");			
			$this->mDatabase->query("update js_tracking_sessions set LastUpdated = unix_timestamp() where id = " . intval($_SESSION["Session"]["ID"]));
		}

		/** comment here */
		function trackAction($action, $value) {
			$this->mDatabase->query("insert into js_tracking_actions(SessionID, CartID, Type, TimeStamp, Value, Status) values(".intval($_SESSION["Session"]["ID"]).", ".intval($_SESSION["cart"]["id"]) . ", '".addslashes2($action)."', unix_timestamp(), '".$value."', 'pending')");
			$this->mDatabase->query("update js_tracking_sessions set LastUpdated = unix_timestamp() where id = " . intval($_SESSION["Session"]["ID"]));
		}

		/** comment here */
		function trackCartAction($action, $value) {
			switch($action) {
				case "add-product":  
				case "remove-product":  
				case "update-product":  
				case "add-favourite":  
				case "remove-favourite":  
				case "apply-promo":  
				case "apply-promo-failed":  
				case "order-failed":  
				case "quick-search":  
				case "full-search":  
					$this->mDatabase->query("insert into js_tracking_actions(SessionID, CartID, Type, TimeStamp, Value, Status) values(".intval($_SESSION["Session"]["ID"]).", ".intval($_SESSION["cart"]["id"]) . ", '".addslashes2($action)."', unix_timestamp(), '".$value."', 'pending')");
					break;			
				case "checkout":
					$this->mDatabase->query("insert into js_tracking_actions(SessionID, CartID, Type, TimeStamp, Value, Status) values(".intval($_SESSION["Session"]["ID"]).", ".intval($_SESSION["cart"]["id"]) . ", '".addslashes2($action)."', unix_timestamp(), '".$value."', 'pending')");
					$this->updateCartStatus("checkout");
					break;			
				case "order":
					$this->mDatabase->query("insert into js_tracking_actions(SessionID, CartID, Type, TimeStamp, Value, Status) values(".intval($_SESSION["Session"]["ID"]).", ".intval($_SESSION["cart"]["id"]) . ", '".addslashes2($action)."', unix_timestamp(), '".$value."', 'pending')");
					$this->updateCartStatus("completed");
					break;			

			}
		}

		/** comment here */
		function newCart() {
				$this->mDatabase->query("insert into js_tracking_cart(SessionID, VisitorID, TimeStamp, LastUpdated, Status, CartValue, ProcessingStatus) values('".intval($_SESSION["Session"]["ID"])."', '".intval($_SESSION["Session"]["VisitorID"])."', unix_timestamp(), unix_timestamp(), 'created', 0, 'processed')");
				$_SESSION["cart"]["id"] = $this->mDatabase->getLastID();
				if ($_SESSION["UserID"]) $this->mDatabase->query("update js_tracking_visitors set CartID = " . intval($_SESSION["cart"]["id"]) . " where id =" . intval($_SESSION["Session"]["VisitorID"]));
				$this->saveCart();
		}

		/** comment here */
		function saveCart() {
			$cart = array("cart" => $_SESSION["cart"], "favourites" => $cartdata["favourites"]);
//			if ($changeStatus) 
//				$this->mDatabase->query("update js_tracking_cart set Status = '".addslashes2($changeStatus)."', LastUpdated = unix_timestamp(), CartData = '".addslashes2(serialize($_SESSION["cart"]))."', CartValue = '".floatval($_SESSION["cart"]["total"])."', ProcessingStatus = 'pending' where id = " . intval($_SESSION["cart"]["id"]));
//			else 
				$this->mDatabase->query("update js_tracking_cart set LastUpdated = unix_timestamp(), CartData = '".addslashes2(serialize($_SESSION["cart"]))."', CartValue = '".floatval($_SESSION["cart"]["total"])."', ProcessingStatus = 'pending' where id = " . intval($_SESSION["cart"]["id"]));
			}

			/** comment here */
			function updateCartStatus($changeStatus) {
				$done = "";
				if ($changeStatus == "completed") {
					$done = ", ProcessingStatus = 'processed' ";
					$this->mDatabase->query("update js_tracking_visitors set cartid = 0 where id = " . intval($_SESSION["Session"]["VisitorID"]));
				}
				$this->mDatabase->query("update js_tracking_cart set Status = '".addslashes2($changeStatus)."', LastUpdated = unix_timestamp() $done where id = " . intval($_SESSION["cart"]["id"]));
			}

		/** comment here */
		function decodeIP() {
			if ($_SERVER["HTTP_X_FORWARDED_FOR"]) $ip = $_SERVER["HTTP_X_FORWARDED_FOR"]; else $ip = $_SERVER["REMOTE_ADDR"]; 
			$ret = array();
			$ret["IP"] = sprintf('%u',ip2long($ip));
			
			
			try {
				$txt = file_get_contents('http://api.db-ip.com/addrinfo?addr='.$ip.'&api_key=free');
				$data = json_decode($txt);
				
				if ($data->country == "ZZ")  {
					$ret["CountryID"] = 3;
					$ret["StateID"] = 44;
					$ret["City"] = "Toronto";
				} else {
					$country = $this->data->getCountryByCode($data->country);
					if ($country) $ret["CountryID"] = $country["ID"]; else $ret["CountryID"] = 3;
					$state = $this->data->getStateByCode($ret["CountryID"], $data->stateprov);
					if ($state) $ret["StateID"] = $state["ID"]; else $ret["StateID"] = 44; 
					$ret["City"] = $data->city;
				}
			} catch (Exception $ex) {
				$ret["CountryID"] = 3;
				$ret["StateID"] = 44;
				$ret["City"] = "Toronto";
			}
			Return $ret;
		}


		function gen_uuid() {
			return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
				// 32 bits for "time_low"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

				// 16 bits for "time_mid"
				mt_rand( 0, 0xffff ),

				// 16 bits for "time_hi_and_version",
				// four most significant bits holds version number 4
				mt_rand( 0, 0x0fff ) | 0x4000,

				// 16 bits, 8 bits for "clk_seq_hi_res",
				// 8 bits for "clk_seq_low",
				// two most significant bits holds zero and one for variant DCE1.1
				mt_rand( 0, 0x3fff ) | 0x8000,

				// 48 bits for "node"
				mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
			);
		}

		function mainSwitch() {

			switch($this->mOperation) {
				case "update-session": $this->updateSession($_GET["value"]); die("ok");
				case "track-link": $this->trackAction("link", $_GET["value"]); die("ok");
				case "track-click": $this->trackAction("click", $_GET["value"]);die("ok");
				default: Return $this->defaultSwitch();
			}
		}
############ DISPLAY ############
	}
?>