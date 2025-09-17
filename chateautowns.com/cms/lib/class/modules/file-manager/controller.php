<?php
/** CSurveyAdmin
* @package surveys
* @author cgrecu
*/


class CAssetAdmin extends CSectionAdmin{

	var $table = "files";
	var $actions = array("edit", "slides", "delete");
	var $mItemsPerPage = 40;
	var $mLabels = array("Files", "File");
	var $mClass = "CAsset";

	var $accessLevel = 2;
	var $maxUploadSize = 64;
	

  /** comment here */
  function __construct() {
	parent::__construct();
	$this->checkAccess();
  }

  /** comment here */
  function checkAccess() {

		$this->maxUploadSize = min($this->maxUploadSize, intval(ini_get("memory_limit")), intval(ini_get("post_max_size")), intval(ini_get("upload_max_filesize")));
		
		$check = is_writeable("../media");
		if (!$check) $this->accessLevel = 1;
		#check user permissions, if read only, set access to 1
		# TBI

//		$this->accessLevel = 1;
  }


  /** comment here */
  function display() {

				$this->enforce();
				$this->title("Manage ". $this->mLabels[0]);

		
		$tpl = template2("lib/class/modules/file-manager/folders.html");

		$tpl->assign("MaxUploadSize", $this->maxUploadSize);

		if ($this->accessLevel < 2) {
			$tpl->assign("NoAccess", "noaccess");
			$tpl->newBlock("NOACCESS");
		}

		if (isset($_GET["folder"])) $id = intval($_GET["folder"]); else $id = 0;
		$struct = array(array("meta" => array(), "folders" => array(), "files" => array()));
		$folders = $this->mDatabase->getAll("select a.*, a.ID as FolderID, a.ParentID as ID from cms_folders a order by a.Level, a.OrderID");
		$children = array();
		foreach ($folders as $key=>$val) {
			$children[$val["ParentID"]][] = $val;
		}
		
		
		if (isset($_GET["q"]) && $_GET["q"]) {
			$search = array();
			$words = explode(" ", $_GET["q"]);
			$fields = array("name", "filename", "path");
			foreach ($fields as $key=>$val) {
				$criteria = array();
				foreach ($words as $key2=>$val2) {
					$criteria[] = " $val like '%".addslashes2(trim($val2))."%' ";
				}
				$search[] = " (".implode(" AND ", $criteria).") ";
			}
			if ($search) $search = " (".implode(" OR ", $search).") "; else $search = " 1=1 ";
			$tpl->assign("Search", $_GET["q"]);
		}  else {
			$search = " 1=1 ";
		}
		$tmp = $this->mDatabase->getAll("select * from cms_files a where $search");
		$files = array();
		$years = array();
		foreach ($tmp as $key=>$val) {
			$files[$val["FolderID"]][] = $val;
			$years[$val["Year"]] = $val["Year"];
		}
		rsort($years);
//		die2($years);
//		if (!$years) $year = date("Y");
		foreach ($years as $k=>$year) {
				$tpl->newBlock("YEARB");
				$tpl->assign("Year", $year);
		}


		foreach ($folders as $key=>$val) {
			if ($val["Level"] == 1) {
				$tpl->newBlock("FOLDER1");
				$tpl->assign("FolderID", $val["FolderID"]);
				$tpl->assign("Level", $val["Level"]);
				$tpl->assign("FolderName", htmlentities($val["Name"]));
				$tpl->assign("Path", "Start :: " . $val["Name"]);
				$tpl->assign("Thumbnail", $val["Thumbnail"]);
				$cnt = 0;
				if (isset($year) && isset($files[$year]) && isset($files[$year][$val["FolderID"]])) $cnt = count($files[$year][$val["FolderID"]]);
				
				if (isset($children[$val["FolderID"]]) && $children[$val["FolderID"]]) {
					foreach ($children[$val["FolderID"]] as $key2=>$val2) {
						$tpl->newBlock("FOLDER2");
						$tpl->assign("FolderID", $val2["FolderID"]);
						$tpl->assign("FolderName", htmlentities($val2["Name"]));
						$tpl->assign("Thumbnail", $val2["Thumbnail"]);
						$tpl->assign("Level", $val2["Level"]);
						$tpl->assign("Path", "Start :: " . $val["Name"] . " :: " . $val2["Name"] . " :: ");
						if (isset($year) && isset($files[$year]) && isset($files[$year][$val2["FolderID"]])) {
							$tpl->assign("FileCount", count($files[$year][$val2["FolderID"]]));
							$cnt += count($files[$year][$val2["FolderID"]]);
						} else {
							$tpl->assign("FileCount", 0);
						}
						if (isset($files[$val2["FolderID"]]) && $files[$val2["FolderID"]]) {
							foreach ($files[$val2["FolderID"]] as $key3=>$val3) {
								$tpl->newBlock("FILE3");
								$tpl->assign("FileID", $val3["ID"]);
								$tpl->assign("FileName", htmlentities($val3["Name"]));
								$tpl->assign("Year", $val3["Year"]);
								$tpl->assign("Extension", $val3["Extension"]);
								$tpl->assign("Thumbnail", $val3["Thumbnail"] ? $val3["Thumbnail"] : ($val3["Extension"] == "svg" ? $val3["Path"] : ""));
								$tpl->assign("Location", "https://" . $_SERVER["HTTP_HOST"] . $val3["Path"]);

								$tpl->assign("Level", $val2["Level"]);
								$tpl->assign("Path", "Start :: " . $val["Name"] . " :: " . $val2["Name"] . " :: ");
							}
						}

					}
				}

				if (isset($files[$val["FolderID"]]) && $files[$val["FolderID"]]) {
					foreach ($files[$val["FolderID"]] as $key3=>$val3) {
						$tpl->newBlock("FILE2");

						$tpl->assign("FileID", $val3["ID"]);
						$tpl->assign("FileName", htmlentities($val3["Name"]));
						$tpl->assign("Year", $val3["Year"]);
						$tpl->assign("Extension", $val3["Extension"]);
						$tpl->assign("Location", "https://" . $_SERVER["HTTP_HOST"] . $val3["Path"]);
						$tpl->assign("Thumbnail", $val3["Thumbnail"] ? $val3["Thumbnail"] : ($val3["Extension"] == "svg" ? $val3["Path"] : ""));

						$tpl->assign("Level", $val["Level"]);
						$tpl->assign("Path", "Start :: " . $val["Name"]. " :: ");
					}
				}

				$tpl->assign("FOLDER1.FileCount", $cnt);

			} 
		}

		if (isset($files[0]) && $files[0]) {
			foreach ($files[0] as $key3=>$val3) {
				$tpl->newBlock("FILE");
				$tpl->assign("FileID", $val3["ID"]);
				$tpl->assign("FileName", htmlentities($val3["Name"]));
				$tpl->assign("Year", $val3["Year"]);
				$tpl->assign("Extension", $val3["Extension"]);
				$tpl->assign("Location", "https://" . $_SERVER["HTTP_HOST"] . $val3["Path"]);
				$tpl->assign("Thumbnail", $val3["Thumbnail"]);
				$tpl->assign("Level", "1");
				$tpl->assign("Path", "Start :: ");
			}
		}

		
		
		Return $tpl->output();


	}

