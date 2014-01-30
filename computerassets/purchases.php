<?php
session_start();
if (!$_SESSION['computerassets']['authorized']) {
  header("Location: index.php");
  exit;
}
?>
<html>
<head>
<title>Computer Assets - Purchases</title>
<link rel="stylesheet" type="text/css" href="asset_styles.css" />
<script src="jquery-1.2.6.min.js"></script>
<script src="asset_functions.js"></script>
</head>
<body onload="loadPurchaseList();">
<?php include("header.php"); ?>
<div id="content">
	<div id="purchase_dates_header">
		<h3 style="margin-top:0;">Purchases</h3>
	</div>
	<div id="purchase_dates_wrapper">
		<div id="purchase_dates">
		</div>
	</div>
	<div id="purchase_items">
	&larr; Select a Purchase Date
	</div>
<?php include("assets_commonelements.php"); ?>
</div>
</body>
</html>

