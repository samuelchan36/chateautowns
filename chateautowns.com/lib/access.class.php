<?php

	class CAccess extends CDefault {

		var $use_mfa = false;
		var $mfa_timeout = 30; // days

		function __construct() {
			parent::__construct();
			if (isset($GLOBALS["doc"])) $GLOBALS["doc"]->skip_history = true;
		}

		/** comment here */
		function displayLogin($post_login = "") {
			if (!$post_login) $post_login = $_SESSION["LastPage"];
			$tpl = _tpl("access/login");
			$tpl->assign("PostLogin", $post_login);
			Return $tpl->output();
		}

		/** comment here */
		function displayForgot() {
			$tpl = _tpl("access/forgot");
			Return $tpl->output();
			
		}

		/** comment here */
		function displayReset($code) {
			$tpl = _tpl("access/reset");
			$tpl->assign("Code", $code);
			Return $tpl->output();
		}

		/** comment here */
		function login() {
			$data = $this->mDatabase->getAll("select * from users  where status <> 'disabled' and email = '".addslashes2($_POST["Email"])."'");
			$user = array();
			if ($data) {
					foreach ($data as $key=>$val) {
						if (password_verify($_POST["Password"], $val["PasswordHash"])) {
							$user = $val;
						}
					}

					if ($this->use_mfa) {
							$mfa = $this->mDatabase->getRow("select * from user_logins where status='active' and UserID = " . $user["ID"] . " and IPAddress = '".addslashes2($_SERVER["REMOTE_ADDR"])."'");
							
							#check last MFA
							if ($mfa && $mfa["TimeStamp"] > 0 && (time() - $mfa["TimeStamp"] <= MFA_TIMEOUT * 86400) ) {
									# authorized less than MFA_TIMEOUT days ago, let them in directly.
									$_SESSION["UserID"] = $user["ID"];
									$_SESSION["User"] = $user;
									die(json_encode(array("response" => "ok", "message" => "", "error" => 0, "url" => $_POST["PostLogin"] ? $_POST["PostLogin"] : "/")));
							} else {
									if ($mfa) $this->mDatabase->query("update user_logins set status = 'expired' where UserID = " . $user["ID"] . " and IPAddress = '".addslashes2($_SERVER["REMOTE_ADDR"])."'"); # deactivate expired authorization
									$_SESSION["MFAUserID"] = $user["ID"];
									$codes = array(rand(100000,999999), rand(100000,999999), rand(100000,999999), rand(100000,999999), rand(100000,999999), rand(100000,999999));
									$code = $codes[rand(0,5)];
									$this->mDatabase->query("update users set MFAAttempts = 0, MFACode = '$code', MFAExpiry = ".(time() + 600)." where id = "  . intval($user["ID"]));

									$email = new CEmail(array("Code" => $code));
									$email->sendRich($user["Email"], COMPANY . " Admin Panel Verification Code", "account/verify-email");

									die(json_encode(array("response" => "ok", "message" => "", "error" => 0, "url" => "/access/verify")));

							}
					} else {
								$_SESSION["UserID"] = $user["ID"];
								$_SESSION["User"] = $user;
								die(json_encode(array("response" => "ok", "message" => "", "error" => 0, "url" => $_POST["PostLogin"] ? $_POST["PostLogin"] : "/")));
					}




			} else {
				die(json_encode(array("response" => "no", "message" => "Invalid username", "error" => 0, "url" => $_SESSION["LastPage"])));	
			}

		}

		/** comment here */
		function logout() {
				$_SESSION["UserID"] = 0;
				$_SESSION["User"] = array();
				redirect("/");
		}

		/** comment here */
		function doReset() {
				$sql = "select * from users where email = '".addslashes2($_POST["Email"])."'";
				$data = $this->mDatabase->getRow($sql);
				if ($data["RecoveryCode"] == $_POST["Code"] && $data["RecoveryCodeExpiry"] >= time()) {

					$this->mDatabase->query("update users set recoverycode = '', recoverycodeexpiry = 0, password = '', passwordhash = '".addslashes2(password_hash($_POST["Password"], PASSWORD_DEFAULT ))."' where id = " . $data["ID"]);
					die(json_encode(array("response" => "ok", "message" => "Your password has been reset. <a href='/access/login'>Click here </a> to login to your account", "error" => 0, "url" => "")));
				} else {
					die(json_encode(array("response" => "no", "message" => "Sorry, your password recovery code is not valid", "error" => 0, "url" => "")));

				}
					
			
		}

		/** comment here */
		function doForgot() {
			$sql = "select * from users where email = '".addslashes2($_POST["Email"])."'";
			$data = $this->mDatabase->getRow($sql);
			if ($data["Email"]) {

				$recovery_code = md5($data["ID"] .  uniqid($data["Email"]) . $data["FirstName"]);
				$this->mDatabase->query("update users set recoverycode = '".addslashes2($recovery_code)."', recoverycodeexpiry = unix_timestamp() + 3 * 86400 where id = " . $data["ID"]);
				
				$params = array("RecoveryCode" => htmlentities($recovery_code));
				$email = new CEmail($params);
				$email->sendRich($data["Email"], "Reset your password", "account/reset-password");
				die(json_encode(array("response" => "ok", "message" => "Instructions on resettting your password have been emailed to " . $data["Email"], "error" => 0, "url" => "")));
			
			} else {
				die(json_encode(array("response" => "no", "message" => "Sorry, this email address doesn't exist", "error" => 0)));
				
			}
			
		}


		/** comment here */
	  function displayVerify() {

		  if (isset($_SESSION["UserID"]) && $_SESSION["UserID"]) redirect("/");
		  if (!isset($_SESSION["MFAUserID"]) || !$_SESSION["MFAUserID"]) {
			  error("Invalid Request");
			  redirect("/");
		  }
			$tpl = _tpl("access/verify");
			Return $tpl->output();
	  }

	  /** comment here */
	  function doVerify() {
				$sql = "select * from users where id = " . intval($_SESSION["MFAUserID"]);
				$data = $this->mDatabase->getRow($sql);

				if (!$data)  {
					die(json_encode(array("response" => "no", "message" => "Invalid username or password", "error" => 0)));
				}	
		
				if ($data["MFAExpiry"] < time()) {
					die(json_encode(array("response" => "no", "message" => "Invalid verification code. Please request a new code.", "error" => 0)));
				}
				if ($data["MFACode"] != $_POST["VerificationCode"]) {
						if ($data["MFAAttempts"] < 5) {
							$this->mDatabase->query("update users set MFAAttempts = MFAAttempts + 1 where id = "  . intval($_SESSION["MFAUserID"]));
							error("Invalid verification code. Please request a new code.");
						}else {
							$this->mDatabase->query("update users set status = 'disabled', MFAAttempts = 0, MFACode = '', MFAExpiry = 0 where id = "  . intval($_SESSION["MFAUserID"]));
							die(json_encode(array("response" => "no", "message" => "Your account has been blocked due to too many attempts. Please contact your IT consultant for assistance", "error" => 0)));
						}
						
				}

				#all checks passed, good to go
				$this->mDatabase->query("update users set MFAAttempts = 0, MFACode = '', MFAExpiry = 0  where id = "  . intval($_SESSION["MFAUserID"]));
				$this->mDatabase->query("insert into user_logins(UserID, TimeStamp, IPAddress) values(".intval($_SESSION["MFAUserID"]).", unix_timestamp(), '".addslashes2($_SERVER["REMOTE_ADDR"])."')");
				$_SESSION["MFAUserID"] = 0;
				$_SESSION["UserID"] = $data["ID"];
				$_SESSION["User"] = $data;
				
				die(json_encode(array("response" => "ok", "message" => "", "error" => 0, "url" => $_POST["PostLogin"] ? $_POST["PostLogin"] : "/")));
	  }

		function mainSwitch() {
			switch($this->mOperation) {
				case "login": Return $this->displayLogin();
				case "forgot-password": Return $this->displayForgot();
				case "reset-password": Return $this->displayReset($_GET["code"]);
				case "verify": Return $this->displayVerify();
				case "do-login": Return $this->login();
				case "do-logout": Return $this->logout();
				case "do-forgot": Return $this->doForgot();
				case "do-reset-password": Return $this->doReset();
				case "do-verify": Return $this->doVerify();
				default: Return $this->defaultSwitch();
			}
		}

}



?>