  /** comment here */
  function displaySlides() {
		
	  $id = intval($_GET["id"]);
				$this->enforce();
				$this->title("Manage Slides");

		$sql = "SELECT a.* from promo_slides a Where a.promoid = " . intval($id) . " order by a.OrderID ASC";
		$data = $this->mDatabase->getAll($sql);
		$tpl = template2("lib/class/modules/promos/slides.html");
		$tpl->assign("ID", $id);
		foreach ($data as $key=>$val) {
			$tpl->newBlock("SLIDE");
			$tpl->assign("SlideID", $val["ID"]);
			$tpl->assign("Caption", str_replace('"', '\"', $val["Caption"]));
			$tpl->assign("Thumbnail", str_replace('"', '\"', $val["Thumbnail"]));
			$tpl->assign("Subtitle", $val["Subtitle"]);
			$tpl->assign("CaptionStyleChecked_" . $val["CaptionStyle"], "checked");
			$tpl->assign("ButtonLabel", $val["ButtonLabel"]);
			$tpl->assign("ButtonLink", $val["ButtonLink"]);
			$tpl->assign("ButtonLabel2", $val["ButtonLabel2"]);
			$tpl->assign("ButtonLink2", $val["ButtonLink2"]);
			$tpl->assign("ButtonStyleChecked_" . $val["ButtonStyle"], "checked");

		}
		Return $tpl->output();
  }

