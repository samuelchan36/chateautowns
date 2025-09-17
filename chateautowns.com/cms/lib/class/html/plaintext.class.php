<?php   
/** CText
* @package html
* @since May 25
* @author cgrecu
*/


class CText extends CHtmlEntity{
	
	var $mTxt;
	var $mName;
	var $settings = array();
	var $value;

  /** comment here */
  function __construct($pID, $pValue, $pAttributes = array(), $settings = array()) {
	  $this->value = $pValue;
	  $this->settings = $settings;
	  parent::__construct($pID, $pAttributes);
	  if (!isset($this->attributes["class"])) $this->attributes["class"] = " form-text"; else $this->attributes["class"] .= " form-text";
  }

  /** comment here */
  function html() {
	$txt = '<div id="'.$this->id.'" ';
	foreach ($this->attributes as $key=>$val) {
		if ($key =="label") continue; 
		$txt .= ' ' . $key . '="'. htmlentities($val) .'" ';
	}
	
	$txt .= '>'.$this->value.'</div>' . "\n";

	Return $txt;
  }

  /** comment here */
  function label() {
	  if (isset($settings["format"]) && $settings["format"] == "full") Return "";
	 if (isset($this->attributes["label"]) && $this->attributes["label"]) Return "<label for='$this->id'>".$this->attributes["label"]."</label>";
	 Return "";
  }

}

?>
