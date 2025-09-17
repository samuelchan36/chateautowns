<?php
/** File Manager
* @package System
* @author Lucian Grecu
*/

class CFileManager {

	/** constructor */
	function __construct() {

	}
	
	/** 

			mixed  	$width  	The new width (smart coordinate), or null.
			mixed  	$height  	The new height (smart coordinate), or null.
			string  	$fit  	'inside', 'outside', 'fill'
			string  	$scale  	'down', 'up', 'any'
	
	*/
	function resize($src, $width = 0, $height = 0, $fit = "inside", $scale = "any") {
		CFileManager::storeOriginal($src);
		try {
			$image = WideImage::load($src);
			$resized = $image->resize($width, $height, $fit, $scale);
			$resized->saveToFile($src);
		} catch (Exception $ex) {

		}
		return $src;
	}

	/** comment here */
	function uploadToPath($name, $path) {
		$parts = explode("/", $path);
		$base = "";
		while ($slice = array_shift($parts)) {
			$base .= $slice . "/";
			@mkdir($base);
		}


		if($_FILES[$name]["tmp_name"]) {
			$path = $path . "/" . $_FILES[$name]["name"]; # resized file
            if(move_uploaded_file($_FILES[$name]["tmp_name"], ".." . $path)) {
                Return $path;
            }
        }
		Return "";
	}

	/** comment here */
	function uploadToFile($name, $path, $target) {

		$parts = explode("/", $path);
		$base = "";
		while ($slice = array_shift($parts)) {
			$base .= $slice . "/";
			@mkdir($base);
		}

		if($_FILES[$name]["tmp_name"]) {
			$tmp = pathinfo($_FILES[$name]["name"]);
			$path = $path . "/" . $target . "." . $tmp["extension"] ; # resized file
            if(move_uploaded_file($_FILES[$name]["tmp_name"], ".." . $path)) {
                Return $path;
            }
        }
		Return "";

		
	}

	/** resize image to specified width, set last parameter to false if the image should NOT be enlarged when smaller than the target width */
	function fitWidth($src, $width, $enlarge = true) {
			CFileManager::storeOriginal($src);
			try {
				$image = WideImage::load($src);
				$scale = "any"; if (!$enlarge) $scale = "down";
				$resized = $image->resize($width, null, "inside", $scale);
				$resized->saveToFile($src);
			} catch (Exception $ex) {

			}
			return $src;
	}

	/** resize image to specified width, set last parameter to false if the image should NOT be enlarged when smaller than the target width */
	function fitHeight($src, $height, $enlarge = true) {
			CFileManager::storeOriginal($src);
			try {
				$image = WideImage::load($src);
				$scale = "any"; if (!$enlarge) $scale = "down";
				$resized = $image->resize(null, $height, "inside", $scale);
				$resized->saveToFile($src);
			} catch (Exception $ex) {

			}
			return $src;
	}

	/** resize image to fit inside a box (inside = false) or outside the box (inside = true), without cropping */
	function fitToBox($src, $width = 0, $height = 0, $enlarge = true, $inside = false) {
			CFileManager::storeOriginal($src);
			try {
				$image = WideImage::load($src);
				$scale = "any"; if (!$enlarge) $scale = "down";
				$fit = "outside"; if (!$inside) $fit = "inside";
				$resized = $image->resize($width, $height, $fit, $scale);
//				$resized = $resized->crop("center", "center", $width, $height);
				$resized->saveToFile($src);
			} catch (Exception $ex) {

			}
			return $src;
	
	}



	/** crop image to specific width and height */
	function thumbnail($src, $width = 0, $height = 0, $crop_origin = array("center", "center")) {
		CFileManager::storeOriginal($src);
		try {
			$image = WideImage::load($src);
			$resized = $image->resize($width, $height, "outside", "any");
			$resized = $resized->crop($crop_origin[0], $crop_origin[1], $width, $height);

			$resized->saveToFile($src);
			} catch (Exception $ex) {

			}
		return $src;

	}

	/** fits the image inside the box without cropping and fills the box with specified color */
	function fitInBox($src, $width = 0, $height = 0, $color = array(0,0,0), $enlarge = true) {
		CFileManager::storeOriginal($src);
		try {
			$image = WideImage::load($src);
			$scale = "any"; if (!$enlarge) $scale = "down";
			$resized = $image->resize($width, $height, "inside", $scale);
//			$resized = $resized->crop("center", "center", $width, $height);
			$color = $resized->allocateColor($color[0], $color[1], $color[2]);
			$resized = $resized->resizeCanvas($width, $height, "center", "center", $color);
			$resized->saveToFile($src);
			} catch (Exception $ex) {

			}
		return $src;
		
	}

	/** comment here */
	function verifyPath($path = "", $folder = "") {
//		die(mb_detect_encoding($path));
		$path = mb_strtolower($path);
		if (file_exists($folder . $path)) {
			$tmp = pathinfo($path);
			$path = $tmp["dirname"] . "/" . $tmp["filename"] . "." . date("Ymd")  . "." . $tmp["extension"] ; # resized file
			$counter = 1;
			while (file_exists($folder . $path)) {
				$path = $tmp["dirname"] . "/" . $tmp["filename"] . "." . date("Ymd")  . "." . ($counter++). "." . $tmp["extension"];
			}
		}
		$path = mb_strtolower($path);
		Return $path;
	}

	/** comment here */
	function storeOriginal($path) {
		$tmp = pathinfo($path);
		@mkdir("../media/uploads/" . date("Ymd"));
		$newpath = CFileManager::verifyPath("../media/uploads/" . date("Ymd") . "/" . $tmp["filename"] . "." . $tmp["extension"]);
		copy($path, $newpath); 
		if (mb_strtolower($tmp["extension"]) == "jpg") ; CFileManager::correctImageOrientation($path);
		Return true;
	}


	function correctImageOrientation($filename) {
//		die($filename);
	  if (function_exists('exif_read_data')) {
		$exif = @exif_read_data($filename);
		if($exif && isset($exif['Orientation'])) {
		  $orientation = $exif['Orientation'];
		  if($orientation != 1){
			$img = imagecreatefromjpeg($filename);
			$deg = 0;
			switch ($orientation) {
			  case 3:
				$deg = 180;
				break;
			  case 6:
				$deg = 270;
				break;
			  case 8:
				$deg = 90;
				break;
			}
			if ($deg) {
			  $img = imagerotate($img, $deg, 0);        
			}
			// then rewrite the rotated image back to the disk as $filename 
			imagejpeg($img, $filename, 95);
		  } // if there is some rotation necessary
		} // if have the exif orientation info
	  } // if function exists      
	}




}


?>