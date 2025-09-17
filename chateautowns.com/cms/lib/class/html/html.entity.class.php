<?php   
/** Html entities
* @package HTML
* @since February 06
* @author cgrecu
*/
class CHtmlEntity{

  var $id;
  var $attributes;
  var $mDatabase;

  /** constructor */
  function __construct($pID = 0, $pAttributes = array()) {
		$this->id = $pID;
		if (!isset($this->attributes["name"]) || !$this->attributes["name"]) $this->attributes["name"] = $this->id;

		foreach ($pAttributes as $key=>$val) {
			$this->attributes[$key] = $val;
		}

  }

  /** comment here */
  function html() {
		Return "";
  }
  
  /** comment here */
  function display() {
	Return $this->html();
  }
}

?>