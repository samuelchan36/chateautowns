<?php   
/** CInputT
* @package html
* @author cgrecu
*/


class CPassword extends CInput {
/** comment here */

  function __construct($pID, $pAttributes = array(), $help = array()) {
	parent::__construct($pID, "password", $pAttributes, $help);	  
  }


}

?>
