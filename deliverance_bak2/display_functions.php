<?php

// check to see if an administrative account is logged in AND make sure they're not on the staging servers.
// if logged in and live, display the 'edit | view' options
if (($_COOKIE['Deliverance'] == 'allowEdits' || $_COOKIE['Deliverance_test'] == 'allowEdits') && stristr($_SERVER['SERVER_NAME'], 'satest') === false && stristr($_SERVER['SERVER_NAME'], 'sutest') === false && stristr($_SERVER['SERVER_NAME'], 'rctest') === false) {
	// WebAuth session var present so allow editing
	echo '<div style="z-index:100; top:20px; right:20px; position:absolute; font-family:Arial; font-size:11px;"><a href="' . $_SERVER['PHP_SELF'] . '?edit=on">edit</a> | <a href="' . $_SERVER['PHP_SELF'] . '?edit=off">browse</a></div>';
	
	// if user has turned on 'edit' mode, show thin red line across top
	if ($_GET['edit'] == 'on') {
		echo '<div style="z-index:200; top:0; right:0; position:absolute; width:100%; height:2px; background-color:#ff0000;"></div>';
	}

/* 
	############################ EDIT ON 9/23/2010 ##########################
	## Having some issues w/scope when these vars are outside of the functions.
	## Global works fine here, but if these functions are called inside of another function,
	## as on dining page, the edit URL gets lost. Moving a copy into each function below.
	
	// check domain for test server (satest and sutest) and serve URLs to edit UI accordingly
	if (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false) {
		// point to the live edit UI
		$editURL = 'https://trinity.sunion.arizona.edu/commontools';
	} else {
		// point to the test edit UI
		$editURL = 'http://deliverance.test/commontools';
	}
*/

}


// FUNCTION: STATIC FEED
function staticFeed($displayBlockID) {
	
	
		## where are we? ##
		// check domain for test server (satest and sutest) and serve URLs to edit UI accordingly
		if (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false) {
			// point to the live edit UI
			$editURL = 'https://trinity.sunion.arizona.edu/commontools';
		} else {
			// point to the test edit UI
			$editURL = 'http://deliverance.test/commontools';
		} // END IF (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false)
	
		###############################################
		## code and include to support page tracking ##
		// define type for use in page/displayblock tracking
		$type = 'static';
	
		// include the page tracker code
		include("/Library/WebServer/commontools/deliverance/page_tracker.php");
		## end page tracking ##
		###############################################
	
		// QUERY: get a static resourceID to display
		$result = mysql_query("SELECT resourceID FROM current WHERE displayBlockID = " . $displayBlockID . "");
		$row = mysql_fetch_array($result);
		
		// if NULL returned, get the default value for this display block
		if (!$row) {
			$result = mysql_query("SELECT resourceID FROM defaults WHERE displayBlockID = " . $displayBlockID . "");
			$row = mysql_fetch_array($result);
		} // END IF for default check
		
		// QUERY: get the resource parameters based on its ID
		$result = mysql_query("SELECT * FROM resource WHERE id = " . $row['resourceID'] . "");
		$row = mysql_fetch_array($result);
		
		// check for edit mode
		if ($_GET['edit'] != 'on') {
		
			// check for a link on this resource
			if (!$row['resourceLink']) {
				// display the image only
				echo '<img src="' . $row['filePath'] . '" title="' . $row['altTxt'] . '" alt="' . $row['altTxt'] . '" />';
			} else {
				// display the linked image
				echo '<a href="' . $row['resourceLink'] . '"><img src="' . $row['filePath'] . '" title="' . $row['altTxt'] . '" alt="' . $row['altTxt'] . '" /></a>';
			} // END IF for link check
		
		// if edit mode is on, then link the display block to the display block editor
		} else {
			// global $editURL;
			echo '<a href="' . $editURL . '/deliverance/add_resource_static_display_block.php?displayBlockID=' . $displayBlockID . '"><img src="' . $row['filePath'] . '" title="EDIT: STATIC" alt="EDIT: STATIC" /></a>';
		} // end IF check for edit mode on

} // END FUNCTION STATIC FEED


