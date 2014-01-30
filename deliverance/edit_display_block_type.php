<?php

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
<title>Edit Display Block Type ~ Deliverance ~ Student Affairs ~ The University of Arizona</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body class="adminUI">
<div id="container">
  <div id="mainContent">
    <h2>Deliverance</h2>
	<h3>Edit Display Block Type</h3>
	
	<form>
	<fieldset style="border:1px solid #cccccc; margin:20px 0 20px 0; padding:10px 0 10px 10px; width:250px;">
    <legend>Display Block Properties</legend>
	
	Name:<br />
	<input type="text" maxlength="100" name="displayBlockName" /><br />
	
	Dimensions (W x H in px):<br />
	400 x 300<br />
	
	Display Type:<br />
	<select name="displayType">
		<option value="">Choose one...</option>
		<option value="static" selected="selected">Static</option>
		<option value="random">Random</option>
		<option value="sequential">Sequential</option>
	</select><br />
	
	<input type="submit" name="update" value="Update Block" />
	<p style="font-size:10px; border-top:1px dotted #cccccc; width:90%; margin-top:5px; padding-top:5px;">Note: Please be aware that changing a display block 'type' will erase all of this display block's previously assigned resources. You will need to rebuild the display block queue from scratch. Proceed with caution...</p>
	
	</fieldset>
	</form>

	<!-- end #mainContent --></div>
<!-- end #container --></div>
</body>
</html>