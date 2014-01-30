<?php

// for deliverance pages
// check which domain we're on and call the appropriate db include
if (stristr($_SERVER['SERVER_NAME'], 'union') || stristr($_SERVER['SERVER_NAME'], 'studentaffairs') || stristr($_SERVER['SERVER_NAME'], 'campusrec')) {
	
	// connect to live database
	include("/Library/WebServer/commontools/mysql_link.inc");
	
} else {
	
	// connect to replica database
	include("/Library/WebServer/commontools/mysql_link_replica.inc");
	
}

?>