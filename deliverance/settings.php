<?php

#################################
# SETTINGS FILE FOR DELIVERANCE #
#################################

######################
## GENERAL SETTINGS ##
######################

	#########################
	## MAXIMUM UPLOAD SIZE ##
	#########################
	// for uploading a new resource AND replacing an existing resource
	// INSTRUCTIONS
	// to edit max file upload size, only the number assigned to the $uploadMaxFile var needs changing
	// use the example formula below to calculate the number (based on megabytes)
	// the plain text is automatically calculated as long as megabytes are used as the unit
	
	// file size (converted to bytes) [for example: 1MB = 1 * 1024 * 1024 = 1048576]
	$uploadMaxFile = 1048576;
	
	// plain text for error notifications and form text etc.
	$uploadMaxText = $uploadMaxFile / 1024 / 1024 . 'MB'; // change this formula and label ONLY if using a different unit
	## END ##
	#########
	
	#############################################
	## MAXIMUM NUMBER OF PROCESSOR SCRIPT RUNS ##
	#############################################
	// FOR EDITOR ACCESS LEVEL
	// starting out by giving editors two updates per day
	$maxUpdates = 2;
	## END ##
	#########
	
	#######################
	## COOKIE EXPIRATION ##
	#######################
	// set in seconds (3600 seconds per hour)
	$cookieTime = 3600;
	## END ##
	#########
	
	############################
	## RESOURCE HISTORY RANGE ##
	############################
	// set resource age (days passed since today) to control length of pulldown on displayblock editor pages

	/////////////
	## random ##
	$resourceRangeRandom = date('Y-m-d',strtotime('-30 days',strtotime($today))) ;

	////////////////
	## sequential ##
	$resourceRangeSequential = date('Y-m-d',strtotime('-90 days',strtotime($today))) ;

	////////////
	## static ##
	$resourceRangeStatic = date('Y-m-d',strtotime('-90 days',strtotime($today))) ;

	## END ##
	#########

## END GENERAL SETTINGS ##
##########################

## SETTINGS FOR UPLOADING RESOURCES
// Check to see if we're on the upload page
if ($page == 'upload') {
	
	// check domain for test server (deliverance.test) and adjust paths accordingly
	if (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false) {
		// upload to appropriate live site
		// set path info for new resources
		switch ($_POST['site']) {
			case 'union':
			$_SESSION['uploadPath'] = '/Library/WebServer/uploads/Dropbox/uploads/union/deliverance_resources/'; // the full upload path for php
			$virtualPath = '/uploads/deliverance_resources/'; // the site-specific/virtual path to save in the db
			// delete line below if no problems 10/25/10
			// $browserURL = 'http://union.arizona.edu/deliverance_resources/'; // the path for http viewing -> change this when live
			$uploadLink = 'http://union.arizona.edu/uploads/deliverance_resources/'; // the path for http viewing of the immediately uploaded file when the form displays an error with some field other than the file upload
			break;
	
			case 'affairs':
			$_SESSION['uploadPath'] = '/Library/WebServer/uploads/Dropbox/uploads/studentaffairs/deliverance_resources/'; // the full upload path for php
			$virtualPath = '/uploads/deliverance_resources/'; // the site-specific/virtual path to save in the db
			// delete line below if no problems 10/25/10
			// $browserURL = 'http://studentaffairs.arizona.edu/deliverance_resources/'; // the path for http viewing -> change this when live
			$uploadLink = 'http://studentaffairs.arizona.edu/uploads/deliverance_resources/'; // the path for http viewing of the immediately uploaded file when the form displays an error with some field other than the file upload
			break;
		
			case 'campusrec':
			$_SESSION['uploadPath'] = '/Library/WebServer/uploads/Dropbox/uploads/campusrec/deliverance_resources/'; // the full upload path for php
			$virtualPath = '/uploads/deliverance_resources/'; // the site-specific/virtual path to save in the db
			// delete line below if no problems 10/25/10
			// $browserURL = 'http://campusrec.arizona.edu/deliverance_resources/'; // the path for http viewing -> change this when live
			$uploadLink = 'http://campusrec.arizona.edu/uploads/deliverance_resources/'; // the path for http viewing of the immediately uploaded file when the form displays an error with some field other than the file upload
			break;
		
		}		
		
	} else {
	
		// upload to test site
		$_SESSION['uploadPath'] = '/Library/WebServer/svnstaging/deliverance.test/uploads/deliverance_resources/'; // the full upload path for php
		$virtualPath = '/uploads/deliverance_resources/'; // the site-specific/virtual path to save in the db
		// delete line below if no problems 10/25/10
		// $browserURL = 'http://deliverance.test/deliverance_resources/'; // the path for http viewing -> change this when live
		$uploadLink = 'http://deliverance.test/uploads/deliverance_resources/'; // the path for http viewing of the immediately uploaded file when the form displays an error with some field other than the file upload
	
	}// END IF (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false)
} // END IF ($page == 'upload')

