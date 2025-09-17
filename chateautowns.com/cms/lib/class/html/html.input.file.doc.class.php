<?php   
/** CInputFile
* @package html
* @author cgrecu
*/


class CInputFileDoc extends CInputFile {
/** comment here */

  function __construct($pID, $pAttributes = array(), $help = array()) {
		parent::__construct($pID, $pAttributes, $help);	  
		$this->attributes["accept"] = ".doc,.docx,.xml,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/pdf";
  }



}

?>
