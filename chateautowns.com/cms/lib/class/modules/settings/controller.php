<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CSettingAdmin extends CSectionAdmin{

	var $table = "jobs";
	var $actions = array("edit", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Site Configuration", "Job Listing");
	var $mClass = "CSetting";
	

  /** comment here */
  function __construct() {
	parent::__construct();
  }


  /** comment here */
  function display() {
				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

		$tpl = template2("lib/class/modules/settings/view.html");

//			$this->loadSettings();
			$tmp = $this->mDatabase->getAll("select a.Name as Section, a.ID as SectID, a.Comments as SectionComments, b.* from cms_setting_groups a left outer join cms_settings b on a.ID = b.SectionID order by a.ID, b.Name asc");
			$settings = array();

			foreach ($tmp as $key=>$val) {
				$settings[$val["Section"]][] = $val;
			}

			foreach ($settings as $key=>$val) {
				$tpl->newBlock("SECTION");
				$tpl->assign("Name", $key);
				$tpl->assign("Comments", $val[0]["SectionComments"]);
				$tpl->assign("ID", $val[0]["SectID"]);
				foreach ($val as $key2=>$val2) {
					if (!$val2["ID"])  continue;
					$tpl->newBlock("SETTING");
					$tpl->assign("ID", $val2["ID"]);
					$tpl->assign("Name", $val2["Name"]);
					$tpl->assign("Value", $val2["Value"]);
					$tpl->assign("Comments", $val2["Comments"]);
				}
			}

		Return $tpl->output();

  }

  /** comment here */
  function loadSettings() {
				$txt = file_get_contents("../settings.php");
				if ($txt) {
					$lines = explode("\n", $txt);
					$sections = array();
					$section = "";
					foreach ($lines as $key=>$val) {
						$val = trim($val);
						$first = substr($val, 0, 1);
						if (!$first || $first == "?" || $first == "<" || $first =="<") continue;
						if ($first == "/") {
							$section = substr($val, 2, -2);
							$sections[$section] = array("data" => array(), "comments" => "");
						} else {
							if ($first == "d") {
								$tmp = explode("//", $val);
								$setting = substr(trim($tmp[0]), 7, -2);
//								die2($setting);
								$firstpos = strpos($setting, '"', 1);
								$name = substr($setting, 1, $firstpos-1);
								$secondpos = strpos($setting, '"', $firstpos + 1);
								$value = substr($setting, $secondpos + 1, -1);
								$sections[$section]["data"][$name] = array($value, $tmp[1]);
							} else {
								if ($first == "#") $sections[$section]["comments"] = substr($val, 1);
							}
						}
					}
				}

				
				$this->mDatabase->query("delete from cms_setting_groups");	
				$this->mDatabase->query("delete from cms_settings");	
				foreach ($sections as $key=>$val) {
					$this->mDatabase->query("insert into cms_setting_groups(Name, Comments) values('".addslashes2(trim($key))."','".addslashes2($val["comments"])."')");
					$id = $this->mDatabase->getLastID();
					foreach ($val["data"] as $key2=>$val2) {
						$this->mDatabase->query("insert into cms_settings(SectionID, Name, Value, Comments) values(".intval($id).",'".addslashes2($key2)."','".addslashes2($val2[0])."','".addslashes2($val2[1])."')");
					}
				}
  }

  /** comment here */
  function publishSettings() {
		$tmp = $this->mDatabase->getAll("select a.Name as Section, a.ID as SectID, a.Comments as SectionComments, b.* from cms_setting_groups a left outer join cms_settings b on a.ID = b.SectionID order by a.ID, b.Name asc");
		$settings = array();

		foreach ($tmp as $key=>$val) {
			$settings[$val["Section"]][] = $val;
		}
		
		$txt = "<?php \n\n";
			foreach ($settings as $key=>$val) {
				$txt .= "\t /* " . trim($key) . "*/ \n";
				if ($val[0]["SectionComments"]) $txt .= "\t #" . trim($val[0]["SectionComments"]) . "\n";
				$txt .= "\n";
				foreach ($val as $key2=>$val2) {
					if (!$val2["ID"])  continue;
					$txt .= "\t define(\"" . trim($val2["Name"]) . "\", \"" . trim(addslashes2($val2["Value"])) . "\");";
					if ($val2["Comments"])  $txt .= " //" . trim($val2["Comments"]);
					$txt .= "\n";
				}
				$txt .= "\n";
				$txt .= "\n";

			}
			$txt .= "\n";
			$txt .= "?>";

			$fh = fopen("../settings.php", "w");
			fwrite($fh, $txt);
			fclose($fh);
			die("done");


  }


  /** comment here */
  function deleteGroup($id) {
	$this->mDatabase->query("delete from cms_settings where sectionid = " . intval($id));
	$this->mDatabase->query("delete from cms_setting_groups where id = " . intval($id));
	die('ok');
  }

  /** comment here */
  function deleteSetting($id) {
	$this->mDatabase->query("delete from cms_settings where id = " . intval($id));
	die("ok");	
  }

  /** comment here */
  function createGroup($value) {
	$this->mDatabase->query("insert into cms_setting_groups(Name) values('".addslashes2($value)."')");
	die('<div class="settings-section" data-id="'.intval($this->mDatabase->getLastID()).'">
		<div class="title">'.$value.'</div>
		<div class="comments"></div>
		<div class="setting-group">
		</div>
		</div>');
  }

  /** comment here */
  function createSetting($id, $value) {
	$this->mDatabase->query("insert into cms_settings(SectionID, Name) values(".intval($id).",'".addslashes2($value)."')");
	die('<div class="settings-section" data-id="'.intval($this->mDatabase->getLastID()).'">
		<div class="setting" data-id="'.intval($this->mDatabase->getLastID()).'">
					<label>'.$value.'</label>
					<div><input type="text" value=""/></div>
					<span></span>
				</div>');
	
  }

    /** comment here */
  function updateSetting($id, $value) {
	$this->mDatabase->query("update cms_settings set value = '".addslashes2($value)."' where id = " . intval($id));
	die('ok');
  }


  /** comment here */
  function mainSwitch() {
	switch($this->mOperation) {
		case "delete-group": Return $this->deleteGroup($_GET["id"]);
		case "delete-setting": Return $this->deleteSetting($_GET["id"]);
		case "new-group": Return $this->createGroup($_GET["value"]);
		case "new-setting": Return $this->createSetting($_GET["id"], $_GET["value"]);
		case "update-setting": Return $this->updateSetting($_GET["id"], $_GET["value"]);
		case "publish-settings": Return $this->publishSettings();
		case "load-settings": Return $this->loadSettings();
		default:
		  Return CSectionAdmin::mainSwitch();
	}
  }
}

?>