## SETTINGS FOR EDITING OR REPLACING A RESOURCE
// Check to see if we're on the edit or replace resource pages
if ($page == 'edit' || $page == 'replace') {

	// check domain for test server (deliverance.test) and adjust paths accordingly
	if (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false) {
		// upload to appropriate live site
		// set path info for replaced resources
		switch ($resource['site']) {
			case 'union':
			$_SESSION['uploadPath'] = '/Library/WebServer/uploads/Dropbox/uploads/union/deliverance_resources/'; // the full upload path for php
			$_SESSION['deletePath'] = '/Library/WebServer/uploads/Dropbox/uploads/union/deliverance_resources/'; // the partial delete path for php; remaining path info stored in db
			$virtualPath = '/uploads/deliverance_resources/'; // the site-specific/virtual path to save in the db
			$browserURL = 'http://union.arizona.edu'; // the path for http viewing of the files already uploaded
			$uploadLink = 'http://union.arizona.edu/uploads/deliverance_resources/'; // the path for http viewing of the immediately uploaded file when the form displays an error with some field other than the file upload
			break;
	
			case 'affairs':
			$_SESSION['uploadPath'] = '/Library/WebServer/uploads/Dropbox/uploads/studentaffairs/deliverance_resources/'; // the full upload path for php
			$_SESSION['deletePath'] = '/Library/WebServer/uploads/Dropbox/uploads/studentaffairs/deliverance_resources/'; // the partial delete path for php; remaining path info stored in db
			$virtualPath = '/uploads/deliverance_resources/'; // the site-specific/virtual path to save in the db
			$browserURL = 'http://studentaffairs.arizona.edu'; // the path for http viewing of the files already uploaded
			$uploadLink = 'http://studentaffairs.arizona.edu/uploads/deliverance_resources/'; // the path for http viewing of the immediately uploaded file when the form displays an error with some field other than the file upload
			break;
		
			case 'campusrec':
			$_SESSION['uploadPath'] = '/Library/WebServer/uploads/Dropbox/uploads/campusrec/deliverance_resources/'; // the full upload path for php
			$_SESSION['deletePath'] = '/Library/WebServer/uploads/Dropbox/uploads/campusrec/deliverance_resources/'; // the partial delete path for php; remaining path info stored in db
			$virtualPath = '/uploads/deliverance_resources/'; // the site-specific/virtual path to save in the db
			$browserURL = 'http://campusrec.arizona.edu'; // the path for http viewing of the files already uploaded
			$uploadLink = 'http://campusrec.arizona.edu/uploads/deliverance_resources/'; // the path for http viewing of the immediately uploaded file when the form displays an error with some field other than the file upload
			break;
		
		}
	
	} else {

		$_SESSION['uploadPath'] = '/Library/WebServer/svnstaging/deliverance.test/uploads/deliverance_resources/'; // the full upload path for php
		$_SESSION['deletePath'] = '/Library/WebServer/svnstaging/deliverance.test/uploads/deliverance_resources/'; // the partial delete path for php; remaining path info stored in db
		$virtualPath = '/uploads/deliverance_resources/'; // the site-specific/virtual path to save in the db
		$browserURL = 'http://deliverance.test'; // the path for http viewing of the files already uploaded
		$uploadLink = 'http://deliverance.test/uploads/deliverance_resources/'; // the path for http viewing of the immediately uploaded file when the form displays an error with some field other than the file upload

	} // END IF (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false)
} // END IF ($page == 'replace')


## SETTINGS FOR CREATING A DISPLAY BLOCK
// no special settings here yet


## SETTINGS FOR EDITING A DISPLAY BLOCK
// Check to see if we're on any pages other than the edit, replace or upload pages
if (!$page) {

	// check domain for test server (deliverance.test) and adjust paths accordingly
	if (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false) {
		// browse resources from appropriate live site
	
		switch ($rowDisplay['site']) {
			case 'union':
			$browserURL = 'http://union.arizona.edu'; // domain to precede path
			break;
	
			case 'affairs':
			$browserURL = 'http://studentaffairs.arizona.edu'; // domain to precede path
			break;
		
			case 'campusrec':
			$browserURL = 'http://campusrec.arizona.edu'; // domain to precede path
			break;
		} // END SWITCH
	
	} else {
	
		$browserURL = 'http://deliverance.test'; // the path for http viewing of the files already uploaded
	
	} // END IF (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false)
} // END IF (!$page)

// can probably delete this, but let's wait and see once it's centralized...
/*
switch ($rowResource['site']) {
	case 'union':
	$browserURL = 'http://sutest.arizona.edu'; // domain to precede path -> change this when live
	break;
	
	case 'affairs':
	$browserURL = 'http://satest.arizona.edu'; // domain to precede path -> change this when live
	break;
}
*/

?>