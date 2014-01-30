<?php

// required for access via session var enabled by webauth
session_start();

// define page so that correct values are from settings.php
$page = 'upload';

// check to see if they have logged in via webauth
if ($_SESSION['allowEdits']) {
	
// call db include file
include("inc_db.php");

// include the functions file
	include("functions.php");	

	// select database
	mysql_select_db("deliverance", $DBlink)
		or die(mysql_error());

	// include the CMS settings file
	include("settings.php");

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
<title>Upload Resource ~ Deliverance ~ Student Affairs ~ The University of Arizona</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body class="adminUI">
<div id="container">
  <div id="mainContent">
    <h2>Deliverance</h2>
	<h3>Upload Resource</h3>

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
		} else {
			$_SESSION['resourceName'] = cleanFormData($_POST['resourceName']);
		}

		// Check file dimensions
		if ($_POST['dimensions'] == "") {
			$errors = true;
			$error_msg['dimensions'] = 'Please select the dimensions.';
			$error_class['dimensions'] = ' class="error"';
			$error_class_detail['dimensions'] = ' class="errorDetail"';
			$error_field_class['dimensions'] = ' class="bgError"';
		} else {
			$_SESSION['dimensions'] = $_POST['dimensions'];
		}
		
		// Check file type
/*		if ($_POST['type'] == "") {
			$errors = true;
			$error_msg['type'] = 'Please select the file type.';
			$error_class['type'] = ' class="error"';
			$error_class_detail['type'] = ' class="errorDetail"';
			$error_field_class['type'] = ' class="bgError"';
		} else {
			$_SESSION['type'] = $_POST['type'];
		}
*/
		// Check website
		if ($_POST['site'] == "") {
			$errors = true;
			$error_msg['site'] = 'Please select the website.';
			$error_class['site'] = ' class="error"';
			$error_class_detail['site'] = ' class="errorDetail"';
			$error_field_class['site'] = ' class="bgError"';
		} else {
			$_SESSION['site'] = $_POST['site'];
		}

		// Check alt text
		if (empty($_POST['altTxt'])) {
			$errors = true;
			$error_msg['altTxt'] = 'Please enter alt text.';
			$error_class['altTxt'] = ' class="error"';
			$error_class_detail['altTxt'] = ' class="errorDetail"';
			$error_field_class['altTxt'] = ' class="bgError"';
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
		
		// only check the file uploads if they've submitted the file's destination site
		if ($_POST['site']) {
		
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
	
		// or if they haven't submitted what the destination site is, show this error
		} else {
			$error_resume = true;
			$errors = true;
			$error_msg['resourceFile'] = 'You must select a site before attaching a file.';
			$error_class['resourceFile'] = ' class="error"';
			$error_class_detail['resourceFile'] = ' class="errorDetail"';
			$error_field_class['resourceFile'] = ' bgError';
		} // end IF POST site
			
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
		if ($_POST['dimensions'] != "" && $_SESSION['fileUploadOK']) {
			
			// read the dimensions of the uploaded image
			$imgDimensions = getimagesize($_SESSION['uploadPath'] . $_SESSION['sysFileName']);
		
			if ($imgDimensions) {
				$w = $imgDimensions[0];
				$h = $imgDimensions[1];

				// query to get submitted dimensions
				$size = mysql_query("SELECT * FROM dimensions WHERE id = " . $_SESSION['dimensions'] . "");
						
				if ($size) {
					$row = mysql_fetch_array($size);

					if ($w != $row['width'] || $h != $row['height']) {
						$errors = true;
						$error_msg['dimensions'] = 'Uploaded file\'s dimensions [W:' . $w . ' x H:' . $h . '] do not match your selection.';
						$error_class['dimensions'] = ' class="error"';
						$error_class_detail['dimensions'] = ' class="errorDetail"';
						$error_field_class['dimensions'] = ' class="bgError"';
							
					} // end IF not equal
							
				} // end IF we have a submitted size
						
			} // end IF we have an image file to measure
			
		} // end IF for checking an upload file's dimensions

} // end IF POST

