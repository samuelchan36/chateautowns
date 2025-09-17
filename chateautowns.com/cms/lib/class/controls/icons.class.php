<?php

	class CIcons {

		var $mIcons = array();
		var $mSelected = array();
		var $mExtraParam = "";

		var $access;

		function __construct($pIcons = array()) {
		  
		  $this->access = new SAccess();

		  $imgs = array();
		  $imgs["edit"] = array("edit?id=##ID##", '<i class="fas fa-edit"></i>', "Edit");
		  $imgs["view"] = array("view?id=##ID##", '<i class="fas fa-eye"></i>', "Preview");
		  $imgs["preview"] = array("preview?id=##ID##\" target=\"_blank", '<i class="fas fa-eye"></i>', "Preview");
		  $imgs["slides"] = array("slides?id=##ID##", '<i class="fas fa-images"></i>', "Manage Slides");
		  $imgs["approval"] = array("approve?id=##ID##\" class=\"table-icon confirm-icon", '<i class="far fa-thumbs-up"></i>', "Approval");
		  $imgs["details"] = array("details?id=##ID##", '<i class="fas fa-eye"></i>', "Details");
		  $imgs["filters"] = array("filters?id=##ID##", '<i class="fas fa-filter"></i>', "Manage Filters");
		  $imgs["attributes"] = array("attributes?id=##ID##", '<i class="fas fa-th"></i>', "Manage Attributes");

		  $imgs["addresses"] = array("addresses?id=##ID##", '<i class="fas fa-th"></i>', "Manage Addresses");
		  $imgs["invoice"] = array("invoice?id=##ID##", '<i class="fas fa-file-invoice-dollar"></i>', "Send Invoice");

		  $imgs["code"] = array("get-code?id=##ID##", '<i class="fas fa-code"></i>', "Get Code");
		  $imgs["sms"] = array("sms?id=##ID##", '<i class="fas fa-mobile-alt"></i>', "Send SMS");


		  $imgs["files"] = array("files?id=##ID##", '<i class="fas fa-images"></i>', "Manage Files");
		  $imgs["assign"] = array("assign?id=##ID##", "<img align=\"middle\" src=\"/cms/lib/images/common/small/icon-edit.png\" border=\"0\">", "Complete Order");
		  $imgs["review"] = array("edit?id=##ID##", "<img align=\"middle\" src=\"/cms/lib/images/common/small/icon-view.png\" border=\"0\">", "Review Complaint");
		  $imgs["undo"] = array("undo?id=##ID##", "<img align=\"middle\" src=\"/cms/lib/images/common/small/document_refresh.png\" border=\"0\">", "Undo");
		  $imgs["on"] = array("toggle?type=off&id=##ID##", "<img align=\"middle\" src=\"/cms/lib/images/common/small/bullet_ball_glass_green.png\" border=\"0\">", "Enabled", "Click to disable");
		  $imgs["off"] = array("toggle?type=on&id=##ID##", "<img align=\"middle\" src=\"/cms/lib/images/common/small/bullet_ball_glass_red.png\" border=\"0\">", "Disabled", "Click to enable");
		  $imgs["delete"] = array("delete?id=##ID##\" class=\"delete-icon", '<i class="fas fa-trash-alt"></i>', "Delete");
		  $imgs["up"] = array("move?type=up&id=##ID##", '<i class="fas fa-sort-amount-up"></i>', "Move Up");
		  $imgs["down"] = array("move?type=down&id=##ID##", '<i class="fas fa-sort-amount-down"></i>', "Move Down");
		  $imgs["archive"] = array("archive?id=##ID##", "<img align=\"middle\" src=\"/cms/lib/images/common/small/icon-close.png\" border=\"0\">", "Archived");
		  $imgs["publish"] = array("", "<img align=\"middle\" src=\"/cms/lib/images/common/small/bullet_ball_glass_blue.png\" border=\"0\" title=\"Published\" alt=\"Published\">", "Published");
		  $imgs["draft"] = array("publish?id=##ID##", "<img align=\"middle\" src=\"/cms/lib/images/common/small/bullet_ball_glass_yellow.png\" border=\"0\">", "Publish Draft");
		  $imgs["pwd"] = array("change_pass?id=##ID##", "<img align=\"middle\" src=\"/cms/lib/images/common/small/icon-reset_pass.png\" border=\"0\">", "Change Password");
		  $imgs["usr"] = array("edit_rights?id=##ID##", "<img align=\"middle\" src=\"/cms/lib/images/common/small/star_blue.png\" border=\"0\">", "Assign User Rights");
		  $imgs["list"] = array("list?id=##ID##", '<i class="fas fa-list-ul"></i>', "View Elements");
		  $imgs["list2"] = array("list2?id=##ID##", '<i class="fab fa-leanpub"></i>', "Spell check");
		  $imgs["print"] = array("print?id=##ID##", "<img align=\"middle\" src=\"/images/common/small/printer2.png\" border=\"0\">", "Print");
		  $imgs["email"] = array("email?id=##ID##", '<i class="far fa-envelope"></i>', "Test Email");
		  $imgs["resend"] = array("resend-email?id=##ID##", '<i class="far fa-envelope"></i>', "Resend Email");
		  $imgs["eblast"] = array("eblast?id=##ID##", '<i class="fas fa-mail-bulk"></i>', "Eblast");
		  $imgs["comments"] = array("comments?id=##ID##", "<img align=\"middle\" src=\"/images/common/small/document_text.png\" border=\"0\">", "View Comments");
		  $imgs["docs"] = array("main?n=docs&id=##ID##", "<img align=\"middle\" src=\"/images/common/small/folders.png\" border=\"0\">", "View Documents");
		  $imgs["clone"] = array("clone?id=##ID##", '<i class="fas fa-clone"></i>', "Clone");
		  $this->mIcons = $imgs;
		  $this->mSelected = $pIcons;
		}



  		function getIcons($pIcons = array()) {
		  if ($pIcons) $this->mSelected = $pIcons;
		  $ret = array();
		

		  foreach ($this->mSelected as $key2=>$val2) {
			if (!$this->access->check($val2)) continue;
			$val = $this->mIcons[$val2];
			$url = getLink() . $val[0];
			if ($this->mExtraParam) $url .= "&". $this->mExtraParam;
			$vHref = new CHref($url, $val[1]);
			$vHref->attributes["title"] = $val[2];
			$vHref->attributes["class"]= "table-icon";
			$ret[$val2] = $vHref;
		  }
		  Return $ret;
		}

		/** comment here */
		function displayIcons($pIcons = array()) {
		  if ($pIcons) $this->mSelected = $pIcons;
		  $rows = array();
		  foreach ($this->mSelected as $key2=>$val2) {
			  if (!$this->access->check($val2)) continue;
			$val = $this->mIcons[$val2];
			$url = $GLOBALS["vSiteManager"]->getBaseLink($GLOBALS["vUrlManager"]->mSection) . $val[0];
			if ($this->mExtraParam) $url .= "&". $this->mExtraParam;
			$vHref = new CHref($url, $val[1]);
			$vHref->attributes["title"]  = $val[2];
			$rows[] = $vHref->display();
		  }
		  Return $rows;
		}

		function getIcon($pIcon) {
		  $icon = $this->mIcons[$pIcon];
		  $url = getLink() . $icon[0];
		  if ($this->mExtraParam) $url .= "&". $this->mExtraParam;
		  $vHref = new CHref($url, str_replace(">", "title='".$icon[2]."' alt='".$icon[2]."'>", $icon[1]));
		  $vHref->attributes["title"]  = $icon[2];
		  Return $vHref;
		}

		function displayLegend($pIcons = array()) {
		  if ($pIcons) $this->mSelected = $pIcons;
		  $txt = "<b>Legend:&nbsp;&nbsp;&nbsp;</b>";
		  foreach ($this->mSelected as $key2=>$val2) {
			  if (!$this->access->check($val2)) continue;
			$val = $this->mIcons[$val2];
		  	$txt.= "$val[1]&nbsp;$val[2]&nbsp;&nbsp;&nbsp;&nbsp;";
		  }
		  Return $txt;
		}

	}

?>