<?php   
/** CInputEmail
* @package html
* @since May 21
* @author cgrecu
*/


class CInputEmail extends CTextInput {

  /** comment here */
  function __construct($pID, $pAttributes = array(), $help = array()) {
	$pAttributes["data-type"] = "email";
	if (!isset($pAttributes["class"])) $pAttributes["class"] = "js-email"; else $pAttributes["class"] .= " js-email";
	parent::__construct($pID, $pAttributes, $help);	  
  }


}

?>
