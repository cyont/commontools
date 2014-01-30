<?php

// required for access via session var enabled by webauth
session_start();

// get today's date in format compatible w/date stored in db
$today = date('Y-m-d',time());
 
// call db include file
include("inc_db.php");

// select database
mysql_select_db("deliverance", $DBlink)
	or die(mysql_error());

// if "X" has been clicked, delete the resource's entry in the randomfeed table
if ($_GET['deleteResource']) {
	
	// execute the delete
	$result = mysql_query("DELETE FROM randomfeed WHERE id = " . $_GET['deleteResource'] . "");

}

// update displayblock table if a new resource has been assigned to it
if ($_POST['assignResource']) {
	
	// put the displayblock id back into the GET var so page will load latest resource
	$_GET['displayBlockID'] = $_POST['displayBlockID'];

	// see if a default already exists and either UPDATE or INSERT
	if ($_POST['makeDefault']) {

		// QUERY: to see if default exists
		$result = mysql_query("SELECT * FROM defaults WHERE displayBlockID = " . $_GET['displayBlockID'] . "");
		$default = mysql_fetch_array($result);
		
		// if a default already exists
		if ($default) {
			
			$query = "UPDATE defaults SET
			resourceID = \"" . $_POST['newResource'] . "\"
			WHERE displayBlockID = \"" . $_GET['displayBlockID'] . "\"";
			
			// check for errors saving to the db
			if (!mysql_query($query,$DBlink)) {

				## EMAIL
				// BEGIN: prepare to send the error notification email
				$to = 'kmbeyer@email.arizona.edu';
				$subject = 'ERROR: SA MARKETING CMS ADD DEFAULT RESOURCE TO RANDOM DISPLAY BLOCK';
				$body = "There was a problem saving the resource information to the database on " . date('m/d/Y H:i:s') . "."; 
				// END: prepare to send the error notification email

				// BEGIN: send the error email
				mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>'); // no bcc on this version
				// END: mail script

				die('Error:1 ' . mysql_error());
		
			// there's no error
			} else {
			
				$success = true;
		
			} // END IF db error

		// there's no default
		} else {
			
			// assign form data to vars
			$newResource = $_POST['newResource'];
			$displayBlockID = $_GET['displayBlockID'];
			
			// query to add resource to db
			$query = "INSERT INTO defaults
			(resourceID, displayBlockID)
			VALUES ($newResource, $displayBlockID)";

			// check for errors saving to the db
			if (!mysql_query($query,$DBlink)) {

				## EMAIL
				// BEGIN: prepare to send the error notification email
				$to = 'kmbeyer@email.arizona.edu';
				$subject = 'ERROR: SA MARKETING CMS RANDOM DISPLAY DEFAULT RESOURCE ADDITION';
				$body = "There was a problem saving a resource to the database on " . date('m/d/Y H:i:s') . "."; 
				// END: prepare to send the error notification email

				// BEGIN: send the error email
				mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>'); // no bcc on this version
				// END: mail script

				die('Error:2 ' . mysql_error());

			} else {
		
				$success = true;		
		
			} // END IF db error
			
		} // END IF ELSE ($default)
	
	// this is a new data entry, not a default
	} else {
		
		## verify that no existing queues are in conflict with the new queue request
		// first get the dates being requested
		// convert the dates from the js calendar into the db format
		$formStartDate = strtotime($_POST['startDate']);
		$startDate = date('Y-m-d',$formStartDate);
		$formEndDate = strtotime($_POST['endDate']);
		$endDate = date('Y-m-d',$formEndDate);

		// had to add this in after i had the other logic working, hence the odd ordering of this code...
		// make sure the date form isn't empty, that the start is not after the end and that the start is not in the past
		if (!empty($startDate) && !empty($endDate) && ($startDate <= $endDate) && ($startDate >= $today)) {

			// expand the range
			// put the dates in vars we can change
			$low = $startDate;
			$high = $endDate;
		
			// init the counter
			$count = 0;

			// loop through all the dates being requested
			while ($low <= $high) {
				
				// put dates in an array
				$newRange[$count] = $low;
	
				// increment to next day
				$nextDay = strtotime('+1 day', strtotime($low));
				$low = date('Y-m-d',$nextDay);
				$count++;
					
			} // END WHILE  ($low <= $high)

		
			// QUERY: get sequential feed info where displayIDs match and limit to end dates of today or later -- resource IDs do NOT matter in the case of sequential queues
			$result = mysql_query("SELECT * FROM randomfeed WHERE displayBlockID = " . $_GET['displayBlockID'] . " AND  endDate >= '" . $today . "' AND resourceID = " . $_POST['newResource'] . " ORDER BY endDate ASC");
		
			// init the counter for the array
			$queue = 0;

			while ($latestResource = mysql_fetch_array($result)) {
		
				// assign the lowest and highest dates from the above queries
				$low = $latestResource['startDate'];
				$high = $latestResource['endDate'];

				// loop through all the dates that exist in the current queue
				while ($low <= $high) {
				
					// put dates in an array
					$range[$count] = $low;
	
					// increment to next day
					$nextDay = strtotime('+1 day', strtotime($low));
					$low = date('Y-m-d',$nextDay);
					$count++;
					
				} // END WHILE  ($low <= $high)
			
				// increment the counter
				$queue++;
			
			} // END WHILE ($latestResource = mysql_fetch_array($result))

		// else we have an error with the input
		} else {
			$dateError = true;
			$dateMsg = 'ERROR: The dates you selected are in the past, overlap each other or weren\'t filled out completely.';
		} // END IF (!empty($startDate) && !empty($endDate) && ($startDate <= $endDate) && ($startDate >= $today))

		// if ranges exist in the current queue or in the requested dates, find the overlap
		if ($range && $newRange) {
			$overlap = array_intersect($range, $newRange);
		}

		// if we have a duplicate date
		if (count($overlap) > 0) {
			$dateError = true;
			$dateMsg = 'ERROR: A conflict between your selected dates and the current display queues was detected. Please try again.';
		} else {
			
			$rangeOK = true;
		} // END IF (count($overlap) > 0)

		// if everything checks out, add the request to the db
		if ($rangeOK && !$dateError) {

			// assign form data to vars
			$newResource = $_POST['newResource'];
			$displayBlockID = $_GET['displayBlockID'];

			## save into the db and provide feedback ##
			// query to update sequential feed table with new resource and display block info
			$query = "INSERT INTO randomfeed
			(resourceID, displayBlockID, startDate, endDate)
			VALUES ($newResource, $displayBlockID, '$startDate', '$endDate')";

			// check for errors saving to the db
			if (!mysql_query($query,$DBlink)) {

				## EMAIL
				// BEGIN: prepare to send the error notification email
				$to = 'kmbeyer@email.arizona.edu';
				$subject = 'ERROR: SA MARKETING CMS ADD RESOURCE TO RANDOM DISPLAY BLOCK';
				$body = "There was a problem saving the resource information to the database on " . date('m/d/Y H:i:s') . "."; 
				// END: prepare to send the error notification email

				// BEGIN: send the error email
				mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>'); // no bcc on this version
				// END: mail script

				die('Error:3 ' . mysql_error());

			} else {
			
				$success = true;
		
			} // END IF db error

		} // END IF ($rangeOK && !$dateError)
		
	} // END IF ($_POST['makeDefault'])

} // END IF ($_POST['assignResource'])

