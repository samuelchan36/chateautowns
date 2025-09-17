<?php

	class CParser {

		
		function __construct() {
		}

		
		/** comment here */
		function loadFiles($folder) {
			$files = array();
			$this->readFolder($folder, $files);
			Return $files;
		}
		/** comment here */
		function readFolder($folder, &$files) {
			
			$d = dir($folder);
			if ($d) {
					while (false !== ($entry = $d->read())) {
						if ($entry ==".") continue;
						if ($entry =="..") continue;
						if (is_dir($folder . "/" . $entry)) {
							$this->readFolder($folder . "/" . $entry, $files);
						} else {
							$files[] = $folder . "/" . $entry;
						}
					}
			}				
		}

		
		/** comment here */
		function parsePage($path) {
//			die($this->mTable);
//			Return true;
			$buf = file_get_contents($this->mBasePath ."/" . $folder . "/" . $entry);
			$filedata = pathinfo ($entry);
//die($entry);
//die2($filedata);
			$pageid = $this->mDatabase->getValue($this->mTable, "ID", "Location = '".$this->mBasePath . $folder."/" . $entry . "'");

			$obj = new CContent($this->mTable, $pageid);

			$reloadContent = false;
			if ($this->mForceReload) $reloadContent = true;
			if (!$pageid) $reloadContent = true;
			else {
				$time = filemtime($this->mBasePath ."/" . $folder . "/" . $entry);
				if ($time > $obj->mRowObj->LastPublished) {
					$reloadContent = true;
				}
			}

			$oldcontent = $obj->mRowObj->Content;
			if ($reloadContent) $obj->mRowObj->Content = $buf;

			$x = getTextBetweenTags($buf, "h1");
			$y = getTextBetweenTags($buf, "p");

			if ($this->mMode == "page" && !$obj->mRowObj->Title) $obj->mRowObj->Title = $x[0];
			if (!$obj->mRowObj->Title) $obj->mRowObj->Title = $filedata["filename"];
			if ($this->mMode == "page") {
				if (!$obj->mRowObj->SEOTitle) $obj->mRowObj->SEOTitle = $obj->mRowObj->Title;
				if (!$obj->mRowObj->Description) $obj->mRowObj->Description = $y[0];
			}
			if (!$obj->mRowObj->Address) $obj->mRowObj->Address = $folder . "/" . $filedata["filename"];
			if ($this->mMode == "page") {
				if (!$obj->mRowObj->BaseURL) $obj->mRowObj->BaseURL = $folder . "/";
			}
			if (!$obj->mRowObj->URLName) $obj->mRowObj->URLName = $filedata["filename"];
			if (!$obj->mRowObj->Location) $obj->mRowObj->Location = $this->mBasePath . $folder . "/" . $entry;

			if (!$obj->mRowObj->TimeStamp) $obj->mRowObj->TimeStamp = time();
			if (!$obj->mRowObj->LastUpdated) $obj->mRowObj->LastUpdated = $obj->mRowObj->TimeStamp; // last time the page had any change, even if the content was not affected
			if (!$obj->mRowObj->LastPublished) $obj->mRowObj->LastPublished = $obj->mRowObj->TimeStamp; // last time the page was published to disk through the CMS
			if (!$obj->mRowObj->LastCMSChange) $obj->mRowObj->LastCMSChange = $obj->mRowObj->TimeStamp; // last time the content changed in the CMS
			if (!$obj->mRowObj->UserID) $obj->mRowObj->UserID = 1;
			if ($this->mMode == "page") {
				if (!$obj->mRowObj->Status) $obj->mRowObj->Status = "enabled";
				if (!$obj->mRowObj->ShowPage) $obj->mRowObj->ShowPage = "yes";
				if (!$obj->mRowObj->Sitemap) $obj->mRowObj->Sitemap = "yes";
				if (!$obj->mRowObj->SitemapPriority) $obj->mRowObj->SitemapPriority = 1;
				if (!$obj->mRowObj->SearchStatus) $obj->mRowObj->SearchStatus = "yes";
			}
			if (!$obj->mRowObj->ContentStatus) $obj->mRowObj->ContentStatus = "yes";
			if (!$obj->mRowObj->Published) $obj->mRowObj->Published = "yes";
			

			$obj->easySave();

		}


}





?>