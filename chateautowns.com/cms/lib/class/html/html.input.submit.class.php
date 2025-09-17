<?php   
/** CSubmit
* @package html
* @since February 06
* @author cgrecu
*/


class CSubmit extends CInput {
	
	/** comment here */
	function __construct($pID, $pValue = "Submit", $pAttributes = array(), $help = array()) {
		$pAttributes["value"] = $pValue;
		parent::__construct($pID, "submit", $pAttributes, $help);	  

  }


}

?>
