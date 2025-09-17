<?php   
/** CDropzone
* @package controls
* @since Sept 27
* @author lgrecu
*/


class CDropzone {
	
  var $id = 0;
  var $contentid = 0;
  var $manager = "system";
  var $attributes = array();
  var $files = array();
  var $filedata = "";

  /** comment here */
  function __construct($id = 0, $manager = "system") {
	  $this->id = "dropzone";
	  $this->contentid = $id;
	  $this->manager = $manager;
  }

  /** comment here */
  function html() {
	  if (!$_SESSION["dropzone"]) $_SESSION["dropzone"] = array();
	  $_SESSION["dropzone"][$this->id] = array();
	 
	  if ($this->files) {
		   $filedata = "";
		  foreach ($this->files as $key=>$val) {
				$_SESSION["dropzone"][$this->id][$key] = $val;
				$_SESSION["dropzone"][$this->id][$key]["Deleted"] = false;

				$filedata .=  '<div class="slide-row" data-id="'.$key.'"><input type="hidden" name="SlideID[]" value="'.$key.'"><div class="thumbnail"><img src="'.$path.'"></div><div class="caption"><h6><input data-id="'.$key.'" name="Caption[]" placeholder="Caption (optional)" value="'.$val["Name"].'" data-name="Caption" ></h6><div><textarea data-id="'.$key.'" data-name="Description" name="Description[]" placeholder="Description (optional)">'.$val["Description"].'</textarea></div></div><div class="actions"><a href="" class="delete"><img src="/cms/lib/images/common/small/delete2.png"></a></div></div>';
		  }
		  $this->filedata =  $filedata;
	  }
	$txt = '
		<link rel="stylesheet" type="text/css" href="/cms/css/dropzone.css"/>

		<div id="dropzone" class="dropzone" data-id="'.$this->contentid.'">
			<p></p>
		</div>

		<div id="statusmsg">
				
		</div>

		<div id="slides-body">'.$this->filedata.'
		</div>

		<script>
			var dropzoneManager = "'.$this->manager.'";
		</script>	
		<script type="text/javascript" src="/cms/js/dropzone.js" ></script>
	';

	Return $txt;
  }

  /** comment here */
  function label() {
	 Return "";
  }

}

?>