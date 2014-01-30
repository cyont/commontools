<?php

// PENDING...
// use session vars for reviewing errors, either in each form value as an IF or as a separate iteration of the form
// upload must be verified and saved prior to updating other data AND prior to deleting the original resource
// 9/9/10: still need to handle deleting old file after successful upload, and double check the validation... seems good so far

// required for access via session var enabled by webauth
session_start();

// define page so that correct values are from settings.php
$page = 'replace';
	
// get today's date in format compatible w/date stored in db
$today = date('Y-m-d',time());
	
// check to see if they have logged in via webauth
if ($_SESSION['allowEdits']) {
	
// call db include file
include("inc_db.php");

// include the functions file
	include("functions.php");	

	// select database
	mysql_select_db("deliverance", $DBlink)
		or die(mysql_error());

	// QUERY: get resource info
	$result = mysql_query("SELECT * FROM resource WHERE id = " . $_GET['id'] . "");
	$resource = mysql_fetch_array($result);
	
		// assign the site
		$_SESSION['site'] = $resource['site'];
		
	// include the CMS settings file
	# => DO NOT <= change the position of this include in this page #
	include("settings.php");
	
		// assign full path of resource to be replaced, and then deleted
		// -> this is a combo of settings file info and path info stored in db
		# this must come AFTER the settings.php include
		$_SESSION['fileToDelete'] = $_SESSION['deletePath'] . $resource['filePath'];
	
	// QUERY: get dimensions info
	$result = mysql_query("SELECT * FROM dimensions WHERE id = " . $resource['dimensionsID'] . "");
	$dimensions = mysql_fetch_array($result);

	// FILE UPLOAD RESET
	// delete the uploaded file if they've clicked '[change file]' and the file exists

	if ($_GET['resetUpload']) { // check to see if they clicked Change File

		$fileToDelete = $_SESSION['uploadPath'] . $_SESSION['sysFileName']; // assign the file path and name to a variable

		if (file_exists($fileToDelete) && $fileToDelete != $_SESSION['uploadPath']) { // if they haven't already clicked Change File to remove it, file should exist. also check to make sure it's not just a path to 'uploads/' and not a file
		
			unset($_SESSION['fileUploadOK']); // reset the file upload session variable toggle
			unlink($_SESSION['uploadPath'] . $_SESSION['sysFileName']); // delete the file
		
		}
	} // end IF reset
	
	// FULL SESSION RESET
	if ($_GET['startOver']) { // check to see if they clicked Start Over

		$fileToDelete = $_SESSION['uploadPath'] . $_SESSION['sysFileName']; // assign the file path and name to a variable
	
		if (file_exists($fileToDelete) && $fileToDelete != $_SESSION['uploadPath']) { // if they haven't already clicked Start Over to remove it, file should exist. also check to make sure it's not just a path to 'uploads/' and not a file
		
			unlink($_SESSION['uploadPath'] . $_SESSION['sysFileName']); // delete the file
			$temp = $_SESSION['webauth']['netID']; // store netID temporarily so that session unset doesn't cause problems when resubmitting
			$temp2 = $_SESSION['allowEdits']; // store the session var for allowing edits
			session_unset(); // clear out the session variables
			$_SESSION['webauth']['netID'] = $temp; // reassign netID after clearing the session vars
			$_SESSION['allowEdits'] = $temp2; // reassign after clearing the session vars
		
		} else {
			$temp = $_SESSION['webauth']['netID']; // store netID temporarily so that session unset doesn't cause problems when resubmitting
			$temp2 = $_SESSION['allowEdits']; // store the session var for allowing edits
			session_unset(); // clear out the session variables
			$_SESSION['webauth']['netID'] = $temp; // reassign netID after clearing the session vars
			$_SESSION['allowEdits'] = $temp2; // reassign after clearing the session vars

		}
	} // end IF full session reset

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Replace Resource ~ Deliverance ~ Student Affairs ~ The University of Arizona</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body class="adminUI">
<div id="container">
  <div id="mainContent">
    <h2>Deliverance</h2>
	<h3>Replace Resource</h3>

<?php
	// if the form has been submited, assign variables and check for errors
	if ($_POST['upload']) {
		
		// assign w/o error checking
		$_SESSION['headline'] = cleanFormData($_POST['headline']);
		$_SESSION['subtext'] = cleanFormData($_POST['subtext']);
		$_SESSION['description'] = cleanFormData($_POST['description']);
		$_SESSION['resourceLink'] = cleanFormData($_POST['resourceLink']);

		######################
		## check for errors ##
		######################
		
		// Check resource name
		if (empty($_POST['resourceName'])) {
			$errors = true;
			$error_msg['resourceName'] = 'Please enter the name of the resource.';
			$error_class['resourceName'] = ' class="error"';
			$error_class_detail['resourceName'] = ' class="errorDetail"';
			$error_field_class['resourceName'] = ' class="bgError"';
			$_SESSION['resourceName'] = cleanFormData($_POST['resourceName']);
		} else {
			$_SESSION['resourceName'] = cleanFormData($_POST['resourceName']);
		}

		// Check alt text
		if (empty($_POST['altTxt'])) {
			$errors = true;
			$error_msg['altTxt'] = 'Please enter alt text.';
			$error_class['altTxt'] = ' class="error"';
			$error_class_detail['altTxt'] = ' class="errorDetail"';
			$error_field_class['altTxt'] = ' class="bgError"';
			$_SESSION['altTxt'] = cleanFormData($_POST['altTxt']);
		} else {
			$_SESSION['altTxt'] = cleanFormData($_POST['altTxt']);
		}
		
		// BEGIN FILE UPLOAD LOGIC
		// Validate the type. Should match what they said in the form
		
		###############################################################################
		# this code supports the full set of file types we want to ultimately address #
		// $allowed = array ('application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'image/jpeg', 'image/gif', 'video/quicktime', 'video/x-flv');
		# end full file type list #
		###########################
		
			$allowed = array ('image/jpeg', 'image/gif', 'image/png');
			if (in_array($_FILES['resourceFile']['type'], $allowed)) {

				// assign the file type
				if ($_FILES['resourceFile']['type'] == 'image/jpeg') {
					$_SESSION['type'] = 'jpg';
				} else if ($_FILES['resourceFile']['type'] == 'image/gif') {
					$_SESSION['type'] = 'gif';
				} else if ($_FILES['resourceFile']['type'] == 'image/png') {
					$_SESSION['type'] = 'png';
				}
			
				// save the file name and flag upload as OK so that form does not accept another upload
				// note: the file will need to be deleted if they upload another one
				$_SESSION['fileUploadOK'] = true;
				$_SESSION['fileName'] = $_FILES['resourceFile']['name'];
			
				// save the file size
				$_SESSION['fileSize'] = $_FILES['resourceFile']['size'];

				// set file name prefix to ensure unique names and avoid overwriting files
				$filePrefix = 'dlvr_' . time() . '_';
				$_SESSION['sysFileName'] = $filePrefix . $_SESSION['fileName'];
	
				// Move the file to the designated directory
				if (move_uploaded_file ($_FILES['resourceFile']['tmp_name'], $_SESSION['uploadPath'] . $filePrefix . "{$_FILES['resourceFile']['name']}")) {			
				} // End of move... IF.
		
			} else if (!$_SESSION['fileUploadOK']) { // We have an invalid file type
				$error_resume = true;
				$errors = true;
				$error_msg['resourceFile'] = 'The file format was invalid. Please attach a JPG, GIF or PNG.';
				$error_class['resourceFile'] = ' class="error"';
				$error_class_detail['resourceFile'] = ' class="errorDetail"';
				$error_field_class['resourceFile'] = ' bgError';
			}
	
		// Check for an error based on the FILES' array codes:
		if ($_FILES['resourceFile']['error'] > 0) {
			$error_resume = true;
			$errors = true;
			$errorPrefix = 'The file could not be uploaded because ';
	
			// Save a message based upon the error.
			switch ($_FILES['resourceFile']['error']) {
				case 1:
					$error_msg['resourceFile'] = $errorPrefix . 'it exceeds 2 MB.'; // this is the upload_max_filesize setting in php.ini
					break;
				case 2:
					$error_msg['resourceFile'] = $errorPrefix . 'it exceeds 512K.'; // this is the max size defined in the form
					break;
				case 3:
					$error_msg['resourceFile'] = $errorPrefix . 'it was only partially uploaded.';
					break;
				case 4:
					$error_msg['resourceFile'] = $errorPrefix . 'you did not attach anything.';
					break;
				case 6:
					$error_msg['resourceFile'] = $errorPrefix . 'no temporary folder was available.';
					break;
				case 7:
					$error_msg['resourceFile'] = $errorPrefix . 'the system was unable to write to the disk.';
					break;
				case 8:
					$error_msg['resourceFile'] = $errorPrefix . 'the file upload stopped.';
					break;
				default:
					$error_msg['resourceFile'] = $errorPrefix . 'a system error occurred.';
					break;
			} // End of switch.
	
			// Cleanup/delete the temp file if it still exists, which it shouldn't actually:
			if (file_exists ($_FILES['resourceFile']['tmp_name']) && is_file($_FILES['resourceFile']['tmp_name']) ) {
				unlink ($_FILES['resourceFile']['tmp_name']);
			} // END if file exists

		} // END check for error in FILES' array codes
	
		// Check file dimensions with regard to an uploaded file
		if ($_SESSION['fileUploadOK']) {
			
			// read the dimensions of the uploaded image
			$imgDimensions = getimagesize($_SESSION['uploadPath'] . $_SESSION['sysFileName']);
		
			if ($imgDimensions) {
				$w = $imgDimensions[0];
				$h = $imgDimensions[1];

				if ($w != $dimensions['width'] || $h != $dimensions['height']) {
					$errors = true;
					$error_msg['dimensions'] = 'Uploaded file\'s dimensions [W:' . $w . ' x H:' . $h . '] do not match current dimensions.';
					$error_class['dimensions'] = ' class="error"';
					$error_class_detail['dimensions'] = ' class="errorDetail"';
					$error_field_class['dimensions'] = ' class="bgError"';
							
				} // end IF not equal
							
			} // end IF we have an image file to measure
			
		} // end IF for checking an upload file's dimensions

} // end IF POST

if ($errors || !$_POST['upload']) {
// show the form -->

?>

	<div style="float:left; margin-top:10px; width:325px; height:650px; overflow:auto;">
	<img src="<? echo $browserURL . $resource['filePath'];  ?>" <? if ($dimensions['width'] > 300) { echo 'width="300"'; }?> alt="<? echo $resource['altTxt']; ?>" title="<? echo $resource['altTxt']; ?>" align="top" />
	</div>
	
	<div style="float:left; margin:10px 0 0 10px;">
	
	<form enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] ?>" method="post">
	<fieldset style="border:1px solid #cccccc; margin:0 0 20px 0; padding:10px 0 10px 10px; width:450px; height:650px;">
    <legend>Resource Info</legend>
	
	<div style="width:45%; padding-right:10px; float:left;">
	<p><strong>Resource Specs</strong><br />
	<? echo $resource['resourceName']; ?><br />
	<? echo $resource['fileSize']; ?>K <? echo $resource['type']; ?></p>
	</div>

	<div style="width:45%; float:left;">
	<p><strong>Current Display</strong><br />

	<?
	
		## Series of queries to get all instances of resource's current and future use
		// QUERY: get use as default resource
		$resultDefault = mysql_query("SELECT * FROM defaults WHERE resourceID = " . $_GET['id'] . "");
	
		// QUERY: get use as static resource
		$resultStatic = mysql_query("SELECT * FROM displayblock WHERE resourceID = " . $_GET['id'] . "");
		
		// QUERY: get use as random resource
		$resultRandom = mysql_query("SELECT * FROM randomfeed WHERE resourceID = " . $_GET['id'] . " AND endDate >= '" . $today . "' ORDER BY endDate ASC");
	
		// QUERY: get use as sequential resource
		$resultSequential = mysql_query("SELECT * FROM sequentialfeed WHERE resourceID = " . $_GET['id'] . " AND endDate >= '" . $today . "' ORDER BY endDate ASC");

		## display the results
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
		
	?>

	</div>
		
		<br class="clear" />
	
	<!-- RESOURCE NAME -->
	<span<?php echo $error_class['resourceName']; ?>><strong>Resource Name:</strong></span>
	<span<?php echo $error_class_detail['resourceName']; ?>><?= ($error_msg['resourceName']) ? '<br />' . $error_msg['resourceName'] : '' ?></span><br />
	<input type="text" maxlength="100" name="resourceName" value="<?php if ($_POST || $_GET['resetUpload']) { echo cleanFormData($_SESSION['resourceName']); } else { echo $resource['resourceName']; } ?>" <?php echo $error_field_class['resourceName']; ?> /><br />&nbsp;<br />

	<!-- DIMENSIONS -->	
	<span<?php echo $error_class['dimensions']; ?>><strong>Dimensions (W x H in px):</strong></span>
	<span<?php echo $error_class_detail['dimensions']; ?>><?= ($error_msg['dimensions']) ? '<br />' . $error_msg['dimensions'] : '' ?></span><br />
	Current: <? echo $dimensions['width'] . ' x ' . $dimensions['height']; ?>
	<br />&nbsp;<br />

	<!-- HEADLINE -->	
	<strong>Headline:</strong><br />
	<input type="text" maxlength="100" name="headline" value="<?php if ($_POST || $_GET['resetUpload']) { echo cleanFormData($_SESSION['headline']); } else { echo $resource['headline']; } ?>" /><br />&nbsp;<br />

	<!-- SUBTEXT -->
	<strong>Subtext:</strong><br />
	<input type="text" maxlength="100" name="subtext" value="<?php if ($_POST || $_GET['resetUpload']) { echo cleanFormData($_SESSION['subtext']); } else { echo $resource['subtext']; } ?>" /><br />&nbsp;<br />

	<!-- ALT TEXT -->
	<span<?php echo $error_class['altTxt']; ?>><strong>Alt text:</strong></span>
	<span<?php echo $error_class_detail['altTxt']; ?>><?= ($error_msg['altTxt']) ? '<br />' . $error_msg['altTxt'] : '' ?></span><br />
	<input type="text" maxlength="100" name="altTxt" value="<?php if ($_POST || $_GET['resetUpload']) { echo cleanFormData($_SESSION['altTxt']); } else { echo $resource['altTxt']; } ?>" <?php echo $error_field_class['altTxt']; ?> /><br />&nbsp;<br />

	<!-- DESCRIPTION -->
	<strong>Description (for internal use):</strong><br />
	<textarea name="description" rows="5" cols="15"><?php if ($_POST || $_GET['resetUpload']) { echo cleanFormData($_SESSION['description']); } else { echo $resource['description']; } ?></textarea><br />&nbsp;<br />

	<!-- FILE TO UPLOAD -->
	<span<?php echo $error_class['resourceFile']; ?>><strong>File to Upload</strong> <span style="font-size:9px;">(jpg, gif, png / &lt; 512K):</span></span>
	<span<?php echo $error_class_detail['resourceFile']; ?>><?= ($error_msg['resourceFile']) ? '<br />' . $error_msg['resourceFile'] : '' ?></span><br />
	<?php
		if ($_SESSION['fileUploadOK']) {
			echo '<em>File Uploaded: <a href="' . $uploadLink . $_SESSION['sysFileName'] . '" target="_blank">' . $_SESSION['fileName'] . '</a></em><br />
			<a href="replace_resource.php?resetUpload=1&id=' . $_GET['id'] . '">[change file]</a><br />&nbsp;<br />';
		} else {
	?>

	<!-- 512 upload size limit/server limit is 2 MB --><input name="MAX_FILE_SIZE" value="524288" type="hidden" /><input name="resourceFile" type="file" class="upload" /><br />&nbsp;<br />
<?php 
		}
?>
	<!-- LINK -->
	<strong>Link:</strong><br />
	<input type="text" maxlength="100" name="resourceLink" value="<?php if ($_POST || $_GET['resetUpload']) { echo cleanFormData($_SESSION['resourceLink']); } else { echo $resource['resourceLink']; } ?>" /><br />
	<span style="font-size:11px;">Use a "site relative" path (eg /mealplans/ or /dining/sumc/cactusgrill/index.php) unless the link is to a different domain.</span><br />&nbsp;<br />

	<input type="submit" name="upload" value="Replace" />&nbsp;&nbsp;<button type="button" onClick="window.location='replace_resource.php?startOver=1&id=<? echo $_GET['id']; ?>'">Clear</button>
	
	</fieldset>
	</form>
	
	</div>
	
	<br class="clear" />
	
<?php
} else {
	
	## save into the db and provide feedback ##
	
	// 1. move session vars into vars
	$filePath = $virtualPath . $_SESSION['sysFileName']; // merge the path with the name to get the full path
	$fileSize = round($_SESSION['fileSize'] / 1024, 0); // divide to get KB and round the filesize to 0 decimals
	$type = $_SESSION['type'];
	$resourceName = mysql_real_escape_string($_SESSION['resourceName']);
	$headline = mysql_real_escape_string($_SESSION['headline']);
	$subtext = mysql_real_escape_string($_SESSION['subtext']);
	$altTxt = mysql_real_escape_string($_SESSION['altTxt']);
	$description = mysql_real_escape_string($_SESSION['description']);
	$resourceLink = mysql_real_escape_string($_SESSION['resourceLink']);
	
	// 2. edit the resource data in the db
	$query = "UPDATE resource SET 
	
	filePath = \"" . $filePath . "\",
	fileSize = \"" . $fileSize . "\",
	type = \"" . $type . "\",
	resourceName = \"" . $resourceName . "\",
	headline = \"" . $headline . "\",
	subtext = \"" . $subtext . "\",
	altTxt = \"" . $altTxt . "\",
	description = \"" . $description . "\",
	resourceLink = \"" . $resourceLink . "\"

	WHERE id = \"" . $_GET['id'] . "\"";
	
	// 3. check for errors saving to the db
	if (!mysql_query($query,$DBlink)) {

		## EMAIL
		// BEGIN: prepare to send the error notification email
		$to = 'kmbeyer@email.arizona.edu';
		$subject = 'ERROR: SA MARKETING CMS RESOURCE REPLACEMENT';
		$body = "There was a problem saving a resource to the database on " . date('m/d/Y H:i:s') . "."; 
		// END: prepare to send the error notification email

		// BEGIN: send the error email
		mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>'); // no bcc on this version
		// END: mail script

		die('Error:1 ' . mysql_error());

	} else {
		
		// delete the original resource file
		unlink($_SESSION['fileToDelete']); // delete the file
		
		echo '<p class="confirmation">Resource replacement successful!</p>
		<ul>
			<li><a href="index.php">Return to main menu</a></li>
		</ul>';
		
		$temp = $_SESSION['webauth']['netID']; // store netID temporarily so that session unset doesn't cause problems when resubmitting
		$temp2 = $_SESSION['allowEdits']; // store the session var for allowing edits
		session_unset(); // clear out the session variables
		$_SESSION['webauth']['netID'] = $temp; // reassign netID after clearing the session vars
		$_SESSION['allowEdits'] = $temp2; // reassign after clearing the session vars
			
	} // END IF db error

} // end IF/else ($errors || !$_POST['upload'])
?>

	<!-- end #mainContent --></div>
<!-- end #container --></div>
</body>
</html>

<?php
	
	// no webauth session vars enabled
	} else {
		
		echo 'Please return to the <a href="index.php">main page</a> and log in.';
		
	}

?>