  /** comment here */
  function getThumbnail($data) {
		Return "<img src='".$data["Thumbnail"]."'>";
  }

  /** comment here */
  function getCaption($data) {
		Return $data["Caption"]."<br>" . $data["Description"];
  }

//  /** comment here */
//  function uploadSlide() {
//		$ret = array("ret" => "no", "thumbnail" => "", "caption" => "", "description" => "", "id" => "");
//		if ($_FILES["file"]["tmp_name"]) {
//			$slide = new CAssetSlide(0, "promo_slides");
//			$_POST["PromoID"] = $_GET["id"];
//	        $slide->registerForm();
//	        $slide->mRowObj->PromoID = intval($_GET["id"]);
//	        $slide->mRowObj->ButtonStyle = "white";
//	        $slide->mRowObj->CaptionStyle = "white";
//
//			$slide->setCommonFields();
//			
//			$data = $this->mDatabase->getRow("select ID, Width, Height from promos where id = " . intval($slide->mRowObj->PromoID));
//			if ($data["Width"] && !$data["Height"]) $slide->uploadImage("file", $this->table, "", $data["Width"], 0, "fitwidth", "Image"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false
//			if ($data["Width"] && $data["Height"]) 	$slide->uploadImage("file", $this->table, "", $data["Width"], $data["Height"], "fitoutside", "Image"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false
//			if (!$data["Width"]) $slide->uploadImage("file", $this->table, "", 0,0, "nothing", "Image"); // FieldName, FolderName, FileName, Width, Height, ResizeType, TableColumn, Save = false
//			$slide->mRowObj->Thumbnail = str_replace(".", "-tn.", $slide->mRowObj->Image);
//			copy(".." . $slide->mRowObj->Image, ".." . $slide->mRowObj->Thumbnail);
//			CAsset::fitToBox(".." . $slide->mRowObj->Thumbnail, 384, 216);
//			$slide->_save();
//
//			$ret["ret"] = "ok";
//			$ret["id"] = $slide->mRowObj->ID;
//			$ret["thumbnail"] = $slide->mRowObj->Thumbnail;
//			$ret["caption"] = str_replace('"', '\"', $slide->mRowObj->Caption);
//			$ret["subtitle"] = str_replace('"', '\"', $slide->mRowObj->Subtitle);
//			$ret["label"] = str_replace('"', '\"', $slide->mRowObj->ButtonLabel);
//			$ret["link"] = str_replace('"', '\"', $slide->mRowObj->ButtonLink);
//			$ret["label2"] = str_replace('"', '\"', $slide->mRowObj->ButtonLabel2);
//			$ret["link2"] = str_replace('"', '\"', $slide->mRowObj->ButtonLink2);
//
//		}
//		die(json_encode($ret));
//  }
//
//  /** comment here */
//  function deleteSlide() {
//		$id = intval($_GET["id"]);
//		$slide = new CAssetSlide($id, "promo_slides");
//		$slide->delete();
//	die("ok");
//  }
//
//  /** comment here */
//  function updateSlide() {
//	$id = intval($_GET["id"]);
//	$slide = new CAssetSlide($id, "promo_slides");
//	$slide->registerForm($_GET);
//	$slide->easySave();
//	die("ok");
//  }

  /** comment here */
  function updateOrder() {
	$order = explode(",", $_GET["data"]);
	if ($order) {
		foreach ($order as $key=>$val) {
			$this->mDatabase->query("update promo_slides set OrderID = $key where id = " . intval($val));
		}
	}
	die("ok");
  }

  /** comment here */
  function updateFolder($id, $txt) {
	$this->mDatabase->query("update cms_folders set name = '".addslashes2($txt)."' where id = " . intval($id));
	die(json_encode($ret));
  }

