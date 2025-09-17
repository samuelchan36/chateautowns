<?php   
/** Mail Manager
* @package System
* @since Mar 2003
* @author Son Nguyen
*/

class CMailer extends phpmailer {
	var $From     = "";
    var $FromName = "";
	var $PluginDir = "";

	/** constructor */
	function CMailer() {
		$this->Mailer = "mail";  // tmp
	}
	/** old system, don't use it anymore(?)
	function prepareMessage($fields,$htmlPage,$txtPage) {
		$this->gHelper = $GLOBALS['gHelper'];

		$html = $this->gHelper->getContentPage($htmlPage);
		$html = $this->gHelper->prepareEmailPage($html);
		$html = $this->gHelper->replaceAry($fields,$html);

		$txt = $this->gHelper->getContentPage($txtPage);
		$txt = $this->gHelper->replaceAry($fields,$txt);
				
		$this->Body = $html;
		$this->AltBody = $txt;
		$this->Subject = $this->gHelper->getSubjectContentPage($htmlPage);
	}

	// put the message in the queue with delayed time
	function setDelayedMsg($userid,$htmlPage,$txtPage) {
		$this->gDatabase = $GLOBALS['gDatabase'];
		$this->gHelper = $GLOBALS['gHelper'];

		$html = $this->gHelper->getContentPage($htmlPage);
		$this->Body = $this->gHelper->prepareEmailPage($html);

		$this->AltBody = $this->gHelper->getContentPage($txtPage);
		$this->Subject = $this->gHelper->getSubjectContentPage($htmlPage);
		$this->makeDelayedMsg($userid,43000); // 12hr delay
	}

	// create delay message 
	function makeDelayedMsg($userid,$delay) {
		$this->gDatabase = $GLOBALS['gDatabase'];
		$this->gHelper = $GLOBALS['gHelper'];

		// put the delay msg in
		$fields = array('fromname'=>$this->FromName,
						'fromemail'=>$this->From,
						'subject'=>$this->Subject,
						'html'=>$this->Body,
						'plaintext'=>$this->AltBody,
		);
		$fields = $this->gDatabase->addSlashAry($fields);
		$insertQuery = $this->gDatabase->insertQuery($fields);
		$sql = "INSERT INTO nuke_newsletter $insertQuery";
		$result = $this->gDatabase->query($sql);
		$newsletter_id = mysql_insert_id();

		// put the user who will get this msg in
		$sql = "SELECT * FROM nuke_users WHERE uid='$userid'";
		$row = $this->gDatabase->getRow($sql);
		$sendtoemail = $row['email'];


		$sendtoname = $this->gHelper->getFullName($row);
		$fields = array('uid'=>$userid,
						'newsletter_id'=>$newsletter_id,
						'sendtoemail'=>$sendtoemail,
						'sendtoname'=>$sendtoname,
						'stamp'=>time(),
						'mail_delay'=>$delay,
						'lookup_table'=>'nuke_users',
		);
		$fields = $this->gDatabase->addSlashAry($fields);
		$insertQuery = $this->gDatabase->insertQuery($fields);
		$sql = "INSERT INTO nuke_newsletter_tmp $insertQuery";
		$result = $this->gDatabase->query($sql);
	}
	*/
	
} // class

/** testing place
// Instantiate your new class
$mail = new CMailer();

// Now you only need to add the necessary stuff
$mail->AddAddress("cgrecu@sympatico.com", "Lucian Grecu");
$mail->Subject = "Here is the subject";
$mail->Body    = "This is the message body";
$mail->AddAttachment("mail.class.php");  // optional name

if(!$mail->Send())
{
   echo "There was an error sending the message";
   exit;
}

*/

?>