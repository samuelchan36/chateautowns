<?php 

  class CSimpleContent extends  CDBContent {

	/** comment here */
	function __construct($id = "") {
		$this->supports_draft = false;
		$this->supports_timers = false;
		$this->supports_publishing = false;
		parent::__construct($id);		
	}
  }

 ?>