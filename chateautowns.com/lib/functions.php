<?php 

			/** comment here */
			function template($folder, $file) {
				$file = "html/" . $_SESSION["lang"] . "/" . $folder . "/" . $file . ".html";
				$tpl = new TemplatePower($file);
				$tpl->prepare();
				Return $tpl;
			}

			/** comment here */
			function template2($file) {
				$tpl = new TemplatePower($file);
				$tpl->prepare();
				Return $tpl;
			}

			/** comment here */
			function _tpl($file) {
				$file = "html/" . $_SESSION["lang"] . "/dynamic/" . $file . ".html";
				$tpl = new TemplatePower($file);
				$tpl->prepare();
				Return $tpl;
			}

			/** comment here */
			function _page($file) {
				$file = "html/" . $_SESSION["lang"] . "/pages/" . $file . ".html";
				$tpl = new TemplatePower($file);
				$tpl->prepare();
				Return $tpl;
			}

			/** comment here */
			function _email($file) {
				$file = ROOT_DIR . "/html/" . $_SESSION["lang"] . "/emails/" . $file . ".html";
				$tpl = new TemplatePower($file);
				$tpl->prepare();
				Return $tpl;
			}

			/** comment here */
			function _form($file) {
				$file = "html/" . $_SESSION["lang"] . "/forms/" . $file . ".html";
				$tpl = new TemplatePower($file);
				$tpl->prepare();
				Return $tpl;
			}

			/** comment here */
			function _block($file, $data) {
				$file = "html/" . $_SESSION["lang"] . "/dynamic/" . $file . ".html";
				$tpl = new TemplatePower($file);
				$tpl->prepare();
				foreach ($data as $key=>$val) {
					$tpl->newBlock("ITEM");
					foreach ($val as $key2=>$val2) {
						$tpl->assign($key2, $val2);
					}
				}
				Return $tpl->output();
			}



			/** comment here */
			function xmlentities($txt) {
				Return htmlspecialchars(trim($txt), ENT_QUOTES,"UTF-8");
			}

			/** comment here */
			function xml($txt) {
				$txt = '<?xml version="1.0" encoding="utf-8" ?><data>'.$txt.'</data>';
				ob_end_clean();
				header("Content-type: application/xml");
				echo $txt;exit;

			}

			/** comment here */
			function addslashes2($txt) {
				if (isset($GLOBALS["db"]->mConnection)) Return mysqli_real_escape_string($GLOBALS["db"]->mConnection, $txt);
				else {
					jserror("No database connection present; $txt; " . getFileTrace());
					Return $txt;
				}
			}



			/** comment here */
			function filterApos($txt) {
			  Return  str_replace(array("&apos;", "&acirc;"), "'", $txt);

			}

			  function randStringGen() {
				$len = 12;
				$chars = " ABCD EFGH IJKL MNOP QRST UVWX YZabcd efgh ijkl mnopq rstuv wxyz0 1234 5678 9";
				$num = strlen($chars);
				$txt = "";
				$i = 0;
				while ($i < $len) {
				  $pos = rand(1, $num);
				  $buff = substr($chars, $pos, 1);
				  $txt .= $buff;
				  $i++;
				}
				return $txt;
			  }

			  /** calculate the percentage */
			  function getPercentage($pOne,$pTotal) {
				  $vPercent = ($pTotal!=0)?($pOne/$pTotal)*100:0;
				  return number_format($vPercent,2).'%';
			  }

				/** comment here */
				function sanitizeJs($txt) {
					Return str_replace(array("\n", "\r", "\""), array("", "", "\\\""), $txt);
				}

				function sanitize($string, $force_lowercase = true, $anal = false) {
					$strip = array("~", "`", "!", "@", "#", "$", "%", "^", "&", "*", "(", ")", "_", "=", "+", "[", "{", "]",
								   "}", "\\", "|", ";", ":", "\"", "'", "&#8216;", "&#8217;", "&#8220;", "&#8221;", "&#8211;", "&#8212;",
								   "â€”", "â€“", ",", "<", ".", ">", "/", "?");
					$clean = trim(str_replace($strip, "-", strip_tags($string)));
					$clean = preg_replace('/\s+/', "-", $clean);
					$clean = ($anal) ? preg_replace("/[^a-zA-Z0-9]/", "-", $clean) : $clean ;
					return ($force_lowercase) ?
						(function_exists('mb_strtolower')) ?
							mb_strtolower($clean, 'UTF-8') :
							strtolower($clean) :
						$clean;
				}


