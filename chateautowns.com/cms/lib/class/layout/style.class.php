<?php   
/** CStyle: implements CSS Style
* @package Layout
* @since March 10
* @author cgrecu
*/


class CStyle{
  
  var $mTemplates; //array of styles
  var $mClassName;
  var $mHtmlDoc;
  var $mDatabase;
  var $mFullLinks;
  var $mColText = "#000";


  function __construct() {
  	
	$this->mHtmlDoc = &$GLOBALS["doc"];
//	$this->mFullLinks = $this->mFullLinksMode;
//	$this->mDatabase = &$GLOBALS["vDatabase"];
  }

  /** constructs the style */
  function constructStyle2($pIndex = -1) {
	
	if ($pIndex != -1) 
	  $vTemplate = $this->mTemplates[$pIndex];
	else 
	  $vTemplate = $this->mTemplates;

	if (empty($vTemplate)) 
	  Return "";
	else {
	  $tmp = " style=\"";
	  foreach ($vTemplate as $key=>$val) {
		  if (!(empty($val))) $tmp .= "$key:$val;";
	  }
	  $tmp .= "\"";
	}

	Return $tmp;
  }

  function constructStyle($pIndex = -1) {
 	if ($pIndex != -1 && isset($this->mTemplates[$pIndex])) 
	  $vTemplate = $this->mTemplates[$pIndex];
	else 
	  $vTemplate = $this->mTemplates;
	$tmp = "";
	if (empty($vTemplate)) 
	  Return "";
	else {
	  if (!is_array($vTemplate)) die2($vTemplate);
	  foreach ($vTemplate as $key=>$val) {
		  if (is_array($val)) continue;
		  if (!(empty($val))) $tmp .= "$key:$val;";
	  }
	}
	
	$name = "." . $pIndex . "_" . substr(md5(microtime()), 1, 4); //unique name for the class

	$this->mHtmlDoc->mStyle .= $tmp . " ";

	$this->mClassName = substr($name,1);
	$tmp = " class=\"". $this->mClassName . "\" ";
	$this->mClassName = substr($name,1);
	Return $tmp;

 	
  }

  /** comment here */
  function seta($pKey, $pValue) {
  	
  }

  /** loads a predefined tamplate  */
  function loadTemplate($pName) {

	$vTemplate = array();

	switch ($pName) {
	  case "body":
		$this->mTemplates["background-color"] = "#FFFFFF";
		$this->mTemplates["margin-top"] = "2px";
		$this->mTemplates["margin-left"] = "2px";
		$this->mTemplates["font-family"] = "verdana";
		$this->mTemplates["font-size"] = "8pt";
		$this->mTemplates["font-weight"] = "normal";
		$this->mTemplates["font-style"] = "xx-small";
		$this->mTemplates["color"] = "#333";
		break;


	}
  } 

}
?>
