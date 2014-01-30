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
<title>Upload Resource ~ Deliverance ~ Student Affairs ~ The University of Arizona</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />
</head>

<body class="adminUI">
<div id="container">
  <div id="mainContent">
    <h2>Deliverance</h2>
	<h3>Create Campaign</h3>
	
	<form>
	<fieldset style="border:1px solid #cccccc; margin:20px 0 20px 0; padding:10px 0 10px 10px; width:250px;">
    <legend>Campaign Info</legend>
	
	Campaign Name:<br />
	<input type="text" maxlength="100" name="campaignName" /><br />
	
	Description (for internal use):<br />
	<textarea name="description" rows="5" cols="15"></textarea><br />

	<input type="submit" name="create" value="Create" />&nbsp;&nbsp;<input type="reset" name="reset" value="Clear" />
	
	</fieldset>
	</form>

	<!-- end #mainContent --></div>
<!-- end #container --></div>
</body>
</html>