function sanitize_text_for_urls( $str ) 
{
	$friendlyURL = htmlentities($str, ENT_COMPAT, "UTF-8", false); 
    $friendlyURL = preg_replace('/&([a-z]{1,2})(?:acute|lig|grave|ring|tilde|uml|cedil|caron);/i','\1',$friendlyURL);
    $friendlyURL = html_entity_decode($friendlyURL,ENT_COMPAT, "UTF-8"); 
    $friendlyURL = preg_replace('/[^a-z0-9-]+/i', '-', $friendlyURL);
    $friendlyURL = preg_replace('/-+/', '-', $friendlyURL);
    $friendlyURL = trim($friendlyURL, '-');
    $friendlyURL = strtolower($friendlyURL);
    return $friendlyURL;
//  return strtolower( strtr( preg_replace('/[^a-zA-Z0-9-\s]/u', '', iconv( 'UTF-8', 'ASCII//TRANSLIT', $str )), ' ', '-') );
}

function camelize($str) {        
    return preg_replace("/[\s]+/","$1$2", ucwords(preg_replace('/[\s_]+/', ' ', strtolower(trim($str)))));
} 

function underscore($str) {
    return strtolower(preg_replace('/[\s]+/', '_', humanize(trim($str))));
} 

function humanize($str) {
    return trim(preg_replace("/([A-Z])|_/", " $1$2", $str));
} 

			/** comment here */
	function download($fpath, $fname = "", $inline = false) {

			//		if ($_GET["test"] == 1) echo $fpath . "<br>";
			//		$fpath = "http://www.cmls.ca/" . $fpath;
			//		die($fpath);
			//		if ($_GET["test"] == 1) die($fpath);
					if (!file_exists($fpath)) {
			if (substr($fpath, 0, 1) == "/") {
				$fpath = substr($fpath, 1);
				if (!file_exists($fpath)) die("Invalid filename");
			} else
						die("Invalid filename");
					}

						$tmp = explode("/", $fpath);

			if (!$fname) {
						$fname = array_pop($tmp);
						$tmp2 = explode(" ", $fname);
						$fname = implode("-", $tmp2);
			}
						$fsize = filesize($fpath);
						$bufsize = 20000;
						if(isset($_SERVER['HTTP_RANGE']))  {//Partial download
							if(preg_match("/^bytes=(\\d+)-(\\d*)$/", $_SERVER['HTTP_RANGE'], $matches)) { //parsing Range header
								$from = $matches[1];
								$to = $matches[2];
								if(empty($to))
								{
									$to = $fsize - 1;  // -1  because end byte is included
									  //(From HTTP protocol:
									// 'The last-byte-pos value gives the byte-offset of the last byte in the range; that is, the byte positions specified are inclusive')
								}
								$content_size = $to - $from + 1;
								header("HTTP/1.1 206 Partial Content");
								header("Content-Range: $from-$to/$fsize");
								header("Content-Length: $content_size");
					if (!$inline) {
								header("Content-Type: application/force-download");
								header("Content-Disposition: attachment; filename=$fname");
					} else {
						header('Content-Disposition: inline');
					}
								header("Content-Transfer-Encoding: binary");
								header("Cache-Control: max-age=120");
								if(file_exists($fpath) && $fh = fopen($fpath, "rb"))
								{
									fseek($fh, $from);
									$cur_pos = ftell($fh);
									while($cur_pos !== FALSE && ftell($fh) + $bufsize < $to+1)
									{
										$buffer = fread($fh, $bufsize);
										print $buffer;
										$cur_pos = ftell($fh);
									}
									$buffer = fread($fh, $to+1 - $cur_pos);
									print $buffer;
									fclose($fh);
								} else {
									header("HTTP/1.1 404 Not Found");
									exit;
								}
							} else {
								header("HTTP/1.1 500 Internal Server Error");
								exit;
							}
						} else {// Usual download
							header("HTTP/1.1 200 OK");
							header("Cache-Control: maxage=3600");
							header("Pragma: private");
							header("Content-Length: $fsize");
				if (!$inline) {
							header("Content-Type: application/octet-stream");
							header("Content-Disposition: attachment; filename=$fname");
				} else {
					$info = mime_content_type($fpath);
					header("Content-Type: " . $info);
					header("Content-Disposition: inline");
				}
							header("Content-Transfer-Encoding: binary");
							if(file_exists($fpath) && $fh = fopen($fpath, "rb")){
								while($buf = fread($fh, $bufsize))
								print $buf;
								fclose($fh);
							}
							else
							{
								header("HTTP/1.1 404 Not Found");
							}
						}
				}
				
	/** comment here */
	function formatFilesize($filesize, $digits) {
		if ($filesize < 1024)  Return $filesize . " b";
		if ($filesize < 1024 * 1024)  {
			Return round($filesize/1024, $digits) . " kb";
		}
		if ($filesize < 1024 * 1024 * 1024 )  {
			Return round($filesize/(1024 * 1024), $digits) . " mb";
		}
		if ($filesize < 1024 * 1024 * 1024 * 1024)  {
			Return round($filesize/(1024 * 1024 * 1024 ), $digits) . " gb";
		}
		Return $filesize;
	}


			function getTextBetweenTags($string, $tagname){
				$d = new DOMDocument();
				$d->loadHTML($string);
				$return = array();
				foreach($d->getElementsByTagName($tagname) as $item){
					$return[] = $item->textContent;
				}
				return $return;
			}

