<?php
	date_default_timezone_set('America/Toronto');
	mb_internal_encoding("UTF-8");
	ini_set("default_charset", "UTF-8");
	iconv_set_encoding("internal_encoding", "UTF-8");
	iconv_set_encoding("output_encoding", "UTF-8");
	session_start();


	ob_start();

	require "../config.php";
	require "../settings.php";
	require "../lib/functions.core.php";
	require "../lib/functions.php";
	require "app/settings.php";

	require "include.php";


	$doc = new CDocument();
	try {
		$doc->display();
		ob_flush();
	} catch ( \Exception $e ) {
		ob_end_clean();
		die('fatal');
	}


?>