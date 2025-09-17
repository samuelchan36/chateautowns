<?php

	class CSearch extends CDefault {

		var $error;
		
		function __construct() {
			parent::__construct();
		}
		

		/** comment here */
		function doQuickSearch($what) {
			if (TRACKING == "on") $this->tracking->trackAction("quick-search");
//			$sql = "SELECT a.url, a.name, a.shortdescription from js_index a where a.tags like '%".addslashes2($what)."%' order by name DESC limit 30";
			$words = explode(" ", $what);
			$parts = array();
			foreach ($words as $key=>$val) {
				$parts[] = " (a.Content like '%" . addslashes2($val) . "%') ";
			}
//			die2($parts);
			$sql = "SELECT concat(a.url, '?q=".addslashes2($what)."') as url, a.name, a.shortdescription, a.category, a.type from js_index a where (". implode(" and ", $parts) . ") and (language = 'all' or language = '".$_SESSION["lang"]."') order by a.type, a.name ASC limit 10";
			$data = $this->mDatabase->getAll($sql);

//			$words2 = array();
//			foreach ($words as $key=>$val) {
//				$words2[$key] = "<b>" .$val . "</b>";
//			}
			foreach ($data as $key=>$val) {
				$replace = $val["name"];
				foreach ($words as $key2=>$val2) {
					$replace = highlightStr($replace, $val2);
				}
				$data[$key]["name"] = $replace;
			}

			die(json_encode($data));
		}
		/** comment here */
		function doSearch($what, $return = false) {
			if (TRACKING == "on") $this->tracking->trackAction("full-search");
			$sql = "SELECT concat(a.url, '?q=".addslashes2($what)."') as url,  a.name, a.shortdescription, a.fulldescription, a.image, a.type, a.category, MATCH(a.Content) AGAINST ('" .addslashes2($what). "' IN BOOLEAN MODE) as relevancy from js_index a where (language = 'all' or language = '".$_SESSION["lang"]."') having relevancy > 0 order by a.type, relevancy DESC limit 200";
			$data = $this->mDatabase->getAll($sql);
			if ($return) {

				$words = explode(" ", $what);
				$parts = array();
				foreach ($words as $key=>$val) {
					$parts[] = " (a.Content like '%" . addslashes2($val) . "%') ";
				}
	//			die2($parts);
				$data2 = array();
				if (!$data) {
					$sql = "SELECT  concat(a.url, '?q=".addslashes2($what)."') as url, a.name, a.shortdescription, a.fulldescription, a.image, a.type, a.category, 0 as relevancy from js_index a where (". implode(" and ", $parts) . ") and (language = 'all' or language = '".$_SESSION["lang"]."') order by a.type, a.name ASC limit 200";
					$data2 = $this->mDatabase->getAll($sql);
				}
				Return array($data, $data2);
			} 
				else die(json_encode($data));
		}


}

function highlightStr($haystack, $needle) {

    preg_match_all("/$needle+/i", $haystack, $matches);

    if (is_array($matches[0]) && count($matches[0]) >= 1) {
        foreach ($matches[0] as $match) {
            $haystack = str_replace($match, '<b>'.$match.'</b>', $haystack);
        }
    }
    return $haystack;
}
?>