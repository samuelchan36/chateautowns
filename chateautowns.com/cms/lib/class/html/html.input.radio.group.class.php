<?php   
/** CInputT
* @package html
* @author cgrecu
*/


class CInputRadioGroup extends CFormElement {
/** comment here */
	var $mChecked = false;
	var $mButtons = array();
	var $value = "";

  function __construct($pID, $pButtons = array(), $pAttributes = array(), $help = array()) {
	  	  if ($help) $this->help = $help;

	  parent::__construct($pID, $pAttributes, $help);	  
	  if ($pButtons) $this->addButtons($pButtons);
	  
  }

  /** set the check boolean */
  function addButtons($buttons) {
		

	  foreach ($buttons as $key=>$val) {
			$input = new CInputRadio($this->id . "_" . $key, $val[0], array("name"=>$this->id, "radio-label" => $val[1], "class" => $this->attributes["class"] . " " . $val[2]));
			if ($this->value == $input->value) $input->attributes["checked"] = "checked"; else unset($input->attributes["checked"]);
			$this->mButtons[$key] = $input;
	  }

  }


  /** creates the html code for the input element */
  function html() {

	$tmp = '<div class="radio-group">';
	foreach ($this->mButtons as $key=>$val) {
		$tmp .= $val->html();
	}
	$tmp .= "</div>\n";
	Return $tmp;
  }


}

?>
