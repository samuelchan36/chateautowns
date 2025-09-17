<?php   
/** CInputT
* @package html
* @author cgrecu
*/


class CTextInput extends CInput {
/** comment here */

  function __construct($pID, $pAttributes = array(), $help = array()) {
	parent::__construct($pID, "text", $pAttributes, $help);	  
  }



}

?>
