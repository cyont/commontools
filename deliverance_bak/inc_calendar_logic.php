<?php
## CALENDAR LOGIC STARTS HERE
// get today's date in format compatible w/date stored in db
// $today = date('Y-m-d',time());

// get today's day of the week
$dayName = date('l', time());

// put today into a temp var so we don't have to change the master date
$todayTmp = $today;

## conditionals for add/subtracting one week at a time
// add one week
if ($_GET['addWeek']) {
	
	$_SESSION['addDays'] = ($_SESSION['addDays'] - $_SESSION['subtractDays']) + 7;
	unset($_SESSION['subtractDays']);
	$weekChange = strtotime('+' . $_SESSION['addDays'] . ' days', strtotime($today));
	$thisWeek = date('Y-m-d', $weekChange);
	$todayTmp = $thisWeek;

// subtract one week
} else if ($_GET['subtractWeek']) {
	
	$_SESSION['subtractDays'] = $_SESSION['subtractDays'] - $_SESSION['addDays'] + 7;
	unset($_SESSION['addDays']);
	$weekChange = strtotime('-' . $_SESSION['subtractDays'] . ' days', strtotime($today));
	$thisWeek = date('Y-m-d', $weekChange);
	$todayTmp = $thisWeek;
	
// clear the session vars
} else {
	unset($_SESSION['addDays']);
	unset($_SESSION['subtractDays']);
}

// if the week hasn't been changed or if it has and we're in the current week
if (!$weekChange || $thisWeek == $today) {
	
	## figure out which day it is and...
	// a) flag the current day for highlighting in the calendar
	// b) define an offset using Sunday as the first day of week
	switch ($dayName) {
	
		case 'Sunday':
		$sunday = true;
		$_SESSION['offset'] = 0;
		break;

		case 'Monday':
		$monday = true;
		$_SESSION['offset'] = 1;
		break;

		case 'Tuesday':
		$tuesday = true;
		$_SESSION['offset'] = 2;
		break;

		case 'Wednesday':
		$wednesday = true;
		$_SESSION['offset'] = 3;
		break;

		case 'Thursday':
		$thursday = true;
		$_SESSION['offset'] = 4;
		break;

		case 'Friday':
		$friday = true;
		$_SESSION['offset'] = 5;
		break;

		case 'Saturday':
		$saturday = true;
		$_SESSION['offset'] = 6;
		break;

	} // END Switch
	
} // END IF

// init the counter
$count = 0;

// to get through a full week
while ($count < 7) {
	
	// put the date into an array
	$date[$count] = date('Y-m-d', strtotime('-' . $_SESSION['offset'] . ' day', strtotime($todayTmp)));
	
	// add one day
	$todayTmp = strtotime('+1 day', strtotime($todayTmp));
	
	// format date for consistency with rest of app
	$todayTmp = date('Y-m-d',$todayTmp);
	
	// increment the counter	
	$count++;

}
## CALENDAR LOGIC ENDS HERE
?>		
