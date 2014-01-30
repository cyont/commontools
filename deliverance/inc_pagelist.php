<!-- include a local copy of minified jquery -->
<script src="js/jquery.js"></script>

<?php

	// call db include file
	include("inc_db.php");
	
	// select database
	$dbdeliv = new db("deliverance");
	//mysql_select_db("deliverance", $DBlink)
	//	or die(mysql_error());

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

	// QUERY: run a second time for domain counting and JavaScript creation
	$resultCounter = mysql_query("SELECT * FROM pages ORDER BY domain ASC, path ASC");

	// count the comains and create the JavaScript
	while ($rowCounter = mysql_fetch_array($resultCounter)) {
		
		// if this is on a domain that hasn't been displayed yet, track it and create an instance of JavaScript for jQuery display functionality
		if ($domain != $rowCounter['domain']) {
			// pre-increment ...
			$domainCnt++;

			// create the JavaScript to handle the showing/hiding of divs
			echo '
			<script type="text/javascript">// <![CDATA[
			$(document).ready(function()
			{
				$(\'#hide_this' . $domainCnt . '\').hide();
				$("#clickhere' . $domainCnt . '").click(function()
				{
					$(\'#hide_this' . $domainCnt . '\').toggle(\'slow\');
				});
			});
			// ]]>
			</script>';
			
			// assign the domain so we can check for duplicate domains before adding more JavaScript
			$domain = $rowCounter['domain'];
			
		} // END IF ($domain != $rowCounter['domain'])

	} // END WHILE ($rowCounter = mysql_fetch_array($resultCounter))
		
?>

<p style="margin:2px 0 7px 0; padding:0; font-style:italic;">Live Page List (click to show/hide)</p>
<!--outer div--><!--479px max-->
<div style="margin-right:0px; font-size:10px; line-height:16px; width:400px;">
<!-- new LIVE pages are dynamically added here -->
<?php

	// reset the domain count to 1 so that IDs match up with JavaScript correctly
	$domainCnt = 1;	
	
	// open dummy DIV and OL since we start the loop with a close DIV and OL
	// this offsets the tag imbalance
	echo '<!--open first dummy div for domain loop--><div>';
	echo '<!--open first dummy ol for domain loop--><ol style="margin:0; padding:0;">';
		
	// loop through the results
	while ($row = mysql_fetch_array($result)) {
		
		// if this is on a domain that hasn't been displayed yet, highlight the domain and group pages under it
		if ($domain != $row['domain']) {
	
			// close the first dummy OL and DIV
			echo '</ol><!--close the first dummy OL--></div><!--/close first dummy DIV-->';

		    // check for group-specific access
			if ($campusRec) {
			
				if ($row['domain'] == 'http://campusrec.arizona.edu') {
					// show the domain name and apply the ID
					echo '<a id="clickhere' . $domainCnt . '" title="Click to show/hide page list." href="javascript:;"><span style="font-weight:bold; margin-left:0px;">' . $row['domain'] . '</span></a><br />';
				}

			// no group-specific access required    	
		    } else {
				// show the domain name and apply the ID
				echo '<a id="clickhere' . $domainCnt . '" title="Click to show/hide page list." href="javascript:;"><span style="font-weight:bold; margin-left:0px;">' . $row['domain'] . '</span></a><br />';
		    }
			
			// open the next DIV and OL before we exit the loop
			echo '<!--open next domain--><div id="hide_this' . $domainCnt . '"><!--open next OL--><ol style="margin-bottom:10px; padding-bottom:0;">';
			
			// increment the domain counter
			$domainCnt++;
		} // END IF ($domain != $row['domain'])

		// if the next page is different AND if it's not a 'get_deliv' pre-load page, proceed
		if ($url != $row['domain'] . $row['path'] && !stristr($row['path'], 'get_deliv')) {
			// show the path
			echo '<li><a href="' . $row['domain'] . $row['path'] . '" target="_blank" title="[Last hit: ' . date("m/d/Y g:i:s A", strtotime($row['date'])) . ']">' . $row['path'] . '</a></li>';
		
		} // END IF ($url != $row['domain'] . $row['path'] && !stristr($row['path'], 'get_deliv'))
		
		// assign the url so we can check for duplicate pages before adding to list
		$url = $row['domain'] . $row['path'];
		
		// assign the domain so we can check for duplicate domains before adding to list
		$domain = $row['domain'];
		
	} // END WHILE ($row = mysql_fetch_array($result))
?>
	</ol><!--clode the final domain OL-->
	</div><!--close final domain DIV-->
</div><!--/outer div-->

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