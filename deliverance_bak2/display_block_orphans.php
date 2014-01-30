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
<title>List Display Block Orphans ~ Deliverance ~ Student Affairs ~ The University of Arizona</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body class="adminUI">
<div id="container">
  <div id="mainContent">
    <h1>Deliverance</h1>
	<h3>Display Blocks w/o Default Resource Assignments</h3>
	
	<?php
	
	// QUERY: get all the orphans
	$query = mysql_query("SELECT displayblock.id, displayblock.displayBlockName, displayblock.feedType FROM displayblock LEFT JOIN defaults ON displayblock.id = defaults.displayBlockID WHERE defaults.displayBlockID is NULL ORDER BY displayblock.displayBlockName");
	
	echo '<p style="margin-top:15px">';
	
	while ($noDefault = mysql_fetch_array($query)) {

		// figure out which feedtype and prepend the correct url
		switch ($noDefault['feedType']) {
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

		echo '<a href="' . $url . '?displayBlockID=' . $noDefault['id'] . '">' . $noDefault['displayBlockName'] . '</a><br />';
		$orphans = true;
		
	} // END the WHILE
	
	echo '</p>';
	
	if (!$orphans) {
		echo '<p>None found!</p>';
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