  /** comment here */
  function newFolder($parentid, $folder_names) {
	  ob_start();
	$return = "";
	if (trim($folder_names)) {
		$folders = explode("\n", trim($folder_names));
		$orderid = 0;
		$folder = array();
		if ($parentid) {
			$folder = $this->mDatabase->getRow("select * from cms_folders where id = " . intval($parentid)); 
			$orderid = $this->mDatabase->getValue("cms_folders", "max(orderid)", "parentid = " . intval($parentid));
		} else {
			$orderid = $this->mDatabase->getValue("cms_folders", "max(orderid)", "parentid = 0");
		}
		foreach ($folders as $k=>$value) {
			$fields = array(); 
			if ($parentid) {
				$fields["Level"] = $folder["Level"] + 1;
				if ($folder) $fields["Guid"] = $folder["Guid"] . "/" . uniqid(""); else $fields["Guid"] = "/media/files/" . uniqid("");
			} else {
				@mkdir("../media/files");
				$fields["Level"] = 1;
				$fields["Guid"] = "/media/files/" . uniqid("");
			}
			
			@mkdir(".." . $fields["Guid"]);
			@mkdir(".." . $fields["Guid"] . "/thumbnails");
			$fields["Name"] = $value;
			$fields["ParentID"] = intval($parentid);
			$fields["Status"] = "enabled";
			$fields["Children"] = 0;
			$fields["TimeStamp"] = time();
			$fields["OrderID"] = $orderid + $k + 1;
			$this->mDatabase->query("insert into cms_folders". $this->mDatabase->makeInsertQuery($fields));
			$id = $this->mDatabase->getLastID();
			$return .= '<div class="folder level-'.$fields["Level"].'" data-path="Start :: ' . ($folder["Name"] ? $folder["Name"] . " :: " : "") . $fields["Name"] . ' :: " data-count="0"><span><input type="text" id="FolderID_'.$id.'" data-id="'.$id.'" value="Untitled" ></span></div>';
		}
	}
	ob_end_clean();
	die($return);
  }

  /** comment here */
  function deleteFolder($id) {
	  if (intval($id)) {
			$me = $this->mDatabase->getRow("select * from cms_folders where id = " . intval($id));
			$folders = $this->mDatabase->getAll("select ID, Guid from cms_folders where ParentID = " . intval($id));
			$ids = array(intval($id));
			foreach ($folders as $key=>$val) {
				$ids[] = $val["ID"];
			}
	  }

	  if ($ids) {
			$this->mDatabase->query("delete from cms_folders where id in (" . implode(",", $ids) . ")");
			$files = $this->mDatabase->getAll("select Path, Thumbnail from cms_files where FolderID in (" . implode(",", $ids) . ")");
			foreach ($files as $key=>$val) {
				@unlink(".." . $val["Path"]);
				@unlink(".." . $val["Thumbnail"]);
			}
			$this->mDatabase->query("delete from cms_files where FolderID in (" . implode(",", $ids) . ")");
	  }
	  foreach ($folders as $key=>$val) {
		rmdir (".." . $val["Guid"] . "/thumbnails");
		rmdir (".." . $val["Guid"]);
	  }
		rmdir (".." . $me["Guid"] . "/thumbnails");
		rmdir (".." . $me["Guid"]);
	  die('OK');
  }

  function updateFileName($id, $txt) {
	$this->mDatabase->query("update cms_files set name = '".addslashes2($txt)."' where id = " . intval($id));
	die(json_encode($ret));
  }

  /** comment here */
  function deleteFile($id) {
		$document = new CAsset($id, "cms_files");
		if ($document->mRowObj->ID) {
			$this->mDatabase->query("delete from cms_files where id = " . intval($id));
			unlink(".." . $document->mRowObj->Path);
			unlink(".." . $document->mRowObj->Thumbnail);
		}

		die("File deleted");
  }


