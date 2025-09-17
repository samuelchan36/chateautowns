<?php   
/** CInputT
* @package html
* @author cgrecu
*/


class CButton extends CInput {
/** comment here */

  function __construct($pID, $pValue, $pAttributes = array(), $help = array()) {
	parent::__construct($pID, "button", $pAttributes, $help);	  
	$this->value = $pValue;
  }


}

?>
