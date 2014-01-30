<?php

// required for access via session var enabled by webauth
session_start();
	
// check to see if they have logged in via webauth
if ($_SESSION['allowEdits']) {

	// call db include file
	include("inc_db.php");

	// select database
	mysql_select_db("deliverance", $DBlink)
		or die(mysql_error());

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Active Display Blocks ~ Deliverance ~ Student Affairs ~ The University of Arizona</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body class="adminUI">
<div id="container">
  <div id="mainContent">
    <h1>Deliverance</h1>
	<h3>Active Display Blocks</h3>
	<p style="color:#ff0000; font-size:10px; padding:0; float:left;">Use the 'page trimmer' in <a href="admin.php">Admin view</a> to purge pages w/o hits for more than 6 months.</span></p>
	<p style="margin:-15px 0 0 0; float:right;"><a href="index.php">Main Menu</a></p>

	<br class="clear" />
	
	<?php
	
	// QUERY: count total unique display blocks in use
	$countBlocks = mysql_query("SELECT COUNT(DISTINCT displayBlockID) FROM pages");
	$total = mysql_fetch_array($countBlocks);

	// handle the filter/sort preferences
	if ($_POST['sort']) {
		if ($_POST['sort'] == 'displayBlockID') {
			$query = mysql_query("SELECT * FROM pages ORDER BY displayBlockID " . $_POST['order'] . "");
		} else if ($_POST['sort'] == 'location') {
			$query = mysql_query("SELECT * FROM pages ORDER BY domain, path " . $_POST['order'] . "");
		} else if ($_POST['sort'] == 'type') {
			$query = mysql_query("SELECT * FROM pages ORDER BY type " . $_POST['order'] . "");
		} else if ($_POST['sort'] == 'date') {
			$query = mysql_query("SELECT * FROM pages ORDER BY date " . $_POST['order'] . "");
		}
	} // END IF ($_POST['sort'])

	if (!$_POST || $_POST['sort'] == '') {
		// DEFAULT QUERY
		$query = mysql_query("SELECT * FROM pages ORDER BY domain ASC, path ASC");
	} // END IF (!$_POST || $_POST['sort'] == '')

	?>

	<!-- sort form -->
	<form enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
	<select name="sort" onChange="this.form.submit();" style="width:100px;">
	<option value="">Sort by...</option>
	<option<?= $_POST['sort'] == 'displayBlockID' ? ' selected' : '' ?> value="displayBlockID">Display Block ID</option>
	<option<?= $_POST['sort'] == 'location' ? ' selected' : '' ?> value="location">Location</option>
	<option<?= $_POST['sort'] == 'type' ? ' selected' : '' ?> value="type">Type</option>
	<option<?= $_POST['sort'] == 'date' ? ' selected' : '' ?> value="date">Last Hit</option>
	</select>

	<?php
	
	if ($_POST['order']) {
		echo '<input type="checkbox" name="order" checked="checked" value="DESC" onClick="this.form.submit();" /> <span style="color:#333333;">Reverse Order</span>';
		if (!$_POST['sort']) {
			echo '<br /><span style="color:#ff0000; font-size:10px; display:block; padding-top:2px;">Please choose a sorting method.</span>';
		}
	} else {
		echo '<input type="checkbox" name="order" value="DESC" onClick="this.form.submit();" /> <span style="color:#333333;">Reverse Order</span>';
	} // END IF ($_POST['order'])
	
	?>
	
	<!-- close the form -->
	</form>	

	<?php

	// set a counter
	$i = 1;

	echo '<table style="margin-top:5px;" cellpadding="5">
	<tr>
		<th>Count</td>
		<th>ID#</th>
		<th>Name</th>
		<th>Location</th>
		<th>Dimensions</th>
		<th>Type</th>
		<th>Last Hit</th>
	</tr>';
	
	// init counter for alternating row colors
	$cnt = 0;
	
	while ($row = mysql_fetch_array($query)) {
		
		// QUERY: get the display block info
		$result = mysql_query("SELECT * FROM displayblock WHERE id = " . $row['displayBlockID'] . "");
		$displayBlock = mysql_fetch_array($result);

		// QUERY: get the dimension info
		$resultDimensions = mysql_query("SELECT * FROM dimensions WHERE id = " . $displayBlock['dimensionsID'] . "");
		$dimensions = mysql_fetch_array($resultDimensions);

		echo "<tr style=\"font-size:10px;"; if($cnt%2 == 0) { echo "background-color:#efefef;\">"; } else { echo "\">"; }
			echo '<td>' . $i . '</td>
			<td>' . $displayBlock['id'] . '</td>
			<td><a href="add_resource_' . $row['type'] . '_display_block.php?displayBlockID=' . $displayBlock['id'] . '" title="Edit this display block">' . $displayBlock['displayBlockName'] . '</a></td>
			<td><a href="' . $row['domain'] . $row['path'] . '" title="View/edit display blocks attached to this page" target="_blank">' . $row['domain'] . $row['path'] . '</a></td>
			<td>' . $dimensions['width'] . ' x ' . $dimensions['height'] . '</td>
			<td>' . $row['type'] . '</td>
			<td>' . date("m/d/Y g:i:s A", strtotime($row['date'])) . '</td>			
			';
		echo '</tr>';
		
		$active = true;
		
		// increment the counters
		$i++;
		$cnt++;
		
	} // END the WHILE
	
		// decrement the counter so total is accurate
		$i--;
	
	echo '<tr style="font-size:10px;">
			<td style="color:#00cc00"><strong>' . $i . '</strong></td>
			<td style="color:#00cc00"><strong>' . $total[0] . '</strong></td>
			<td colspan="5" style="color:#000000"><strong>TOTALS</strong> (instances/unique)</td>
	</tr>';
	echo '</table>';
	
	if (!$active) {
		echo '<p>No active display blocks found!</p>';
	}

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