<?php
/** CLogo
* @package pages
* @author cgrecu
*/


class CAccess extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}

  }

?>