<?php

// required for access via session var enabled by webauth
session_start();

// get today's date in format compatible w/date stored in db
$today = date('Y-m-d',time());
 
// call db include file
include("inc_db.php");

// select database
$dbdeliv = new db("deliverance");
//mysql_select_db("deliverance", $DBlink)
//	or die(mysql_error());

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

		<div>
		<p style="padding-top:5px;">To see a listing of all resources compatible with this Display Block, click the "Search" button. Or customize your search by entering part of the resource name and/or its age. (<a href="add_resource_static_display_block.php?displayBlockID=<? echo $_GET['displayBlockID'] ?>">back</a>)</p>
			</div>
			<div>
			<form style="padding:0 0 10px 0; enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] . '?displayBlockID=' . $_GET['displayBlockID']; ?>" method="POST"">
			<input type="text" name="searchString" value="<? echo $_POST['searchString'] ?>" style="width:200px;" />
			<select name="range">
				<option value="">Resource Age...</option>
				<option<?= $_POST['range'] == '-30' ? ' selected' : '' ?> value="-30">1 month</option>
				<option<?= $_POST['range'] == '-60' ? ' selected' : '' ?> value="-60">2 months</option>
				<option<?= $_POST['range'] == '-90' ? ' selected' : '' ?> value="-90">3 months</option>
				<option<?= $_POST['range'] == '-120' ? ' selected' : '' ?> value="-120">4 months</option>
				<option<?= $_POST['range'] == '-150' ? ' selected' : '' ?> value="-150">5 months</option>
				<option<?= $_POST['range'] == '-180' ? ' selected' : '' ?> value="-180">6 months</option>
				<option<?= $_POST['range'] == '-365' ? ' selected' : '' ?> value="-365">1 year</option>
				<option<?= $_POST['range'] == 'All' ? ' selected' : '' ?> value="All">All</option>
			</select>&nbsp;&nbsp;
			<input type="submit" name="search" value="Search" />
			</form>
			</div>

			<div style="height:300px; overflow:auto;">
			<?php

			// calculate the range
			$range = date('Y-m-d',strtotime($_POST['range'] . ' days',strtotime($today)));

			if ($_POST['range'] != 'All' && $_POST['range'] != '') {

				// QUERY: get all appropriate resources within the specified range
				$resultResMatch = mysql_query("SELECT * FROM resource WHERE dimensionsID = " . $rowDisplay['dimensionsID'] . " AND site = '" . $rowDisplay['site'] . "' AND uploadDate >= '" . $range . "' AND resourceName LIKE '%" . $_POST['searchString'] . "%' ORDER BY resourceName");

				// QUERY: get all appropriate resources within the specified range
				// used for the count display
				$resultCnt = mysql_query("SELECT * FROM resource WHERE dimensionsID = " . $rowDisplay['dimensionsID'] . " AND site = '" . $rowDisplay['site'] . "' AND uploadDate >= '" . $range . "' AND resourceName LIKE '%" . $_POST['searchString'] . "%' ORDER BY resourceName");

			} else if ($_POST['search']) {

				// QUERY: get all appropriate resources regardless of age
				$resultResMatch = mysql_query("SELECT * FROM resource WHERE dimensionsID = " . $rowDisplay['dimensionsID'] . " AND site = '" . $rowDisplay['site'] . "' AND resourceName LIKE '%" . $_POST['searchString'] . "%' ORDER BY resourceName");

				// QUERY: get all appropriate resources regardless of age
				// used for the count display
				$resultCnt = mysql_query("SELECT * FROM resource WHERE dimensionsID = " . $rowDisplay['dimensionsID'] . " AND site = '" . $rowDisplay['site'] . "' AND resourceName LIKE '%" . $_POST['searchString'] . "%' ORDER BY resourceName");

			} // END IF ($_POST['range'] != 'All' && $_POST['range'] != '')

			// check to see if we have any results at all
			if ($resultResMatch) {

				// count the results
				$count = mysql_num_rows($resultCnt);
				
				// display the appropriate message with the count
				if ($count > 0) {
					echo '<p style="color:#339933;">' . $count . ' matching resource(s).</p>';
				} else {
					echo '<p style="color:#ff0000;">' . $count . ' matching resources. Please try again.</p>';
				} // END IF ($count > 0)

				// show the results
				while ($rowResList = mysql_fetch_array($resultResMatch)) {
			
					// QUERY: get dimensions
					$result = mysql_query("SELECT * FROM dimensions WHERE id = " . $rowResList['dimensionsID'] . "");
					$dimensions = mysql_fetch_array($result);

			?>

					<div style="margin-bottom:20px;">
						<img src="<? echo $browserURL . $rowResList['filePath']; ?>" <? if ($dimensions['width'] > 250) { echo 'width="250"'; }?> alt="<? echo $rowResList['altTxt']; ?>" title="<? echo $rowResList['altTxt']; ?>" align="middle" />
						<input type="button" value="Select" onClick="window.location='add_resource_static_display_block.php?displayBlockID=<? echo $_GET['displayBlockID'] ?>&resourceID=<? echo $rowResList['id']; ?>'" />
					</div>

			<?php
			
				} // END WHILE ($rowResList = mysql_fetch_array($resultResMatch))
			
			} // END IF ($resultResMatch)
			
			?>
			
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