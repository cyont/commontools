<?php

	// call db include file
	include("inc_db.php");
	
	// select database
	mysql_select_db("deliverance", $DBlink)
		or die(mysql_error());

	// check domain for test server (elvis) and serve URLs to edit UI accordingly
	if (stristr($_SERVER['SERVER_NAME'], 'deliverance.test') === false) {
		$live = true;
		// point to the live sites; not in use right now...
		$unionURL = 'http://union.arizona.edu';
		$saURL = 'http://studentaffairs.arizona.edu';
		$crURL = 'http://campusrec.arizona.edu';
	} else {
		$live = false;
		// point to the dev site; not in use right now...
		// there is no difference between theese two pages other than display block IDs.
		// that is what separates/'tests' the union resources from the sa resources
		$unionURL = 'http://deliverance.test/union.php';
		$saURL = 'http://deliverance.test/sa.php';
	}

?>

<?php
// are we on the live version of Deliverance?
if ($live) {
	
	// QUERY: get all the pages that have been tracked in the page tracking db
	$result = mysql_query("SELECT * FROM pages ORDER BY domain ASC, path ASC");
		
?>

<p style="margin:2px 0 7px 0; padding:0; font-style:italic;">Live Page List</p>
<ol style="margin-right:20px; font-size:10px; line-height:16px;">
<!-- new LIVE pages are dynamically added here -->
<?php
	while ($row = mysql_fetch_array($result)) {
		
		// if this is on a domain that hasn't been displayed yet, highlight the domain and group pages under it
		if ($domain != $row['domain']) {
			echo '<span style="font-weight:bold; color:#000000; margin-left:-15px;">' . $row['domain'] . '</span><br />';
		}

		// if the next page is different proceed
		if ($url != $row['domain'] . $row['path']) {
			
			echo '<li><a href="' . $row['domain'] . $row['path'] . '" target="_blank" title="[Last hit: ' . date("m/d/Y g:i:s A", strtotime($row['date'])) . ']">' . $row['path'] . '</a></li>';
		
		}
		
		// assign the url so we can check for duplicate pages before adding to list
		$url = $row['domain'] . $row['path'];
		
		// assign the domain so we can check for duplicate domains before adding to list
		$domain = $row['domain'];

	} // END WHILE ($row = mysql_fetch_array($result))
?>
</ol>

<?php 
// if not, then show the test pages only
} else {
?>

<p style="margin:2px 0 2px 0; padding:0; font-style:italic;">Test Pages</p>
<ul style="margin-right:20px;">
<!-- add new TEST pages here -->
<li><a href="http://deliverance.test/union.php" target="_blank">Deliverance Test Page: Union-Specific Resources</a></li>
<li><a href="http://deliverance.test/sa.php" target="_blank">Deliverance Test Page: SA-Specific Resources</a></li>
<li><a href="http://deliverance.test/cr.php" target="_blank">Deliverance Test Page: Campus Rec-Specific Resources</a></li>
<li><a href="http://union.arizona.edu/dlvr_test.php" target="_blank">Union Live Test</a> (test file on LIVE server)</li>
<li><a href="http://studentaffairs.arizona.edu/dlvr_test.php" target="_blank">Student Affairs Live Test</a> (test file on LIVE server)</li>
</ul>

<?php
}
?>