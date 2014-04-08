<?php

// for deliverance pages
// check which domain we're on and call the appropriate db include
if (stristr($_SERVER['SERVER_NAME'], 'union') || stristr($_SERVER['SERVER_NAME'], 'studentaffairs') || stristr($_SERVER['SERVER_NAME'], 'campusrec')) {
	
	// connect to live database
	//include("/var/www/commontools/includes/mysqli.inc");
	require_once("/var/www/commontools/includes/mysqli.inc");
	
} else {
	
	// connect to replica database
	//include("/Library/WebServer/commontools/mysql_link_replica.inc");
	require_once("/var/www/commontools/includes/mysqli.inc");
	require_once("/var/www/commontools/mysql_link_replica.inc");

}

// select database

$GLOBALS['dbdeliv'] = new db_mysqli("deliverance");

?>