// check to see if they have logged in via webauth
if ($_SESSION['allowEdits']) {

	// QUERY: get displayblock info
	$result = mysql_query("SELECT * FROM displayblock WHERE id = " . $_GET['displayBlockID'] . "");
	$rowDisplay = mysql_fetch_array($result);
	
	// QUERY: get random feed info
	$resultRandom = mysql_query("SELECT * FROM randomfeed WHERE displayBlockID = " . $_GET['displayBlockID'] . " AND (startDate = '" . $today . "' OR endDate >= '" . $today . "') ORDER BY startDate "); // changed from endDate
	
	// QUERY: get default resource info
	$resultDefault = mysql_query("SELECT * FROM defaults WHERE displayBlockID = " . $_GET['displayBlockID'] . "");
	$rowDefault = mysql_fetch_array($resultDefault);
	
		// QUERY: get default resource specs
		$resultDefaultSpecs = mysql_query("SELECT * FROM resource WHERE id = " . $rowDefault['resourceID'] . "");
		if ($resultDefaultSpecs) {
		$rowDefaultSpecs = mysql_fetch_array($resultDefaultSpecs);
		}
		
		// Only run the query if a default exists
		if ($rowDefaultSpecs) {
			// QUERY: get resource dimensions
			$result = mysql_query("SELECT * FROM dimensions WHERE " . $rowDefaultSpecs['dimensionsID'] . " = id");
			$rowDimensionsDefault = mysql_fetch_array($result);
		} // END IF ($rowDefaultSpecs)
	
	// QUERY: get dimensions info
	$result = mysql_query("SELECT * FROM dimensions WHERE id = " . $rowDisplay['dimensionsID'] . "");
	$rowDimensions = mysql_fetch_array($result);

	// include the CMS settings file
	# => DO NOT <= change the position of this include in this page #
	include("settings.php");
	
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Add Resource to Random Display Block ~ Deliverance ~ Student Affairs ~ The University of Arizona</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />

<script language="javascript" SRC="js/CalendarPopup.js"></script>
<script language="javascript">document.write(getCalendarStyles());</script>

<!-- link calendar files  -->
<!-- source location -->
<!-- http://www.softcomplex.com/products/tigra_calendar/ -->
<script language="JavaScript" src="js/calendar_us.js"></script>
<link rel="stylesheet" href="css/calendar.css">

</head>

<body class="adminUI">

<div id="container">
	<div id="mainContent">
	
<!--		<a href="#" title="Add a resource to this display block"><img src="/deliverance/images/plus_sign.gif" width="13" height="13" alt="Add a resource to this display block" id="plusSign" /></a>-->
			
    <h2>Deliverance</h2>
	<h3>Add Resource to Random Display Block <span style="color:#F00">#<? echo $rowDisplay['id']; ?></span></h3>
	<p style="margin:10px 0 0 0; float:left;"><strong>Display Block Properties</strong></p>
	<p style="margin:10px 0 0 0; float:right;"><a href="index.php">Main Menu</a></p>
	
	<br class="clear" />
	
		<div id="displayProps">
		<p style="margin:0; padding-bottom:5px;">Name: <? echo $rowDisplay['displayBlockName']; ?><br />
		Dimensions:  <? echo $rowDimensions['width']; ?> x <? echo $rowDimensions['height']; ?> (W x H)<br />
		Feed Type: <? echo $rowDisplay['feedType']; ?></p>
		</div><!-- /resourceProps -->
		
		<div id="resources">

		<!-- BEGIN group -->
			<div class="resourceImg">
			<?
				// if no default exists, let them know
				if ($rowDefaultSpecs) {
			?>
				<img src="<? echo $browserURL . $rowDefaultSpecs['filePath'];  ?>" <? if ($rowDimensionsDefault['width'] > 150) { echo 'width="150"'; }?> alt="<? echo $rowDefaultSpecs['altTxt']; ?>" title="<? echo $rowDefaultSpecs['altTxt']; ?>" /><br />
				<a href="edit_resource.php?id=<? echo $rowDefaultSpecs['id']; ?>"><? echo $rowDefaultSpecs['resourceName'];  ?></a>
			<?
				} else {
					echo '<p style="color:#ff0000; padding-top:15px; text-align:center;">NO DEFAULT!</p>';
				} // END IF ($rowDefaultSpecs)
			?>
			</div><!-- /resourceImg -->
			
			<div class="resourceImgInfo">
			<p><strong>Default</strong></p>
			</div><!-- /resourceImgInfo -->
		<!-- END group -->

		<?
		// init future flag
		$future = false;
		
		// look through all the results for random resource queues
		while ($rowRandomResources = mysql_fetch_array($resultRandom)) {
			
			// if this queue hasn't started yet, set a flag
			if ($rowRandomResources['startDate'] > $today) {
				$future = true;
			}
			
			// QUERY: get resource specs
			# use the resource ID to query resource table
			# then get path and dimensions and display scaled versions
			$result = mysql_query("SELECT * FROM resource WHERE " . $rowRandomResources['resourceID'] . " = id");
			$rowCurrentResource = mysql_fetch_array($result);
			
				// QUERY: get resource dimensions
				$result = mysql_query("SELECT * FROM dimensions WHERE " . $rowCurrentResource['dimensionsID'] . " = id");
				$rowDimensions = mysql_fetch_array($result);

		?>
		<!-- BEGIN group -->
			<div class="resourceImg">
			<img src="<? echo $browserURL . $rowCurrentResource['filePath'];  ?>" <? if ($rowDimensions['width'] > 150) { echo 'width="150"'; }?> alt="<? echo $rowCurrentResource['altTxt']; ?>" title="<? echo $rowCurrentResource['altTxt']; ?>" /><br />
			<a href="edit_resource.php?id=<? echo $rowCurrentResource['id']; ?>"><? echo $rowCurrentResource['resourceName'];  ?></a>
			</div><!-- /resourceImg -->
			
			<div class="resourceImgInfo">
			<p><strong <? if ($future) { echo ' style="color:#70adcd"'; } ?>>Queue</strong> [ <a href="add_resource_random_display_block.php?deleteResource=<? echo $rowRandomResources['id'] . '&displayBlockID=' . $_GET['displayBlockID']; ?>" title="Remove resource from this display block during all dates associated with this queue.">X</a> ]<br />
			
			<span class="resourceDates">
			<?
				// assign the start and end dates to vars
				$startDate = $rowRandomResources['startDate'];
				$endDate = $rowRandomResources['endDate'];
	
				// reformat start dates for smaller footprint in queue column
				$startDateShort = strtotime($rowRandomResources['startDate']);
				$startDateShort = date('m/d', $startDateShort);
				
				// reformat end dates for smaller footprint in queue column
				$endDateShort = strtotime($rowRandomResources['endDate']);
				$endDateShort = date('m/d', $endDateShort);
				
				// if it's a future queue, highlight in blue
				if ($future) {
					echo '<span style="color:#70adcd;">' . $startDateShort . ' - ' . $endDateShort . '</span><br />';
				} else {
					echo $startDateShort . ' - ' . $endDateShort . '<br />';
				} // END IF ($future)
		
				## COUNT TOTAL DAYS
				// init $days
				$days = 0;
				
				// loop through all the dates
				while ($startDate <= $endDate) {
					
					// increment to next day
					$nextDay = strtotime('+1 day', strtotime($startDate));
					$startDate = date('Y-m-d',$nextDay);
					$days++;
					
				} // END WHILE ($startDate <= $endDate)

				// if it's a future queue, highlight in blue
				if ($future) {
					echo '<span style="color:#70adcd;">' . $days . ' days total</span><br />';
				} else {
					echo $days . ' days total<br />';					
				} // END IF ($future)
		
				// re-assign the start and end dates to vars
				$startDate = $rowRandomResources['startDate'];
				$endDate = $rowRandomResources['endDate'];

				## COUNT REMAINING DAYS
				// init $days
				$days = 0;
			
				// assign $today to new var so rest of page doesn't fail due to reliance on $today var
				$start = $today;
				
				// loop through all the dates
				while (($start >= $startDate) && ($start <= $endDate)) {
					
					// set flag to show 'remaining' count down text
					$showRemaining = true;
					
					// increment to next day
					$nextDay = strtotime('+1 day', strtotime($start));
					$start = date('Y-m-d',$nextDay);
					$days++;
					
				} // END WHILE
				
				// if a countdown exists, show it
				if ($days > 0) {
					echo $days . ' days remaining<br />';
				}
				
			?>
			
			</span></p>
			</div><!-- /resourceImgInfo -->
		<!-- END group -->
		
		<?
		} // END WHILE ($rowRandomResources = mysql_fetch_array($resultRandom))
		?>

		</div><!-- /resources -->
		
	<?php
	// don't query unless a resource selection has been made
	if ($_POST['resource']) {
		// QUERY: get selected resource specs
		$result = mysql_query("SELECT * FROM resource WHERE id = " . $_POST['resource'] . "");
		$resource = mysql_fetch_array($result);
		
		// QUERY: get dimensions
		$result = mysql_query("SELECT * FROM dimensions WHERE id = " . $resource['dimensionsID'] . "");
		$dimensions = mysql_fetch_array($result);
		
		## Series of queries to get all instances of resource's current and future use
		// QUERY: get use as default resource
		$resultDefault = mysql_query("SELECT * FROM defaults WHERE resourceID = " . $_POST['resource'] . "");
	
		// QUERY: get use as static resource
		$resultStatic = mysql_query("SELECT * FROM displayblock WHERE resourceID = " . $_POST['resource'] . "");
		
		// QUERY: get use as random resource
		$resultRandom = mysql_query("SELECT * FROM randomfeed WHERE resourceID = " . $_POST['resource'] . " AND endDate >= '" . $today . "' ORDER BY endDate ASC");
	
		// QUERY: get use as sequential resource
		$resultSequential = mysql_query("SELECT * FROM sequentialfeed WHERE resourceID = " . $_POST['resource'] . " AND endDate >= '" . $today . "' ORDER BY endDate ASC");

	} // END if ($_POST['resource'])
	?>
		
		<div id="infoOptions">
			<p style="padding-top:15px;"><strong>INFO &amp; OPTIONS</strong></p>

			<div id="currentDisplay">
			<p><strong>Display Queue</strong><br />

	<?
	// don't enter the WHILE unless a resource selection has been made
	if ($_POST['resource']) {
		
		// show the DEFAULT instances
		while ($rowDefault = mysql_fetch_array($resultDefault)) {
			// QUERY: get displayblock name
			$resultName = mysql_query("SELECT * FROM displayblock WHERE id = " . $rowDefault['displayBlockID'] . "");
			$rowName = mysql_fetch_array($resultName);
			
			// figure out which feedtype and prepend the right url
			switch ($rowName['feedType']) {
				case 'static':
				$url = 'add_resource_static_display_block.php';
				break;
			
				case 'random':
				$url = 'add_resource_random_display_block.php';
				break;
			
				case 'sequential';
				$url = 'add_resource_sequential_display_block.php';
				break;
			} // END SWITCH
			
			// display the linked name
			echo '<a href="' . $url . '?displayBlockID=' . $rowName['id'] . '">' . $rowName['displayBlockName'] . '</a> [df]<br />';
		} // end the WHILE
		
		// show the STATIC instances
		while ($rowStatic = mysql_fetch_array($resultStatic)) {
			echo '<a href="add_resource_static_display_block.php?displayBlockID=' . $rowStatic['id'] . '">' . $rowStatic['displayBlockName'] . '</a> [st]<br />';
		} // end the WHILE

		// show the RANDOM instances
		while ($rowRandom = mysql_fetch_array($resultRandom)) {
			// QUERY: get displayblock name
			$resultName = mysql_query("SELECT * FROM displayblock WHERE id = " . $rowRandom['displayBlockID'] . "");
			$rowName = mysql_fetch_array($resultName);
			// display the linked name
			echo '<a href="add_resource_random_display_block.php?displayBlockID=' . $rowRandom['displayBlockID'] . '" title="' . $rowRandom['startDate'] .' through '. $rowRandom['endDate'] . '">' . $rowName['displayBlockName'] . '</a> [rd]<br />';
		} // end the WHILE

		// show the SEQUENTIAL instances
		while ($rowSequential = mysql_fetch_array($resultSequential)) {
			// QUERY: get displayblock name
			$resultName = mysql_query("SELECT * FROM displayblock WHERE id = " . $rowSequential['displayBlockID'] . "");
			$rowName = mysql_fetch_array($resultName);
			// display the linked name
			echo '<a href="add_resource_sequential_display_block.php?displayBlockID=' . $rowSequential['displayBlockID'] . '" title="' . $rowSequential['startDate'] .' through '. $rowSequential['endDate'] . '">' . $rowName['displayBlockName'] . '</a> [sq]<br />';
		} // end the WHILE
		
	} // END if ($_POST['resource']) 
	?>
			
			</p>
			</div><!-- /currentDisplay -->
	
			<div id="resourceSpecs">
			<p><strong>Resource Specs</strong><br />
			<? echo $resource['resourceName']; ?><br />
			<? echo $resource['fileSize']; ?>K <? echo $resource['type']; ?><br />
			<? echo $dimensions['width'] . ' x ' . $dimensions['height']; ?> (W x H)
			</p>
			</div><!-- /resourceSpecs -->

		<div id="resRun">
		<p style="margin-bottom:0;"><strong>Resources &amp; Run Dates</strong></p>

		<?php
	
	// QUERY: get all appropriate resources
	$resultResMatch = mysql_query("SELECT * FROM resource WHERE dimensionsID = " . $rowDisplay['dimensionsID'] . " AND site = '" . $rowDisplay['site'] . "' ORDER BY resourceName");
	
		?>

	<!-- CHOOSE RESOURCE FORM -->
	<form style="padding:0; margin:2px 0 0 0;" enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] . '?displayBlockID=' . $_GET['displayBlockID']; ?>" method="POST">
		<select name="resource" onChange="this.form.submit();" style="width:185px;">
			<option value="">Choose a resource...</option>
			<?
			while ($rowResList = mysql_fetch_array($resultResMatch)) {
			?>
				<option value="<?php echo $rowResList['id']; ?>" <?php if ($_POST['resource'] == $rowResList['id']) { echo ' selected="selected"';} ?>><?php echo $rowResList['resourceName']; ?></option>
	
			<?php
			} // END WHILE
			?>
		</select>
	</form>

	<!-- CHOOSE DATES FORM -->
	<form name="runDates" style="padding:0; margin:0;" enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
		<span style="font-size:9px; display:block; margin-top:5px;">&nbsp;Begin</span>
		<input type="text" name="startDate" />
		<script language="JavaScript">

		new tcal ({
			// form name
			'formname': 'runDates',
			// input name
			'controlname': 'startDate',
		});
		</script>
		
		<br />
		
		<span style="font-size:9px;">&nbsp;End</span><br />
		<input type="text" name="endDate" />
		<script language="JavaScript">

		new tcal ({
			// form name
			'formname': 'runDates',
			// input name
			'controlname': 'endDate',
		});
	
		</script>

		</div><!-- /resRun -->

		<div id="resContainer">
		
	<?php
	// don't show the image unless a resource selection has been made
	if ($_POST['resource']) {
	?>
		<img src="<? echo $browserURL . $resource['filePath']; ?>" <? if ($dimensions['width'] > 300) { echo 'width="300"'; }?> alt="<? echo $resource['altTxt']; ?>" title="<? echo $resource['altTxt']; ?>" /><br />
	<?php
	}
	?>

		</div><!-- /resContainer -->
		
		<div id="resOptions" style="width:275px;">

		<input type="checkbox" name="makeDefault" value="yes" title="Make this the default resource for this display block" /> Set as default resource<br />&nbsp;<br />
			
		<input type="hidden" name="displayBlockID" value="<? echo $_GET['displayBlockID'] ?>"
		<input type="hidden" name="newResource" value="<? echo $_POST['resource'] ?>"
		<input type="submit" name="assignResource" value="Assign Resource" />
	</form>
	
	<?
		// determine which feedback message to display
		if ($success) {
			echo '<p class="confirmationSmall">Assignment successful!</p>';
		} else if ($dateError) {
			echo '<p class="errorSmall">' . $dateMsg . '</p>';
		}
	?>
	
	<br />&nbsp;<br />
	
		<a href="upload_resource.php">Upload a new resource</a>
		</div><!-- resOptions -->

		</div><!-- /infoOptions -->

		<br class="clear" />

	<?php
		include("inc_calendar_logic.php");
	?>

		<div id="calBlock">
		<p><strong>Current Schedule</strong></p>
		
		<div id="calBlockInner">
		
			<div class="day">Sun<br />
			<?
				echo $date[0];
				$dayOfWeek = $date[0];
			?>
				<div class="dayBlock"<? if ($sunday) { echo ' style="background-color:#9afabc"'; } ?>>
				<ul>
					<?php
					include("inc_random_cal.php");
					?>
				</ul>
				</div><!-- /dayBlock -->
				<!--[ <a href="">+</a> ]-->
			</div><!-- /day -->

			<div class="day">Mon<br />
			<?
				echo $date[1];
				$dayOfWeek = $date[1];
			?>
				<div class="dayBlock"<? if ($monday) { echo ' style="background-color:#9afabc"'; } ?>>
				<ul>
					<?php
					include("inc_random_cal.php");
					?>
				</ul>
				</div><!-- /dayBlock -->
				<!--[ <a href="">+</a> ]-->
			</div><!-- /day -->

			<div class="day">Tue<br />
			<?
				echo $date[2];
				$dayOfWeek = $date[2];
			?>
				<div class="dayBlock"<? if ($tuesday) { echo ' style="background-color:#9afabc"'; } ?>>
				<ul>
					<?php
					include("inc_random_cal.php");
					?>
				</ul>
				</div><!-- /dayBlock -->
				<!--[ <a href="">+</a> ]-->
			</div><!-- /day -->

			<div class="day">Wed<br />
			<?
				echo $date[3];
				$dayOfWeek = $date[3];
			?>
				<div class="dayBlock"<? if ($wednesday) { echo ' style="background-color:#9afabc"'; } ?>>
				<ul>
					<?php
					include("inc_random_cal.php");
					?>
				</ul>
				</div><!-- /dayBlock -->
				<!--[ <a href="">+</a> ]-->
			</div><!-- /day -->

			<div class="day">Thu<br />
			<?
				echo $date[4];
				$dayOfWeek = $date[4];
			?>
				<div class="dayBlock"<? if ($thursday) { echo ' style="background-color:#9afabc"'; } ?>>
				<ul>
					<?php
					include("inc_random_cal.php");
					?>
				</ul>
				</div><!-- /dayBlock -->
				<!--[ <a href="">+</a> ]-->
			</div><!-- /day -->

			<div class="day">Fri<br />
			<?
				echo $date[5];
				$dayOfWeek = $date[5];
			?>
				<div class="dayBlock"<? if ($friday) { echo ' style="background-color:#9afabc"'; } ?>>
				<ul>
					<?php
					include("inc_random_cal.php");
					?>
				</ul>
				</div><!-- /dayBlock -->
				<!--[ <a href="">+</a> ]-->
			</div><!-- /day -->

			<div class="day">Sat<br />
			<?
				echo $date[6];
				$dayOfWeek = $date[6];
			?>
				<div class="dayBlockRight"<? if ($saturday) { echo ' style="background-color:#9afabc"'; } ?>>
				<ul>
					<?php
					include("inc_random_cal.php");
					?>
				</ul>
				</div><!-- /dayBlockRight -->
				<!--[ <a href="">+</a> ]-->
			</div><!-- /day -->

			<br class="clear" />			
			
		</div><!-- /calBlockInner -->

		<div style="text-align:center"><a href="add_resource_random_display_block.php?subtractWeek=true&displayBlockID=<? echo $_GET['displayBlockID']; ?>">Previous</a> | <a href="add_resource_random_display_block.php?displayBlockID=<? echo $_GET['displayBlockID']; ?>">Current</a> | <a href="add_resource_random_display_block.php?addWeek=true&displayBlockID=<? echo $_GET['displayBlockID']; ?>">Next</a></div>
	
			<br class="clear" />
	
		</div><!-- /calBlock -->
		
		<br class="clear" />
					
	<!-- /mainContent --></div>

<!-- /container --></div>

<!-- div for displaying calendar popups -->
<div id="startCal" style="position:absolute;visibility:hidden;background-color:#ffffff;layer-background-color:#ffffff;"></div>

</body>
</html>

<?php
	
	// no webauth session vars enabled
	} else {
		
		echo 'Please return to the <a href="index.php">main page</a> and log in.';
		
	} // END IF ($_SESSION['allowEdits'])

?>