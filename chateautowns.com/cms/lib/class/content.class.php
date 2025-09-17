<?php   
  
  class CContent extends CDBContent {
	
	/** comment here */
	function __construct($pTable, $pID = "") {
		$this->table = $pTable;
		parent::__construct($pID);
	}
  }

?>