<?php   
/** CInputT
* @package html
* @author cgrecu
*/


class CInputTime extends CInput {
/** comment here */

	var $alt;

  function __construct($pID, $pAttributes = array(), $help = array()) {
	if (isset($pAttributes["class"])) $pAttributes["class"] .= " js-time"; else $pAttributes["class"] = "w200 js-time"; 
	parent::__construct($pID, "text", $pAttributes, $help);	  
  }


}

?>