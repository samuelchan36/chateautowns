<?php
class CDocument {
	
	var $mCss = array();
	var $mJs = array();
	var $mMeta = array();
	var $mTemplate = "html/en/master.html";
	var $mTemplateObj;

	var $mController = "";

	var $mTestMode = true;
	var $mErrors = array();

	var $mCurrentPage = "";

	var $mPageInfo = array("PageTitle" => "", "PageDescription" => "", "PageImage" => "", "PageAddress" => "");

	var $mVersion = SITE_VERSION;

	var $mLanguages = array(
												"en" => array("English", "/"),
												"fr" => array("French", "/fr")
											);

	var $mLanguageCss = "";
	var $skip_history = false;

	/** comment here */
	function __construct() {


//		$this->detectUser();
		$this->detectLanguage();
		$this->mTemplate = "html/".$_SESSION["lang"]."/master.html";
		$this->mJs[] = "https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js";
		$this->mJs[] = "https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js";
			
		$this->mJs[] = "/lib/plugins/touch-punch/touch.punch.js";
		$this->mJs[] = "/lib/plugins/vimeo/vimeo.js";


		#fancybox
		$this->mCss[] = "/lib/plugins/fancybox/jquery.fancybox.css?v=2.1.5";
		$this->mJs[] = "/lib/plugins/fancybox/jquery.fancybox.pack.js?v=2.1.5";

		#flowtype
//		$this->mJs[] = "/lib/plugins/flowtype/flowtype.js";
		
		#lettering
		$this->mJs[] = "/lib/plugins/lettering/jquery.lettering-0.6.1.min.js";

		#textillate
		$this->mJs[] = "/lib/plugins/textillate/jquery.textillate.js";
		

		#animate number
		$this->mJs[] = "/lib/plugins/animate_number/jquery.animateNumber.min.js";

		#select2
		$this->mJs[] = "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/js/select2.min.js";
		$this->mCss[] = "https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.0/css/select2.min.css";

		#recaptcha	
		$this->mJs[] = "https://www.google.com/recaptcha/api.js?hl=en&render=" . RECAPTCHA_SITE_KEY;

		# dropzone
//		$this->mJs[] = "/lib/plugins/dropzone/dropzone.js";
		
		# cleave
		$this->mJs[] = "/lib/plugins/cleave/cleave.min.js";
		$this->mJs[] = "/lib/plugins/cleave/addons/cleave-phone.ca.js";

		#slick
		$this->mCss[] = "/lib/plugins/slick/slick.css";
		$this->mJs[] = "/lib/plugins/slick/slick.min.js";

		#zoom
//		$this->mJs[] = "/lib/plugins/zoom-master/jquery.zoom.min.js";

		#transit
		$this->mJs[] = "//cdnjs.cloudflare.com/ajax/libs/velocity/2.0.6/velocity.min.js";	

		$this->mCss[] = "/lib/css/default.css";
		$this->mCss[] = "/css/animate.css";
		$this->mCss[] = "https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css";
//		$this->mCss[] = "/css/animations.css";
		$this->mCss[] = "/css/main.css";

		
		if ($this->mLanguageCss) $this->mCss[] = $this->mLanguageCss;

		$this->mJs[] = "/lib/js/tools.js";
		$this->mJs[] = "/lib/js/forms.js";
		$this->mJs[] = "/lib/js/scrolling.js";
		$this->mJs[] = "/lib/js/search.js";
		$this->mJs[] = "/lib/js/main.js";	

		$this->mJs[] = "/js/main.js";

		#google maps
//		$this->mJs[] = "/lib/plugins/google/google.js";
//		$this->mJs[] = "https://maps.googleapis.com/maps/api/js?key=AIzaSyDxIR3CaL7lbGXwEWepDlIbDt8WPn-KEao&callback=gmaploaded";
//		$this->mJs[] = "/lib/plugins/google/markerclusterer.js";

		# jCRM
		if (JAI_TRACKING == "yes") $this->mJs[] = "https://crm.joeyai.email/tracking/track.js";

		$this->captureLeadSource();

	}


