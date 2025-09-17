<?php   
/** 
* @package html
* @since May 21
* @author cgrecu
*/


class CInputInt extends CTextInput {
  
  /** comment here */
  function __construct($pID, $pAttributes = array(), $help = array()) {
	$pAttributes["data-type"] = "integer";
	if (isset($pAttributes["class"])) $pAttributes["class"] = $pAttributes["class"] . " js-integer"; else $pAttributes["class"] = "js-integer";
	parent::__construct($pID, $pAttributes, $help);	  
  }


}

?>
