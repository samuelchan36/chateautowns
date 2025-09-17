<?php   
/** html input types
* @package HTML
* @since February 09
* @author cgrecu
*/
class CInput extends CFormElement{


  /** constructor */
  function __construct($pID, $pType, $pAttributes = array(), $help = array()) {
	$pAttributes["type"] = $pType;
	parent::__construct($pID, $pAttributes, $help, $help);	  
  }

  /** comment here */
  function html() {
	$txt = '<input id="'.$this->id.'" value="'.htmlentities($this->value ? $this->value : "").'"';

	foreach ($this->attributes as $key=>$val) {
		if ($key == "label") continue;
		$txt .= ' ' . $key . '="'. htmlentities($val) .'"';
	}
	
	$txt .= '/>' . "\n";

	Return $txt;
  }


}

?>