<?php 


/** comment here */
function getLink($s = "") {
//	die2($_GET);
	if (!$s) $s = $_GET["s"];
	if (!$s) $s = $GLOBALS["doc"]->mModule;
	Return "/cms/" . $s . "/";
}





function buildMonth($month, $events) {
			$yr = date("Y", $month);
			$dt2 = $month - 86400;
			$wkday = date("w", $month);
			$days = date("t", $month);
			$days2 = date("t", $dt2);
			$calendar = array();
			for($i=$wkday-1; $i>=0; $i--) {
				$open = 1;
				if ($i == 0 || $i == 6) $open=0;
				if ($month - 86400 * ($wkday - $i) <= time()) $open = 0;
				$calendar[] = array($days2 - $i, $month - 86400 * ($wkday - $i), 0, $open, date("Ymd", $month - 86400 * ($wkday - $i)));
			}
			for($i=1; $i<= $days; $i++) {
				$open = 1;
				$flg = count($calendar) % 7;
				if ($flg == 0 || $flg == 6) $open=0;
				if ($month + 86400 * ($i-1) <= time()) $open = 0;
				$calendar[] = array($i, $month + 86400 * ($i-1), 1, $open, date("Ymd", $month + 86400 * ($i-1)));
				$lastdate =  $month + 86400 * ($i-1);
			}
			$lines = 7 * ceil(count($calendar)  / 7) -  count($calendar);

			for($i=1; $i<=$lines; $i++) {
				$open = 0;
				$calendar[] = array($i, $lastdate + 86400, 0, $open, date("Ymd", $lastdate + 86400));
			}



			$txt = "<table class='calendar' cellpadding='0' cellpadding='0'>";
			$txt .= "<tr><th class='active'>Sunday</th><th class='active'>Monday</th><th class='active'>Tuesday</th><th class='active'>Wednesday</th><th class='active'>Thursday</th><th class='active'>Friday</th><th class='active'>Saturday</th></tr>";
			foreach ($calendar as $key=>$val) {
				if ($events && $events[$val[4]]) $cls = 'class=\'active\'  onmouseover=\'showEvent('.$events[$val[4]][0].')\'  onmouseout=\'hideEvent('.$events[$val[4]][0].')\' '; else $cls = "";
				if ($val[1]) {
					$l =  "<div>";
					if ($events && $events[$val[4]] &&  $val[2])  {
							$f = $events[$val[4]][4];
							$l .= $f($events[$val[4]]);
					} 
					$l .= "<span>".$val[0]."</span></div>";  
				} else $l = "<div></div>";
				if ($key%7==0 && $key) $txt .= "</tr>";
				if ($key%7==0) $txt .= "<tr>";
				if (!$val[2])  $txt .= "<td class=\"diff-month\"><div>".$l."</div></td>"; else $txt .= "<td $cls>".$l."</td>";
			}
			$txt .= "</tr></table>";
			return $txt;
	}


	/** comment here */
	function jsclean($txt) {
		Return str_replace(array("\n", "\r", "\""), array("", "", "\\\""), $txt);
	}

	/** comment here */
	function cleanupPath($path) {
		Return str_replace(array(" ", "'", '"', '.'), "", $path);
	}

		function remove_array_empty_values($array, $remove_null_number = true) {
		$new_array = array();
		$null_exceptions = array();

		foreach ($array as $key => $value) {
			$value = trim($value);

			if($remove_null_number) {
				$null_exceptions[] = '0';
			}

			if(!in_array($value, $null_exceptions) && $value != "") {
				$new_array[] = $value;
			}
		}

		return $new_array;
	}
	
	function myTruncate($string, $limit, $break=".", $pad="...")
	{
	  // return with no change if string is shorter than $limit
	  if(strlen($string) <= $limit) return $string;

	  // is $break present between $limit and the end of the string?
	  if(false !== ($breakpoint = strpos($string, $break, $limit))) {
	    if($breakpoint < strlen($string) - 1) {
	      $string = substr($string, 0, $breakpoint) . $pad;
	    }
	  }

	  return $string;
	}
	
	function encode_full_url(&$url) 
	{ 
	    $url = urlencode($url); 
	    $url = str_replace("%2F", "/", $url); 
	    $url = str_replace("%3A", ":", $url); 
	    return $url; 
	}


	function tep_rewrite_email($content) {
	  $email_patt = '([A-Za-z0-9._%-]+)\@([A-Za-z0-9._%-]+)\.([A-Za-z0-9._%-]+)';
	  $mailto_pattern = '#\<a[^>]*?href=\"mailto:\s?' . $email_patt . '[^>]*?\>[^>]*?<\/a\>#';
	  $rewrite_result = '<span class="mailme">\\1 AT \\2 DOT \\3</span>';
	  $content = preg_replace($mailto_pattern, $rewrite_result, $content);
	  $content = preg_replace('#' . $email_patt . '#', $rewrite_result, $content);
	  return $content;
	}
	
		/** comment here */
	function seoword($txt) {
		Return str_replace(array('"', "'", '$', '#','%','@','^','&','*','(',')',' ','!','?','~',']','[','+','=','_',' ','–','’', '-', ":"), "-", $txt);
	}

  function searchtags($txt, $tag) {
	  $ret = array();
	  $y = 0;
	 while ($x = strpos(" " . $txt, '{' . $tag, $y)) {
		$y = strpos(" " . $txt, '}', $x);
		$ret[] = substr(" " . $txt, $x, $y - $x + 1);
	 }
	 Return $ret;
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

	/** comment here */
	function guid($type = "") {
		$path = substr(com_create_guid(), 1, -1);
		$GLOBALS["db"]->query("insert into items(Guid, Type) values('".$path."', '". addslashes2($type) . "')");
		Return array("path" => $path, "id" => $GLOBALS["db"]->getLastID());
	}


	/** comment here */
	function format_phone($phone) {

		if (strlen($phone) == 11) {
			Return substr($phone, 1,3) .  "." .  substr($phone, 4,3) . "." . substr($phone, 7);
		} 

		if (strlen($phone) == 10) {
			Return substr($phone, 0,3) . "." .  substr($phone, 6,3) . "." . substr($phone, 7);
		} 

		Return $phone;
	}

	/** comment here */
	function format_postal_code($postal_code) {
		if (strlen($postal_code) == 6) {
			Return substr($postal_code, 0,3) . " " .  substr($postal_code, 3,3);
		} 

		Return $postal_code;
		
	}



	/** comment here */
	function updateSearchIndex() {
		$GLOBALS["db"]->query("truncate search_index");
		$GLOBALS["db"]->query("insert into search_index(Title, Summary, Link, Content) select a.Code, 'Project', concat('/projects/view?id=', a.ID), concat(a.Code) from projects a");
		$GLOBALS["db"]->query("insert into search_index(Title, Summary, Link, Content) select concat(a.Code, ' - ', b.Name), 'Area', concat('/areas/view?id=', b.ID), concat(a.Code, ' ', b.Name) from projects a, project_areas b where a.id = b.projectid");
		$GLOBALS["db"]->query("insert into search_index(Title, Summary, Link, Content) select a.Name, 'Customer', concat('/customers/view?id=', a.ID), concat(a.name, ' ', a.Address, ' ', a.PostalCode, ' ', a.Country, ' ', a.Province, ' ', a.City, ' ', a.Email, ' ', a.Phone) from customers a");
		Return true;
	}

	/** comment here */
	function notify($itemid, $title, $type = "Update") {
		$notification = new CNotification();
		$notification->create($itemid, $title, $type);
	}

	function filter_filename($filename, $beautify=true) {
			// sanitize filename
			$filename = preg_replace(
				'~
				[<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
				[\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
				[\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
				[#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
				[{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
				~x',
				'-', $filename);
			// avoids ".", ".." or ".hiddenFiles"
			$filename = ltrim($filename, '.-');
			// optional beautification
			if ($beautify) $filename = beautify_filename($filename);
			// maximize filename length to 255 bytes http://serverfault.com/a/9548/44086
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			$filename = mb_strcut(pathinfo($filename, PATHINFO_FILENAME), 0, 255 - ($ext ? strlen($ext) + 1 : 0), mb_detect_encoding($filename)) . ($ext ? '.' . $ext : '');
			return $filename;
	}

	function beautify_filename($filename) {
		// reduce consecutive characters
		$filename = preg_replace(array(
			// "file   name.zip" becomes "file-name.zip"
			'/ +/',
			// "file___name.zip" becomes "file-name.zip"
			'/_+/',
			// "file---name.zip" becomes "file-name.zip"
			'/-+/'
		), '-', $filename);
		$filename = preg_replace(array(
			// "file--.--.-.--name.zip" becomes "file.name.zip"
			'/-*\.-*/',
			// "file...name..zip" becomes "file.name.zip"
			'/\.{2,}/'
		), '.', $filename);
		// lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
		$filename = mb_strtolower($filename, mb_detect_encoding($filename));
		// ".file-name.-" becomes "file-name"
		$filename = trim($filename, '.-');
		return $filename;
	}
?>