<?php 
/** form generator
* @package HTML
* @since February 06
* @author cgrecu
*/


class CForm extends CHtmlEntity {

	var $template_file = "";
	var $template;
	var $structure = array();
	var $current_block = "";
	var $button_cancel = "";
	var $no_footer = false;

  /** constructor */
  function __construct($pID, $pAttributes = array(), $pTemplate = "") {
	
	parent::__construct($pID, $pAttributes);

	if (!$pTemplate) $pTemplate = "lib/html/forms/default.html";
	$this->template_file =$pTemplate; 
	$this->structure = array("hidden" => array());
	if (!isset($this->attributes["name"]) || !$this->attributes["name"]) $this->attributes["name"] = $pID;
	if (!isset($this->attributes["method"]) || !$this->attributes["method"]) $this->attributes["method"] = "POST";
	if (!isset($this->attributes["id"]) && $this->attributes["name"]) $this->attributes["id"] = $this->attributes["name"];

	$this->attributes["domType"] = "form";
  }

  /** comment here */
  function addBlock($pName, $options = array()) {
		if (!isset($options["label"]) || !$options["label"]) $options["label"] = $pName;
		if (!isset($options["class"]) || !$options["class"]) $options["class"] = "standard-form-block";
		$this->structure[$pName]["options"] = $options;
		if (!isset($this->structure[$pName]["data"]) || !$this->structure[$pName]["data"]) $this->structure[$pName]["data"] = array();
		$this->current_block = $pName;
  }

  /** comment here */
  function addElement($pElement, $pBlock = "") {
	
	//	die($pElement->mType);
		if (!$pBlock && !$this->current_block) $pBlock = "Edit information";
		if ($pBlock) {
			if ($this->structure[$pBlock]) {
				$this->current_block = $pBlock;
			} else {
				$this->addBlock($pBlock);
			}
		}
		
		if (!isset($pElement->attributes["type"])) $pElement->attributes["type"] = "";
		if ($pElement->attributes["type"] == "file") $this->attributes["enctype"] = "multipart/form-data";

		if ($pElement->attributes["type"] == "hidden") $this->structure["hidden"][$pElement->id] = $pElement;
		else {
			$this->structure[$this->current_block]["data"][$pElement->id] = $pElement;
		}

  }

  /** comment here */
  function gotoBlock($pBlock) {
  	$this->current_block = $pBlock;
  }

  /** comment here */
  function display() {

		$this->template = template2($this->template_file);
		foreach ($this->attributes as $key=>$val) {
			$this->template->assign($key, $val);
		}

	  if ($this->structure["hidden"]) {

		$this->template->newBlock("HIDDENFIELDS");
		if (!isset($field_data)) $field_data = "";
		foreach ($this->structure["hidden"] as $key=>$val) {
			$field_data .= $val->html();
		}
		$this->template->assign("Data", $field_data);
	  }


	  foreach ($this->structure as $key=>$val) {
		  if ($key == "hidden") continue;
		$this->template->newBlock("FORMBLOCK");
		$this->template->assign("Label", $val["options"]["label"]);
		$this->template->assign("Class", $val["options"]["class"]);
		$this->template->assign("BlockID", "blk-" .strtolower($key));
		

		foreach ($val["data"] as $key2=>$element) {
			$this->template->newBlock("FORMDATA");


			if (isset($element->attributes["class"])) {
				$class = explode(" ", $element->attributes["class"]);
				foreach ($class as $key3=>$val3) {
					$class[$key3] = "form-row-" . $val3;
				}
				$class= implode(" ", $class);
			} else {
				$class = "";
			}
			
			$attributes = "";
			if (isset($element->attributes["config-max-length"])) {
				$class.= " js-max-length";
				$attributes .= 'config-max-length="'.intval($element->attributes["config-max-length"]).'" ';
			}
			if (isset($element->settings["format"]) && $element->settings["format"] == "full") 
				$txt = '<div class="form-row form-row-no-label '.$class.'" id="form-row-'.$element->id.'"><div class="field input-'.$element->attributes["type"].' '.$element->attributes["class"] . '" '.$attributes.'>'.$element->html().'</div></div>';
			else
				$txt = '<div class="form-row '.$class.'" id="form-row-'.$element->id.'"><div class="label">'.$element->label().'</div><div class="field input-'.$element->attributes["type"].' '.$element->attributes["class"] . '" '.$attributes.'>'.$element->html().'</div></div>';
			$this->template->assign("FormRow", $txt);
		}
		
	  }
		
		if (!$this->no_footer) {
			  $this->template->newBlock("FOOTER");
			  $button = new CSubmit("Submit", "Submit");
			  $this->template->assign("Input", $button->html());
			  if (!$this->button_cancel) 
			  $this->button_cancel = new CButton("bt-cancel", "Cancel");
			  $this->template->assign("InputCancel", $this->button_cancel->html());
		}
	  Return $this->template->output();;
  }



}

?>