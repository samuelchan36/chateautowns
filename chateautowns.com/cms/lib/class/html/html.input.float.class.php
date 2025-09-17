<?php   
/** CInputFloat
* @package html
* @since May 21
* @author cgrecu
*/


class CInputFloat extends CTextInput {
  
  /** comment here */
  function __construct($pID, $pAttributes = array(), $help = array()) {
	$pAttributes["data-type"] = "float";
	if (isset($pAttributes["class"])) $pAttributes["class"] = $pAttributes["class"] . " js-float"; else $pAttributes["class"] = "js-float";
	parent::__construct($pID, $pAttributes, $help);	  
  }


}

?>
