<?php

require "config.php";
require "settings.php";
require "lib/functions.core.php";
require "lib/functions.php";
require "lib/database.class.php";

if (is_writeable(ROOT_DIR . "/media")) {
	@mkdir(ROOT_DIR . "/media/logs");
}

@unlink("media/logs/db-errors.txt");
@unlink("media/logs/other-errors.txt");
@unlink("media/logs/errors.txt");
@unlink("media/logs/debug.txt");
@unlink("media/logs/fatal.txt");


$db = new CDatabase(false);
$con = mysqli_connect(DB_HOST,DB_USER,DB_PWD);
if (!$con) {
		die("Error connecting to the database");
}
$db->mConnection = $con;

		$create = false;
		try {
				if (mysqli_select_db($con, DB_DB)) die("Database exists"); else $create = true;
		} catch (Exception $ex) {
			$create = true;
		}
		
		if ($create) {
				$db->query("CREATE DATABASE `".DB_DB."` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
				mysqli_select_db($con, DB_DB);
				$db->query("CREATE TABLE `cms_drafts` ( `ID` int(10) unsigned NOT NULL AUTO_INCREMENT, `ContentTable` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `ContentID` int(11) DEFAULT NULL, `ContentData` longtext COLLATE utf8mb4_unicode_ci, `TimeStamp` int(11) DEFAULT NULL, PRIMARY KEY (`ID`), KEY `ContentTable` (`ContentTable`,`ContentID`) ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
				$db->query("CREATE TABLE `cms_files` ( `ID` int(10) unsigned NOT NULL AUTO_INCREMENT, `FolderID` int(11) DEFAULT NULL, `Year` int(11) DEFAULT NULL, `Name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `Filename` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `Path` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `Filesize` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL, `Extension` varchar(32) COLLATE utf8_unicode_ci DEFAULT NULL, `Thumbnail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `ParentID` int(11) DEFAULT NULL, `Status` enum('enabled','disabled') COLLATE utf8_unicode_ci DEFAULT NULL, `TimeStamp` int(11) DEFAULT NULL, `Downloads` int(11) DEFAULT NULL, `MimeType` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, PRIMARY KEY (`ID`), KEY `FolderID` (`FolderID`), KEY `Year` (`Year`) ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
				$db->query("CREATE TABLE `cms_folders` ( `ID` int(10) unsigned NOT NULL AUTO_INCREMENT, `Guid` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `Name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `Thumbnail` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `ParentID` int(11) DEFAULT NULL, `Level` int(11) DEFAULT NULL, `Status` enum('enabled','disabled') COLLATE utf8_unicode_ci DEFAULT NULL, `TimeStamp` int(11) DEFAULT NULL, `Children` int(11) DEFAULT NULL, `OrderID` int(11) NOT NULL DEFAULT '1', PRIMARY KEY (`ID`), KEY `Guid` (`Guid`) ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
				$db->query("CREATE TABLE `cms_group_rights` ( `ID` int(10) unsigned NOT NULL AUTO_INCREMENT, `RightID` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `GroupID` int(11) DEFAULT NULL, `Section` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `Operation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL, PRIMARY KEY (`ID`), KEY `RightID` (`RightID`,`GroupID`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
				$db->query("CREATE TABLE `cms_log_queries` ( `ID` int(10) unsigned NOT NULL AUTO_INCREMENT, `TimeStamp` int(11) DEFAULT NULL, `Response` int(11) DEFAULT NULL, `Query` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, PRIMARY KEY (`ID`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
				$db->query("CREATE TABLE `cms_pages` ( `ID` int(10) unsigned NOT NULL AUTO_INCREMENT, `Title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL, `SEOTitle` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL, `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `PageImage` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL, `URLName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL, `IsRoot` enum('yes','no') CHARACTER SET utf8 NOT NULL DEFAULT 'no', `MasterPage` int(11) DEFAULT NULL, `BaseLanguage` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'en', `BaseURL` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL, `Address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL, `Filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL, `Location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL, `Content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `DiskContent` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `WorkingContent` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `TimeStamp` int(11) DEFAULT NULL, `UserID` int(11) DEFAULT NULL, `SyncStatus` enum('in-sync','newer-on-disk','newer-on-cms') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in-sync', `Status` enum('enabled','disabled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'enabled', `LastPublished` int(11) DEFAULT NULL, `LastDiskChange` int(11) DEFAULT NULL, `LastCMSChange` int(11) DEFAULT NULL, `Sitemap` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'yes', `SitemapPriority` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1', `ShowInSearch` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'yes', `AccessLevel` enum('public','private') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public', `Published` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'yes', PRIMARY KEY (`ID`), KEY `URLName` (`URLName`), KEY `Status` (`Status`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
				$db->query("CREATE TABLE `cms_setting_groups` ( `ID` int(10) unsigned NOT NULL AUTO_INCREMENT, `Name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `Comments` varchar(512) COLLATE utf8mb4_unicode_ci DEFAULT NULL, PRIMARY KEY (`ID`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
				$db->query("CREATE TABLE `cms_settings` ( `ID` int(10) unsigned NOT NULL AUTO_INCREMENT, `SectionID` int(11) DEFAULT NULL, `Name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `Value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `Comments` text COLLATE utf8mb4_unicode_ci, PRIMARY KEY (`ID`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
				$db->query("CREATE TABLE `cms_templates` ( `ID` int(10) unsigned NOT NULL AUTO_INCREMENT, `Title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL, `Filename` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL, `Location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL, `Type` enum('master','template','form','email') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'template', `Content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `DiskContent` longtext COLLATE utf8_unicode_ci, `TimeStamp` int(11) NOT NULL DEFAULT '0', `UserID` int(11) NOT NULL DEFAULT '0', `LastPublished` int(11) NOT NULL DEFAULT '0', `LastDiskChange` int(11) NOT NULL DEFAULT '0', `LastCMSChange` int(11) NOT NULL DEFAULT '0', `SyncStatus` enum('in-sync','newer-on-disk','newer-on-cms') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'in-sync', `Status` enum('enabled','disabled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'enabled', `Published` enum('yes','no') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'yes', PRIMARY KEY (`ID`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
				$db->query("CREATE TABLE `cms_timers` ( `ID` int(10) unsigned NOT NULL AUTO_INCREMENT, `ContentClass` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `ContentTable` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `ContentID` int(11) DEFAULT NULL, `TimeStamp` int(11) DEFAULT NULL, `Type` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `PublishDate` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `PublishTime` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `PublishDateTime` int(11) DEFAULT NULL, `PublishYMD` int(11) DEFAULT NULL, PRIMARY KEY (`ID`), KEY `ContentClass` (`ContentClass`,`ContentTable`,`ContentID`,`Type`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
				$db->query("CREATE TABLE `cms_tracking` ( `ID` int(10) unsigned NOT NULL AUTO_INCREMENT, `Action` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `ContentTable` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `ContentID` int(11) DEFAULT NULL, `ContentData` longtext COLLATE utf8mb4_unicode_ci, `TimeStamp` int(11) DEFAULT NULL, `UserID` int(11) DEFAULT NULL, PRIMARY KEY (`ID`), KEY `ContentTable` (`ContentTable`), KEY `ContentID` (`ContentID`), KEY `Action` (`Action`) ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
				$db->query("CREATE TABLE `cms_user_groups` ( `ID` int(11) unsigned NOT NULL AUTO_INCREMENT, `Name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '', `DeleteFlag` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'yes', `AdminGroup` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no', PRIMARY KEY (`ID`) ) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
				$db->query("CREATE TABLE `cms_user_rights` ( `ID` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '', `Name` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `Section` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL, `OrderID` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`ID`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
				$db->query("CREATE TABLE `cms_users` ( `ID` int(10) unsigned NOT NULL AUTO_INCREMENT, `Username` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `Email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `Password` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `PasswordHash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `RecoveryCode` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `RecoveryCodeExpiry` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `GroupID` int(11) DEFAULT NULL, `Name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `FirstName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `LastName` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL, `Status` enum('enabled','disabled') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'enabled', `TimeStamp` int(11) DEFAULT NULL, PRIMARY KEY (`ID`), KEY `Username` (`Username`,`Email`) ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");
				$db->query("insert  into `cms_user_groups`(`ID`,`Name`,`DeleteFlag`,`AdminGroup`) values (1,'Admin','no','yes')");
				$db->query("insert  into `cms_users`(`ID`,`Username`,`Email`,`Password`,`PasswordHash`,`RecoveryCode`,`RecoveryCodeExpiry`,`GroupID`,`Name`,`FirstName`,`LastName`,`Status`,`TimeStamp`) values (2,'lgrecu','lgrecu@joeyai.com','','$2y$10$6lyl4h.1vvH9dy1lCxuT0O8SAoox3MmMoQsBNMUY3sQd.pZY4x3lO','','0',1,'Lucian',NULL,NULL,'enabled',NULL)");
		}

if (!is_writeable("media")) echo "WARNING: Media folder is not writeable <br\>";
if (!is_writeable("media")) echo "WARNING: HTML folder is not writeable <br\>";
echo "<hr>";
die("Database sucessfully initialized. <a href='/'>Click here to continue</a>");
echo "<hr>";
?>