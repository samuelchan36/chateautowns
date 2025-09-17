<?php

	class CData extends CDataManager {


		function __construct() {
			parent::__construct();
		}

		/** comment here */
		function getProjects() {
			$data = $this->mDatabase->getAll("select * from projects where status = 'enabled' order by orderid asc");
			Return $data;
		}

		/** comment here */
		function getTeam() {
			$data = $this->mDatabase->getAll("select a.Name as Team, b.* from departments a, team b where a.ID = b.DepartmentID and a.Status = 'enabled' and b.Status = 'enabled' order by a.OrderID, b.OrderID");
			$ret = array();
			foreach ($data as $key=>$val) {
				if (!isset($ret[$val["Team"]])) $ret[$val["Team"]]= array();
				$ret[$val["Team"]][] = $val;
			}
			Return $ret;
		}


		/** comment here */
		function getNews($count = 3) {
			$data = $this->mDatabase->getAll("select* from news where status = 'enabled' order by articledate desc limit " . intval($count));
			Return $data;
		}

		/** comment here */
		function getArticle($id) {
			$data = $this->mDatabase->getRow("select * from news where status = 'enabled' and guid = '".addslashes2($id)."'");
			Return $data;
		}

		/** comment here */
		function getServices() {
			$data = $this->mDatabase->getAll("select* from services where status = 'enabled' order by OrderID ASC");
			Return $data;
		}

		/** comment here */
		function getFaq() {
			$data = $this->mDatabase->getAll("select* from faq where status = 'enabled' order by OrderID ASC");
			Return $data;
		}

}



?>