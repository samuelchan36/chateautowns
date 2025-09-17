<?php

	class CMain extends CDefault {



		function __construct() {
			parent::__construct();

		}



	/** comment here */
		function doLogin() {
			die(json_encode(array("response" => "no", "message" => "Invalid credentials.", "error" => 101)));						
		}

		function doRegister() {

				$leadID = 0;

				if (USE_RECAPTCHA == "yes") {
						require 'lib/plugins/re-captcha/autoload.php';
						$siteKey = RECAPTCHA_SITE_KEY;
						$secret = RECAPTCHA_SECRET;

						if(!empty($_POST['g-recaptcha-response'])) {
								$recaptcha = new \ReCaptcha\ReCaptcha($secret);
								$resp = $recaptcha->setScoreThreshold(0.8)->setExpectedAction("submit")->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);
								if($resp->isSuccess()) {
									$_POST["RecaptchaScore"] = $resp->getScore();
									# USING RECAPTCHA, PASSED
										$leadID = $this->jCrm($_POST, $_POST["cid"]);	

//										$content = new CContent("inquiries");
//										$content->registerForm();
//										$content->mRowObj->TimeStamp = time();
//										$content->easySave();

//										$email  = new CEmail($_POST);
//										$email->sendRich("info@midtownoakville.com",  "New Midtown Inquiry", "notification");

										$email  = new CEmail($_POST);
//										$email->sendRich($_POST["Email"],  "Thank you for your registration", "contact");

										die(json_encode(array("response" => "ok", "message" => "<p>Thank you for your registration.</p>", "error" => 0, "id" => $leadID)));
								} else {
									die(json_encode(array("response" => "no", "message" => "Captcha code not valid.", "error" => 100)));						
								}
							} else {
								die(json_encode(array("response" => "no", "message" => "Captcha code not present.", "error" => 101)));						
							}
				} else {

					# NOT USING RECAPTCHA
//					$leadID = $this->jCrm($_POST, $_POST["cid"]);

//					$email  = new CEmail($_POST);
//					$email->sendRich($_POST["Email"],  "Thank you for your registration", "thank-you");

					die(json_encode(array("response" => "ok", "message" => "", "error" => 0, "id" => $leadID)));
				}
				die(json_encode(array("response" => "no", "message" => "An error has occured.", "error" => 900)));
		}


		/** comment here */
		function jCrm($data, $cid = 0) {
			$url = "https://crm.joeyai.email/do/register-json.php";
			$data ["aid"] = JAI_ACCOUNT_ID;
			$data["apikey"] = JAI_APIKEY;
			$data["IPAddress"] = $_SERVER["HTTP_X_FORWARDED_FOR"] ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
			$data["SourceIP"] = $_SERVER['SERVER_ADDR'] ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
			if (!$data["Campaign"]) $data["Campaign"] = $_SESSION["utm_campaign"];
			if (!$data["AcquiredVia"]) $data["AcquiredVia"] = $_SESSION["utm_source"];
			if (isset($_POST["RecaptchaScore"])) $data["SpamScore"] = 100 * $_POST["RecaptchaScore"]; else $data["SpamScore"] = 50;
			if (isset($_POST["jsTimeStamp"])) {
				$data["AntiSpam"] = array("TimeSpent" => time() - $_POST["jsTimeStamp"], "ScrollEvents" => $_POST["jsInteractions1"], "MouseEvents" => $_POST["jsInteractions2"], "KeyEvents" => $_POST["jsInteractions3"], "FormClicked" => $_POST["jsInteractions4"]);
				if (!$data["AntiSpam"]["ScrollEvents"]) $data["SpamScore"] = $data["SpamScore"] - 10;
				if ($data["AntiSpam"]["MouseEvents"] < 50) $data["SpamScore"] = $data["SpamScore"] - 20;
				if ($data["AntiSpam"]["KeyEvents"] < 1) $data["SpamScore"] = $data["SpamScore"] - 10;
				if ($data["AntiSpam"]["FormClicked"] =="no") $data["SpamScore"] = $data["SpamScore"] - 30;
				if ($data["AntiSpam"]["TimeSpent"] < 5) $data["SpamScore"] = $data["SpamScore"] -  90;
				if ($data["AntiSpam"]["TimeSpent"] < 10) $data["SpamScore"] = $data["SpamScore"] -  50;
			}

//			die2($data);
			

			try {
					$ch = curl_init($url);
					curl_setopt($ch, CURLOPT_POST      ,1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
					curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($ch,CURLOPT_SSL_VERIFYHOST, false);
					curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
					curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
					curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					  'Content-Type: application/json'
					));
					$ret = json_decode(curl_exec($ch));
					Return $ret->id;
			} catch (Exception $ex){
					Return 0;
			}
			
		}


		function mainSwitch() {
			switch($this->mOperation) {
				case "do-login": Return $this->doLogin();
				default: Return $this->defaultSwitch();
			}
		}

}

?>