<?php   
/** element in a form, a base class to be extended
* @package HTML
* @since February 06
* @author cgrecu
*/


class CFormElement extends CHtmlEntity {

  var $value;
  var $defaultValue = "";
  var $settings = array();

  /** constructor */
  function __construct($pID, $pAttributes = array(), $settings = array()) {
		$this->settings = $settings;
		parent::__construct($pID, $pAttributes);
		if (isset($pAttributes["value"]) && $pAttributes["value"]) {
			$this->value = $pAttributes["value"];
		} else {
			if (isset($_POST[$pID])) $this->value = $_POST[$pID];
		}
		if (!$this->value && $this->defaultValue) $this->value =$this->defaultValue;
		 
		if (!isset($this->attributes["class"])) $this->attributes["class"] = "";
  }


  /** comment here */
  function label() {
	  if (isset($this->settings["format"]) && $this->settings["format"] == "full") Return "";
	  $classes = array();
	  if (isset($this->attributes["class"]) && trim($this->attributes["class"])) $classes = explode(" ", trim($this->attributes["class"]));
	  foreach ($classes as $key=>$val) {
		$classes[$key] = "label-" . $val;
	  }
	  $help = "";
	  if (isset($this->settings["help"])) $help .= '<img src="/lib/img/dark/info.svg" data-help="'.$this->settings["help"].'">';
	 Return '<label for="' .  $this->id .'" class="'.implode(" ", $classes).'">'.(isset($this->attributes["label"]) ? $this->attributes["label"] : "")." $help</label>";
  }

  
}

?>
