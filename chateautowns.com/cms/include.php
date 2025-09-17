<?php
/* ************************************************************************************************************ *
**	 Common classes first - these will change very rarely
***************************************************************************************************************/
include "../vendor/autoload.php";


require_once "lib/class/functions.php";

require_once "lib/class/database.class.php";
require_once "lib/class/file.class.php";
require_once "lib/class/document.class.php";
require_once "lib/class/parser.class.php";



require_once "lib/class/db.content.class.php";
require_once "lib/class/content.class.php";
require_once "lib/class/content.simple.class.php";
require_once "../lib/template.power.class.php";
require_once "lib/class/section.admin.class.php";
require_once "lib/class/title.class.php";
require_once "lib/class/access.class.php";

require_once "lib/class/layout/style.class.php";
require_once "lib/class/layout/table.class.php";
require_once "lib/class/layout/table.box.class.php";
require_once "lib/class/layout/table.grid.class.php";
require_once "lib/class/layout/grid.class.php";
require_once "lib/class/layout/table.form.class.php";

require_once "lib/class/html/html.entity.class.php";
require_once "lib/class/html/html.form.class.php";
require_once "lib/class/html/html.form.element.class.php";
require_once "lib/class/html/html.textarea.class.php";
require_once "lib/class/html/html.rich.textarea.class.php";
require_once "lib/class/html/html.input.class.php";
require_once "lib/class/html/html.input.file.class.php";
require_once "lib/class/html/html.input.file.image.class.php";
require_once "lib/class/html/html.input.file.doc.class.php";
require_once "lib/class/html/html.input.text.class.php";
require_once "lib/class/html/html.input.password.class.php";
require_once "lib/class/html/html.input.hidden.class.php";
require_once "lib/class/html/html.input.button.class.php";
require_once "lib/class/html/html.input.submit.class.php";
require_once "lib/class/html/html.input.checkbox.class.php";
require_once "lib/class/html/html.input.radio.class.php";
require_once "lib/class/html/html.input.date.class.php";
require_once "lib/class/html/html.input.time.class.php";
require_once "lib/class/html/html.input.radio.group.class.php";
require_once "lib/class/html/html.select.class.php";
require_once "lib/class/html/html.input.int.class.php";
require_once "lib/class/html/html.input.email.class.php";
require_once "lib/class/html/html.input.float.class.php";
require_once "lib/class/html/plaintext.class.php";

require_once "lib/class/html/html.href.class.php";

require_once "lib/class/controls/smart.table.class.php";
require_once "lib/class/controls/icons.class.php";
require_once "lib/class/controls/browse.help.class.php";
require_once "lib/class/controls/dropzone.class.php";

require_once "lib/class/plugins/wideimage/WideImage.php";

include "../lib/email.class.php";

/* ************************************************************************************************************ *
**	 Custom modules - these are unique to each application
***************************************************************************************************************/


# home
require_once "app/admin.class.php";

# standard modules
//include "lib/class/modules/system/controller.php";

foreach ($cms_core_modules as $key=>$val) {
	if (file_exists("lib/class/modules/".$key."/controller.php")) {
		include "lib/class/modules/".$key."/controller.php";
		if ($key != "system") include "lib/class/modules/".$key."/model.php";
	}
}

foreach ($cms_modules as $key=>$val) {
	if (file_exists("app/modules/".$key."/controller.php")) {
		include "app/modules/".$key."/controller.php";
		include "app/modules/".$key."/model.php";
	}
}

//$cms_modules["system"]=array("CSystem", ""); 

?>