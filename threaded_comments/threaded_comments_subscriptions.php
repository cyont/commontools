<?php

//connect to database
include("/Library/WebServer/commontools/mysql_link.inc");

//select database
mysql_select_db("threaded_comments", $DBlink)
		or die(mysql_error());

//deletes a subscriber
//the unsubscribe variable is set when a user clicks the unsibscribe link in an email
if(isset($_GET['unsubscribe'])){
	if($_GET['unsubscribe'] == 'true') {
		$id = $_GET['id'];
		$sql = "DELETE FROM comment_subscriptions WHERE id=" . $id . "";
		
		if (!mysql_query($sql,$DBlink)) {
			die('Error: ' . mysql_error());
		} 
		echo "You have been unsubscribed.";
	} else {
		echo "There was an error unsubscribing you from the list. Please try again.";
	}	
} 


$temp = mysql_query("SELECT * FROM comment_pages WHERE page_id='" . $page_id . "'");
$row = mysql_fetch_array($temp);
$url = $row['page'];
$sql = mysql_query("SELECT * FROM comment_subscriptions WHERE page_id='" . $page_id . "'");

//loops through all email addresses subscribed to a certain thread and sends them an email notifying
//them of a new comment
while($row = mysql_fetch_array($sql)) {
	
	$id = $row['id'];
	$email = $row['email'];
	$name = $row['name'];
	if(!isset($subject)) {
		$subject = "A new comment has been posted";
	}
	if(!isset($message_body)) {
		$message_body = "To view the comment please click the following link:";
	}
	$message = 
	"Hello " . $name . ",

$message_body 

" . $_SESSION['site'] . $url . "
	
If you wish to unsubscribe from these notifications, click the following link:

";

 //checks to see if there are variables in the url
 if(substr_count($url, '?') != 0){
	 //if there are variables, add unsubscribe variabes to existing variables
	 $message = $message . $_SESSION['site'] . $url . "&unsubscribe=true&id=" . $id;
 } else {
	 //if there are not variables, add variables to end of url
	 $message = $message . $_SESSION['site'] . $url . "?unsubscribe=true&id=" . $id;
 }

mail($email,$subject,$message,"From: The University of Arizona <noreply@email.arizona.edu>");
	
//echo "mail sent";
	
}

//inserts a new email address into the subscriber table
if(isset($_POST['allComments'])){
	
	$result = mysql_query("SELECT email, page_id FROM comment_subscriptions");
	while($row = mysql_fetch_array($result)){

// kmb [12/1/09]: added new variables to track email before it gets unset up above so that new/duplicate subscriptions are logged correctly
// if(($row['email'] == $email) && ($row['page_id'] == $page_id)){
		if(($row['email'] == $newEmail) && ($row['page_id'] == $page_id)){

			$duplicate = true;
			
		} 
	}
	
	//if the email address is already subscribed, tell the user, otherwise add subscription
	if($duplicate){
		echo "<div style=\"color:red;\">You have already subscribed to this list of comments.</div>";
	} else {

// kmb [12/1/09]: added new variables to track email before it gets unset up above so that new/duplicate subscriptions are logged correctly
/* 		$sql = "INSERT INTO comment_subscriptions (email, page_id, name)
					VALUES ('$email','$page_id','$name')";
*/

		$sql = "INSERT INTO comment_subscriptions (email, page_id, name)
					VALUES ('$newEmail','$page_id','$newName')";
				
		if (!mysql_query($sql,$DBlink)) {
			die('Error: ' . mysql_error());
		} 
	}
} 

?>