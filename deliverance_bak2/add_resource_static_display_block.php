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

// update displayblock table if a new resource has been assigned to it
if ($_POST['assignResource']) {

	// put the displayblock id back into the GET var so page will load latest resource
	$_GET['displayBlockID'] = $_POST['displayBlockID'];
		
	// see if a default already exists and either UPDATE or INSERT
	if ($_POST['makeDefault']) {

		// QUERY: to see if default exists
		$result = mysql_query("SELECT * FROM defaults WHERE displayBlockID = " . $_GET['displayBlockID'] . "");
		$default = mysql_fetch_array($result);
		
		// if a default already exists, update it
		if ($default) {
			
			$query = "UPDATE defaults SET
			resourceID = \"" . $_POST['newResource'] . "\"
			WHERE displayBlockID = \"" . $_GET['displayBlockID'] . "\"";
			
			// check for errors saving to the db
			if (!mysql_query($query,$DBlink)) {

				## EMAIL
				// BEGIN: prepare to send the error notification email
				$to = 'kmbeyer@email.arizona.edu';
				$subject = 'ERROR: SA MARKETING CMS ADD DEFAULT RESOURCE TO STATIC DISPLAY BLOCK';
				$body = "There was a problem saving information to the database on " . date('m/d/Y H:i:s') . "."; 
				// END: prepare to send the error notification email

				// BEGIN: send the error email
				mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>'); // no bcc on this version
				// END: mail script

				die('Error:1 ' . mysql_error());
		
			// no error
			} else {
			
				$success = true;
		
			} // END IF db error

		// there's no default resource, so add a new one
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
				$subject = 'ERROR: SA MARKETING CMS ADD DEFAULT RESOURCE TO STATIC DISPLAY BLOCK';
				$body = "There was a problem saving information to the database on " . date('m/d/Y H:i:s') . "."; 
				// END: prepare to send the error notification email

				// BEGIN: send the error email
				mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>'); // no bcc on this version
				// END: mail script

				die('Error:2 ' . mysql_error());

			} else {
		
				$success = true;		
		
			} // END IF db error
			
		} // END IF ELSE ($default)
	
	// this is a new data entry, not a default, so proceed to add a new resource
	} else {
	
		## save into the db and provide feedback ##
		// 1. query to update display block with new resource
		$query = "UPDATE displayblock SET
		resourceID = \"" . $_POST['newResource'] . "\"
		WHERE id = \"" . $_GET['displayBlockID'] . "\"";

		// 2. check for errors saving to the db
		if (!mysql_query($query,$DBlink)) {

		## EMAIL
		// BEGIN: prepare to send the error notification email
		$to = 'kmbeyer@email.arizona.edu';
		$subject = 'ERROR: SA MARKETING CMS ADD RESOURCE TO STATIC DISPLAY BLOCK';
		$body = "There was a problem saving information to the database on " . date('m/d/Y H:i:s') . ".";
		// END: prepare to send the error notification email

		// BEGIN: send the error email
		mail ($to, $subject, $body, 'From: SA Marketing Web Team <noreply@email.arizona.edu>'); // no bcc on this version
		// END: mail script

			die('Error:3 ' . mysql_error());
			
		} else {
			
			$success = true;
		
		} // END IF db error

	} // END else this is a new data entry

} // END IF ($_POST['assignResource'])
	
