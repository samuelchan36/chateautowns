<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

	class CEmail {
		
		var $mAttachments = array();
		var $mHeaders = "";
		var $template = "";
		var $mParams = array();
		var $mFrom = "";
		var $mFromAddress = "";
		var $replyTo = "";
		var $priority = 3;

		var $Host = SMTP_HOST;
		var $Port = SMTP_PORT;
		var $Username = SMTP_USER;
		var $Password = SMTP_PWD;
		var $is_smtp = false;

		var $mailer;

		var $root_folder = "";

		/** comment here */
		function __construct($params = array()) {
			$this->mParams = $params;
			$this->template = "/html/" . $_SESSION["lang"] . "/email.master.html";
			if (USE_SMTP == "yes")  $this->is_smtp = true;
		}

		/** comment here */
		function addHeader($txt) {
			$this->mHeaders .= $txt . "\n";
		}
	

		/** comment here */
		function send($to, $subject, $message) {

			if (!$this->mFrom) $this->mFrom = EMAIL_FROM;
			if (!$this->mFromAddress) $this->mFromAddress = SENDER_FROM;

			$this->mail($this->mFrom, $this->mFromAddress, $to, $to, $subject, $message);
		}


		/** comment here */
		function sendtoAdmin($subject, $message) {
			if (!$this->mFrom) $this->mFrom = EMAIL_FROM;
			if (!$this->mFromAddress) $this->mFromAddress = SENDER_FROM;

			$this->mail($this->mFrom, $this->mFromAddress, EMAIL_ADMIN, EMAIL_ADMIN, $subject, $message);

			
		}
		
		/** comment here */
		function sendRich($to, $subject, $template) {
			$tpl = _email($template);
			foreach ($this->mParams as $key=>$val) {
				$tpl->assign($key, nl2br($val));
			}
			$tpl->assign("SERVER", "//" . $_SERVER["HTTP_HOST"]);
			$txt = $tpl->output();

			if (!$this->mFrom) $this->mFrom = EMAIL_FROM;
			if (!$this->mFromAddress) $this->mFromAddress = SENDER_FROM;

			$this->mail($this->mFrom, $this->mFromAddress, $to, $to, $subject, $txt, $this->mAttachments);
		}

		/** comment here */
		function sendtoAdminRich($subject, $template) {
			$tpl = _email($template);
			foreach ($this->mParams as $key=>$val) {
				$tpl->assign($key, nl2br($val));
			}
			$txt = $tpl->output();

			if (!$this->mFrom) $this->mFrom = EMAIL_FROM;
			if (!$this->mFromAddress) $this->mFromAddress = SENDER_FROM;
			$this->mail(EMAIL_FROM, SENDER_FROM, EMAIL_ADMIN, EMAIL_ADMIN, $subject, $txt, $this->mAttachments);
		}

		/** comment here */
		private function mail($from, $fromAddress, $to, $toAddress, $subject, $message, $attachments = array()) {

			$ret = false;

			try {
					if (!$toAddress)  Return false;
					$tpl = template2(ROOT_DIR . $this->template);
					$tpl->assign("SERVER", "//" . $_SERVER["HTTP_HOST"]);

					$tpl->assign("Body", $message);
					$txt = $tpl->output();
//					jsdebug($txt);

					ini_set("sendmail_from", SENDER_FROM);

					$this->mailer = new PHPMailer(true);
					$this->mailer->SMTPDebug = false;//SMTP::DEBUG_SERVER; 
					$this->mailer->CharSet = 'utf-8';
					$this->mailer->Encoding = 'base64';

					if ($this->is_smtp) {
						$this->mailer->isSMTP();	// Send using SMTP
						$this->mailer->SMTPAuth = true;
						$this->mailer->Host = $this->Host;
						$this->mailer->Port = $this->Port;
						$this->mailer->Username = $this->Username;
						$this->mailer->Password = $this->Password;
						$this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
					} else {
						$this->mailer->isMail(); // use mail()
					}
							
					$this->mailer->setFrom($fromAddress, $from);
					$this->mailer->addAddress($toAddress,$to);     

					if ($this->replyTo) $this->mailer->addReplyTo($this->replyTo, $this->mFrom);

					if ($this->priority) $this->mailer->Priority = $this->priority;

					if ($attachments) {
						foreach ($attachments as $key=>$val) {
							$this->mailer->addAttachment($val);
						}
					}


					$this->mailer->isHTML(true);                                  
					$this->mailer->Subject = $subject;
					$this->mailer->Body    = $txt;
					$ret = $this->mailer->send();

					$this->mailer->clearAddresses();					
			} catch (Exception $ex) {
					jsdebug($ex);
			}
			Return $ret;
		}


	}
?>