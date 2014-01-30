<?php

//connect to database
include("/Library/WebServer/commontools/mysql_link.inc");


//select database 
mysql_select_db("threaded_comments", $DBlink)
		or die(mysql_error());
		
//go into this if statement if the delete link was pressed. 
if(isset($_GET['delete_id'])){
	$id = $_GET['delete_id'];
	$sql = "DELETE FROM comments WHERE page_id=" . $id . "";
			
	if (!mysql_query($sql,$DBlink)) {
		die('Error: ' . mysql_error());
	} 
	
	$sql = "DELETE FROM comment_subscriptions WHERE page_id=" . $id . "";
			
	if (!mysql_query($sql,$DBlink)) {
		die('Error: ' . mysql_error());
	} 
	
	$sql = "DELETE FROM comment_pages WHERE page_id=" . $id . "";
			
	if (!mysql_query($sql,$DBlink)) {
		die('Error: ' . mysql_error());
	} 
}
		
echo "<h2>Threaded Comments Backend</h2>";

?>
<table cellpadding="3" cellspacing="0">
	<tr bgcolor="#999999">
    	<th>Page ID</th>
        <th>Page</th>
        <th># of Comments</th>
        <th># of Subscribers</th>
        <th>Action</th>
    </tr>
<?php

$result = mysql_query("SELECT * FROM comment_pages ORDER BY page_id");

while($row = mysql_fetch_array($result)){
	$comment_count = 0;
	$subscriber_count = 0;
	echo "<tr>";
	echo "<td align=\"center\">" . $row['page_id'] . "</td>";
	echo "<td><a href=" . $row['site'] . $row['page'] . ">" . $row['site'] . $row['page'] . "</td>";
	
	//counts the number of comments for a given page
	$sql = mysql_query("SELECT comment_id FROM comments WHERE page_id = " . $row['page_id'] . "");
	while($temp = mysql_fetch_array($sql)){
		$comment_count++;
	}
	
	echo "<td align=\"center\">$comment_count</td>";
	
	//counts the number of subscribers for a given page
	$sql = mysql_query("SELECT email FROM comment_subscriptions WHERE page_id = " . $row['page_id'] . "");
	while($temp = mysql_fetch_array($sql)){
		$subscriber_count++;
	}
	echo "<td align=\"center\">$subscriber_count</td>";
	echo "<td><a href=\"threaded_comments_backend.php?delete_id=" . $row['page_id'] . "\">Delete</a></td>";
	echo "</tr>";
}
	
?>		
</table>	

		



