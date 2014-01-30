<?php

## check for valid netID to gain access for editorial control
$users = array('kmbeyer', 'sanorris', 'micheleh', 'malfred', 'jennywendt', 'bphinney', 'jlowry13', 'samwang', 'andreab3', 'jedc', 'watsont', 'mgolab13', 'alhall', 'ababis', 'khucko', 'niegocki', 'jtoddmillay', 'twicker', 'ilee');

## additional checks for appropriate access level
// adds ability to view the main ADMIN page and run the processor script
if ($page == 'admin') {
	$users = array('kmbeyer', 'sanorris', 'bphinney', 'ababis');
}

// check against the array of users
if (in_array($_SESSION['webauth']['netID'], $users)) {
	$grantAccess = true;
} else {
	$grantAccess = false;
}

## check for group-specific levels of access
// Campus Rec user group
$cr = array('watsont', 'mgolab13', 'alhall'); // add NetIDs for Campus Rec users here

// Check against Campus Rec group
if (in_array($_SESSION['webauth']['netID'], $cr)) {
	$campusRec = true;
}

?>