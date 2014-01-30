<?php

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
<title>Upload Resource ~ Deliverance ~ Student Affairs ~ The University of Arizona</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body class="adminUI">
<div id="container">
  <div id="mainContent">
    <h2>Deliverance</h2>
	<h3>Manage Campaigns</h3>
	
	<table cellpadding="0" cellspacing="0" style="margin-top:10px;">
	<tr bgcolor="#cccccc" style="color:#000000;">
		<th style="padding:3px; width:20%;">Name</th>
		<th style="padding:3px; width:40%;">Description</th>
		<th style="padding:3px; width:10%;">Resources</th>
		<th style="padding:3px; width:10%;">Display Blocks</th>
		<th style="padding:3px; width:10%;">Edit</th>
		<th style="padding:3px; width:10%;">Delete</th>
	</tr>
	<tr valign="top">
		<td style="padding:3px;">Redington BYOB</td>
		<td style="padding:3px;">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</td>
		<td style="padding:3px;"><a href="#">View All</a></td>
		<td style="padding:3px;"><a href="#">View All</a></td>
		<td style="padding:3px;"><a href="edit_campaign.php">Edit</a></td>
		<td style="padding:3px;"><a href="#">Delete</a></td>
	</tr>
	<tr valign="top" bgcolor="#efefef">
		<td style="padding:3px;">IQ Breakfast</td>
		<td style="padding:3px;">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</td>
		<td style="padding:3px;"><a href="#">View All</a></td>
		<td style="padding:3px;"><a href="#">View All</a></td>
		<td style="padding:3px;"><a href="edit_campaign.php">Edit</a></td>
		<td style="padding:3px;"><a href="#">Delete</a></td>
	</tr>
	<tr valign="top">
		<td style="padding:3px;">Cactus Grill Breakfast</td>
		<td style="padding:3px;">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</td>
		<td style="padding:3px;"><a href="#">View All</a></td>
		<td style="padding:3px;"><a href="#">View All</a></td>
		<td style="padding:3px;"><a href="edit_campaign.php">Edit</a></td>
		<td style="padding:3px;"><a href="#">Delete</a></td>
	</tr>
	</table>

	<!-- end #mainContent --></div>
<!-- end #container --></div>
</body>
</html>