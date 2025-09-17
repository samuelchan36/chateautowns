<?php   
/** CBoxTable
* @package Layout
* @since March 11
* @author cgrecu
*/


class CBoxTable extends CTable {


  /** constructor */
  function __construct($pBody, $pHeader = "", $pFooter = "", $pStyle='') {
	parent::__construct();
	$this->mBody = $pBody;
	$this->mHeader = $pHeader;
	$this->mFooter = $pFooter;
  	if ($pStyle) $this->loadTemplate($pStyle);
  }
 
  /** display the Header part */
  function displayElement($pIndex) {
	switch($pIndex) {
		case "header":
  		  $vText = $this->mHeader;
		  break;
		case "body":
  		  $vText = $this->mBody;
		  break;
		case "footer":
  		  $vText = $this->mFooter;
		  break;
	}

  	if ($vText == "") Return "";
	if ($pIndex == "body") {
	  $vOnMouseOver = isset($this->mTemplates["onMouseOver"])?" onMouseOver=\"" . $this->mTemplates["onMouseOver"] . "\"":'';
	  $vOnMouseOut = isset($this->mTemplates["onMouseOut"])?" onMouseOut=\"" . $this->mTemplates["onMouseOut"] . "\"":'';
	} else {
	  $vOnMouseOver = "";
	  $vOnMouseOut = "";
	};
	$tmp = "<tr $vOnMouseOver $vOnMouseOut><td" . $this->constructStyle($pIndex) . ">$vText</td></tr>";
	Return $tmp;
  }

  function display() {
	if (empty($this->mTemplates)) $this->loadTemplate("default");
	$tmp = "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\" border-collapse=\"$this->mBorderCollapse\" id=\"". $this->mID . "\" ". $this->constructStyle("table"). $this->mJavaScript->display() .">";
	$tmp .= $this->displayElement("header");
	$tmp .= $this->displayElement("body");
	$tmp .= $this->displayElement("footer");
	$tmp .= "</table>";
	Return $tmp;
  }

