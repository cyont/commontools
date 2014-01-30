<?php
/********************************************************************************
*
* This include allows for an application to manage the users
* allowed access based off of database driven user management.
* Errors should be numbered chronologically for debugging purposes.
*
* Applications which use this include should register any page which they include
* this code on. They should keep track of wether the user is valid or not in there
* own code across their pages.
*     This include provides 2 session variables which are flags:
*          $_SESSION['access_control']['allowed'] which is a boolean showing if
*               have access or not
*          $_SESSION['access_control']['admin'] which is a boolean showing if
*               they are registered as an admin of the access_control system.
*               Presently this should be all of our web developers staff, this
*               gives us the ability to masquerade as super users.
*
* author: Nicholas Bischof [nbischof@email.arizona.edu]
*
********************************************************************************/
session_start();
// This will unset wether the user can access the current app so there is
// left showing access allowed from the past
$_SESSION['access_control']['allowed'] = FALSE;
$_SESSION['access_control']['admin'] = FALSE;
$cur_path = $_SERVER["SCRIPT_NAME"];
$cur_domain = $_SERVER['HTTP_HOST'];
// If the current path has no page or file specified assume index.php
if (strripos($cur_path,'?')) {
  $cur_path = substr($cur_path,0,strripos($cur_path,'?'));
}
if (substr($cur_domain,0,4) == "www.") {
  $cur_domain = substr($cur_domain,4);
}
if (substr($cur_path,0,16)=="/marketing/wiki/") {
  $cur_path = "/marketing/wiki/";
}
if (substr($cur_path,-1)=="/") {
	$cur_path .= "index.php";
}
else if (substr($cur_path,-4)!=".php") {
	// If there is currently no extension on the given path assume .php
	$cur_path .= ".php";
}
// This will show the domain for our individual test domains
if (substr($cur_domain,-3)==".su"||substr($cur_domain,-3)==".sa"||substr($cur_domain,-3)==".sb") {
	$cur_domain = substr($cur_domain,-2);
}
else if (substr($cur_domain,-4)==".rec") {
        $cur_domain = substr($cur_domain,-3);
}
else if ($cur_domain=="sutest.arizona.edu" || $cur_domain=="satest.arizona.edu") {
	$cur_domain = substr($cur_domain,0,2);
}
else if ($cur_domain=="rctest.arizona.edu") {
        $cur_domain = "rec";
}
else {
  // If not it must be one of the live domains
  if ($cur_domain == "union.arizona.edu" || $cur_domain == "sutest.sunion.arizona.edu" || $cur_domain == "elvis.sunion.arizona.edu:8089") {
	  $cur_domain = "su";
  }
  else if ($cur_domain == "studentaffairs.arizona.edu" || $cur_domain == "satest.sunion.arizona.edu") {
	  $cur_domain = "sa";
  }
  else if ($cur_domain == "offcampus.arizona.edu") {
	  $cur_domain = "och";
  }
  else if ($cur_domain == "catwalk.arizona.edu") {
	  $cur_domain = "cw";
  }
  else if ($cur_domain == "campusrec.arizona.edu") {
          $cur_domain = "rec";
  }
}
// If the information from webauth is not already present present the user with the webauth include
include('/Library/WebServer/commontools/webauth/include.php');
// Pull there netID into this variable
$user = $_SESSION['webauth']['netID'];
// This is the mysql link include
require('/Library/WebServer/commontools/mysql_link.inc');
// If the include did not establish the link properly, display error 1 and stop exec.
if (!$DBlink) {
	die("<div>[error 1]&nbsp;".mysql_error()."</div>");
}
// Select the appropriate database with the link from the include
$DBselected = mysql_select_db("access_control", $DBlink);
// Make sure the database was properly selected
if (!$DBselected) {
	die("<div>[error 2]&nbsp;".mysql_error()."</div>");
}
// Query for the list of applications with this domain to make sure a valid domain was found
$query = 'SELECT * FROM domain WHERE abbreviation="'.$cur_domain.'";';
$result = mysql_query($query, $DBlink);
// Make sure the query actually came back properly
if (!$result) {
	die("<div>[error 3]&nbsp;".mysql_error()."</div>");
}
else {
	// So if no rows came back throw an unknown domain exception
	if(!mysql_fetch_assoc($result)) {
		die("<div>[error 4]&nbsp;Uknown Domain Exception for the domain $cur_domain</div>");
	}
}
// grab the information matched to the user logged in through webauth
$query = 'SELECT * FROM user WHERE netid="'.$user.'";';
// Make sure the query was successful
$result = mysql_query($query, $DBlink);
if (!$result) {
	die("<div>[error 5]&nbsp;".mysql_error()."</div>");
}
else {
	// At this point we should now have the users information for access control
	$user_info = mysql_fetch_assoc($result);
	// If they are an admin we allow them access without checking their permission to the app
	if ($user_info['admin']) {
		// Verify that the current app is registered before granting any access
		$query = 'SELECT id FROM application WHERE url="'.$cur_path.'" AND domain="'.$cur_domain.'";';
		$result = mysql_query($query, $DBlink);
		// Make sure the query was successful
		if (!$result) {
			die("<div>[error 6]&nbsp;".mysql_error()."</div>");
		}
		else {
			// veryify that the app is registered and if so grant this admin user there access
			$appID = mysql_fetch_assoc($result);
			if (!$appID) {
				die("<div>[error 7]&nbsp;Uknown Application Exception</div><div>Please add the following to the application tables.<br />Path: $cur_path <br />Domain: $cur_domain </div>");
			}
			// Set the session variable flag to show they are allowed access at the 
			// time and location of this include being called
			$_SESSION['access_control']['allowed'] = TRUE;
			// Set the session variable flag that shows this user is an admin
			// (some apps may find this useful)
			$_SESSION['access_control']['admin'] = TRUE;
		}
	}
	else {
		// If at this point in branch user was not an admin
		// First check to see if this app is registered
		$query = 'SELECT id FROM application WHERE url="'.$cur_path.'" AND domain="'.$cur_domain.'";';
		$result = mysql_query($query, $DBlink);
		// Make sure the query was successful
		if (!$result) {
			die("<div>[error 8]&nbsp;".mysql_error()."</div>");
		}
		else {
			// If the app is there store the appID
			$appID = mysql_fetch_assoc($result);
			// If not assume not registered in the DB and give error
			if (!$appID) {
				die("<div>[error 9]&nbsp;Uknown Application Exception</div>");
			}
			// Look to see if this user has permission to use this app
			$query = 'SELECT id FROM permissions WHERE appID="'.$appID['id'].'" AND userID="'.$user_info['id'].'";';
			$result = mysql_query($query, $DBlink);
			// Make sure the query was successful
			if (!$result) {
				die("<div>[error 10]&nbsp;".mysql_error()."</div>");
			}
			else {
				// Actually grab the row pertaining to the permission entry
				$permission = mysql_fetch_assoc($result);
				// If the permission variable is greater than zero they have a row
				// which has an id greater than zero, this shows they have permission
				if ($permission>0 && $permission!=FALSE) {
					// Set the session variable flag to show they are allowed access at the 
					// time and location of this include being called
					$_SESSION['access_control']['allowed'] = TRUE;
					// Set the session variable flag that shows this user is NOT an admin
					// (some apps may find this useful)
					$_SESSION['access_control']['admin'] = FALSE;
				}
				else {
					// Set the session variable flag to show they are NOT allowed access at the 
					// time and location of this include being called
					$_SESSION['access_control']['allowed'] = FALSE;
					// Set the session variable flag that shows this user is NOT an admin
					// (some apps may find this useful)
					$_SESSION['access_control']['admin'] = FALSE;
				}
			}
		}
	}
}
// Close the connection so the DBlink is not available to where this is included
mysql_close($DBlink);
?>
