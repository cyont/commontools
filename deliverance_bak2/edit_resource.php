<?php

// required for access via session var enabled by webauth
session_start();

// define page so that correct values are from settings.php
$page = 'edit';

// get today's date in format compatible w/date stored in db
$today = date('Y-m-d',time());

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Edit Resource ~ Deliverance ~ Student Affairs ~ The University of Arizona</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body class="adminUI">
<div id="container">
  <div id="mainContent">
    <h2>Deliverance</h2>
	<h3>Edit Resource</h3>

<?php

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
	
	// QUERY: get dimensions info
	$result = mysql_query("SELECT * FROM dimensions WHERE id = " . $resource['dimensionsID'] . "");
	$dimensions = mysql_fetch_array($result);
	
	// include the CMS settings file
	# => DO NOT <= change the position of this include in this page #
	include("settings.php");

?>

<?php
	// if the form has been submited, assign variables and check for errors
	if ($_POST['change']) {
		
		// assign w/o error checking
		$headline = cleanFormData($_POST['headline']);
		$subtext = cleanFormData($_POST['subtext']);
		$description = cleanFormData($_POST['description']);
		$resourceLink = cleanFormData($_POST['resourceLink']);

		######################
		## check for errors ##
		######################
		
		// Check resource name
		if (empty($_POST['resourceName'])) {
			$errors = true;
			$error_msg['resourceName'] = 'Please enter the name of the resource. You left this field blank. It has been filled with the current value from the database.';
			$error_class['resourceName'] = ' class="error"';
			$error_class_detail['resourceName'] = ' class="errorDetail"';
			$error_field_class['resourceName'] = ' class="bgError"';
		} else {
			$resourceName = cleanFormData($_POST['resourceName']);
		}

		// Check alt text
		if (empty($_POST['altTxt'])) {
			$errors = true;
			$error_msg['altTxt'] = 'Please enter alt text. You left this field blank. It has been filled with the current value from the database.';
			$error_class['altTxt'] = ' class="error"';
			$error_class_detail['altTxt'] = ' class="errorDetail"';
			$error_field_class['altTxt'] = ' class="bgError"';
		} else {
			$altTxt = cleanFormData($_POST['altTxt']);
		}

} // end IF POST

if ($errors || !$_POST['change']) {
// show the form -->

?>

	<div style="float:left; margin-top:10px; width:325px; height:600px; overflow:auto;">
	<img src="<? echo $browserURL . $resource['filePath'];  ?>" <? if ($dimensions['width'] > 300) { echo 'width="300"'; }?> alt="<? echo $resource['altTxt']; ?>" title="<? echo $resource['altTxt']; ?>" align="top" />
	</div>
	
	<div style="float:left; margin:10px 0 0 10px;">
	
	<form enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] ?>" method="post">
	<fieldset style="border:1px solid #cccccc; margin:0 0 20px 0; padding:10px 0 10px 10px; width:450px; height:565px;">
    <legend>Resource Info</legend>
	
	<div style="width:45%; padding-right:10px; float:left;">
	<p><strong>Resource Specs</strong><br />
	<? echo $resource['resourceName']; ?><br />
	<? echo $resource['fileSize']; ?>K <? echo $resource['type']; ?><br />
	<? echo $dimensions['width'] . ' x ' . $dimensions['height']; ?> (W x H)</p>
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
	<input type="text" maxlength="100" name="resourceName" value="<?php echo $resource['resourceName']; ?>" <?php echo $error_field_class['resourceName']; ?> /><br />&nbsp;<br />

	<!-- HEADLINE -->	
	<strong>Headline:</strong><br />
	<input type="text" maxlength="100" name="headline" value="<?php echo $resource['headline']; ?>" /><br />&nbsp;<br />

	<!-- SUBTEXT -->
	<strong>Subtext:</strong><br />
	<input type="text" maxlength="100" name="subtext" value="<?php echo $resource['subtext']; ?>" /><br />&nbsp;<br />

	<!-- ALT TEXT -->
	<span<?php echo $error_class['altTxt']; ?>><strong>Alt text:</strong></span>
	<span<?php echo $error_class_detail['altTxt']; ?>><?= ($error_msg['altTxt']) ? '<br />' . $error_msg['altTxt'] : '' ?></span><br />
	<input type="text" maxlength="100" name="altTxt" value="<?php echo $resource['altTxt']; ?>" <?php echo $error_field_class['altTxt']; ?> /><br />&nbsp;<br />

	<!-- DESCRIPTION -->
	<strong>Description (for internal use):</strong><br />
	<textarea name="description" rows="5" cols="15"><?php echo $resource['description']; ?></textarea><br />&nbsp;<br />

	<!-- LINK -->
	<strong>Link:</strong><br />
	<input type="text" maxlength="100" name="resourceLink" value="<?php echo $resource['resourceLink']; ?>" /><br />
	<span style="font-size:11px;">Use a "site relative" path (eg /mealplans/ or /dining/sumc/cactusgrill/index.php) unless the link is to a different domain.</span><br />&nbsp;<br />

	<a href="replace_resource.php?id=<? echo $_GET['id']; ?>">Replace resource</a>&nbsp;&nbsp;<input type="submit" name="change" value="Submit Changes" />
	
	</fieldset>
	</form>
	
<?php
} else {
	
	## save into the db and provide feedback ##
	
	// 1. clean the data
	$resourceName = mysql_real_escape_string($resourceName);
	$headline = mysql_real_escape_string($headline);
	$subtext = mysql_real_escape_string($subtext);
	$altTxt = mysql_real_escape_string($altTxt);
	$description = mysql_real_escape_string(description);
	$resourceLink = mysql_real_escape_string($resourceLink);

	// 2. edit the resource data in the db
	$query = "UPDATE resource SET 
	
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
		$subject = 'ERROR: SA MARKETING CMS RESOURCE EDIT';
		$body = "There was a problem editing a resource in the database on " . date('m/d/Y H:i:s') . "."; 
		// END: prepare to send the error notification email

		// BEGIN: send the error email
		mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>'); // no bcc on this version
		// END: mail script

		die('Error:1 ' . mysql_error());

	} else {
		
		echo '<p class="confirmation">Edit successful!</p>
		<ul>
			<li><a href="index.php">Return to main menu</a></li>
		</ul>';
		
	} // END IF db error

} // end IF/else ($errors || !$_POST['upload'])

?>
	
	</div>
	
	<br class="clear" />

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