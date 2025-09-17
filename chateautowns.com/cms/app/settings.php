<?php
	
		define("TINYMCE_IMAGE_SIZES", "1280,1280x720");
		define('FOLDER_PATH', $_SERVER["DOCUMENT_ROOT"] . '\\media\\uploads\\');
		define("MFA_ENABLED", "no");
		define("MFA_TIMEOUT", "30");
		define("MASTER_CSS", "https://use.typekit.net/zpj1wza.css");

		#languages
		$cms_languages  = array(
											array("en", "English")
										);

		# core modules
		$cms_core_modules = array(); // key = url sectionname; first param = controller class; second param = user right
		$cms_core_modules["users"]=array("CUserAdmin", "users"); 
		$cms_core_modules["access"]=array("CAccessAdmin", "users"); 
		$cms_core_modules["user-groups"]=array("CUserGroupAdmin", "usergroups"); 
		$cms_core_modules["pages"]=array("CPageAdmin", "pages"); 
		$cms_core_modules["system"]=array("CSystem", "pages"); 
		$cms_core_modules["file-manager"]=array("CAssetAdmin", "filemanager"); 
		$cms_core_modules["master-templates"]=array("CTemplateAdmin", "templates"); 
		$cms_core_modules["email-templates"]=array("CEmailTemplateAdmin", "templates"); 
		$cms_core_modules["activation"]=array("CActivationAdmin", "users"); 
		$cms_core_modules["site-assets"]=array("CSiteAssetAdmin", "pages"); 
		$cms_core_modules["sample"]=array("CSampleAdmin", "users"); 


		# user defined modules
		$cms_modules = array(); // key = url sectionname; first param = controller class; second param = user right
//		$cms_modules["news"]=array("CNewsAdmin", "news"); 
//		$cms_modules["departments"]=array("CDepartmentAdmin", "team"); 
		$cms_modules["team"]=array("CTeamAdmin", "team"); 
		$cms_modules["products"]=array("CProductAdmin", "projects"); 
		$cms_modules["product-documents"]=array("CProductDocumentAdmin", "projects"); 
		$cms_modules["locations"]=array("CLocationAdmin", "projects"); 
		$cms_modules["facilities"]=array("CFacilityAdmin", "projects"); 
//
//		$cms_modules["cities"]=array("CCityAdmin", "communities"); 
//		$cms_modules["communities"]=array("CCommunityAdmin", "communities"); 
//		$cms_modules["sales-offices"]=array("CSalesOfficeAdmin", "communities"); 
//		$cms_modules["testimonials"]=array("CTestimonialAdmin", "pages"); 
//		$cms_modules["video-testimonials"]=array("CVideoTestimonialAdmin", "pages"); 
//		$cms_modules["resources"]=array("CResourceAdmin", "pages"); 
//		$cms_modules["galleries"]=array("CGalleryAdmin", "communities"); 
//		$cms_modules["gallery-images"]=array("CGalleryImageAdmin", "communities"); 
//
//		$cms_modules["collections"]=array("CCollectionAdmin", "communities"); 
//		$cms_modules["model-types"]=array("CModelTypeAdmin", "communities"); 
//		$cms_modules["models"]=array("CModelAdmin", "communities"); 
//		$cms_modules["elevations"]=array("CElevationAdmin", "communities"); 
//		$cms_modules["community-resources"]=array("CCommunityResourceAdmin", "communities"); 


		
		#menu
		$cms_menu = array();

		$cms_menu["pages"] = array("Pages", array(
																		array("pages", "Pages"),
																		array("master-templates", "Master Templates"),
																		array("file-manager", "File Manager"),
																		array("activation", "Activations"),
																		array("site-assets", "Assets (Css & Js)")
																	));
		$cms_menu["products"] = array("Products");
		$cms_menu["locations"] = array("Locations");
		$cms_menu["facilities"] = array("Facilities");
		$cms_menu["team"] = array("Team");
		$cms_menu["products"] = array("Products", array(
																		array("products", "Products"),
																		array("product-documents", "Product Documents"),
																		array("product-images", "Product Images")
																	));
		$cms_menu["users"] = array("Users & Access", array(
																				array("users", "Users"),
																				array("user-groups", "Groups")
																			)
														);

	$access_rules = array();
	#core rights
	$access_rules["system"] = array("System", array());
	$access_rules["access"] = array("Access", array());
	$access_rules["filemanager"] = array("Documents", array());
	$access_rules["pages"] = array("Site Content", array());
	$access_rules["templates"] = array("Templates", array());
	$access_rules["users"] = array("Admin Users", array());
	$access_rules["usergroups"] = array("Admin Groups", array());
	$access_rules["news"] = array("News", array());
	$access_rules["team"] = array("Team", array());
	$access_rules["projects"] = array("Projects", array());
	$access_rules["communities"] = array("Communities", array());


	# user defined
//	$access_rules["clients"] = array("Clients", array());


	/*
		Access rules

		1. Level 1 -> Section Access (implied)
		2. Level 2 -> Operation (defined)
		3. Custom rights

	*/

		#javascript
?>