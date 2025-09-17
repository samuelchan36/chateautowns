<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CAccessAdmin extends CSectionAdmin{

	var $table = "cms_users";
	var $actions = array("edit", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Logos", "Logo");
	var $mClass = "CLogo";
	


  /** comment here */
  function __construct() {
	parent::__construct();
  }


  /** comment here */
  function display() {

	  if (isset($_SESSION["gUserID"]) && $_SESSION["gUserID"]) redirect("/cms");
		$tpl = template2("lib/class/modules/access/view.html");
		$tpl->assign("Logo", CONF_SITE_LOGO_ALT);
		Return $tpl->output();
  }

 
    /** comment here */
  function displayVerify() {

	  if (isset($_SESSION["gUserID"]) && $_SESSION["gUserID"]) redirect("/cms");
	  if (!isset($_SESSION["gMFAUserID"]) || !$_SESSION["gMFAUserID"]) {
		  error("Invalid Request");
		  redirect("/cms/access/login");
	  }
		$tpl = template2("lib/class/modules/access/verify.html");
		$tpl->assign("Logo", CONF_SITE_LOGO_ALT);
		Return $tpl->output();
  }

  /** comment here */
  function doVerify() {
			$sql = "select * from cms_users where id = " . intval($_SESSION["gMFAUserID"]);
			$data = $this->mDatabase->getRow($sql);
//			die2($data);
			if (!$data)  {
				error("Invalid username or password");
				redirect("/cms/access/login");
			}	
	
			if ($data["MFAExpiry"] < time()) {
				
				error("Invalid verification code. Please request a new code.");
				redirect("/cms/access/login");
			}
			if ($data["MFACode"] != $_POST["VerificationCode"]) {
					if ($data["MFAAttempts"] < 5) {
						$this->mDatabase->query("update cms_users set MFAAttempts = MFAAttempts + 1 where id = "  . intval($_SESSION["gMFAUserID"]));
						error("Invalid verification code. Please check your input and try again.");
						redirect("/cms/access/verify");
					}else {
						$this->mDatabase->query("update cms_users set status = 'disabled', MFAAttempts = 0, MFACode = '', MFAExpiry = 0 where id = "  . intval($_SESSION["gMFAUserID"]));
						error("Your account has been blocked due to too many attempts. Please contact your IT consultant for assistance");
						redirect("/cms/access/login");
					}
					
			}

			#all checks passed, good to go
			$this->mDatabase->query("update cms_users set MFAAttempts = 0, MFACode = '', MFAExpiry = 0  where id = "  . intval($_SESSION["gMFAUserID"]));
			$this->mDatabase->query("insert into cms_user_logins(UserID, TimeStamp, IPAddress) values(".intval($_SESSION["gMFAUserID"]).", unix_timestamp(), '".addslashes2($_SERVER["REMOTE_ADDR"])."')");
			$_SESSION["gMFAUserID"] = 0;
			$_SESSION["gUserID"] = $data["ID"];
			redirect("/cms");
  }

 /** comment here */
  function doLogin($username, $password) {
//		  if ($_SESSION["gUserID"]) redirect("/cms");
			$sql = "select * from cms_users where (email = '".addslashes2($username)."' or username = '".addslashes2($username)."')";
			$data = $this->mDatabase->getAll($sql);
			$user = array();
			foreach ($data as $key=>$val) {
				if (password_verify($password, $val["PasswordHash"])) {
					$user = $val;
					break;
				}
			}

			if ($user) {

				if ($user["Status"] != "enabled") {
					error("Your account is deactivated. Please contact your IT consultant for assistance.");
					redirect("/cms/access/login");
				}
				
				if (MFA_ENABLED == "yes") {
						$mfa = $this->mDatabase->getRow("select * from cms_user_logins where status='active' and UserID = " . $user["ID"] . " and IPAddress = '".addslashes2($_SERVER["REMOTE_ADDR"])."'");
						
						#check last MFA
						if ($mfa && $mfa["TimeStamp"] > 0 && (time() - $mfa["TimeStamp"] <= MFA_TIMEOUT * 86400) ) {
								# authorized less than MFA_TIMEOUT days ago, let them in directly.
								$_SESSION["gUserID"] = $user["ID"];
								redirect("/cms");
						} else {
								if ($mfa) $this->mDatabase->query("update cms_user_logins set status = 'expired' where UserID = " . $user["ID"] . " and IPAddress = '".addslashes2($_SERVER["REMOTE_ADDR"])."'"); # deactivate expired authorization
								$_SESSION["gMFAUserID"] = $user["ID"];
								$codes = array(rand(100000,999999), rand(100000,999999), rand(100000,999999), rand(100000,999999), rand(100000,999999), rand(100000,999999));
								$code = $codes[rand(0,5)];
								$this->mDatabase->query("update cms_users set MFAAttempts = 0, MFACode = '$code', MFAExpiry = ".(time() + 600)." where id = "  . intval($user["ID"]));

								$msg    = "<html><head><head><body><p style='font-size: 20px;'>Your six digits verification code is ".$code. "<br><br>Please note that the code expires in 10 minutes.</p></body></html>";
								$email = new CEmail();
								$email->send($user["Email"], COMPANY . " Admin Panel Verification Code", $msg);

								redirect("/cms/access/verify");
						}
				} else {
					$_SESSION["gUserID"] = $user["ID"];
					redirect("/cms");
				}

			} else {
				error("Invalid username or password");
				redirect("/cms/access/login");
			}
	
  }

    /** comment here */
  function displayReset($code) {

	  if ($_SESSION["gUserID"]) redirect("/cms");
		$tpl = template2("lib/class/modules/access/reset-password.html");
		$tpl->assign("Logo", CONF_SITE_LOGO_ALT);
		$tpl->assign("Code", $code);
		Return $tpl->output();
  }


  /** comment here */
  function doReset() {
		$sql = "select * from cms_users where email = '".addslashes2($_POST["Email"])."'";
		$data = $this->mDatabase->getRow($sql);
		if ($data["RecoveryCode"] == $_POST["Code"] && $data["RecoveryCodeExpiry"] >= time()) {

			$this->mDatabase->query("update cms_users set recoverycode = '', recoverycodeexpiry = 0, password = '', passwordhash = '".addslashes2(password_hash($_POST["Password"], PASSWORD_DEFAULT ))."' where id = " . $data["ID"]);
//			include "../lib/plugins/mail/phpmailer.class.php";
//			ini_set("sendmail_from", SENDER_FROM);
//
//			$mail = new PHPMailer();
//			$mail->From = EMAIL_FROM_ADDRESS;
//			$mail->Sender = EMAIL_FROM_ADDRESS;
//			$mail->FromName = EMAIL_FROM;
//			$mail->AddAddress($data["Email"],$data["Email"]);
//			$mail->AddReplyTo(EMAIL_FROM_ADDRESS, EMAIL_FROM);
//
//			$mail->Priority = 3;
//			$mail->WordWrap = 250;  // set word wrap to 50 characters
//			$mail->IsHTML(true);
//
//			$mail->Subject = "Reset your password";
//			$mail->Body    = "<html><head><head><body><p style='font-size: 20px;'>You have requested a password reset, please use this link to set a new password. <br><br><a href='http://scma.joeyai.cloud/cms/access/reset-password/" . htmlentities($recovery_code) . "'>Reset password</a><br><br>Please note that this link will exire in 3 days.</p></body></html>";
//			$mail->AltBody    = $mail->Body;
//			$ret = $mail->Send();			
			error("Your password has been reset");
		} else {
			error("Sorry, your password recovery code is not valid");
		}
		redirect("/cms/access/login");

  }


    /** comment here */
  function displayForgot() {

	  if ($_SESSION["gUserID"]) redirect("/cms");
		$tpl = template2("lib/class/modules/access/forgot.html");
		$tpl->assign("Logo", CONF_SITE_LOGO_ALT);
		Return $tpl->output();
  }



  /** comment here */
  function doForgot($email) {
		$sql = "select * from cms_users where email = '".addslashes2($email)."'";
		$data = $this->mDatabase->getRow($sql);
		if ($data["Email"]) {

			$recovery_code = md5($data["ID"] .  uniqid($data["Email"]) . $data["FirstName"]);
			$this->mDatabase->query("update cms_users set recoverycode = '".addslashes2($recovery_code)."', recoverycodeexpiry = unix_timestamp() + 3 * 86400 where id = " . $data["ID"]);
//			include "../lib/plugins/mail/phpmailer.class.php";
//			ini_set("sendmail_from", SENDER_FROM);

			$msg    = "<html><head><head><body><p style='font-size: 20px;'>You have requested a password reset, please use this link to set a new password. <br><br><a href='http://".$_SERVER["HTTP_HOST"]."/cms/access/reset-password/" . htmlentities($recovery_code) . "'>Reset password</a><br><br>Please note that this link will exire in 3 days.</p></body></html>";

			$email = new CEmail();
			$email->send($data["Email"], "Reset your password", $msg);
			error("Instructions on resettting your password have been emailed to " . $data["Email"]);
		} else {
			error("Sorry, this email address doesn't exist");
		}
				redirect("/cms/access/login");

  }



  /** comment here */
  function doLogout() {
	$_SESSION["gUserID"] = 0;
	redirect("/cms/access/login");
  }


  /** comment here */
  function mainSwitch() {
	switch($this->mOperation) {
		case "login": Return $this->display();
		case "verify": Return $this->displayVerify();
		case "do-verify": Return $this->doVerify();
		case "do-login": Return $this->doLogin($_POST["Username"], $_POST["Password"]);
		case "reset-password": Return $this->displayReset($_GET["code"]);
		case "do-reset": Return $this->doReset();
		case "forgot": Return $this->displayForgot();
		case "do-forgot": Return $this->doForgot($_POST["Email"]);
		case "logout": Return $this->doLogout();
		default:
		  Return CSectionAdmin::mainSwitch();
	}
  }
}

?>
