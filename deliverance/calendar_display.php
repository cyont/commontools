<?
session_start();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<body>

<?php

// get today's date in format compatible w/date stored in db
$today = date('Y-m-d',time());

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
	
	// figure out which day it is and...
	// a) flag the day for highlighting in the calendar as 'today'
	// b) set the offset based on Sunday as first day of week
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

?>

<br />&nbsp;<br />

[<? if ($sunday) { echo '<span style="color:#ff0000">'; } else { echo '<span>'; } ?>sunday (<? echo $date[0]; ?>)</span>] - 

[<? if ($monday) { echo '<span style="color:#ff0000">'; } else { echo '<span>'; } ?>monday (<? echo $date[1]; ?>)</span>] - 

[<? if ($tuesday) { echo '<span style="color:#ff0000">'; } else { echo '<span>'; } ?>tuesday (<? echo $date[2]; ?>)</span>] - 

[<? if ($wednesday) { echo '<span style="color:#ff0000">'; } else { echo '<span>'; } ?>wednesday (<? echo $date[3]; ?>)</span>] - 

[<? if ($thursday) { echo '<span style="color:#ff0000">'; } else { echo '<span>'; } ?>thursday (<? echo $date[4]; ?>)</span>] - 

[<? if ($friday) { echo '<span style="color:#ff0000">'; } else { echo '<span>'; } ?>friday (<? echo $date[5]; ?>)</span>] - 

[<? if ($saturday) { echo '<span style="color:#ff0000">'; } else { echo '<span>'; } ?>saturday (<? echo $date[6]; ?>)</span>]

<br />&nbsp;<br />
<div><a href="calendar_display.php?subtractWeek=true">Previous week</a> | <a href="calendar_display.php">Current week</a> | <a href="calendar_display.php?addWeek=true">Next week</a></div>

</body>
</html>