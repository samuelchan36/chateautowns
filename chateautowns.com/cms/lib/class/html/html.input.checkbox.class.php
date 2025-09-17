<?php 
/** CInputT
* @package html
* @author cgrecu
*/


class CCheckbox extends CInput {

	var $force_check = false;
	var $defaultValue = "";
	var $choices = array();

  function __construct($pID, $pChoices = array("yes", "no"), $pAttributes = array(), $help = array()) {
		
		if (is_array($pChoices)) $this->choices = $pChoices; else $this->choices = array($pChoices, "no");
		
		if (!isset($pAttributes["value"])) {
			$pValue = "";
			if (isset($_POST[$pID])) $pValue = $_POST[$pID];
			if (!$pValue) $pValue = $this->defaultValue;
			$pAttributes["value"] = $pValue;
		}

		if (!isset($pAttributes["checked"]) || !$pAttributes["checked"]) {
			if ($pAttributes["value"] == $this->choices[0]) $pAttributes["checked"] = "checked"; else unset($pAttributes["checked"]); 
		}

		$pAttributes["data-on"] = $this->choices[0];
		$pAttributes["data-off"] = $this->choices[1];

		parent::__construct($pID, "checkbox", $pAttributes, $help);	  
  }

  /** comment here */
  function html() {
		
	$txt = '<div class="checkbox"><input id="'.$this->id.'" value="'.htmlentities($this->value).'"';
		foreach ($this->attributes as $key=>$val) {
			if ($key == "label") continue;
			$txt .= ' ' . $key . '="'. htmlentities($val) .'"';
		}
	
	$txt .= '/>';
//	if ($this->attributes["value-label"]) 
	$txt .='<label for="'.$this->id.'">'. (isset($this->attributes["value-label"]) ? $this->attributes["value-label"] : $this->attributes["value"]).'</label>';
	$txt .= '</div>' . "\n";

	Return $txt;
  }



}

?>