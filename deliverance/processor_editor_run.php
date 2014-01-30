<?php

// required for toggle to track updates to avoid reload of page triggering another update AND to track netID of person running the script
session_start();

// assign vars for tracking purposes
$page = 'processor_editor_run.php';
$action = 'processor';
$time = time();

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Run Updates ~ Deliverance ~ Student Affairs ~ The University of Arizona</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body class="adminUI">
<div id="containerPop">
  <div id="mainContent">

<?php
// include the CMS settings file
include("settings.php");

// make sure access is from the correct page
if ($_GET['update']) {
	
	// make sure the correct session toggle is set
	if ($_SESSION['allowUpdates']) {

		// reset the session toggle to avoid processing on a page reload
		$_SESSION['allowUpdates'] = false;

		// call db include file
		include("inc_db.php");

		// select database
		$dbdeliv = new db("deliverance");
			//mysql_select_db("deliverance", $DBlink)
			//	or die('Error: 1 ' . mysql_error());
		
		$result = mysql_query("SELECT * FROM scriptcounter WHERE id = 1");
		$row = mysql_fetch_array($result);

		if ($row['counter'] < $maxUpdates) {
	

## MAIN PROCESSOR SCRIPT STARTS HERE ##
// figure out which server the script is being run on...	
if (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false) {
	$server = 'LIVE SERVER';
} else {
	$server = 'TEST SERVER';
}

	##################################################################
	## The following scripts will process the 'displayblock' table	##
	## and update the 'current' table with the current day's run	##
	##################################################################
	
	// get today's date in format compatible w/date stored in db
	$today = date('Y-m-d',time());
	
	// init vars for use in tracking script's successful execution
	$staticOK = 'pending';
	$randomOK = 'pending';
	$sequentialOK = 'pending';
	
	// QUERY: get all the static feeds
	$staticResult = mysql_query("SELECT * FROM displayblock WHERE feedType = 'static'");
	
	// QUERY: get all the random feeds
	$randomResult = mysql_query("SELECT * FROM displayblock WHERE feedType = 'random'");

	// QUERY: get all the sequential feeds
	$sequentialResult = mysql_query("SELECT * FROM displayblock WHERE feedType = 'sequential'");



	#######################
	## STATIC PROCESSING ##
	///////////////////////
	
	// put the static values into a temporary array
	$i = 0;
	while ($staticRow = mysql_fetch_array($staticResult)) {
		// make sure a default value exists
		if ($staticRow['resourceID']) {
			$staticTmp[$i]['resourceID'] = $staticRow['resourceID'];
			$staticTmp[$i]['displayBlockID'] = $staticRow['id'];
		} else {
			// if there's no static resource defined in the displayBlock table, get a resource value from the defaults
			// QUERY: get the default resource value for the static block
			$default = mysql_query("SELECT * FROM defaults WHERE displayBlockID =  " . $staticRow['id'] . "");
			$defaultValue = mysql_fetch_array($default);
			// assign the values to the tmp arrays
			$staticTmp[$i]['resourceID'] = $defaultValue['resourceID'];
			$staticTmp[$i]['displayBlockID'] = $defaultValue['displayBlockID'];
		} // END IF ($staticRow['resourceID'])
		$i++;
	} // END WHILE ($staticRow = mysql_fetch_array($staticResult))
	//--/END STATIC PROCESSING



	#######################
	## RANDOM PROCESSING ##
	///////////////////////
	
	// init the counters for the outer and inner while loops
	$i = 0; // outer
	$j = 0; // inner

	// init the counter for default resource tracking	
	$k = 0;
		
	// loop through the list of random feed values
	while ($randomRow = mysql_fetch_array($randomResult)) {
		
		// find all the matching displayblock IDs in the random feed table that should run today
		$randomFeedResult = mysql_query("SELECT * FROM randomfeed WHERE displayBlockID = " . $randomRow['id'] . " AND startDate <= '" . $today . "' AND endDate >= '" . $today . "' ");

		// put the random values into a temporary array
		while ($randomFeedRow = mysql_fetch_array($randomFeedResult)) {
			$randomTmp[$j]['resourceID'] = $randomFeedRow['resourceID'];
			$randomTmp[$j]['displayBlockID'] = $randomFeedRow['displayBlockID'];
			
			
			// find the default resource for each displayblock ID
			$randomDefault = mysql_query("SELECT * FROM defaults WHERE displayBlockID = " . $randomFeedRow['displayBlockID'] . " ");
			$randomDefaultValue = mysql_fetch_array($randomDefault);

			// if we have not already logged this displayblock ID, log it			
			if ($tmpDisplay != $randomDefaultValue['displayBlockID']) {
				$randomTmpDefault[$k]['resourceID'] = $randomDefaultValue['resourceID'];
				$randomTmpDefault[$k]['displayBlockID'] = $randomDefaultValue['displayBlockID'];
		
				// increment the counter for the array
				$k++;
	
			} // END IF ($tmpDisplay != $randomDefaultValue['displayBlockID'])

			// track the most recent displayblock ID for comparison next time through the loop
			$tmpDisplay = $randomDefaultValue['displayBlockID'];
			
			$j++; // increment inner loop
		} // END WHILE ($randomFeedRow = mysql_fetch_array($randomFeedResult))

		$i++; // increment outer loop
		
	} // END WHILE ($randomRow = mysql_fetch_array($randomResult))
	//--/END RANDOM PROCESSING



	###########################
	## SEQUENTIAL PROCESSING ##
	///////////////////////////
	
	// init the counters for the outer and inner while loops
	$i = 0; // outer
	$j = 0; // inner
	
	// loop through the list of random feed values
	while ($sequentialRow = mysql_fetch_array($sequentialResult)) {
		
		// find all the matching displayblock IDs in the random feed table that should run today
		$sequentialFeedResult = mysql_query("SELECT * FROM sequentialfeed WHERE displayBlockID = " . $sequentialRow['id'] . " AND startDate <= '" . $today . "' AND endDate >= '" . $today . "' ");

		// put the sequential values into a temporary array
		while ($sequentialFeedRow = mysql_fetch_array($sequentialFeedResult)) {
			$sequentialTmp[$j]['resourceID'] = $sequentialFeedRow['resourceID'];
			$sequentialTmp[$j]['displayBlockID'] = $sequentialFeedRow['displayBlockID'];
			$j++; // increment inner loop
		} // END WHILE ($sequentialFeedRow = mysql_fetch_array($sequentialFeedResult))

		$i++; // increment outer loop
		
	} // END WHILE ($sequentialRow = mysql_fetch_array($sequentialResult))
	//--/END SEQUENTIAL PROCESSING



	###################
	## SAVE THE DATA ##
	###################
	
	
	// EMPTY the 'current' table so we can insert the new data for today
	$table='current';

	// check for errors clearing the table and provide on-screen feedback and error email if it fails
	if (!mysql_query("TRUNCATE $table")) {

		echo '<br /><p>There was a problem purging the database. Please email <a href="mailto:kmbeyer@email.arizona.edu">kmbeyer@email.arizona.edu</a> for assistance. Updates to the static, random and sequential values failed.</p>';

		## EMAIL
		// BEGIN: prepare to send the error notification email
		$to = 'kmbeyer@email.arizona.edu, samarketingcritical@gmail.com';
		$subject = 'ERROR: DELIVERANCE PROCESSOR SCRIPT: TRUNCATE TABLE FAILED';
		$body = "There was a problem purging the 'current' table in the database on " . date('m/d/Y H:i:s') . ". No values were updated. This script was executed on the " . $server . "."; 
		// END: prepare to send the error notification email

		// BEGIN: send the error email
		mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>');
		// END: mail script
				
		die('Error: TRUNCATE FAILED -> ' . mysql_error());
			
		} // END IF (!mysql_query("TRUNCATE $table"))

	######################
	## SAVE STATIC DATA ##
	//////////////////////
	
	// INSERT STATIC DATA INTO DB
	// loop through the static values
	$i = 0;
	while ($i < count($staticTmp)) {
		
		// make sure a value exists
		if ($staticTmp[$i]['resourceID']) {
			$resourceID = $staticTmp[$i]['resourceID'];
			$displayBlockID = $staticTmp[$i]['displayBlockID'];
	
			$query = "INSERT INTO current
			(resourceID, displayBlockID)
			VALUES ('$resourceID', '$displayBlockID')";

			// check for errors saving to the db
			if (!mysql_query($query,$DBlink)) {

				echo '<br /><p>There was a problem saving the changes to the database. Please email <a href="mailto:kmbeyer@email.arizona.edu">kmbeyer@email.arizona.edu</a> for assistance. Updates to the static, random and sequential values failed.</p>';

				
				## EMAIL
				// BEGIN: prepare to send the error notification email
				$to = 'kmbeyer@email.arizona.edu, samarketingcritical@gmail.com';
				$subject = 'ERROR: DELIVERANCE PROCESSOR SCRIPT: SAVE STATIC DATA';
				$body = "There was a problem saving the static data to the database on " . date('m/d/Y H:i:s') . ". Random and sequential values were not updated either. This script was executed on the " . $server . ".";
				// END: prepare to send the error notification email

				// BEGIN: send the error email
				mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>');
				// END: mail script
				
				die('Error: 2 ' . mysql_error());
			
				} else {
					
					// track as successfull
					$staticOK = true;
					
				} // END IF for query errors
		} // END IF ($staticTmp[$i]['resourceID'])
		
		// increment the count
		 $i++;

	} // END WHILE ($i < count($staticTmp))
	//--/END INSERT STATIC DATA INTO DB



	######################
	## SAVE RANDOM DATA ##
	//////////////////////

	// INSERT RANDOM DATA INTO DB
	// loop through the random values
	$i = 0;
	while ($i < count($randomTmp)) {
		
		$resourceID = $randomTmp[$i]['resourceID'];
		$displayBlockID = $randomTmp[$i]['displayBlockID'];
	
		$query = "INSERT INTO current
		(resourceID, displayBlockID)
		VALUES ('$resourceID', '$displayBlockID')";

		// check for errors saving to the db
		if (!mysql_query($query,$DBlink)) {

			echo '<br /><p>There was a problem saving the changes to the database. Please email <a href="mailto:kmbeyer@email.arizona.edu">kmbeyer@email.arizona.edu</a> for assistance. Static values were updated, but random and sequential values were not.</p>';
			
			## EMAIL
			// BEGIN: prepare to send the error notification email
			$to = 'kmbeyer@email.arizona.edu, samarketingcritical@gmail.com';
			$subject = 'ERROR: DELIVERANCE PROCESSOR SCRIPT: SAVE RANDOM DATA';
			$body = "There was a problem saving the random data to the database on " . date('m/d/Y H:i:s') . ". Static values were updated, but random and sequential values were not. This script was executed on the " . $server . ".";
			// END: prepare to send the error notification email

			// BEGIN: send the error email
			mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>');
			// END: mail script
			
			die('Error: 3 ' . mysql_error());
						
			} else {
	
				// track as successfull
				$randomOK = true;
						
			} // END IF for query errors
			
		// increment the count
		 $i++;
		 
	} // END WHILE ($i < count($randomTmp))
	//--/END INSERT RANDOM DATA INTO DB



	##############################
	## SAVE RANDOM DEFAULT DATA ##
	//////////////////////////////

	// INSERT RANDOM DEFAULTS INTO DB
	// loop through the random values
	$i = 0;
	while ($i < count($randomTmpDefault)) {
		
		$resourceID = $randomTmpDefault[$i]['resourceID'];
		$displayBlockID = $randomTmpDefault[$i]['displayBlockID'];
	
		$query = "INSERT INTO current
		(resourceID, displayBlockID)
		VALUES ('$resourceID', '$displayBlockID')";

		// check for errors saving to the db
		if (!mysql_query($query,$DBlink)) {

			echo '<br /><p>There was a problem saving the changes to the database. Please email <a href="mailto:kmbeyer@email.arizona.edu">kmbeyer@email.arizona.edu</a> for assistance. Static values were updated, but random and sequential values were not.</p>';
			
			## EMAIL
			// BEGIN: prepare to send the error notification email
			$to = 'kmbeyer@email.arizona.edu, samarketingcritical@gmail.com';
			$subject = 'ERROR: DELIVERANCE PROCESSOR SCRIPT: SAVE RANDOM DATA';
			$body = "There was a problem saving the random data to the database on " . date('m/d/Y H:i:s') . ". Static values were updated, but random and sequential values were not. This script was executed on the " . $server . "."; 
			// END: prepare to send the error notification email

			// BEGIN: send the error email
			mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>');
			// END: mail script
			
			die('Error: 3.5 ' . mysql_error());
						
			} else {
	
				// track as successfull
				$randomOK = true;
						
			} // END IF for query errors
			
		// increment the count
		 $i++;
		 
	} // END WHILE ($i < count($randomTmpDefault))
	//--/END INSERT RANDOM DEFAULTS INTO DB



	##########################
	## SAVE SEQUENTIAL DATA ##
	//////////////////////////
	
	// INSERT SEQUENTIAL DATA INTO DB
	// loop through the sequential values
	$i = 0;
	while ($i < count($sequentialTmp)) {
		
		$resourceID = $sequentialTmp[$i]['resourceID'];
		$displayBlockID = $sequentialTmp[$i]['displayBlockID'];
	
		$query = "INSERT INTO current
		(resourceID, displayBlockID)
		VALUES ('$resourceID', '$displayBlockID')";

		// check for errors saving to the db
		if (!mysql_query($query,$DBlink)) {

			echo '<br /><p>There was a problem saving the changes to the database. Please email <a href="mailto:kmbeyer@email.arizona.edu">kmbeyer@email.arizona.edu</a> for assistance. Static and random values were updated, but sequential values were not.</p>';
			
			## EMAIL
			// BEGIN: prepare to send the error notification email
			$to = 'kmbeyer@email.arizona.edu, samarketingcritical@gmail.com';
			$subject = 'ERROR: DELIVERANCE PROCESSOR SCRIPT: SAVE SEQUENTIAL DATA';
			$body = "There was a problem saving the sequential data to the database on " . date('m/d/Y H:i:s') . ". Static and random values were updated, but sequential values were not. This script was executed on the " . $server . ".";
			// END: prepare to send the error notification email

			// BEGIN: send the error email
			mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>');
			// END: mail script
			
			die('Error: 4 ' . mysql_error());			
			
			} else {
	
				// track as successfull
				$sequentialOK = true;
						
			} // END IF for query errors

		// increment the count
		 $i++;
	} // END while ($i < count($sequentialTmp))
	//--/END INSERT SEQUENTIAL DATA INTO DB
	
	// if all data saved into db successfully, send an email
	if (($staticOK || $staticOK == 'pending') && ($randomOK || $randomOK == 'pending') && ($sequentialOK || $sequentialOK == 'pending')) {
		
		// check for a NetID
		if ($_SESSION['webauth']['netID']) {
			$user = strtoupper($_SESSION['webauth']['netID']);
		} else {
			$user = 'CRON JOB';
		}
		
		## EMAIL
		// BEGIN: prepare to send the 'success' notification email
		$to = 'kmbeyer@email.arizona.edu';
		$subject = 'SUCCESS: DELIVERANCE PROCESSOR SCRIPT';
		$body = "All new Deliverance data was saved into the database without incident on " . date('m/d/Y H:i:s') . ". This script was executed using the [" . $page . "] page on the " . $server . ". The script was executed by " . $user . "."; 
		// END: prepare to send the 'success' notification email

		// BEGIN: send the error email
		mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>');
		// END: mail script
	
		// track the script counter	
		$result = mysql_query("SELECT * FROM scriptcounter WHERE id = 1");
		$row = mysql_fetch_array($result);
		$currentCount = $row['counter']+1;
	
		$query = "UPDATE scriptcounter SET
		lastrun = \"" . $today . "\",
		counter = \"" . $currentCount . "\"
		WHERE id = \" 1 \"";
			
		// check for errors saving to the db
		if (!mysql_query($query,$DBlink)) {

			die('Error:1 ' . mysql_error());
		
		// there's no error
		} else {
			
			$success = true;
		
		} // END IF db error
		
		echo '<p style="text-align:center;"><strong>Update request received and processing.</strong><br />
		You have <span style="font-weight:bold; color:#ff0000;">' . ($maxUpdates - $currentCount) . '</span> update(s) remaining today.</p>';

		## HISTORY
		// BEGIN: save history into db		
		$query = "INSERT INTO history
		(netID, action, server, page, timestamp)
		VALUES ('$user', '$action', '$server', '$page', $time)";

		// check for errors saving to the db
		if (!mysql_query($query,$DBlink)) {

			echo '<br /><p>There was a problem saving the changes to the database. Please email <a href="mailto:kmbeyer@email.arizona.edu">kmbeyer@email.arizona.edu</a> for assistance. All values were updated for the Deliverance queue, but the history was not saved.</p>';
			
			## EMAIL
			// BEGIN: prepare to send the error notification email
			$to = 'kmbeyer@email.arizona.edu, samarketingcritical@gmail.com';
			$subject = 'ERROR: DELIVERANCE PROCESSOR SCRIPT: SAVE HISTORY FAILED';
			$body = "There was a problem saving the Deliverance history to the database on " . date('m/d/Y H:i:s') . ". All values were updated for the Deliverance queue, but the history was not saved. This script was executed on the " . $server . "."; 
			// END: prepare to send the error notification email

			// BEGIN: send the error email
			mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>');
			// END: mail script
			
			die('Error: 5 ' . mysql_error());			
			
		} // END IF (!mysql_query($query,$DBlink))
		## END HISTORY
		
	} // END IF	($staticOK && $randomOK && $sequentialOK)
	## MAIN PROCESSOR SCRIPT ENDS HERE ##

		} else {
			echo '<p style="text-align:center;">Sorry, you have already run the maximum number of updates today.<br />Please try again tomorrow.</p>';
		} // if ($row['counter'] < $maxUpdates)

	} else {
		echo '<p style="text-align:center;">Please return to the <a href="processor_editor.php">main page</a> to initiate another update.</p>';
	} // END  ($_SESSION['allowUpdates'])

} else {
		echo '<p style="text-align:center;">You appear to have accessed this page incorrectly. Please <a href="processor_editor.php">try again</a>.</p>';
} // if ($_GET['update'])
	
?>

	<p style="text-align:center; font-size:10px; padding-top:40px;"><a href="javascript:;" onclick="parent.close()">close/cancel</a></p>
	</div><!--/mainContent-->
</div><!--/containerPop-->

</body>
</html>