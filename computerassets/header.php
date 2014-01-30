<style type="text/css">
html, body {
	margin:0;
	padding:0;
}
html, body, table, tr, td {
	font-family:Helvetica, Verdana, Geneva, sans-serif;
	font-size:12px;
}
a, img {
	border:none;
	text-decoration:none;
	color:#000000;
}
#header {
	width:1000px;
	float:left;
}
#header_left {
	float:left;
	width:300px;
}
#header_right {
	float:right;
	width:700px;
}
#header_title {
	margin-left:12px;
}
#header_menu {
	margin-left:12px;
}
#pipe {
	margin-left:5px;
	margin-right:5px;
	font-size:16px;
}
#header_button {
	text-align:right;
	margin-right:12px;
	margin-top:42px;
}
#content {
	width:1000px;
	float:left;
}
#header_button input {
	border:1px solid #5A85CF;
}
</style>
<script src="/jslib/jquery.js"></script>
<?php
$currentPage = $_SERVER["PHP_SELF"];
$currentPage = Explode('/', $currentPage);
$currentPage = substr($currentPage[count($currentPage) - 1],0,-4);
?>
<div id="header">
	<div id="header_right">
		<div id="header_button">
		<?php
			if($currentPage == "jquery"){
				echo '<div style="float:right;border:1px solid #036;color:#036;padding:4px;cursor:pointer;" onclick="addComputerDialogHandler();">New Computer</div>';
				echo '<div style="float:right;margin-right:20px;margin-top:-28px;">
					<label for="serialnum">Serial#</label>
   					<input type="text" name="serialnum" id="serialnum" onchange="findSerialnum(this.value)"/><br /><br />
					<label for="assettag">Asset Tag</label>
					<input type="text" name="assettag" id="assettag" onchange="findAssettag(this.value)"/>
				</div>';
				echo '<div style="float:right;margin-right:20px;margin-top:-28px;">
					<label for="mac">MAC Address</label>
   					<input type="text" name="mac" id="mac" onchange="findMac(this.value)"/>
   				</div>';
			}
			if($currentPage == "dns"){
				echo '<div style="float:right;border:1px solid #036;color:#036;padding:4px;cursor:pointer;" onclick="adddns();">Add an IP</div>';
			}
			if($currentPage == "licenses"){
        echo '<div style="float:right;border:1px solid #036;color:#036;padding:4px;cursor:pointer;" onclick="newSoftwareDialog();">New Software</div>';
      }
			if($currentPage == "purchases"){
				echo '<div style="float:right;border:1px solid #036;color:#036;padding:4px;cursor:pointer;" onclick="addpurchase();">New Purchase</div>';
			}
		?>
		</div>
	</div>
	<div id="header_left">
		<div id="header_title"><h2 style="color:#003366;">Computer Assets</h2></div>
		<div id="header_menu">
			<a href="jquery.php" <?php if($currentPage == "jquery"){echo 'style="color:#009900;"';} ?> >Computers</a>
			<span id="pipe">|</span>
			<a href="licenses.php" <?php if($currentPage == "licenses"){echo 'style="color:#009900;"';} ?> >Licenses</a>
			<span id="pipe">|</span>
			<a href="dns.php" <?php if($currentPage == "dns"){echo 'style="color:#009900;"';} ?> >IP &amp; DNS</a>
			<span id="pipe">|</span>
			<a href="purchases.php" <?php if($currentPage == "purchases"){echo 'style="color:#009900;"';} ?> >Purchases</a>
			</div>
	</div>
</div>
<div style="width:1000px;float:left;margin-bottom:20px;"><hr style="width:990px;color:#5A85CF;"></div>