	/** comment here */
	function display() {
		
		$body = $this->body();

		$tpl = template2($this->mTemplate); 
		$this->mTemplateObj = $tpl;
		$tpl->assign("Error", $this->error());
		$tpl->assign("Head", $this->head());

		$tpl->assign("Body", $body);

		$tpl->assign("Year", date("Y"));
		$tpl->assign("Company", COMPANY);

		$tpl->assign("PageTitle", $this->mPageInfo["PageTitle"]);
		$tpl->assign("PageDescription", $this->mPageInfo["PageDescription"]);
		$tpl->assign("PageAddress", $this->mPageInfo["PageAddress"]);
		$tpl->assign("PageImage", $this->mPageInfo["PageImage"]);
		$tpl->assign("SITE_TITLE", SITE_TITLE);
		$tpl->assign("PrimaryColour", CONF_PRIMARY_COLOUR);
		$tpl->assign("SiteIcon", CONF_SITE_ICON);
		
		if (SOCIAL_FB != "") $tpl->assign("SOCIAL_FB", SOCIAL_FB); 
		if (SOCIAL_TWITTER != "") $tpl->assign("SOCIAL_TWITTER", SOCIAL_TWITTER); 
		if (SOCIAL_LINKEDIN != "") $tpl->assign("SOCIAL_LINKEDIN", SOCIAL_LINKEDIN); 
		if (SOCIAL_INSTAGRAM != "") $tpl->assign("SOCIAL_INSTAGRAM", SOCIAL_INSTAGRAM); 
		if (SOCIAL_YOUTUBE != "") $tpl->assign("SOCIAL_YOUTUBE", SOCIAL_YOUTUBE); 


		if (USE_JCRM == "yes") {
			$tpl->assign("jaiClientID", JAI_CLIENT_ID);
			$tpl->assign("jaiAccountID", JAI_ACCOUNT_ID);
			$tpl->assign("jaiWebsiteID", JAI_WEBSITE_ID);
		}

		$tpl->assign("AnalyticsCode", CONF_ANALYTICS_CODE);
		$tpl->assign("AdwordsCode", CONF_AWORDS_CODE);
		$tpl->assign("FacebookCode", CONF_FB_CODE);


		$tpl->assign("Language", $_SESSION["lang"]);
		$tpl->assign("TimeStamp", time());
		$tpl->assign("RecaptchaKey", RECAPTCHA_SITE_KEY);

		if (!$this->skip_history) $_SESSION["LastPage"] = $_SERVER["HTTP_X_REWRITE_URL"];

		$tpl->printToScreen();
	}

	/** comment here */
	function detectLanguage() {
		$datalib = new CData();

		$language = "en";

		#1. Check URL
		if (isset($_GET["o"])) {
			$parts = explode("/", $_GET["o"]);
			foreach ($this->mLanguages as $key=>$val) {
				if ($key == $parts[0]) $language = $parts[0];
			} 
		}

		if (!isset($_SESSION["lang"]) && $language == "en" && isset($_SERVER["HTTP_ACCEPT_LANGUAGE"]) && substr($_SERVER["HTTP_ACCEPT_LANGUAGE"], 0, 2) == "fr") $language = "fr";
		$_SESSION["lang"] = $language;
		if ($_SESSION["lang"] == "fr") {
			setlocale (LC_TIME, "french");
			if (file_exists("css/french.css")) $this->mLanguageCss = "/css/french.css";
		}

	}