if ($errors || !$_POST['upload']) {
// show the form -->

?>
	
	<form enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
	<fieldset style="border:1px solid #cccccc; margin:20px 0 20px 0; padding:10px 0 10px 10px; width:250px;">
    <legend>Resource Specs</legend>
	
	<!-- RESOURCE NAME -->
	<span<?php echo $error_class['resourceName']; ?>><strong>Resource Name:</strong></span>
	<span<?php echo $error_class_detail['resourceName']; ?>><?= ($error_msg['resourceName']) ? '<br />' . $error_msg['resourceName'] : '' ?></span><br />
	<input type="text" maxlength="100" name="resourceName" value="<?php echo $_SESSION['resourceName']; ?>" <?php echo $error_field_class['resourceName']; ?> /><br />&nbsp;<br />

	<!-- DIMENSIONS -->	
	<span<?php echo $error_class['dimensions']; ?>><strong>Dimensions (W x H in px):</strong></span>
	<span<?php echo $error_class_detail['dimensions']; ?>><?= ($error_msg['dimensions']) ? '<br />' . $error_msg['dimensions'] : '' ?></span><br />
	<select name="dimensions"<?php echo $error_field_class['dimensions']; ?>>
		<option value="">Choose one...</option>
	<?php
	
		// query to get dimensions
		$result = mysql_query("SELECT * FROM dimensions ORDER BY width ASC, height ASC");

		while($row = mysql_fetch_array($result)) {
	?>
		<option value="<?php echo $row['id']; ?>" <?php if ($_SESSION['dimensions'] == $row['id']) { echo ' selected="selected"';} ?>><?php echo $row['width'] . ' x ' . $row['height']; ?></option>
	
	<?php
		}
	?>
	</select><br />&nbsp;<br />

	<!-- HEADLINE -->	
	<strong>Headline:</strong><br />
	<input type="text" maxlength="100" name="headline" value="<?php echo $_SESSION['headline']; ?>" /><br />&nbsp;<br />

	<!-- SUBTEXT -->
	<strong>Subtext:</strong><br />
	<input type="text" maxlength="100" name="subtext" value="<?php echo $_SESSION['subtext']; ?>" /><br />&nbsp;<br />

	<!-- SITE -->
	<span<?php echo $error_class['site']; ?>><strong>Website:</strong></span>
	<span<?php echo $error_class_detail['site']; ?>><?= ($error_msg['site']) ? '<br />' . $error_msg['site'] : '' ?></span><br />
	<select name="site"<?php echo $error_field_class['site']; ?>>
		<option value="">Choose one...</option>
		<option<?= $_SESSION['site'] == 'union' ? ' selected' : '' ?> value="union">Union</option>
		<option<?= $_SESSION['site'] == 'campusrec' ? ' selected' : '' ?> value="campusrec">Campus Rec</option>
		<option<?= $_SESSION['site'] == 'affairs' ? ' selected' : '' ?> value="affairs">Student Affairs</option>
	</select><br />&nbsp;<br />	
	
	<!-- ALT TEXT -->
	<span<?php echo $error_class['altTxt']; ?>><strong>Alt text:</strong></span>
	<span<?php echo $error_class_detail['altTxt']; ?>><?= ($error_msg['altTxt']) ? '<br />' . $error_msg['altTxt'] : '' ?></span><br />
	<input type="text" maxlength="100" name="altTxt" value="<?php echo $_SESSION['altTxt']; ?>" <?php echo $error_field_class['altTxt']; ?> /><br />&nbsp;<br />

	<!-- DESCRIPTION -->
	<strong>Description (for internal use):</strong><br />
	<textarea name="description" rows="5" cols="15"><?php echo $_SESSION['description']; ?></textarea><br />&nbsp;<br />

	<!-- FILE TO UPLOAD -->
	<span<?php echo $error_class['resourceFile']; ?>><strong>File to Upload</strong> <span style="font-size:9px;">(jpg, gif, png / &lt; 512K):</span></span>
	<span<?php echo $error_class_detail['resourceFile']; ?>><?= ($error_msg['resourceFile']) ? '<br />' . $error_msg['resourceFile'] : '' ?></span><br />
	<?php
		if ($_SESSION['fileUploadOK']) {
			echo '<em>File Uploaded: <a href="' . $uploadLink . $_SESSION['sysFileName'] . '" target="_blank">' . $_SESSION['fileName'] . '</a></em><br />
			<a href="upload_resource.php?resetUpload=1">[change file]</a><br />&nbsp;<br />';
		} else {
	?>

	<!-- 512K upload size limit/server limit is 2 MB --><input name="MAX_FILE_SIZE" value="524288" type="hidden" /><input name="resourceFile" type="file" class="upload" /><br />&nbsp;<br />
<?php 
		}
?>
	<!-- LINK -->
	<strong>Link:</strong><br />
	<input type="text" maxlength="100" name="resourceLink" value="<?php echo $_SESSION['resourceLink']; ?>" /><br />
	<span style="font-size:11px;">Use a "site relative" path (eg /mealplans/ or /dining/sumc/cactusgrill/index.php) unless the link is to a different domain.</span><br />&nbsp;<br />

	<input type="submit" name="upload" value="Upload" />&nbsp;&nbsp;<button type="button" onClick="window.location='upload_resource.php?startOver=1'">Clear</button>
	
	</fieldset>
	</form>
	
<?php
} else {
	
	## save into the db and provide feedback ##
	
	// 1. move session vars into vars
	$filePath = $virtualPath . $_SESSION['sysFileName']; // merge the path with the name to get the full path
	$fileSize = round($_SESSION['fileSize'] / 1024, 0); // divide to get KB and round the filesize to 0 decimals
	$dimensionsID = $_SESSION['dimensions'];
	$type = $_SESSION['type'];
	$resourceName = mysql_real_escape_string($_SESSION['resourceName']);
	$resourceLink = mysql_real_escape_string($_SESSION['resourceLink']);
	$headline = mysql_real_escape_string($_SESSION['headline']);
	$subtext = mysql_real_escape_string($_SESSION['subtext']);
	$site = mysql_real_escape_string($_SESSION['site']);
	$altTxt = mysql_real_escape_string($_SESSION['altTxt']);
	$description = mysql_real_escape_string($_SESSION['description']);
	
	// 2. query to add resource to db
	$query = "INSERT INTO resource
	(filePath, fileSize, dimensionsID, type, resourceName, resourceLink, headline, subtext, site, altTxt, description)
	VALUES ('$filePath', $fileSize, $dimensionsID, '$type', '$resourceName', '$resourceLink', '$headline', '$subtext', '$site', '$altTxt', '$description')";

	// 3. check for errors saving to the db
	if (!mysql_query($query,$DBlink)) {

		## EMAIL
		// BEGIN: prepare to send the error notification email
		$to = 'kmbeyer@email.arizona.edu';
		$subject = 'ERROR: SA MARKETING CMS RESOURCE UPLOAD';
		$body = "There was a problem saving a resource to the database on " . date('m/d/Y H:i:s') . "."; 
		// END: prepare to send the error notification email

		// BEGIN: send the error email
		mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>'); // no bcc on this version
		// END: mail script

		die('Error:1 ' . mysql_error());

	} else {
		
		echo '<p class="confirmation">Upload successful!</p>
		<ul>
			<li><a href="upload_resource.php">Upload another resource</a></li>
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