<?php

	class CDefault {
		var $mDatabase = "";
		var $mOperation = "main";
		var $data;
		var $tracking;
	
		function __construct() {
			$this->mDatabase = &$GLOBALS["db"];			
			$this->data = new CData();
			$this->tracking = &$GLOBALS["tracking"];			
		}

		
		/** comment here */
		function display() {
			$tpl = _page(DEFAULT_FOLDER_INDEX);
			Return $this->parseCustomBlocks($tpl);
		}

		
		function displayPage($page) {


			$page = strip_tags($page);
			if (isset($_GET["preview"]) && $_GET["preview"]) {
				$data = $this->mDatabase->getRowObj("select a.ID, a.SEOTitle, a.Title, a.Description, a.PageImage, a.Published, Content from cms_pages a where a.Address = '/".addslashes2($page)."' and a.BaseLanguage = '".$_SESSION["lang"]."'");
				if ($data->Published == "no") {
					$data2 = $this->mDatabase->getRow("select ContentData from cms_drafts where ContentTable = 'cms_pages' and ContentID = " . intval($data->ID));
					if ($data2) $data = unserialize($data2["ContentData"]);
				}
				$this->seo(ucwords(mb_strtolower(str_replace(".", "", $data->SEOTitle)))  . "", str_replace('"', "&quote;", $data->Description), $data->PageImage, "");
				Return $this->parseCustomBlocks(null, $data->Content);
			} else {
				
				$data = array();
				if (DB_DB) {
					$data = $this->mDatabase->getRow("select a.ID, a.SEOTitle, a.Title, a.Description, a.PageImage from cms_pages a where a.Status = 'enabled' and a.Address = '/".addslashes2($page)."' and a.BaseLanguage = '".$_SESSION["lang"]."'");
//					if (isset($_GET["test"])) die2($data);
					if (!$data) $data = $this->mDatabase->getRow("select a.ID, a.SEOTitle, a.Title, a.Description, a.PageImage from cms_pages a where a.Status = 'enabled' and a.Address = '/".addslashes2($page)."/' and a.BaseLanguage = '".$_SESSION["lang"]."'");
					if (!$data && DEBUG_MODE == "off") return $this->pageNotFound();
				}
				if ($data) $this->seo(str_replace(".", "", $data["SEOTitle"])  . "", str_replace('"', "&quote;", $data["Description"]), $data["PageImage"], "");
				
				if ($_SESSION["lang"] != "en") $page = substr($page, count($_SESSION["lang"]) + 2);
				$location = "html/".$_SESSION["lang"]."/pages/" . $page . ".html";
				$locationDefault = "html/".$_SESSION["lang"]."/pages/" . $page . "/".DEFAULT_FOLDER_INDEX.".html";
				if (!file_exists($location)) {
					if (!file_exists($locationDefault)) {
						return $this->pageNotFound();
					} else {
						$page = $page . "/".DEFAULT_FOLDER_INDEX;
					}
				}
				$tpl = _page($page);
			}

			if (SOCIAL_FB != "") { $tpl->assign("SOCIAL_FB", SOCIAL_FB); };
			if (SOCIAL_TWITTER != "") { $tpl->assign("SOCIAL_TWITTER", SOCIAL_TWITTER); };
			if (SOCIAL_LINKEDIN != "") { $tpl->assign("SOCIAL_LINKEDIN", SOCIAL_LINKEDIN); };
			if (SOCIAL_INSTAGRAM != "") { $tpl->assign("SOCIAL_INSTAGRAM", SOCIAL_INSTAGRAM); };
			if (SOCIAL_YOUTUBE != "") { $tpl->assign("SOCIAL_YOUTUBE", SOCIAL_YOUTUBE); };

			Return $this->parseCustomBlocks($tpl);
			
		}


		/** comment here */
		function parseCustomBlocks($tpl, $content = "") {
			if ($content) $ret = $content; else $ret = $tpl->output();

			$blocks = explode("###", $ret);

			if (count($blocks) == 1) Return $ret; else {
				$output = "";
				foreach ($blocks as $key=>$val) {
					if ($key%2 == 0) $output .= $val;
					else {
						$tmp = explode("_", $val);
						$funcName = "display" . array_shift($tmp);
						if (method_exists($this, $funcName)) $output .= $this->$funcName($tmp);
					}
				}
			}
			Return $output;
		}


		/** comment here */
		function doRegister() {

			$data = $_POST;
			if ($_SERVER["HTTP_X_FORWARDED_FOR"]) $data["IPAddress"] = $_SERVER["HTTP_X_FORWARDED_FOR"]; else $data["IPAddress"] = $_SERVER["REMOTE_ADDR"]; 
			$data["SourceIP"] = $_SERVER['SERVER_ADDR'] ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
			$serialize = array();
			foreach ($data as $key=>$val) {
				if ($key == "o" || $key == "n") continue;
				if (!is_array($val)) $serialize[] = $key . "=". urlencode($val); 
				else {
					foreach ($val as $key2=>$val2) {
						$serialize[] = $key . "[]=". urlencode($val2); 
					}
				}
			}
			
			$url = "http://wms.tbf.email/registration.php";
			 $ch = curl_init($url);
			 curl_setopt($ch, CURLOPT_POST      ,1);
			 curl_setopt($ch, CURLOPT_POSTFIELDS, implode("&", $serialize));
			 curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
			 curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
			 curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
		
			 $ret = json_decode(curl_exec($ch));

			 if ($ret->Result == "yes") {

				 
//				 $email  = new CEmail($_POST);
//				 $subject = "Thank you for your registration";
//				 $template = "registration";
//				 $email->sendRich($_POST["Email"], $subject, $template);
				 
				die(json_encode(array("response" => "ok", "message" => "Thank you for your registration!")));
			 } else 
				 die(json_encode(array("message" => $ret->Message, "response" => "no")));;
		}
		

		/** comment here */
		function generateSitemap() {
			ob_clean();
			ob_start();
//		     $tpl = template("elements", "sitemap");
            $pages = $this->mDatabase->getAll("SELECT ID, Address, LastPublished as LastUpdated, SitemapPriority FROM cms_pages WHERE Status = 'enabled'  and sitemap = 'yes' ");
			$txt = '<?xml version="1.0" encoding="UTF-8"?>
						<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			foreach ($pages as $key=>$val) {
				$txt .= '<url>
								  <loc>https://' . CONF_LIVE_DOMAIN  . $val["Address"].'</loc>
								  <lastmod>'.date("Y-m-d", $val["LastUpdated"]).'</lastmod>
								  <changefreq>weekly</changefreq>
								  <priority>'.$val["SitemapPriority"].'</priority>
							</url>';
			}

			$txt .= '</urlset>';
			ob_end_clean();
			header("Content-type: application/xml");
            die($txt);

		}

		/** comment here */
		function pageNotFound() {
			if (file_exists("./html/en/pages/404.html")) {
				 $tpl = _page("404");
				Return $this->parseCustomBlocks($tpl);
			} else {
				Return "<article><section class='margin-top-100 content spaced center'><h2>Page not found</h2><p>Sorry, we cannot locate this page!</p></section></article>";
			}
		}

		/** comment here */
		function getRobotsFile() {
			if ($_SERVER["SERVER_NAME"] == CONF_TEST_DOMAIN) {
				die("User-agent: *\nDisallow: /");
			}  else {
				die("User-agent: * \nDisallow: \nAllow:");
			}
		}

		/** comment here */
		function seo($title, $description = "", $image = "", $pageaddress = "") {
			$GLOBALS["doc"]->mPageInfo["PageTitle"] = $title;
			if ($description) $GLOBALS["doc"]->mPageInfo["PageDescription"] = $description;
			if ($image) $GLOBALS["doc"]->mPageInfo["PageImage"] = $image;
			if ($pageaddress) $GLOBALS["doc"]->mPageInfo["PageAddress"] = $pageaddress;
		}

		/** comment here */
		function displayInclude($params) {
			if ($params[0]) {
				$params[0] = str_replace(array("..", "./", ".\\", ":"), "", sanitize($params[0]));
				$tpl = _tpl($params[0]);
				Return $tpl->dump();
			}
		}

		/** comment here */
		function displayForm($params) {
			if ($params[0]) {
				$params[0] = str_replace(array("..", "./", ".\\", ":"), "", sanitize($params[0]));
				$tpl = _form($params[0]);
				Return $tpl->dump();
			}
		}

		/** comment here */
		function doSearch($what) {
			$search = new CSearch();
			$data = $search->doSearch($what, true);
			$tpl = _tpl("search");
			$tpl->assign("Keywords", $what);
			$tpl->assign("Results", count($data[0]) + count($data[1]));
			foreach ($data[0] as $key=>$val) {
				$tpl->newBlock("RESULT");
				foreach ($val as $key2=>$val2) {
					$tpl->assign($key2, $val2);
				}
			}
			foreach ($data[1] as $key=>$val) {
				$tpl->newBlock("RESULT");
				foreach ($val as $key2=>$val2) {
					$tpl->assign($key2, $val2);
				}
			}
			Return $tpl->dump();

		}


		/** comment here */
		function doQuickSearch($what) {
			$search = new CSearch();
			Return $search->doQuickSearch($what);
		}


		function defaultSwitch() {
			switch($this->mOperation) {
				case "": 
				case "main": 
				case $_SESSION["lang"]: 
					Return $this->display();
				case "search": Return $this->displayPage($_GET["o"]);
				case "subscribe": Return $this->subscribe();
                case "do-register": Return $this->doRegister();
				case "refresh-pages": Return $this->refreshPages();
				case "sync-pages": Return $this->syncPages();
				case "refresh-templates": Return $this->refreshTemplates();
				case "sync-templates": Return $this->syncTemplates();
				case "google-sitemap":
				case "get-sitemap":
					Return $this->generateSitemap();
				case "page-not-found": Return $this->pageNotFound();
				case "get-robots-txt": Return $this->getRobotsFile();
				case "do-quick-search": Return $this->doQuickSearch($_GET["q"]);
				case "do-search": 
				case  "/" . $_SESSION["lang"] . "/do-search": Return $this->doSearch($_GET["q"]);
				default: Return $this->displayPage($_GET["o"]);
			}
		}


}

?>