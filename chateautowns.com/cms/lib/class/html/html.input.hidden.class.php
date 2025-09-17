<?php   
/** CHidden
* @package html
* @author cgrecu
*/


class CHidden extends CInput {
/** comment here */

  function __construct($pID, $pAttributes = array(), $help = array()) {
	  if (isset($pAttributes) && !is_array($pAttributes)) $pAttributes = array("value" => $pAttributes); //backward compatibility
		parent::__construct($pID, "hidden", $pAttributes, $help);	  
  }


}

?>