// check to see if they have logged in via webauth
if ($_SESSION['allowEdits']) {
		
	// QUERY: get displayblock info
	$result = mysql_query("SELECT * FROM displayblock WHERE id = " . $_GET['displayBlockID'] . "");
	$rowDisplay = mysql_fetch_array($result);
	
	// QUERY: get resource info
	$result = mysql_query("SELECT * FROM resource WHERE id = " . $rowDisplay['resourceID'] . "");
	
	// only if not empty
	if ($result) {
		$rowResource = mysql_fetch_array($result);	
	}
	
	// QUERY: get default resource info
	$resultDefault = mysql_query("SELECT * FROM defaults WHERE displayBlockID = " . $_GET['displayBlockID'] . "");
	$rowDefault = mysql_fetch_array($resultDefault);
	
		// QUERY: get default resource specs
		$resultDefaultSpecs = mysql_query("SELECT * FROM resource WHERE id = " . $rowDefault['resourceID'] . "");
		
		// only if not empty
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
<title>Add Resource to Static Display Block ~ Deliverance ~ Student Affairs ~ The University of Arizona</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body class="adminUI">
<div id="container">
  <div id="mainContent">
    <h2>Deliverance</h2>
	<h3 style="float:left;">Edit Resource: Static Display Block <span style="color:#F00">#<? echo $rowDisplay['id']; ?></span></h3>
	<p style="margin:10px 15px 0 0; float:right;"><a href="index.php">Main Menu</a></p>
	
	<br class="clear" />

	<div style="border:1px solid #cccccc; width:225px; height:400px; float:left; margin:10px 0 10px 0; background-color:#efefef; position:relative;">
	<?
	// check for a default resource
	if ($rowDefaultSpecs) {
	?>
		<img src="<? echo $browserURL . $rowDefaultSpecs['filePath'];  ?>" <? if ($rowDimensionsDefault['width'] > 150) { echo 'width="150"'; }?> alt="<? echo $rowDefaultSpecs['altTxt']; ?>" title="<? echo $rowDefaultSpecs['altTxt']; ?>" align="top" /> <strong>Default</strong><br />
		<a href="edit_resource.php?id=<? echo $rowDefaultSpecs['id']; ?>"><? echo $rowDefaultSpecs['resourceName'];  ?></a>
	<?
	// there's no default! let the user know
	} else {
		echo '<p style="color:#ff0000; padding-top:15px; text-align:center;">NO DEFAULT!</p>';
	} // END IF ($rowDefaultSpecs)
	?>
	
	<br />&nbsp;<br />
	
	<?
	// only show if a resource has been assigned
	if ($rowResource) {
	?>
		<div style="float:left;">
		<img src="<? echo $browserURL . $rowResource['filePath']; ?>" <? if ($rowDimensions['width'] > 150) { echo 'width="150"'; }?> alt="<? echo $rowResource['altTxt']; ?>" title="<? echo $rowResource['altTxt']; ?>" /><br />
		<a href="edit_resource.php?id=<? echo $rowResource['id']; ?>"><? echo $rowResource['resourceName'];  ?></a>
		</div>
		
	<?
	} // END IF ($rowResource)
	?>
	
	</div>
	
	<div style="border-top:1px solid #cccccc; border-right:1px solid #cccccc;border-bottom:1px solid #cccccc; width:220px; height:400px; float:left; margin-top:10px; margin-right:20px; padding-left:5px; position:relative;">
	<p><strong>Display Block Properties</strong><br />
	Name: <? echo $rowDisplay['displayBlockName']; ?><br />
	Dimensions: <? echo $rowDimensions['width']; ?> x <? echo $rowDimensions['height']; ?> (W x H)<br />
	Feed type: <? echo $rowDisplay['feedType']; ?></p>
<!--		<div style="position:absolute; bottom:2px; right:2px;"><a href="edit_display_block_type.php?displayBlockID=<?// echo $rowDisplay['id']; ?>" title="WARNING: This will erase all resources in the display block queue!">Edit display type</a></div>-->
	</div>

	<div style="border:1px solid #cccccc; width:440px; height:390px; float:left; margin:10px 0 10px 0; padding:5px; position:relative;">
	
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
	
	<div style="width:250px; float:left;">
	
	<?php
	// don't show the image unless a resource selection has been made
	if ($_POST['resource']) {
	?>
		<img src="<? echo $browserURL . $resource['filePath']; ?>" <? if ($dimensions['width'] > 250) { echo 'width="250"'; }?> alt="<? echo $resource['altTxt']; ?>" title="<? echo $resource['altTxt']; ?>" /><br />
	<?php
	}
	?>
	<a href="upload_resource.php">Upload a new resource</a>
	</div>
	
	<?php
	
	// QUERY: get all appropriate resources
	$resultResMatch = mysql_query("SELECT * FROM resource WHERE dimensionsID = " . $rowDisplay['dimensionsID'] . " AND site = '" . $rowDisplay['site'] . "' ORDER BY resourceName");
	
	?>

	<div style="float:left;">
	<form enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] . '?displayBlockID=' . $_GET['displayBlockID']; ?>" method="POST">
		<select name="resource" onChange="this.form.submit();" style="width:185px;">
		<option value="">Choose a resource...</option>
		<?
		while ($rowResList = mysql_fetch_array($resultResMatch)) {
		?>
			<option value="<?php echo $rowResList['id']; ?>" <?php if ($_POST['resource'] == $rowResList['id']) { echo ' selected="selected"';} ?>><?php echo $rowResList['resourceName']; ?></option>
	
		<?php
		} // END WHILE
		?>
		</select><br />&nbsp;<br />
	</form>
	
	<form enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
		<input type="hidden" name="displayBlockID" value="<? echo $_GET['displayBlockID'] ?>"
		<input type="hidden" name="newResource" value="<? echo $_POST['resource'] ?>"
		<input type="checkbox" name="makeDefault" value="yes" title="Make this the default resource for this display block" /> Set as default resource<br />&nbsp;<br />		
		<input type="submit" name="assignResource" value="Assign Resource" />
	</form>
	
	<?
	if ($success) {
		echo '<p class="confirmationSmall">Assignment successful!</p>';
	}
	?>
	
	</div>
	
	<br class="clear" />
	
	<div style="float:left; width:210px; margin-top:20px; padding-right:20px;">

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
	</div>

	<div style="float:left; margin-top:20px; width:180px; padding-right:20px;">
	<p><strong>Resource Specs</strong><br />
	<? echo $resource['resourceName']; ?><br />
	<? echo $resource['fileSize']; ?>K <? echo $resource['type']; ?><br />
	<? echo $dimensions['width'] . ' x ' . $dimensions['height']; ?> (W x H)</p>
	</div>
	
	<br class="clear" />
	
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
		
	} // END IF ($_SESSION['allowEdits'])

?>