  /** comment here */
  function uploadFile($parentid, $file = array()) {
	  ob_start();
	  $ret = array();
		$ret["ret"] = "notok";
		$ret["html"] = "";

		if ($_FILES["file"]["tmp_name"]) {
			$parent = $this->mDatabase->getRow("select * from cms_folders where id = " . intval($parentid));
			if ($file) {
					$fileid = $file["ID"];
					$path = $file["Path"];
					$thumbnail = $file["Thumbnail"];
					unlink(".." . $path);
					unlink(".." . $thumbnail);
			} else {
					$path = $parent["Guid"]. "/" . $_FILES['file']['name'];
					$thumbnail = "";
					if (getimagesize($_FILES['file']['tmp_name'])) {
						$thumbnail = $parent["Guid"]. "/thumbnails/" . $_FILES['file']['name'];
					}
					$fileid = 0;
					if (file_exists(".." . $path)) {
						unlink(".." . $path);
						unlink(".." . $thumbnail);
						$fileid = $this->mDatabase->getValue("cms_files", "ID", "Path='".addslashes2($path)."'");
					}
			}
			

			$ret2 = move_uploaded_file($_FILES['file']['tmp_name'], ".." . $path);
			if ($ret2) {
				if ($thumbnail) {
					copy(".." . $path, ".." . $thumbnail);
					try {
						$this->fm->fitToBox(".." . $thumbnail, 480, 480, true, true);
					} catch (Exception $x) {
						$thumbnail = "";
					}
				}


				$document = new CAsset($fileid, "cms_files");

				$document->mRowObj->FolderID = intval($parentid);
				$document->mRowObj->Year = date("Y");
				$document->mRowObj->Status = "enabled";
				$pathinfo = pathinfo($_FILES['file']['name']);
				$document->mRowObj->Name = trim($pathinfo["filename"]);
				$document->mRowObj->Filename = trim($pathinfo["filename"]);
				$document->mRowObj->Extension = trim($pathinfo["extension"]);
				
				$document->mRowObj->Path = $path;
				$document->mRowObj->Thumbnail = $thumbnail;
				$document->mRowObj->Filesize = filesize(".." . $path);
				$document->mRowObj->TimeStamp= time();
				
				$document->easySave();
				

				if (!$parent["Thumbnail"] && $thumbnail) {
					$this->mDatabase->query("update cms_folders set thumbnail = '".$thumbnail."' where id = " . intval($parentid));
				}

				$ret["ret"] = "ok";	
				$ret["html"] = '<div class="file level-'.$parent["Level"].'" data-path="" data-year="'.date("Y").'">
																<span><input type="text" id="FileID_'.$document->mRowObj->ID.'" data-id="'.$document->mRowObj->ID.'" value="'.htmlentities($document->mRowObj->Name).'" ></span>
																<div class="file-actions">
																		<a data-clipboard-text="https://' . $_SERVER["HTTP_HOST"] . $document->mRowObj->Path.'" title="Get Link" class="get-link" ><i class="fas fa-file-code"></i></a>
																		<a href="https://' . $_SERVER["HTTP_HOST"] . $document->mRowObj->Path.'" title="Download" target="_blank" class="round"><i class="fas fa-arrow-down"></i></a>
																</div>
															</div>';
		}

	}
	ob_end_clean();
	if (!$fileid) die($ret["html"]); else die("");
  }


 /** comment here */
  function updateFile($fileid) {
	  $data = $this->mDatabase->getRow("select * from cms_files where id = " . intval($fileid));
	  Return $this->uploadFile($data["FolderID"], $data);

  }

