<?php

// authenticate with WebAuth
$webauth_splash = '';
require_once('/Library/WebServer/commontools/webauth/include.php');

// CHECK FOR VALID ACCESS
// include list of authorized users
include("webauth.php");

if (!$grantAccess) {

	echo "<p>You are not on the list of authorized users. Please contact <a href=\"mailto:kmbeyer@email.arizona.edu\">kmbeyer@email.arizona.edu</a> if you need access or have questions.</p>";
	echo '<p><a href="https://webauth.arizona.edu/webauth/logout?logout_href=http://' . $_SERVER['SERVER_NAME'] . '/&logout_text=Return%20to%20Home%20Page">Logout of UA NetID WebAuth</a></p>';
	echo '</div>';
	$_SESSION['allowEdits'] = false;
	session_destroy();
	
} else {

	// include the CMS settings file
	include("settings.php");

	// set session to allow access to admin pages. used to check access on admin pages, NOT on the display pages
	$_SESSION['allowEdits'] = true;
	
	// set cookie to allow for cross-domain access to 'edit' mode on display pages
	$value = 'allowEdits';
	
	## assign cookies for arizona.edu and deliverance.test domains	
	// assign cookie name, value, expiration time in seconds, tld and 0 for no secure connection required
	setcookie('Deliverance', $value, time()+$cookieTime, '/', '.arizona.edu', 0);
	setcookie('Deliverance_test', $value, time()+$cookieTime, '/', '.deliverance.test', 0);

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Deliverance ~ Student Affairs ~ The University of Arizona</title>
<link href="css/admin.css" rel="stylesheet" type="text/css" />

<!--begin movie pop-up window script-->
<script language="javascript">
<!-- winBRopen BEGIN
function winBRopen(theURL, Name, popW, popH, scroll, resize) { // V 1.0
var winleft = (screen.width - popW) / 2;
var winUp = (screen.height - popH) / 2;
winProp = 'width='+popW+',height='+popH+',left='+winleft+',top='+winUp+',scrollbars='+scroll+',resizable=0'
Win = window.open(theURL, Name, winProp)
if (parseInt(navigator.appVersion) >= 4) { Win.window.focus(); }
}
// winBRopen END -->
</script>

</head>

<body class="adminUI">
<div id="container">
  <div id="mainContent">
    <h1>Deliverance</h1>
	<?
	if (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false) {
		$testSite = false;
	} else {
		$testSite = true;
	}
	?>
    <p>This tool will allow Student Affairs Marketing editors and designers access to content scheduling and editing on sites that we manage.<br />
	[<a href="https://webauth.arizona.edu/webauth/logout?logout_href=http://<?php echo $_SERVER['SERVER_NAME'] ?>/commontools/deliverance/logout.php&logout_text=Return%20to%20Deliverance">Logout of UA NetID WebAuth</a>] [<a href="javascript:;" onClick="winBRopen('help.php','HELP','500','650','no')" target="_blank">HELP</a>]</p>
	<h2>Editor View<? if ($testSite) { echo ': TEST SITE'; } ?></h2>
	<div style="float:left;">
    <h3>Please choose from the following links:</h3>
    
    <?
    // check for group-specific access
	if ($campusRec) {
    ?>
	
	<!-- show campus rec menu -->
	<ol style="float:left;">
		<li><a href="upload_resource.php">Upload a resource</a></li>
		<li><a href="active_display_blocks.php">View active display blocks</a></li>
		<li><a href="javascript:;" onClick="winBRopen('processor_editor.php','Updates','500','200','no')">Run updates</a><span style="font-size:10px;"> (For "Editors" only. Admins, use the scripts in the <a href="admin.php">Administrator</a> view.)</span></li>
	</ol>

    <?
	// no group-specific access required    	
    } else {
	?>
	
	<ol style="float:left;">
		<li><a href="upload_resource.php">Upload a resource</a></li>
		<li><a href="display_block_orphans.php" target="_blank">View display blocks w/o default resources assigned</a></li>
		<li><a href="active_display_blocks.php">View active display blocks</a></li>
		<li><a href="javascript:;" onClick="winBRopen('processor_editor.php','Updates','500','200','no')">Run updates</a><span style="font-size:10px;"> (For "Editors" only. Admins, use the scripts in the <a href="admin.php">Administrator</a> view.)</span></li>
		<li><a href="admin.php">Switch to "Administrator" view</a></li>
	</ol>
	
	<?
	} // END if ($campusRec)
	?>
	
	</div>
	
	<div style="float:right;">
	<h3>Display Pages</h3>
	<?php
		// include list of pages
		include("inc_pagelist.php");
	?>
	</div>
	
	<br class="clear" />
	
	<!-- end #mainContent --></div>
<!-- end #container --></div>
</body>
</html>

<?php

} // end IF grantaccess

?>