<?php   
/** href entities
* @package HTML
* @since February 06
* @author cgrecu
*/


class CHref extends CHtmlEntity {

  var $url;
  var $label;

  function __construct($pURL="", $pLabel="", $pAttributes = array()) {
	  if (!isset($pAttributes["id"]) || !$pAttributes["id"]) $pAttributes["id"] = uniqid();
	parent::__construct($pAttributes["id"], $pAttributes);
	$this->url = $pURL;
	$this->label = $pLabel;
  }

	/** comment here */
	function html() {
		$txt = '<a href="'.$this->url.'" ';		
		foreach ($this->attributes as $key=>$val) {
			if ($key == "label") continue;
			$txt .= ' ' . $key . '="'. htmlentities($val) .'"';
		}
		$txt .= '>'.$this->label.'</a>';
		Return $txt;

	}
}

?>