   /** comment here */
  function importFiles() {
		  ini_set("memory_limit", "8192M");
		$data = scandir("../img");

		$fields = array(); 
		$fields["Level"] = 1;
		$fields["Guid"] = "/img";
		$fields["Name"] = "Website Images";
		$fields["ParentID"] = 0;
		$fields["Status"] = "enabled";
		$fields["Children"] = 0;
		$fields["TimeStamp"] = time();
		$fields["OrderID"] = 1;
		$this->mDatabase->query("insert into cms_folders". $this->mDatabase->makeInsertQuery($fields));
		$id = $this->mDatabase->getLastID();
		@mkdir("../media/files/" . $id);
		@mkdir("../media/files/" . $id . "/thumbnails");

		foreach ($data as $key=>$val) {
			if ($val == "." || $val == "..") continue;
			$path = "/img/" . $val;
			if (is_file(".." . $path)) {
				$thumbnail = "";
				if (getimagesize(".." . $path)) {
					$thumbnail = "/media/files/" . $id . "/thumbnails/" . $val;
					copy(".." . $path, ".." . $thumbnail);
					try {
						$this->fm->thumbnail(".." . $thumbnail, 480, 480);
					} catch (Exception $x) {
						$thumbnail = "";
					}
					$info = pathinfo(".." . $path);
					$ftime = filemtime(".." . $path);
					$fields = array(); 
					$fields["FolderID"] = $id;
					$fields["Year"] = date("Y", $ftime);
					$fields["Name"] = $info["basename"];
					$fields["Filename"] = $info["basename"];
					$fields["Path"] = $path;
					$fields["Thumbnail"] = $thumbnail;
					$fields["Filesize"] = filesize(".." . $path);
					$fields["Extension"] = $info["extension"];
					$fields["ParentID"] = 0;
					$fields["Status"] = "enabled";
					$fields["Downloads"] = 0;
					$fields["TimeStamp"] = $ftime;
					$fields["MimeType"] = mime_content_type(".." . $path);
					$this->mDatabase->query("insert into cms_files". $this->mDatabase->makeInsertQuery($fields));

				}


				
			} else {
					$fields = array(); 
					$fields["Level"] = 2;
					$fields["Guid"] = "/img/" . $val;
					$fields["Name"] = $val;
					$fields["ParentID"] = $id;
					$fields["Status"] = "enabled";
					$fields["Children"] = 0;
					$fields["TimeStamp"] = time();
					$fields["OrderID"] = 1;
					$this->mDatabase->query("insert into cms_folders". $this->mDatabase->makeInsertQuery($fields));
					$folderid = $this->mDatabase->getLastID();
					@mkdir("../media/files/" . $folderid);
					@mkdir("../media/files/" . $folderid . "/thumbnails");

					$data2 = scandir("../img/" . $val);
				
					foreach ($data2 as $key2=>$val2) {
						if ($val2 == "." || $val2 == "..") continue;
						$path = "/img/" . $val . "/" . $val2;
						if (is_file(".." . $path)) {
							$thumbnail = "";
							if (getimagesize(".." . $path)) {
								$thumbnail = "/media/files/" . $folderid . "/thumbnails/" . $val2;
								copy(".." . $path, ".." . $thumbnail);
								try {
									$this->fm->thumbnail(".." . $thumbnail, 480, 480);
								} catch (Exception $x) {
									$thumbnail = "";
								}
								$info = pathinfo(".." . $path);
								$ftime = filemtime(".." . $path);
								$fields = array(); 
								$fields["FolderID"] = $folderid;
								$fields["Year"] = date("Y", $ftime);
								$fields["Name"] = $info["basename"];
								$fields["Filename"] = $info["basename"];
								$fields["Path"] = $path;
								$fields["Thumbnail"] = $thumbnail;
								$fields["Filesize"] = filesize(".." . $path);
								$fields["Extension"] = $info["extension"];
								$fields["ParentID"] = 0;
								$fields["Status"] = "enabled";
								$fields["Downloads"] = 0;
								$fields["TimeStamp"] = $ftime;
								$fields["MimeType"] = mime_content_type(".." . $path);
								$this->mDatabase->query("insert into cms_files". $this->mDatabase->makeInsertQuery($fields));
						}
					}
				}
			}
		}
		
		error("scan completed");
		redirect("/cms/file-manager");
		die();
		$data = scandir("../media/files");
		foreach ($data as $key=>$val) {
			if ($val == "." || $val == "..") continue;
			$path = "/media/files/" . $val;
			if (is_file(".." . $path)) {
				$ftime = filemtime(".." . $path);
				@mkdir("../media/files/" . date("Y", $ftime));
				@mkdir("../media/files/" . date("Y", $ftime) . "/thumbnails");
				$pathnew = "/media/files/" . date("Y", $ftime) . "/" . $val;

				$thumbnail = "";
				if (getimagesize(".." . $path)) {
					$thumbnail = "/media/files/" . date("Y", $ftime). "/thumbnails/" . $val;
					copy(".." . $path, ".." . $thumbnail);
					try {
						$this->fm->fitToBox(".." . $thumbnail, 480, 480, true, true);
					} catch (Exception $x) {
						$thumbnail = "";
					}
				}

				
				$document = new CAsset(0, "cms_files");
			
				$document->mRowObj->FolderID = 1215;
				$document->mRowObj->Year = date("Y", $ftime);
				$document->mRowObj->Status = "enabled";
				$pathinfo = pathinfo(".." . $path);
				$document->mRowObj->Name = trim($pathinfo["filename"]);
				$document->mRowObj->Filename = trim($pathinfo["filename"]);
				
				$document->mRowObj->Path = $pathnew;
				$document->mRowObj->Thumbnail = $thumbnail;
				$document->mRowObj->Filesize = filesize(".." . $path);
				$document->mRowObj->TimeStamp= $ftime;
				
				$document->easySave();
				rename(".." . $path, "..".  $pathnew);
				
			}
		}
		die2("end");
  }

