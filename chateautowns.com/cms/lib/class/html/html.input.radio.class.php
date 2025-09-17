<?php   
/** CInputT
* @package html
* @author cgrecu
*/


class CInputRadio extends CInput {
/** comment here */

	var $mCheckedValue;

  function __construct($pID, $pValue, $pAttributes = array()) {
	$pAttributes["value"] = $pValue;
	parent::__construct($pID, "radio", $pAttributes);
  }

  /** comment here */
  function html() {
		if (!isset($this->attributes["checked"]) && isset($_POST[$this->id]) && $_POST[$this->id] == $this->value) $this->attributes["checked"] = "checked";
		$txt = '<div class="radio"><input id="'.$this->id.'" value="'.htmlentities($this->value).'"';
		foreach ($this->attributes as $key=>$val) {
			if ($key == "label") continue;
			$txt .= ' ' . $key . '="'. htmlentities($val) .'"';
		}
		$txt .= '/><label for="'.$this->id.'">'.$this->attributes["radio-label"].'</label></div>' . "\n";

		Return $txt;
  }

}

?>
