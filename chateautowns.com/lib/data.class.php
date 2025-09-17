<?php

	class CDataManager {

		var $mDatabase = "";
		var $months = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		var $months_fr = array("Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre");

		function __construct() {
			$this->mDatabase = &$GLOBALS["db"];			
		}


		/** comment here */
		function getData($table, $limit) {
			$sql = "select * from $table order by id desc limit $limit";
			Return $this->mDatabase->getAll($sql);
		}

		/** comment here */
		function block($tpl, $data, $block = "ITEM") {
			foreach ($data as $key=>$val) {
				$tpl->newBlock($block);
				foreach ($val as $key2=>$val2) {
					$tpl->assign($key2, $val2);
				}
			}
		}

		function single($tpl, $data, $block = "ITEM") {
			$tpl->newBlock($block);
			foreach ($data as $key2=>$val2) {
				$tpl->assign($key2, $val2);
			}
		}

		function format($tpl, $data) {
			foreach ($data as $key2=>$val2) {
				$tpl->assign($key2, $val2);
			}
		}



		
}

?>