  /** comment here */
  function getImagesForTiny() {
	$tpl = template2("lib/class/modules/file-manager/tinymce.html");
	$tmp = $this->mDatabase->getAll("select a.ID, a.Thumbnail, a.Path, a.Name, b.Name as Folder from cms_files a, cms_folders b where a.Thumbnail <> '' and a.FolderID = b.ID and b.Status = 'enabled' order by b.OrderID, a.timestamp desc limit 300");
	$data = array();
	foreach ($tmp as $key=>$val) {
		$data[$val["Folder"]][] = $val;
	}
	foreach ($data as $key2=>$val2) {
		$tpl->newBlock("FOLDER");
		$tpl->assign("Name", $key2);
		foreach ($val2 as $key=>$val) {
			$tpl->newBlock("FILE");
			$tpl->assign("Thumbnail", $val["Thumbnail"]);
			$tpl->assign("Path", $val["Path"]);
			$tpl->assign("Name", $val["Name"]);
		}
	}
	echo $tpl->output();die();
  }

/** comment here */
	function uploadTinyImage() {
		ob_start();
		reset ($_FILES);
	  $temp = current($_FILES);
	  if (is_uploaded_file($temp['tmp_name'])){
//		if (isset($_SERVER['HTTP_ORIGIN'])) {
//		  // same-origin requests won't set an origin. If the origin is set, it must be valid.
//		  if (in_array($_SERVER['HTTP_ORIGIN'], $accepted_origins)) {
//			header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
//		  } else {
//			header("HTTP/1.1 403 Origin Denied");
//			return;
//		  }
//		}

		/*
		  If your script needs to receive cookies, set images_upload_credentials : true in
		  the configuration and enable the following two headers.
		*/
		// header('Access-Control-Allow-Credentials: true');
		// header('P3P: CP="There is no P3P policy."');

		// Sanitize input
//		if (preg_match("/([^\w\s\d\-_~,;:\[\]\(\).])|([\.]{2,})/", $temp['name'])) {
//			header("HTTP/1.1 400 Invalid file name.");
//			return;
//		}

//		// Verify extension
//		if (!in_array(strtolower(pathinfo($temp['name'], PATHINFO_EXTENSION)), array("gif", "jpg", "png"))) {
//			header("HTTP/1.1 400 Invalid extension.");
//			return;
//		}

		$folder = $this->mDatabase->getRow("select a.ID from cms_folders a where a.Guid = '/media/files/uploads'");
		if (!$folder) {
			$this->mDatabase->query("update cms_folders set orderid = orderid + 1 where level = 1");
			$fields = array();
			$fields["Level"] = 1;
			$fields["Guid"] = "/media/files/uploads";
			@mkdir("../media/files/");
			@mkdir("../media/files/uploads");
			$fields["Name"] = "Uploads";
			$fields["ParentID"] = 0;
			$fields["OrderID"] = 1;
			$fields["Status"] = "enabled";
			$fields["Children"] = 0;
			$fields["TimeStamp"] = time();
			$this->mDatabase->query("insert into cms_folders". $this->mDatabase->makeInsertQuery($fields));
			$folderid = $this->mDatabase->getLastID();
		} else {
			$folderid = $folder["ID"];
		}

		@mkdir("../media/files/uploads/" . date("Y"));
		@mkdir("../media/files/uploads/" . date("Y") . "/thumbnails");
		$path = "/media/files/uploads/" . date("Y"). "/" . $temp['name'];

		$thumbnail = "";
		if (getimagesize($temp['tmp_name'])) {
			$thumbnail = "/media/files/uploads/" . date("Y"). "/thumbnails/" . $temp['name'];
		}
			
		$fileid = 0;
		if (file_exists(".." . $path)) {
			unlink(".." . $path);
			unlink(".." . $thumbnail);
			$fileid = $this->mDatabase->getValue("cms_files", "ID", "Path='".addslashes2($path)."'");
		}
			
		$ret2 = move_uploaded_file($temp['tmp_name'], ".." . $path);
		if ($ret2) {
			if ($thumbnail) {
				copy(".." . $path, ".." . $thumbnail);
				try {
					$this->fm->fitToBox(".." . $thumbnail, 480, 480, true, true);
				} catch (Exception $x) {
					$thumbnail = "";
				}
			}
			$w = 1920; if (isset($_GET["size"])) $w = intval($_GET["size"]); if (!$w) $w = 1920;
			$this->fm->fitWidth(".." . $path, $w);


			$document = new CAsset($fileid, "cms_files");

			$document->mRowObj->FolderID = intval($folderid);
			$document->mRowObj->Year = date("Y");
			$document->mRowObj->Status = "enabled";
			$pathinfo = pathinfo($temp['name']);
			$document->mRowObj->Name = trim($pathinfo["filename"]);
			$document->mRowObj->Filename = trim($pathinfo["filename"]);
			$document->mRowObj->Extension = trim($pathinfo["extension"]);
			
			$document->mRowObj->Path = $path;
			$document->mRowObj->Thumbnail = $thumbnail;
			$document->mRowObj->Filesize = filesize(".." . $path);
			$document->mRowObj->TimeStamp= time();
			
			$document->easySave();

//		// Accept upload if there was no origin, or if it is an accepted origin
//		$filetowrite = "/media/uploads/"  . $temp['name'];
//		move_uploaded_file($temp['tmp_name'], ".." . $filetowrite);

		// Respond to the successful upload with JSON.
		// Use a location key to specify the path to the saved image resource.
		// { location : '/your/uploaded/image/file'}
			ob_end_clean();
			die(json_encode(array('location' => $document->mRowObj->Path)));
		} else {
			header("HTTP/1.1 500 Server Error");	
		}
	  } else {
		// Notify editor that the upload failed
		header("HTTP/1.1 500 Server Error");
	  }
	}