  function loadTemplate($pName) {
  	$vTemplate = array();
	$this->mTemplates = array();
	switch ($pName) {
	  case "admin":
		$this->mTemplates['table']['width'] = '100%';
		$this->mTemplates['table']['border'] = '1px solid #f6f6f6';
		$this->mTemplates['table']['margin'] = '0 0 7px 0';
		$this->mTemplates['header']['color'] = '#000';
		$this->mTemplates['header']['font-weight'] = 'bold';
		$this->mTemplates['header']['border-bottom'] = '1px solid ' . $this->mTemplates['header']['color'];
		$this->mTemplates['header']['padding'] = "2px";
		$this->mTemplates['body']['border-bottom'] = '1px solid #fafafa';
		$this->mTemplates['body']['padding'] = '0px';
		$this->mTemplates['body']['color'] = '#000';
		$this->mTemplates['footer']['font-size'] = '8pt';
		$this->mTemplates['footer']['font-weight'] = 'normal';
		$this->mTemplates['footer']['color'] = $this->mTemplates['header']['color'];
		$this->mTemplates['footer']['vertical-align'] = 'middle';
		$this->mTemplates['footer']['font-style'] = 'italic';
		$this->mTemplates['footer']['padding'] = '7px 3px 2px';
		$this->mTemplates['footer']['background-color'] = '#fafafa';

		$this->mTemplates['footer']['border-top'] = '1px solid #f6f6f6';
		$this->mTemplates['header']['border-bottom'] = '1px solid #f6f6f6';
		$this->mTemplates['header']['background-color'] = '#fafafa';
		break;
	  case "default":
		$this->mTemplates['table']['width'] = '100%';
		$this->mTemplates['table']['padding'] = '5px';
		$this->mTemplates['body']['padding'] = '5px';
		$this->mTemplates['table']['margin'] = '0px 3px 3px';
		$this->mTemplates['header']['vertical-align'] = 'middle';
		$this->mTemplates['header']['font-weight'] = 'bold';
		$this->mTemplates['header']['font-size'] = '10pt';
		$this->mTemplates['header']['color'] = "#fff";
		$this->mTemplates['header']['padding'] = "3px";
		$this->mTemplates['header']['background-color'] = $this->mColHead;
		$this->mTemplates['table']['border'] = "1px solid $this->mColHead";
		break;
	  case "standard":
	  case "std_left":
	  case "std_right":
		$this->mTemplates['table']['width'] = '100%';
		$this->mTemplates['table']['border-collapse'] = 'collapse';
		$this->mTemplates['table']['border'] = '1px solid #f0f0f0';
		$this->mTemplates['table']['margin'] = '0 0 7px 0';
		
		$this->mTemplates['header']['color'] = '#000';
		$this->mTemplates['header']['font-weight'] = 'bold';
		$this->mTemplates['header']['padding'] = "4px 3px";
		$this->mTemplates['header']['border-bottom'] = '1px solid #f6f6f6';
		$this->mTemplates['header']['background-color'] = '#f0f0f0';
		
		$this->mTemplates['body']['border-bottom'] = '1px solid #fafafa';
		$this->mTemplates['body']['padding'] = '3px 3px';
		$this->mTemplates['body']['color'] = '#000';
		
		$this->mTemplates['footer']['font-size'] = '8pt';
		$this->mTemplates['footer']['font-weight'] = 'normal';
		$this->mTemplates['footer']['color'] = $this->mTemplates['header']['color'];
		$this->mTemplates['footer']['vertical-align'] = 'middle';
		$this->mTemplates['footer']['font-style'] = 'italic';
		$this->mTemplates['footer']['padding'] = '7px 3px 2px';
		$this->mTemplates['footer']['background-color'] = '#fafafa';
		$this->mTemplates['footer']['border-top'] = '1px solid #f6f6f6';
		break;
	  case "standard2":
		$this->mTemplates['table']['width'] = '100%';
		$this->mTemplates['table']['border-collapse'] = 'collapse';
		$this->mTemplates['table']['border'] = '1px solid #f6f6f6';
		$this->mTemplates['table']['margin'] = '0 0 7px 0';
		$this->mTemplates['header']['color'] = '#000';
		$this->mTemplates['header']['font-weight'] = 'bold';
		$this->mTemplates['header']['border-bottom'] = '1px solid ' . $this->mTemplates['header']['color'];
		$this->mTemplates['header']['padding'] = "4px 3px";
		$this->mTemplates['body']['padding'] = '3px 3px';
		$this->mTemplates['body']['color'] = '#000';
		$this->mTemplates['body']['background-color'] = '#fafafa';
		$this->mTemplates['footer']['font-size'] = '8pt';
		$this->mTemplates['footer']['font-weight'] = 'normal';
		$this->mTemplates['footer']['color'] = $this->mTemplates['header']['color'];
		$this->mTemplates['footer']['vertical-align'] = 'middle';
		$this->mTemplates['footer']['font-style'] = 'italic';
		$this->mTemplates['footer']['padding'] = '7px 3px 2px';
		$this->mTemplates['footer']['background-color'] = '#fafafa';

		$this->mTemplates['footer']['border-top'] = '1px solid #f6f6f6';
		$this->mTemplates['header']['border-bottom'] = '1px solid #f6f6f6';
		$this->mTemplates['header']['background-color'] = '#fafafa';
		break;
	  case "stdedit":
		$this->mTemplates['table']['width'] = '100%';
		$this->mTemplates['table']['padding'] = '0px';
		$this->mTemplates['table']['margin'] = '0px';
		$this->mTemplates['table']['border'] = "1px solid #f6f6f6";
		$this->mTemplates['table']['background-color'] = "#fafafa";

		$this->mTemplates['body']['padding'] = '3px 2px 3px 8px';
		$this->mTemplates['body']['font-size'] = '10pt';
		$this->mTemplates['breaker'] = $this->mTemplates['body'];
		$this->mTemplates['footer'] = $this->mTemplates['body'];

		$this->mTemplates['footer']['text-align'] = "center";
		break;
	  case "inner_edit":
		$this->mTemplates['table']['width'] = '96%';
		$this->mTemplates['table']['padding'] = '0px';
		$this->mTemplates['table']['margin'] = '0px';
		$this->mTemplates["header"]["height"] = "24px";
		$this->mTemplates["header"]["vertical-align"] = "middle";
		$this->mTemplates["header"]["color"] = $this->mColHead;
		$this->mTemplates["header"]["font-size"] = "9pt";
		$this->mTemplates["header"]["font-weight"] = "bold";
		$this->mTemplates["header"]["border-bottom"] = "1px solid ".$this->mColHead;
		$this->mTemplates["header"]["padding-left"] = "8px";
		$this->mTemplates['body']['padding'] = '3px';
		$this->mTemplates["body"]["border-bottom"] = "1px solid #f0f0f0";

		$this->mTemplates['footer']['text-align'] = "center";
		break;
	  case "admin_edit":
		$this->mTemplates['table']['width'] = '740px';
		$this->mTemplates['table']['padding'] = '0px';
		$this->mTemplates['table']['margin'] = '0px';
		$this->mTemplates["header"]["height"] = "24px";
		$this->mTemplates["header"]["vertical-align"] = "middle";
		$this->mTemplates["header"]["color"] = $this->mColHead;
		$this->mTemplates["header"]["font-size"] = "9pt";
		$this->mTemplates["header"]["font-weight"] = "bold";
		$this->mTemplates["header"]["border-bottom"] = "2px solid ".$this->mColHead;
		$this->mTemplates["header"]["padding-left"] = "8px";
		$this->mTemplates['body']['padding'] = '10px 0px 0	px';
		//$this->mTemplates["body"]["border-bottom"] = "1px solid #f0f0f0";

		$this->mTemplates['footer']['text-align'] = "center";
		break;
	  case "block":
		$this->mTemplates['table']['width'] = '100%';
		$this->mTemplates['table']['padding'] = '0px';
		$this->mTemplates['table']['margin'] = '0px';
		$this->mTemplates['body']['padding'] = '2px 5px';
		$this->mTemplates["body"]["vertical-align"] = "middle";
		$this->mTemplates["breaker"] = $this->mTemplates["body"];
		$this->mTemplates['breaker']['padding'] = '2px 0px';
		$this->mTemplates["table"]["background-color"] = "#fafafa";
		$this->mTemplates["table"]["border"] = "1px solid #f6f6f6";
		$this->mTemplates["body"] = $this->mTemplates["breaker"];

		$this->mTemplates['footer']['text-align'] = "center";
		$this->mTemplates['footer']['padding'] = "6px 0 3px";
		break;
	  case "schedule":
		$this->mTemplates['table']['width'] = '119px';
		$this->mTemplates['table']['border'] = '1px solid #eee';
		$this->mTemplates['body']['width'] = '20px';
		$this->mTemplates['body']['padding'] = '0px';
		$this->mTemplates['body']['border'] = '1px solid #eee';
		$this->mTemplates['body']['color'] = '#fff';
		$this->mTemplates['body']['text-align'] = 'center';
		$this->mTemplates['header']['font-size'] = '8px';
		$this->mTemplates['header']['text-align'] = 'center';
		break;
	  case "home":
		$this->mTemplates['table']['width'] = '100%';
		$this->mTemplates['table']['border-collapse'] = 'collapse';
		$this->mTemplates['table']['margin'] = '0 0 7px 0';
		$this->mTemplates['table']['border'] = '1px solid #ccc';
		
		$this->mTemplates['header']['color'] = '#fff';
		$this->mTemplates['header']['font-weight'] = 'bold';
		$this->mTemplates['header']['padding'] = "4px 3px";
		$this->mTemplates['header']['border-bottom'] = '1px solid #D7E2EA';
		$this->mTemplates['header']['background-color'] = '#35629e';
		
		$this->mTemplates['body']['border'] = '0px';
		$this->mTemplates['body']['padding'] = '3px 3px';
		$this->mTemplates['body']['color'] = '#000';
		
		$this->mTemplates['footer']['font-size'] = '8pt';
		$this->mTemplates['footer']['font-weight'] = 'normal';
		$this->mTemplates['footer']['color'] = $this->mTemplates['header']['color'];
		$this->mTemplates['footer']['vertical-align'] = 'middle';
		$this->mTemplates['footer']['font-style'] = 'italic';
		$this->mTemplates['footer']['padding'] = '7px 3px 2px';
		$this->mTemplates['footer']['background-color'] = '#fafafa';
		$this->mTemplates['footer']['border-top'] = '1px solid #f6f6f6';
		break;
	  case "home2":
		$this->mTemplates['table']['width'] = '100%';
		$this->mTemplates['table']['border-collapse'] = 'collapse';
		$this->mTemplates['table']['border'] = '1px solid #f0f0f0';
		$this->mTemplates['table']['margin'] = '0 0 7px 0';
		
		$this->mTemplates['header']['color'] = '#2222FF';
		$this->mTemplates['header']['font-weight'] = 'bold';
		$this->mTemplates['header']['padding'] = "4px 3px";
		$this->mTemplates['header']['border-bottom'] = '1px solid #f6f6f6';
		$this->mTemplates['header']['background-color'] = '#f0f0f0';
		
		$this->mTemplates['body']['border-bottom'] = '1px solid #fafafa';
		$this->mTemplates['body']['padding'] = '3px 3px';
		$this->mTemplates['body']['color'] = '#444';
		$this->mTemplates['body']['font-size'] = '8pt';
		$this->mTemplates['breaker'] = $this->mTemplates['body'];
		
		$this->mTemplates['footer']['font-size'] = '8pt';
		$this->mTemplates['footer']['font-weight'] = 'normal';
		$this->mTemplates['footer']['color'] = $this->mTemplates['header']['color'];
		$this->mTemplates['footer']['vertical-align'] = 'middle';
		$this->mTemplates['footer']['font-style'] = 'italic';
		$this->mTemplates['footer']['padding'] = '7px 3px 2px';
		$this->mTemplates['footer']['background-color'] = '#fafafa';
		$this->mTemplates['footer']['border-top'] = '1px solid #f6f6f6';
		break;


	}
  }

}

?>
