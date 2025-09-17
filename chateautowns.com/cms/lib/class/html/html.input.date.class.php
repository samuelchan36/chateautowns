<?php   
/** CInputT
* @package html
* @author cgrecu
*/


class CInputDate extends CInput {
/** comment here */

	var $alt;

  function __construct($pID, $pAttributes = array(), $help = array()) {
	if (isset($pAttributes["class"])) $pAttributes["class"] .= " js-calendar"; else $pAttributes["class"] = "w200 js-calendar"; 
	$pAttributes["autocomplete"] = "off";
	parent::__construct($pID . "_alt", "text", $pAttributes, $help);	  
	$this->alt = new CHidden($pID);
	if (isset($_POST[$pID])) {
		$this->alt->value = $_POST[$pID];
		if ($_POST[$pID]) $this->value = date("F d, Y", $_POST[$pID]);
	}
	
  }

  /** comment here */
  function html() {
	if (!$this->value && $this->alt->value) $this->value = date("F d, Y", $this->alt->value);
	 $txt = parent::html() . $this->alt->html();
	 Return $txt;
  }



}

?>