  /** comment here */
  function sortFolders($order) {
	$order = explode(",", $order);
	foreach ($order as $key=>$val) {
		$this->mDatabase->query("update cms_folders set OrderID = " . $key . " where id = " . intval($val));
	}
	die("sort complete");
  }

  /** comment here */
  function moveFolder($id, $parent) {

	if (!$parent) {
		$parentid = 1;
		$level = 1;
	} else {
		$parentid = intval($parent);
		$level = 2;
	}
//die("update p_files set parentid = $parentid, level = $level where id = " . intval($id));
	$this->mDatabase->query("update cms_folders set parentid = $parentid, level = $level where id = " . intval($id));
	$this->mDatabase->query("update cms_folders set level = " . ($level + 1) . " where parentid = " . intval($id));
	die("folder moved");
  }


  /** comment here */
  function moveFile($id, $parentid) {

	if (!$parentid) die("no parent");
	$this->mDatabase->query("update cms_files set folderid = ". intval($parentid). " where id = " . intval($id));
	die("file moved");
  }

  /** comment here */
  function mainSwitch() {
	switch($this->mOperation) {
		case "slides": Return $this->displaySlides();
		case "upload-slide": Return $this->uploadSlide();
		case "delete-slide": Return $this->deleteSlide();
		case "update-slide": Return $this->updateSlide();
		case "update-order": Return $this->updateOrder();

		case "update-folder": Return $this->updateFolder($_GET["id"], $_GET["value"]);
		case "new-folder": Return $this->newFolder($_GET["id"], $_GET["value"]);
		case "delete-folder": Return $this->deleteFolder($_GET["id"]);

		case "sort-folders": Return $this->sortFolders($_GET["order"]);
		case "move-folder": Return $this->moveFolder($_GET["id"], $_GET["newparent"]);
		case "move-file": Return $this->moveFile($_GET["id"], $_GET["newparent"]);

		case "upload-file": Return $this->uploadFile($_GET["id"]);
		case "update-file-name": Return $this->updateFileName($_GET["id"], $_GET["value"]);
		case "update-file": Return $this->updateFile($_GET["id"]);
		case "delete-file": Return $this->deleteFile($_GET["id"]);

		case "import-files": Return $this->importFiles();

		case "upload-tiny-image": Return $this->uploadTinyImage();
		case "get-images-for-tiny": Return $this->getImagesForTiny();

		default:
		  Return CSectionAdmin::mainSwitch();
	}
  }
}

?>