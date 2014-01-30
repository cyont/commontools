<?php

## CODE TO TRACK LIVE PAGES USING DELIVERANCE AND LOG THEM IN THE PAGES TABLE ##

// see if we're on one of the live servers AND make sure the URL is not a typo/error to avoide duplicate entries
if ((stristr($_SERVER['SERVER_NAME'], 'union') || stristr($_SERVER['SERVER_NAME'], 'studentaffairs') || stristr($_SERVER['SERVER_NAME'], 'campusrec')) && stristr($_SERVER['PHP_SELF'], '//') === false && stristr($_SERVER['PHP_SELF'], 'php/') === false && stristr($_SERVER['PHP_SELF'], 'html/') === false && stristr($_SERVER['PHP_SELF'], 'htm/') === false) {
	
	## if we are, then proceed with tracking the displayblock ##

	// create a MySQL friendly timestamp
	$mysqltime = date ("Y-m-d H:i:s", time());

	// call db include file
	include("inc_db.php");
	
	// select database
	mysql_select_db("deliverance", $DBlink)
		or die(mysql_error());
		
	// get the domain
	$domain = $_SERVER['SERVER_NAME'];
	
	// assign a consistent domain so that 'www' and/or other variations don't result in duplicate entries
	if (stristr($domain, 'union')) {
		$domain = 'http://union.arizona.edu';
	} else if (stristr($domain, 'studentaffairs')) {
		$domain = 'http://studentaffairs.arizona.edu';
	} else if (stristr($domain, 'campusrec')) {
		$domain = 'http://campusrec.arizona.edu';
	} // END IF (stristr($domain, 'union'))
	
	// assign the full file path
	$path = $_SERVER['PHP_SELF'];
	
	// QUERY: see if the record of this display block on this page already exists
	$result = mysql_query("SELECT * FROM pages WHERE displayBlockID = " . $displayBlockID . " AND domain = '" . $domain . "' AND path = '" . $path . "'");
	
	$row = mysql_fetch_array($result);
	
	// if we get results, update the record
	if ($row['id']) {

		$query = "UPDATE pages SET 
		date = \"" . $mysqltime . "\"
		WHERE id = \"" . $row['id'] . "\"";
		
		// check for errors/execute the query
		if (!mysql_query($query,$DBlink)) {
			die(mysql_error());
		} // END IF (!mysql_query($query,$DBlink))
			
	} else {
		
		// it does not exist so add a new record

		// QUERY: add record of display block  to db
		$query = "INSERT INTO pages
		(domain, path, displayBlockID, type, date)
		VALUES ('$domain', '$path', $displayBlockID, '$type', '$mysqltime')";
		
		// check for errors/execute the query
		if (!mysql_query($query,$DBlink)) {
			die(mysql_error());
		} // END IF (!mysql_query($query,$DBlink))
		
	} // END IF ($row['id'])
	
} // END IF (stristr($_SERVER['SERVER_NAME'], 'union') || stristr($_SERVER['SERVER_NAME'], 'studentaffairs'))

?>