if(!function_exists('mime_content_type')) {

    function mime_content_type($filename) {

        $mime_types = array(

            'txt' => 'text/plain',
            'htm' => 'text/html',
            'html' => 'text/html',
            'php' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'xml' => 'application/xml',
            'swf' => 'application/x-shockwave-flash',
            'flv' => 'video/x-flv',

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',

            // archives
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            'exe' => 'application/x-msdownload',
            'msi' => 'application/x-msdownload',
            'cab' => 'application/vnd.ms-cab-compressed',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',

            // adobe
            'pdf' => 'application/pdf',
            'psd' => 'image/vnd.adobe.photoshop',
            'ai' => 'application/postscript',
            'eps' => 'application/postscript',
            'ps' => 'application/postscript',

            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',

            // open office
            'odt' => 'application/vnd.oasis.opendocument.text',
            'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        );

        $ext = strtolower(array_pop(explode('.',$filename)));
        if (array_key_exists($ext, $mime_types)) {
            return $mime_types[$ext];
        }
        elseif (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($finfo, $filename);
            finfo_close($finfo);
            return $mimetype;
        }
        else {
            return 'application/octet-stream';
        }
    }
}


function singular($str) {
    $str = strtolower(trim($str));
    $end = substr($str, -3);
    if($end == 'ies') {
        $str = substr($str, 0, strlen($str)-3).'y';
    } elseif ($end == 'ses') {
        $str = substr($str, 0, strlen($str)-2);
    } else {
        $end = substr($str, -1);
        if($end == 's'){
            $str = substr($str, 0, strlen($str)-1);
        }
    }
    return $str;
} 

function plural($str, $force = false) {
    $str = strtolower(trim($str));
    $end = substr($str, -1);

    if($end == 'y') {
        // Y preceded by vowel => regular plural
        $vowels = array('a', 'e', 'i', 'o', 'u');
        $str = in_array(substr($str, -2, 1), $vowels) ? $str.'s' : substr($str, 0, -1).'ies';
    } elseif ($end == 's') {
        if($force == true) {
            $str .= 'es';
        }
    } else {
        $str .= 's';
    }
    return $str;
} 


	/** comment here */
	function makeDateTime($dt, $tm) {
		$dtm = intval(strtotime(date("d-M-Y", $dt) . " " . strtolower($tm)));
		Return $dtm;
	}


	/** comment here */
	function exportToExcel($filename, $items) {
			require_once 'class/libs/excel/PHPExcel.php';

			$objPHPExcel = new PHPExcel();

			// Set document properties
			$objPHPExcel->getProperties()->setCreator("JoeyAi CMS")
										 ->setLastModifiedBy("Joey Ai")
										 ->setTitle("Office 2007 XLSX Test Document")
										 ->setSubject("Office 2007 XLSX Test Document")
										 ->setDescription("CMS Export")
										 ->setKeywords("office 2007 openxml php")
										 ->setCategory("Registrants");

			$sheet = $objPHPExcel->setActiveSheetIndex(0);

			$headers = array();
			foreach($items[0] as $key => $val) {
				$headers[] = $key;
			}
			foreach ($headers as $key=>$val) {
				$cnt = floor($key / 26);
				if (!$cnt) $cell_index = "";
				else if ($cnt == 1) $cell_index = "A";
				else if ($cnt == 2) $cell_index = "B";
				else if ($cnt == 3) $cell_index = "C";
				else if ($cnt == 4) $cell_index = "D";
				$cell_index .= chr($key - 26 * $cnt + 65);
				$sheet->setCellValue($cell_index . "1", $val);
			}


			foreach ($items as $key2=>$val2) {
				$index = 0;
				foreach ($val2 as $key => $val) {
					if ($key == "TimeStamp") $val = date("F d, Y H:i", $val);
					$cnt = floor($index / 26);
					if (!$cnt) $cell_index = "";
					else if ($cnt == 1) $cell_index = "A";
					else if ($cnt == 2) $cell_index = "B";
					else if ($cnt == 3) $cell_index = "C";
					else if ($cnt == 4) $cell_index = "D";
					$cell_index .= chr($index - 26 * $cnt + 65);
					$sheet->setCellValue($cell_index . ($key2 + 2), $val);
					$index++;
				}
			}



			$objPHPExcel->getActiveSheet()->setTitle('Users-' . date("Ymd.Hi"));

			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
		
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
			header('Content-Disposition: attachment;filename="' .$filename. '.xls"');
			header('Cache-Control: max-age=0');
			// If you're serving to IE 9, then the following may be needed
			header('Cache-Control: max-age=1');

			// If you're serving to IE over SSL, then the following may be needed
			header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
			header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
			header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
			header ('Pragma: public'); // HTTP/1.0
			ob_end_clean();
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			$objWriter->save('php://output');
			exit;
	}

		function hex2RGB($hexStr, $returnAsString = false, $seperator = ',') {
			$hexStr = preg_replace("/[^0-9A-Fa-f]/", '', $hexStr); // Gets a proper hex string
			$rgbArray = array();
			if (strlen($hexStr) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
				$colorVal = hexdec($hexStr);
				$rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
				$rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
				$rgbArray['blue'] = 0xFF & $colorVal;
			} elseif (strlen($hexStr) == 3) { //if shorthand notation, need some string manipulations
				$rgbArray['red'] = hexdec(str_repeat(substr($hexStr, 0, 1), 2));
				$rgbArray['green'] = hexdec(str_repeat(substr($hexStr, 1, 1), 2));
				$rgbArray['blue'] = hexdec(str_repeat(substr($hexStr, 2, 1), 2));
			} else {
				return false; //Invalid hex color code
			}
			return $returnAsString ? implode($seperator, $rgbArray) : $rgbArray; // returns the rgb string or the associative array
		} 

		function rgb2cmyk($var1,$g=0,$b=0) {

		   if(is_array($var1)) {
			  $r = $var1[0];
			  $g = $var1[1];
			  $b = $var1[2];
		   }
		   else $r=$var1;

		   $cyan    = 255 - $r;
		   $magenta = 255 - $g;
		   $yellow  = 255 - $b;
		   $black   = min($cyan, $magenta, $yellow);
		   $cyan    = $black != 255 ? (($cyan    - $black) / (255 - $black)) * 255 : 0; 
		   $magenta = $black != 255 ? (($magenta - $black) / (255 - $black)) * 255 : 0; 
		   $yellow  = $black != 255 ? (($yellow  - $black) / (255 - $black)) * 255 : 0; 
		   return array('c' => floor($cyan / 2.55),
						'm' => floor($magenta / 2.55),
						'y' => floor($yellow / 2.55),
						'k' => floor($black / 2.55));
		}



	/** comment here */
	function convertTime($stringTime) {
		$tmp =explode(" ", $stringTime);
		$tmp2 = explode(":", $tmp[0]);
		$hr = intval($tmp2[0]);
		if (count($tmp2) == 1) $min = 0; else $min = intval($tmp2[1]);
		if (count($tmp) == 1) {
			# 24hr format
		} else {
			#12hr format
			$delta = 0; if (strtoupper(trim($tmp[1])) == "PM") $delta = 12;
			if (!$delta && $hr == 12) $hr = 0;
			$hr += $delta;
		}
		Return $hr * 3600 + $min * 60;
	}
	

?>