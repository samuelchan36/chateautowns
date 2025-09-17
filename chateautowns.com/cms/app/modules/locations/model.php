<?php
/** CLocation
* @package pages
* @author cgrecu
*/


class CLocation extends CDBContent {


	function __construct($id, $table) {
		if ($table) $this->table = $table;
		parent::__construct($id);
	}


/** comment here */
function displayEdit() {
		
		$this->form->addBlock("Details", array("label"=>"Edit Content", "class"=>"block-standard"));
		$this->form->addElement(new CText("State", $this->status, array("label"=>"Content State", "class"=> "status-" . $this->status_code)));
		$this->form->addElement(new CTextInput("Name", array("label"=>"Name", "class"=>"mandatory url-source", "data-target" => "Guid", "data-target-option" => "noslash")));
		$this->form->addElement(new CTextInput("Address", array("label"=>"Address", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("City", array("label"=>"City", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("Province", array("label"=>"Province", "class"=>"mandatory")));
		$this->form->addElement(new CTextInput("PostalCode", array("label"=>"Postal Code", "class"=>"")));
		$this->form->addElement(new CTextInput("Phone", array("label"=>"Phone", "class"=>"")));

		$this->form->addBlock("Location", array("label"=>"Edit Location", "class"=>"block-standard"));
		$this->form->addElement(new CText("LocationHelp", "<p style='max-width: 600px; margin: 0 auto 0 0 '>To obtain coordinates, open up <a href='https://maps.google.com' target='_blank'>Google Maps</a>, find the location on the map and right click on it, the first line in the context menu that opens are the GPS coordinates. <br/><br/>The first value is the latitude, the second is the longitude.</p>", array("label"=>"")));
		$this->form->addElement(new CTextInput("Latitude", array("label"=>"Latitude", "class"=>"")));
		$this->form->addElement(new CTextInput("Longitude", array("label"=>"Longitude", "class"=>"")));
		$this->form->addElement(new CTextInput("MapLink", array("label"=>"Google Maps Link", "class"=>"")));
		


		$this->publishing();
		Return $this->html();

	}

	/** comment here */
	function save() {

        $this->registerForm();
        $this->setCommonFields();
		$this->_save();



	}


  }

?>