<?php
session_start();
if (!$_SESSION['computerassets']['authorized']) {
  header("Location: index.php");
  exit;
}
?>
<html>
<head>
<title>Computer Assets - Licenses</title>
<link rel="stylesheet" type="text/css" href="asset_styles.css" />
<script src="jquery-1.2.6.min.js"></script>
<script src="asset_functions.js"></script>
</head>
<body onload="loadSoftwareList('current');">
<?php include("header.php"); ?>
<div id="content">
	<div id="software_titles_header">
		<div style="float:left;"><h3 style="margin-top:0;">Software</h3></div>
		<div id="software_all" style="float:right;margin-right:740px;border:1px solid #036;padding:4px;cursor:pointer;" onclick="loadSoftwareList('all');">All</div>
		<div id="software_current" style="float:right;border:1px solid #036;padding:4px;margin-right:10px;cursor:pointer;background-color:#036;color:#FFF;" onclick="loadSoftwareList('current');">Current</div>
	</div>
	<div id="software_titles_wrapper">
		<div id="software_titles">
		</div>
	</div>
	<div id="software_licenses">
		<table WIDTH="100%" CELLPADDING=0 CELLSPACING=0 style="border-bottom:1px solid #5A85CF;border-right:1px solid #5A85CF;">
			<tr style="background-color:#CFEBFF;font-weight:bold;cursor:auto;">
				<td style="width:auto;">Description</td>
				<td style="width:130px;">Assigned Machine</td>
				<td style="width:115px;">Purchase Date</td>
				<td style="width:115px;">Visible</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
			</tr>
		</table>
	</div>
	<div id="software_computer">
	<h3>Computer or Purchase</h3>
	</div>
<?php include("assets_commonelements.php"); ?>
</div>
</body>
</html>