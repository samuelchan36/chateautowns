<?php   

class CChart {

	var $mType;

	/** comment here */
	function __construct($type = 'Column3D') {
		$this->mType = $type;
	}

	/** comment here */
  function display($dataset) {
	  require "class/libs/charts/FusionCharts.php";
	  Return renderChartHTML("class/libs/charts/".$this->mType.".swf", "", urlencode($dataset), "Whatever", 820, 400, false);
  }

}
?>