<?php

	class SAccess {
		/** comment here */
		function enforce($right = "all") {
//			Return true;
//die2($GLOBALS["doc"]->mUserRights);

			$module_right = $GLOBALS["doc"]->mModules[$GLOBALS["doc"]->mModule][1];
			if (!$right) $right = "all";
			if (isset($GLOBALS["doc"]->mGroup) && $GLOBALS["doc"]->mGroup->mRowObj->AdminGroup == "yes") Return true;
			if (!isset($GLOBALS["access_rules"][$module_right]) || (!in_array($right, $GLOBALS["access_rules"][$module_right][1]) && $right != "all")) Return true;
			if ($GLOBALS["doc"]->mUserRights[$module_right][$right]) Return true;
			else {
				error("You are not logged in, or you do not have enough privileges to access this resource!", 2);
				redirect("/cms/index.php");
			}
		}

		/** comment here */
		function enforceRight() {
			
		}

		function check($right = "all") {

			$module_right = $GLOBALS["doc"]->mModules[$GLOBALS["doc"]->mModule][1];
			if (!$right) $right = "all";
			if (isset($GLOBALS["doc"]->mGroup) && $GLOBALS["doc"]->mGroup->mRowObj->AdminGroup == "yes") Return true;
			if (!isset($GLOBALS["access_rules"][$module_right]) || (!in_array($right, $GLOBALS["access_rules"][$module_right][1]) && $right != "all")) Return true;
			if (isset($GLOBALS["doc"]->mUserRights[$module_right][$right]) && $GLOBALS["doc"]->mUserRights[$module_right][$right]) Return true;
			Return false;
		}

		/** comment here */
		function checkOther($module_right, $right = "all") {
//			die2($GLOBALS["access_rules"]);
			
			if (!$right) $right = "all";
			if (isset($GLOBALS["doc"]->mGroup) && $GLOBALS["doc"]->mGroup->mRowObj->AdminGroup == "yes") Return true;
			if (!isset($GLOBALS["access_rules"][$module_right]) || (!in_array($right, $GLOBALS["access_rules"][$module_right][1]) && $right != "all")) Return true;
			if (isset($GLOBALS["doc"]->mUserRights[$module_right][$right]) && $GLOBALS["doc"]->mUserRights[$module_right][$right]) Return true;
			Return false;
			
		}

		/** comment here */
		function isAdmin() {
			if (isset($GLOBALS["doc"]->mGroup) && $GLOBALS["doc"]->mGroup->mRowObj->AdminGroup == "yes") Return true;
			Return false;
		}

		/** comment here */
		function isSA() {
			if (isset($GLOBALS["doc"]->mGroup) && $GLOBALS["doc"]->mGroup->mRowObj->AdminGroup == "yes") Return true;
			Return false;

		}

	}
?>