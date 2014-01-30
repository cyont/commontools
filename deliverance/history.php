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

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Event History ~ Deliverance ~ Student Affairs ~ The University of Arizona</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body class="adminUI">
<div id="container">
  <div id="mainContent">
    <h1>Deliverance</h1>
	<h3>Event History</h3>

	<p style="margin:-15px 0 0 0; float:right;"><a href="index.php">Main Menu</a> | <a href="admin.php">Admin Menu</a></p>

	<br class="clear" />
	
	<?php
		
	// calculate the age limits
	if ($_POST['age'] == 0) {
		$limit = 0; // if it's set to 'All' just make it 0
	} else {
		$limit = time() - ($_POST['age'] * 86400); // age limit X number of seconds in a day
	}

	// default setting
	$defaultLimit = time() - (30 * 86400); // 30 days
	
	// handle the filter/sort preferences if they've only chosen a sort by not an age
	if ($_POST['sort'] && $_POST['age'] == '') {
		if ($_POST['sort'] == 'netID') {
			$query = mysql_query("SELECT * FROM history WHERE timestamp > " . $defaultLimit . " ORDER BY netID " . $_POST['order'] . "");
		} else if ($_POST['sort'] == 'action') {
			$query = mysql_query("SELECT * FROM history WHERE timestamp > " . $defaultLimit . " ORDER BY action " . $_POST['order'] . "");
		} else if ($_POST['sort'] == 'script') {
			$query = mysql_query("SELECT * FROM history WHERE timestamp > " . $defaultLimit . " ORDER BY page " . $_POST['order'] . "");
		} else if ($_POST['sort'] == 'site') {
			$query = mysql_query("SELECT * FROM history WHERE timestamp > " . $defaultLimit . " ORDER BY site " . $_POST['order'] . "");
		} else if ($_POST['sort'] == 'resourceName') {
			$query = mysql_query("SELECT * FROM history WHERE timestamp > " . $defaultLimit . " ORDER BY resourceName " . $_POST['order'] . "");
		} else if ($_POST['sort'] == 'timestamp') {
			$query = mysql_query("SELECT * FROM history WHERE timestamp > " . $defaultLimit . " ORDER BY timestamp " . $_POST['order'] . "");
		}
	} // END IF ($_POST['sort'] && $_POST['age'] == '')

	if ($_POST['sort'] && $_POST['age'] != '') {
		if ($_POST['sort'] == 'netID') {
			$query = mysql_query("SELECT * FROM history WHERE timestamp > " . $limit . " ORDER BY netID " . $_POST['order'] . "");
		} else if ($_POST['sort'] == 'action') {
			$query = mysql_query("SELECT * FROM history WHERE timestamp > " . $limit . " ORDER BY action " . $_POST['order'] . "");
		} else if ($_POST['sort'] == 'script') {
			$query = mysql_query("SELECT * FROM history WHERE timestamp > " . $limit . " ORDER BY page " . $_POST['order'] . "");
		} else if ($_POST['sort'] == 'site') {
			$query = mysql_query("SELECT * FROM history WHERE timestamp > " . $limit . " ORDER BY site " . $_POST['order'] . "");
		} else if ($_POST['sort'] == 'resourceName') {
			$query = mysql_query("SELECT * FROM history WHERE timestamp > " . $limit . " ORDER BY resourceName " . $_POST['order'] . "");
		} else if ($_POST['sort'] == 'timestamp') {
			$query = mysql_query("SELECT * FROM history WHERE timestamp > " . $limit . " ORDER BY timestamp " . $_POST['order'] . "");
		}
	} // END IF ($_POST['sort'] && $_POST['age'] != '')

	// if they didn't choose a sort order but did choose an age, show the error message
	if (!$_POST['sort'] && $_POST['age'] != '') {
		$error = true;
	} // END IF (!$_POST['sort'] && $_POST['age'] != '')

	// setup the default query
	if (!$_POST || $_POST['sort'] == '') {
		// DEFAULT QUERY
		$query = mysql_query("SELECT * FROM history WHERE timestamp > " . $defaultLimit . " ORDER BY timestamp DESC");
	} // END IF (!$_POST || $_POST['sort'] == '')
	?>

	<!-- sort form -->
	<form enctype="multipart/form-data" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
	<select name="sort" onChange="this.form.submit();" style="width:125px;">
	<option value="">Sort by...</option>
	<option<?= $_POST['sort'] == 'netID' ? ' selected' : '' ?> value="netID">NetID</option>
	<option<?= $_POST['sort'] == 'action' ? ' selected' : '' ?> value="action">Action</option>
	<option<?= $_POST['sort'] == 'script' ? ' selected' : '' ?> value="script">Script</option>
	<option<?= $_POST['sort'] == 'site' ? ' selected' : '' ?> value="site">Site</option>
	<option<?= $_POST['sort'] == 'resourceName' ? ' selected' : '' ?> value="resourceName">Resource Name</option>
	<option<?= $_POST['sort'] == 'timestamp' ? ' selected' : '' ?> value="timestamp">Time Stamp</option>
	</select>

	<?php
	
	if ($_POST['order']) {
		echo '<input type="checkbox" name="order" checked="checked" value="DESC" onClick="this.form.submit();" /> <span style="color:#333333;">Reverse Order</span>';
		if (!$_POST['sort']) {
			$error = true;
//			echo '<br /><span style="color:#ff0000; font-size:10px; display:block; padding-top:2px;">Please choose a sorting method.</span>';
		}
	} else {
		echo '<input type="checkbox" name="order" value="DESC" onClick="this.form.submit();" /> <span style="color:#333333;">Reverse Order</span>';
	} // END IF ($_POST['order'])
	
	?>
	
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	
	<!-- age limit form -->
	<select name="age" onChange="this.form.submit();" style="width:110px;">
	<option value="">Limit to...</option>
	<option<?= $_POST['age'] == '7' ? ' selected' : '' ?> value="7">Last 7 days</option>
	<option<?= $_POST['age'] == '14' ? ' selected' : '' ?> value="14">Last 14 days</option>
	<option<?= $_POST['age'] == '30' ? ' selected' : '' ?> value="30">Last 30 days</option>
	<option<?= $_POST['age'] == '60' ? ' selected' : '' ?> value="60">Last 60 days</option>
	<option<?= $_POST['age'] == '90' ? ' selected' : '' ?> value="90">Last 90 days</option>
	<option<?= $_POST['age'] == '180' ? ' selected' : '' ?> value="180">Last 180 days</option>
	<option<?= $_POST['age'] == '365' ? ' selected' : '' ?> value="365">Last 365 days</option>
	<option<?= $_POST['age'] == '0' ? ' selected' : '' ?> value="0">No limit</option>
	</select>&nbsp;&nbsp;
	
	<input type="button" value="Reset All" onClick="window.location='history.php'">
	
	<?php

	// show the error message
	if ($error) {
		echo '<br /><span style="color:#ff0000; font-size:10px; display:block; padding-top:2px;">Please choose a sorting method.</span>';
	}
	?>
	
	<br /><span style="font-size:10px;">Default search (no selections) is a 30-day view in order of descending time stamp.</span>
	
	<!-- close the form -->
	</form>
	
	<?php

	// set a counter
	$i = 1;

	echo '<table style="margin-top:5px;" cellpadding="5">
	<tr>
		<th>Event #</td>
		<th>NetID</th>
		<th>Action</th>
		<th>Script</th>
		<th>Site</th>
		<th>Resource Name</th>
		<th>Time Stamp</th>
	</tr>';
	
	// init counter for alternating row colors
	$cnt = 0;
	
	// loop through the results
	while ($row = mysql_fetch_array($query)) {

		// check server and site and assign appropriate URL
		if ($row['server'] == 'TEST SERVER') {
			$url = 'http://deliverance.test';
		} else if ($row['site'] == 'affairs') {
			$url = 'http://studentaffairs.arizona.edu';
		} else if ($row['site'] == 'campusrec') {
			$url = 'http://campusrec.arizona.edu';
		} else if ($row['site'] == 'union') {
			$url = 'http://union.arizona.edu';
		} // END IF ($row['server'] = 'TEST SERVER')
	
		echo "<tr style=\"font-size:10px;"; if($cnt%2 == 0) { echo "background-color:#efefef;\">"; } else { echo "\">"; }
			echo '<td>' . $i . '</td>
			<td>' . $row['netID'] . '</td>
			<td>' . $row['action'] . '</td>
			<td>' . $row['page'] . '</td>
			<td>' . $row['site'] . '</td>
			<td><a href="' . $url . $row['filePath'] . '" target="_blank">' . $row['resourceName'] . '</a></td>
			<td>' . date("m/d/Y g:i:s A", $row['timestamp']) . '</td>
			';
		echo '</tr>';
		
		$results = true;
		
		// increment the counters
		$i++;
		$cnt++;
		
	} // END the WHILE
	
	echo '</table>';
	
	if (!$results) {
		echo '<p>No results to display.</p>';
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