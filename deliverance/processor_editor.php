<?php

// required for access via session var enabled by webauth
session_start();

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

// get today's date in format compatible w/date stored in db
$today = date('Y-m-d',time());
 
// call db include file
include("inc_db.php");

// select database
$dbdeliv = new db("deliverance");
//mysql_select_db("deliverance", $DBlink)
//	or die(mysql_error());

// check to see if they have logged in via webauth
if ($_SESSION['allowEdits']) {
	
	// get db data
	$result = mysql_query("SELECT * FROM scriptcounter WHERE id = 1");
	$row = mysql_fetch_array($result);
	
	// see if the latest run was prior to today and if so, update to current date and zero the counter
	if ($row['lastrun'] < $today) {
	
		// update the db
		$query = "UPDATE scriptcounter SET
		lastrun = \"" . $today . "\",
		counter = \" 0 \"
		WHERE id = \" 1 \"";
			
		// check for errors saving to the db
		if (!mysql_query($query,$DBlink)) {

			die('Error:1 ' . mysql_error());

		}
		
	} // if ($row['lastrun'] < $today)
	
	// get the db data again in case we just updated it above
	$result = mysql_query("SELECT * FROM scriptcounter WHERE id = 1");
	$row = mysql_fetch_array($result);

	// if the max number of daily updates has not been exceeded, give a remaining count and link to the update script
	if ($row['counter'] < $maxUpdates) {
		
		// set a session flag to allow the next page to actually run the update
		$_SESSION['allowUpdates'] = true;
		
		echo '<p style="text-align:center;">You have <span style="font-weight:bold; color:#ff0000;">' . ($maxUpdates - $row['counter']) . '</span> update(s) remaining today.</p>';
		
		echo '<p style="text-align:center;"><input name="clearForm" type="button" value="Run updates" onClick="window.location=\'processor_editor_run.php?update=1\'" /></p>';
				
	} else {
		
		echo '<p style="text-align:center;">Sorry, you have already run the maximum number of updates today.<br />Please try again tomorrow.</p>';
		
	} // if ($row['counter'] < $maxUpdates)

// no webauth session vars enabled
} else {
		
	echo '<p style="text-align:center;">Your session has expired. Please close this window and return to the main page of Deliverance to login.</p>';
		
} // END IF ($_SESSION['allowEdits'])

?>

	<p style="text-align:center; font-size:10px; padding-top:40px;"><a href="javascript:;" onclick="parent.close()">close/cancel</a></p>
	</div><!--/mainContent-->
</div><!--/containerPop-->

</body>
</html>