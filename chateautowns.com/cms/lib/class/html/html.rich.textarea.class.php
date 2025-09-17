<?php   
/** CRichTextArea
* @package html
* @since May 04
* @author cgrecu
*/


class CRichTextArea extends CTextArea {
  
  /** comment here */
  function __construct($pID, $pAttributes = array(), $help = array()) {
	if (isset($help["editor-mode"])) $cls = $help["editor-mode"]; else $cls = "rich-editor";
	if (isset($pAttributes["class"])) $pAttributes["class"] .= " " . $cls; else $pAttributes["class"] = $cls;
	parent::__construct($pID, $pAttributes, $help);	  
  }


}

?>
