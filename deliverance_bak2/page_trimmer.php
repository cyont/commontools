<?php

#### CODE TO REMOVE OLD PAGES FROM THE PAGE TRACKING ####
// currently configured for 180 day cutoff, see below
#########################################################

## BEGIN: calculate all the timing
// define number of seconds in a day
$day = 86400;

// set the age limit in days
$age = 180;

// calculate the oldest date we'll accept in unix time
$range = time() - ($day * $age);

// convert the oldest acceptable date to a MySQL friendly timestamp
$cutoff = date("Y-m-d H:i:s", $range);
## END: calculate all the timing

// call db include file
include("inc_db.php");
	
// select database
mysql_select_db("deliverance", $DBlink)
	or die(mysql_error());


// QUERY: find pages older than 6 months
$result = mysql_query("SELECT * FROM pages WHERE date < '" . $cutoff . "'");
	
// set a counter so we can track the number of pages deleted
$i = 0;

while ($row = mysql_fetch_array($result)) {
	
	// execute the delete
	$query = mysql_query("DELETE FROM pages WHERE id = " . $row['id'] . "");

	// increment the count
	$i++;

} // END WHILE ($row = mysql_fetch_array($result))
	
// provide some basic feedback

echo $i . ' page(s) older than ' . $age . ' days removed from the Deliverance page tracking table.';

?>