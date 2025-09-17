<?php   
/** CInputFile
* @package html
* @author cgrecu
*/


class CInputFileImage extends CInputFile {
/** comment here */

  function __construct($pID, $pAttributes = array(), $help = array()) {
		parent::__construct($pID, $pAttributes, $help);	  
		$this->attributes["accept"] = "image/*";
  }



}

?>
