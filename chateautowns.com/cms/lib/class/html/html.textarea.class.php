<?php   
/** text area, plain text
* @package HTML
* @since February 06
* @author cgrecu
*/


class CTextArea extends CFormElement{

  /** constructor */
  function __construct($pID, $pAttributes = array(), $help = array()) {
	parent::__construct($pID, $pAttributes, $help);	  
  }

  /** comment here */
  function html() {

	$txt = '<textarea id="'.$this->id.'" ';
	foreach ($this->attributes as $key=>$val) {
		$txt .= ' ' . $key . '="'. htmlentities($val) .'"';
	}
	
	$txt .= '/>'.htmlentities($this->value).'</textarea>' . "\n";

	Return $txt;
  }



}

?>