// FUNCTION: RANDOM FEED
function randomFeed($displayBlockID) {


		## where are we? ##
		// check domain for test server (satest and sutest) and serve URLs to edit UI accordingly
		if (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false) {
			// point to the live edit UI
			$editURL = 'https://trinity.sunion.arizona.edu/commontools';
		} else {
			// point to the test edit UI
			$editURL = 'http://deliverance.test/commontools';
		} // END IF (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false)

		###############################################
		## code and include to support page tracking ##
		// define type for use in page/displayblock tracking
		$type = 'random';
	
		// include the page tracker code
		include("/Library/WebServer/commontools/deliverance/page_tracker.php");
		## end page tracking ##
		###############################################
		
		// QUERY: get a random resourceID to display
		$result = mysql_query("SELECT resourceID FROM current WHERE displayBlockID = " . $displayBlockID . " ORDER BY RAND() LIMIT 1");
		$row = mysql_fetch_array($result);
		
		// if NULL returned, get the default value for this display block
		if (!$row) {
			$result = mysql_query("SELECT resourceID FROM defaults WHERE displayBlockID = " . $displayBlockID . "");
			$row = mysql_fetch_array($result);
		} // END IF for default check
		
		// QUERY: get the resource parameters based on its ID
		$result = mysql_query("SELECT * FROM resource WHERE id = " . $row['resourceID'] . "");
		$row = mysql_fetch_array($result);

		// check for edit mode
		if ($_GET['edit'] != 'on') {

			// check for a link on this resource
			if (!$row['resourceLink']) {
				// display the image only
				echo '<img src="' . $row['filePath'] . '" title="' . $row['altTxt'] . '" alt="' . $row['altTxt'] . '" />';
			} else {
				// display the linked image
				echo '<a href="' . $row['resourceLink'] . '"><img src="' . $row['filePath'] . '" title="' . $row['altTxt'] . '" alt="' . $row['altTxt'] . '" /></a>';
			} // END IF for link check

		// if edit mode is on, then link the display block to the display block editor
		} else {
			// global $editURL;
			echo '<a href="' . $editURL . '/deliverance/add_resource_random_display_block.php?displayBlockID=' . $displayBlockID . '"><img src="' . $row['filePath'] . '" title="EDIT: RANDOM" alt="EDIT: RANDOM" /></a>';
		} // end IF check for edit mode on

} // END FUNCTION RANDOM FEED


// FUNCTION: SEQUENTIAL FEED
function sequentialFeed($displayBlockID) {


		## where are we? ##
		// check domain for test server (satest and sutest) and serve URLs to edit UI accordingly
		if (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false) {
			// point to the live edit UI
			$editURL = 'https://trinity.sunion.arizona.edu/commontools';
		} else {
			// point to the test edit UI
			$editURL = 'http://deliverance.test/commontools';
		} // END IF (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false)
		
		###############################################
		## code and include to support page tracking ##
		// define type for use in page/displayblock tracking
		$type = 'sequential';
	
		// include the page tracker code
		include("/Library/WebServer/commontools/deliverance/page_tracker.php");
		## end page tracking ##
		###############################################

		// QUERY: get a sequential resourceID to display
		$result = mysql_query("SELECT resourceID FROM current WHERE displayBlockID = " . $displayBlockID . "");
		$row = mysql_fetch_array($result);
		
		// if NULL returned, get the default value for this display block
		if (!$row) {
			$result = mysql_query("SELECT resourceID FROM defaults WHERE displayBlockID = " . $displayBlockID . "");
			$row = mysql_fetch_array($result);
		} // END IF for default check
		
		// QUERY: get the resource parameters based on its ID
		$result = mysql_query("SELECT * FROM resource WHERE id = " . $row['resourceID'] . "");
		$row = mysql_fetch_array($result);

		// check for edit mode
		if ($_GET['edit'] != 'on') {

			// check for a link on this resource
			if (!$row['resourceLink']) {
				// display the image only
				echo '<img src="' . $row['filePath'] . '" title="' . $row['altTxt'] . '" alt="' . $row['altTxt'] . '" />';
			} else {
				// display the linked image
				echo '<a href="' . $row['resourceLink'] . '"><img src="' . $row['filePath'] . '" title="' . $row['altTxt'] . '" alt="' . $row['altTxt'] . '" /></a>';
			} // END IF for link check

		// if edit mode is on, then link the display block to the display block editor
		} else {
			// global $editURL;
			echo '<a href="' . $editURL . '/deliverance/add_resource_sequential_display_block.php?displayBlockID=' . $displayBlockID . '"><img src="' . $row['filePath'] . '" title="EDIT: SEQUENTIAL" alt="EDIT: SEQUENTIAL" /></a>';
		} // end IF check for edit mode on

} // END FUNCTION SEQUENTIAL FEED

?>