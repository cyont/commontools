<?php

// required for access via session var enabled by webauth
session_start();
	
// check to see if they have logged in via webauth
if ($_SESSION['allowEdits']) {
			
// call db include file
include("inc_db.php");

// select database
$dbdeliv = new db("deliverance");
//	mysql_select_db("deliverance", $DBlink)
//		or die(mysql_error());

	// include the functions file
	include("functions.php");	

	// include the CMS settings file
	include("settings.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Create Display Block ~ Deliverance ~ Student Affairs ~ The University of Arizona</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body class="adminUI">
<div id="container">
  <div id="mainContent">
    <h2>Deliverance</h2>
	<h3>Create Display Block</h3>
	
<?php
	// if the form has been submited, assign variables and check for errors
	if ($_POST['create']) {
		
		// assign w/o error checking
		$displayType = 'image'; // hard coded until/unless we add additional options

		######################
		## check for errors ##
		######################
		
		// Check display block name
		if (empty($_POST['displayBlockName'])) {
			$errors = true;
			$error_msg['displayBlockName'] = 'Please enter the name of the display block.';
			$error_class['displayBlockName'] = ' class="error"';
			$error_class_detail['displayBlockName'] = ' class="errorDetail"';
			$error_field_class['displayBlockName'] = ' class="bgError"';
		} else {
			$displayBlockName = cleanFormData($_POST['displayBlockName']);
		}

		// Check display block dimensions
		if ($_POST['dimensions'] == "") {
			$errors = true;
			$error_msg['dimensions'] = 'Please select the dimensions.';
			$error_class['dimensions'] = ' class="error"';
			$error_class_detail['dimensions'] = ' class="errorDetail"';
			$error_field_class['dimensions'] = ' class="bgError"';
		} else {
			$dimensions = $_POST['dimensions'];
		}
		
		// Check feed type
		if ($_POST['feedType'] == "") {
			$errors = true;
			$error_msg['feedType'] = 'Please select the display type.';
			$error_class['feedType'] = ' class="error"';
			$error_class_detail['feedType'] = ' class="errorDetail"';
			$error_field_class['feedType'] = ' class="bgError"';
		} else {
			$feedType = $_POST['feedType'];
		}

		// Check website
		if ($_POST['site'] == "") {
			$errors = true;
			$error_msg['site'] = 'Please select the website.';
			$error_class['site'] = ' class="error"';
			$error_class_detail['site'] = ' class="errorDetail"';
			$error_field_class['site'] = ' class="bgError"';
		} else {
			$site = $_POST['site'];
		}

	} // end IF POST

if ($errors || !$_POST['create']) {
// show the form -->
	
?>
	
	<form enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
	<fieldset style="border:1px solid #cccccc; margin:20px 0 20px 0; padding:10px 0 10px 10px; width:250px;">
    <legend>Display Block Properties</legend>
	
	<!-- DISPLAY BLOCK NAME -->
	<span<?php echo $error_class['displayBlockName']; ?>><strong>Display Block Name:</strong></span>
	<span<?php echo $error_class_detail['displayBlockName']; ?>><?= ($error_msg['displayBlockName']) ? '<br />' . $error_msg['displayBlockName'] : '' ?></span><br />
	<input type="text" maxlength="100" name="displayBlockName" value="<?php echo $displayBlockName; ?>" <?php echo $error_field_class['displayBlockName']; ?> /><br />&nbsp;<br />


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
		<option value="<?php echo $row['id']; ?>" <?php if ($_POST['dimensions'] == $row['id']) { echo ' selected="selected"';} ?>><?php echo $row['width'] . ' x ' . $row['height']; ?></option>
	
	<?php
		}
	?>
	</select><br />&nbsp;<br />
	
	<!-- DISPLAY TYPE -->
	<span<?php echo $error_class['feedType']; ?>><strong>Feed Type:</strong></span>
	<span<?php echo $error_class_detail['feedType']; ?>><?= ($error_msg['feedType']) ? '<br />' . $error_msg['feedType'] : '' ?></span><br />
	<select name="feedType"<?php echo $error_field_class['feedType']; ?>>
		<option value="">Choose one...</option>
		<option<?= $_POST['feedType'] == 'static' ? ' selected' : '' ?> value="static">Static</option>
		<option<?= $_POST['feedType'] == 'random' ? ' selected' : '' ?> value="random">Random</option>
		<option<?= $_POST['feedType'] == 'sequential' ? ' selected' : '' ?> value="sequential">Sequential</option>
	</select><br />&nbsp;<br />	
	
	<!-- SITE -->
	<span<?php echo $error_class['site']; ?>><strong>Website:</strong></span>
	<span<?php echo $error_class_detail['site']; ?>><?= ($error_msg['site']) ? '<br />' . $error_msg['site'] : '' ?></span><br />
	<select name="site"<?php echo $error_field_class['site']; ?>>
		<option value="">Choose one...</option>
		<option<?= $_POST['site'] == 'union' ? ' selected' : '' ?> value="union">Union</option>
		<option<?= $_POST['site'] == 'campusrec' ? ' selected' : '' ?> value="campusrec">Campus Rec</option>
		<option<?= $_POST['site'] == 'affairs' ? ' selected' : '' ?> value="affairs">Student Affairs</option>
	</select><br />&nbsp;<br />	
	
	<input type="submit" name="create" value="Create Block" />&nbsp;&nbsp;<button type="button" onClick="window.location='create_display_block.php'">Clear</button>
	<p style="font-size:10px; border-top:1px dotted #cccccc; width:90%; margin-top:5px; padding-top:5px;">Note: New display blocks are assigned an automatic ID. The ID must be linked to the appropriate location on the website in order for the display block to function properly.</p>
	
	</fieldset>
	</form>

<?php
} else {
	
	## save into the db and provide feedback ##
	
	// 1. process any open text field data before inserting into db
	$displayBlockName = mysql_real_escape_string($displayBlockName);
	
	// 2. query to add resource to db
	$query = "INSERT INTO displayblock
	(displayBlockName, displayType, dimensionsID, feedType, site)
	VALUES ('$displayBlockName', '$displayType', $dimensions, '$feedType', '$site')";

	// 3. check for errors saving to the db
	if (!mysql_query($query,$DBlink)) {

		## EMAIL
		// BEGIN: prepare to send the error notification email
		$to = 'kmbeyer@email.arizona.edu';
		$subject = 'ERROR: SA MARKETING CMS DISPLAY BLOCK CREATION';
		$body = "There was a problem saving a resource to the database on " . date('m/d/Y H:i:s') . "."; 
		// END: prepare to send the error notification email

		// BEGIN: send the error email
		mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>'); // no bcc on this version
		// END: mail script

		die('Error:1 ' . mysql_error());

	} else {
		
		echo '<p class="confirmation">Display block created!</p>
		<p><strong>Next Step</strong>:<br />
		You <strong style="color:#ff0000">MUST</strong> assign at least a default resource to this display block before linking it to any live site.</p>
		<ul>
			<li>View <a href="display_block_orphans.php">list of display blocks</a> with no default resources assigned</li>
			<li><a href="create_display_block.php">Create another display block</a></li>
			<li><a href="index.php">Return to main menu</a></li>
		</ul>';
		
		$temp = $_SESSION['allowEdits']; // store session var temporarily so that session unset doesn't cause problems when resubmitting
		session_unset(); // clear out the session variables
		$_SESSION['allowEdits'] = $temp; // reassign session var after clearing
		
		
	} // END IF db error

} // end IF/else ($errors || !$_POST['create'])
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