	/** comment here */
	function head() {
		
		if (!$this->mPageInfo["PageTitle"]) $this->mPageInfo["PageTitle"] = SITE_TITLE;
		if (!$this->mPageInfo["PageDescription"]) $this->mPageInfo["PageDescription"] = SITE_DESCRIPTION;
		if (!$this->mPageInfo["PageImage"]) $this->mPageInfo["PageImage"] = CONF_SITE_IMAGE;
		if (!$this->mPageInfo["PageAddress"]) $this->mPageInfo["PageAddress"] = $_SERVER["REQUEST_URI"];

		if ($this->mPageInfo["PageImage"] && substr($this->mPageInfo["PageImage"], 0, 4) != "http") $this->mPageInfo["PageImage"] = "https://" . CONF_LIVE_DOMAIN . $this->mPageInfo["PageImage"];
		if ($this->mPageInfo["PageAddress"] && substr($this->mPageInfo["PageAddress"], 0, 4) != "http") $this->mPageInfo["PageAddress"] = "https://" . CONF_LIVE_DOMAIN . $this->mPageInfo["PageAddress"];
		
		$txt = "<title>". $this->mPageInfo["PageTitle"] ."</title>
					<meta name=\"description\" content=\"".$this->mPageInfo["PageDescription"]."\" />\n";

		foreach ($this->mMeta  as $key=>$val) {
			$txt .= $val  . "\n";
		}

		if (CONF_TEST_DOMAIN == $_SERVER["HTTP_HOST"]) $txt .= '<meta name="robots" content="noindex">' . "\n";
		
		$prefetch = array();
		foreach ($this->mJs as $key=>$val) {
			$external = false; $check = substr($val, 0, 2); if ($check == "//" || $check == "ht") $external = true;
			if (!$external) {
				$val = $val . "?v=" . $this->mVersion;
			} else {
				$info = parse_url($val);
				$prefetch[$info["host"]] = $info["host"];
			}
			$txt .= '<script type="text/javascript" src="'.$val.'" ></script>' . "\n";
		}

		foreach ($this->mCss  as $key=>$val) {
			$external = false; $check = substr($val, 0, 2); if ($check == "//" || $check == "ht") $external = true;
			if (!$external) {
				$val = $val . "?v=" . $this->mVersion;
			} else {
				$info = parse_url($val);
				$prefetch[$info["host"]] = $info["host"];
			}
			$txt .= '<link rel="stylesheet" type="text/css" href="'.$val.'"/>' . "\n";
		}

		foreach ($prefetch as $key=>$val) {
			$txt .= '<link rel="dns-prefetch" href="//'.$val.'"/>' . "\n";
		}


		Return $txt;
	}

	/** comment here */
	function body() {
		$this->mController = new CManager();
		return $this->mController->mainSwitch();
	}


	/** comment here */
	function error() {
		$ret = "";
		if (isset($_SESSION["error"]) && isset($_SESSION["error"]["message"])) {
			if ($_SESSION["error"]["message"]) $ret = '<div class="error error-'.$_SESSION["error"]["type"].'"><p>'. $_SESSION["error"]["message"] .'</p><a id="error-close"><em class="far fa-times-circle"></em></a></div>';
			unset($_SESSION["error"]);
		} else {
			$ret = '<div class="error hide"><a id="error-close"><em class="far fa-times-circle"></em></a></div>';
		}
		Return $ret;
	}

	/** comment here */
	function captureLeadSource() {
		# capture lead source
		if (!isset($_GET["utm_campaign"]) || !$_SESSION["utm_campaign"]) {
			if (isset($_COOKIE["utm_campaign"])) $_SESSION["utm_campaign"] = $_COOKIE["utm_campaign"];  else $_SESSION["utm_campaign"] = "None"; 
			if (isset($_COOKIE["utm_source"])) $_SESSION["utm_source"] = $_COOKIE["utm_source"];  else $_SESSION["utm_source"] = "Direct"; 
		}
		if (isset($_GET["utm_campaign"])) {
			$_SESSION["utm_campaign"] = $_GET["utm_campaign"]; 
			$_SESSION["utm_source"] = $_GET["utm_source"]; 
			setcookie("utm_campaign", $_GET["utm_campaign"], time() + 86400 * 7);
			setcookie("utm_source", $_GET["utm_source"], time() + 86400 * 7);
		}
		
	}	

	/** comment here */
	function detectUser() {
		if (!isset($_SESSION["UserID"]) || !$_SESSION["UserID"]) {
			
			if (isset($_GET["s"]) && $_GET["s"] == "access") {

			} else {
				redirect("/access/login");
			}
		}
	}
	
	
}
?>