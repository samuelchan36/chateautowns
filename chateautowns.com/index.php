<?php

	header('Vary: User-Agent');
	date_default_timezone_set('America/Toronto');
	mb_internal_encoding("UTF-8");
	ini_set("default_charset", "UTF-8");
	#iconv_set_encoding("internal_encoding", "UTF-8");
	#iconv_set_encoding("output_encoding", "UTF-8");

	ini_set( 'session.cookie_httponly', 1 );
	ini_set('session.use_only_cookies', 1);
	ini_set('session.cookie_secure', 1);

	session_start();
	ob_start();
	require "config.php";
	require "settings.php";
	require "lib/functions.core.php";
	require "include.php";


	$doc = new CDocument();

	try {
		$doc->display();
		ob_flush();
	} catch ( \Exception $e ) {
		ob_end_clean();
		die2($e);
		die('fatal');
	}
	
?>