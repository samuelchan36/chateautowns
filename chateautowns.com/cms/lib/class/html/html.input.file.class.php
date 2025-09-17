<?php   
/** CInputFile
* @package html
* @author cgrecu
*/


class CInputFile extends CInput {
/** comment here */

  function __construct($pID, $pAttributes = array(), $help = array()) {
	  if (!isset($pAttributes["class"])) $pAttributes["class"] = "";
	  $pAttributes["class"] .= " for-file";
	  if (!isset($pAttributes["value-label"])) $pAttributes["value-label"] = "";
		parent::__construct($pID, "file", $pAttributes, $help);	  
  }

  /** comment here */
  function html() {
	if (!isset($this->attributes["name"]) || !$this->attributes["name"]) $this->attributes["name"] = $this->id;
	if (isset($this->attributes["multiple"])) $this->attributes["name"] = $this->attributes["name"] . "[]";

	$txt = '<input id="'.$this->id.'" value="'.$this->value.'"';

	foreach ($this->attributes as $key=>$val) {
		$txt .= ' ' . $key . '="'. $val .'"';
	}
	
	$txt .= '/>';
	$txt .= "<label for='".$this->id."' class='label-for-file'>".($this->attributes["value-label"] ? $this->attributes["value-label"] : "Upload")."</label><div class='input-file-list'>";
	if ($this->value) {
		$tmp = pathinfo($this->value);
		$tmp['extension'] = strtolower($tmp['extension']);
		if ($tmp['extension'] == "jpg" || $tmp['extension'] == "gif" || $tmp['extension'] == "png" || $tmp['extension'] == "svg" || $tmp['extension'] == "jpeg" || $tmp['extension'] == "bmp" || $tmp['extension'] == "svg") {
			$txt .= "<a class=\"preview-link fancybox\" href='".htmlentities($this->value)."'><img src='".htmlentities($this->value)."' style='border: 1px solid #eee; background: #eee;'/></a><br/><a href='' onclick='$(\"#".$this->id."\").val(\"\"); $(\"#".$this->id."_delete_me\").val(\"delete\"); $(this).parent().find(\".preview-link\").remove();return false;'>delete</a>";
		} else
			$txt .= "<a class=\"preview-link\" href='".htmlentities($this->value)."'>preview</a><br/><a href=''  onclick='$(\"#".$this->id."\").val(\"\"); $(\"#".$this->id."_delete_me\").val(\"delete\"); $(this).parent().find(\".preview-link\").remove();return false;'>delete</a>";
	}

	$txt .= "<input name='".$this->id."_delete_me'  id='".$this->id."_delete_me' value='' type='hidden'></div>\n";

	Return $txt;
  }



}

?>
