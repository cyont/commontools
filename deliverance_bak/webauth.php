<?php

## check for valid netID to gain access for editorial control
$users = array('kmbeyer', 'sanorris', 'jmasson', 'nbischof', 'mburton2', 'micheleh', 'jndewitt', 'rjsteed', 'malfred', 'bphinney');

## additional checks for appropriate access level
// adds ability to view the main ADMIN page and run the processor script
if ($page == 'admin') {
	$users = array('kmbeyer', 'sanorris', 'jmasson', 'nbischof', 'bphinney');
}

// check against the array of users
if (in_array($_SESSION['webauth']['netID'], $users)) {
	$grantAccess = true;
} else {
	$grantAccess = false;
}

?>
