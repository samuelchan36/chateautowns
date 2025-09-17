<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CSystem extends CSectionAdmin{



  /** comment here */
  function __construct() {
	parent::__construct();
  }


	/** comment here */
	function safeUrl($address, $options = "") {
		
		if (substr($address, 0, 1) != "/") $address = "/" . $address;

		# ensure page is not a duplicate of an existing page
		$parts = explode("/", $address);
		foreach ($parts as $key=>$val) {
			$parts[$key] = trim(sanitize_text_for_urls(str_replace("&", "and", $val)));
		}
		$address = implode("/", $parts);
		
		$address = substr($address, 1);
		if ($options == "addslash") $address = "/" . $address; 
  	  $ret = array("output" => $address);
	  die(json_encode($ret));
	}

	/** comment here */
	function executeSql() {
		$txt = "<div style='padding: 50px;'><form action='/cms/system/do-execute-sql' method='post'><textarea rows='10' cols='120' name='sql'></textarea><br><br><button type='submit'>execute</button></form></div>";
		Return $txt;
	}

	/** comment here */
	function doSql() {
		if ($_POST["sql"]) {
			$data = $this->mDatabase->getAll($_POST["sql"]);
			if ($data) {
				$txt = '<div style="padding: 50px 20px"><code>'. $_POST["sql"] .'</code><br><br><table cellspacing="0" cellpadding="8" border="1" width="100%"><tr>';
				foreach ($data[0] as $key=>$val) {
					$txt .= "<th>".addslashes2($key)."</th>";
				}
				$txt .="</tr>";
				foreach ($data as $key=>$val) {
					$txt .="<tr>";
					foreach ($val as $key2=>$val2) {
						$txt .="<td>".$val2."</td>";
					}
					$txt .="</tr>";
				}
				$txt .="</table></div>";
				Return $txt;
			} else {
				Return "<p>No results</p>";
			}
			Return "<p>No query</p>";
		}

	}

	/** comment here */
	function buildIndex() {
//		$this->mDatabase->query("truncate js_index");
//		$this->mDatabase->query("insert into js_index(Type, Category, ContentID, Url, Name, ShortDescription, Image, FullDescription, Tags, Content, Language) select 'page', 'Website Pages', ID, Address, SEOTitle, Description, PageImage, Description, '', concat(Title, '<br>', URLName, '<br>', Content), BaseLanguage from cms_pages where Status = 'enabled'");
//		$this->mDatabase->query("insert into js_index(Type, Category, ContentID, Url, Name, ShortDescription, Image, FullDescription, Tags, Content, Language) select 'event', 'Events', ID, Link, Name, City, Image, Description, concat(EventCode, ' ', Type), concat(Name, '<br>', Description, '<br>', Location, '<br>', Address, '<br>', City, '<br>', Province, '<br>', EventCode, '<br>', Type, from_unixtime(startdate, '%M %Y')), 'all' from events where Status = 'active' and startdate > unix_timestamp()");
//		$this->mDatabase->query("insert into js_index(Type, Category, ContentID, Url, Name, ShortDescription, Image, FullDescription, Tags, Content, Language) select 'news', 'News', ID, Url, Title, Summary, Image, Summary, '', concat(Title, '<br>', Source, '<br>', Summary, '<br>', Author), 'all' from news");
//		$this->mDatabase->query("insert into js_index(Type, Category, ContentID, Url, Name, ShortDescription, Image, FullDescription, Tags, Content, Language) select 'jobs', 'Jobs', ID, Link, Name, Name, Image, Name, '', concat(Name, '<br>', Company, '<br>', Location), 'all' from careers where status = 'enabled'");
	}

	/** comment here */
	function getOptions() {
		if ($_GET["source"] && $_GET["source"] != "undefined") $data = $this->mDatabase->getAll("select ID, Name from " . $_GET["source"] . " where ".$_GET["condition"]." = ".intval($_GET["value"])." order by 2 asc"); else $data = array();
		die(json_encode($data));
	}

	/** comment here */
	function checkUnique() {
		$tmp = explode(",", $_GET["condition"]);
		$data = $this->mDatabase->getRow("select count(*) as cnt from " . $tmp[0] . " where ".$tmp[1]." = '".addslashes2($_GET["value"])."' and ID <> " . intval($tmp[2]));
		if ($data["cnt"] > 0) die("no"); else die("ok");
		
	}

	
  /** comment here */
  function uploadFile($id) {
		$ret = array();
		$ret["ret"] = "notok";
		$ret["html"] = "";

		if ($_FILES["file"]["tmp_name"]) {
			@mkdir("../media/uploads/");
			@mkdir("../media/uploads/" . date("Y"));
			$path = "/media/uploads/" . date("Y"). "/" . $_FILES['file']['name'];
			$ret2 = move_uploaded_file($_FILES['file']['tmp_name'], ".." . $path);
			if ($ret2) {
				if (!isset($_SESSION["dropzone"][$id]) || !$_SESSION["dropzone"][$id]) $_SESSION["dropzone"][$id] = array();
				$fileid = count($_SESSION["dropzone"][$id]);
				$_SESSION["dropzone"][$id][$fileid] = array("Path" => $path, "Name" => $_FILES["file"]["name"], "ID" => 0, "Description" => "", "Deleted" => false);
				$ret["ret"] = "ok";	
				$ret["html"] = '<div class="slide-row" data-id="'.$fileid.'"><input type="hidden" name="SlideID[]" value="'.$fileid.'"><div class="thumbnail"><img src="'.$path.'"></div><div class="caption"><h6><input class="file-data" data-id="'.$fileid.'" name="Caption[]" placeholder="Caption (optional)" value="'.$_FILES["file"]["name"].'" data-name="Name" ></h6><div><textarea class="file-data" data-id="'.$fileid.'" data-name="Description" name="Description[]" placeholder="Description (optional)"></textarea></div></div><div class="actions"><a href="" class="delete" data-id="'.$fileid.'" ><img src="/cms/lib/images/common/small/delete2.png"></a></div></div>';
			}
		}

		die(json_encode($ret));
  }

  /** comment here */
  function deleteFile($id, $fileid) {
		$_SESSION["dropzone"][$id][$fileid]["Deleted"] = true;
		die("File deleted");
  }

  /** comment here */
  function updateFile($id, $fileid, $field, $value) {
	$_SESSION["dropzone"][$id][$fileid][$field] = $value;
	die("done");
  }

  /** comment here */
  function updateFileOrder($id, $order) {
	 $tmp = explode(",", $order);
	 $files = array();
	 foreach ($tmp as $key=>$val) {
		$files[] = $_SESSION["dropzone"][$id][$val];
	 }
	 $_SESSION["dropzone"][$id] = $files;
	 die("done");
  }

  /** comment here */
  function mainSwitch() {
	switch($this->mOperation) {
		case "execute-sql": Return $this->executeSql();
		case "do-execute-sql": Return $this->doSql();
		case "safe-url": Return $this->safeUrl($_GET["text"], $_GET["options"]);
		case "build-index": Return $this->buildIndex();
		case "get-options": Return $this->getOptions();
		case "check-unique": Return $this->checkUnique();

		case "upload-slide": Return $this->uploadFile($_GET["id"]);
		case "delete-slide": Return $this->deleteFile($_GET["id"], $_GET["fileid"]);
		case "update-slide": Return $this->updateFile($_GET["id"], $_GET["fileid"], $_GET["field"], $_GET["value"]);
		case "order-slide": Return $this->updateFileOrder($_GET["id"], $_GET["data"]);

		default: die();

	}
  }
}

?>
