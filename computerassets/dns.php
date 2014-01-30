<?php
session_start();
if (!$_SESSION['computerassets']['authorized']) {
  header("Location: index.php");
  exit;
}
?>
<html>
<head>
<title>Computer Assets - Computers</title>
<link rel="stylesheet" type="text/css" href="asset_styles.css" />
<script src="jquery-1.2.6.min.js"></script>
<script src="asset_functions.js"></script>
</head>
<body onload="loadDnsList();">
<?php include("header.php"); ?>
<div id="content">
<div id="machine_list"></div>
<div id="machine_details"><br /> &larr; Select a Computer/Device</div>
<?php include("assets_commonelements.php"); ?>
</div>
</div>
</body>
</html>
