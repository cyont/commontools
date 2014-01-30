<?php
/*
Threaded Comments Script
Written by David Glassanos
dglassan@email.arizona.edu
Fall 2009

This script allows someone to include this file in the body of a web page and it will add a threaded comments system
to the page. The script will add the page into the database automatically if it is not already in there. The script
also allows for commenters to subscribe to a thread and recieve email updates when a new comment has been submitted. 
Anyone who has subscribed to a thread can unsubscribe at any time by clicking the unsubscribe link at the end of the
email that is sent out. 

-------------------------------------------------------------------------------------------------------------------
OPTIONS: 
		 
$width - the width in pixels that you would like to display the comments on the page

$inputSize - The width in pixels that you would like the input fields form Name and Email to be

$textAreaCols - The number of columns you would like the comment textarea to have

$textAreaRows - The number of rows you would like the comment textarea to have

$subject - If you would like to have a custom subject in your emails sent out to subscribers, set the $subject variable with a string
			The default subject line is "A new comment has been posted"
			
$message_body - If you would like to have a custom message in your emails sent out to subscribers, set the $message_body variable with a string
			The default message_body line is "To view the comment please click the following link:"
			
			The full email will look like this:
			
			From: 		noreply@email.arizona.edu
			Subject: 	A new comment has been posted   <------ $subject variable
			Date:		01/01/2009
			To:			<subscriber email>

			
			Hello <subscriber name>,

			To view the comment please click the following link:   <-------- $message_body variable

			http://union.arizona.edu/sample_page.php 
	
			if you wish to unscubscribe from these comments, please click the following link:
	
			http://union.arizona.edu/sample_page.php?unsubscribe=true&id=20
			


-------------------------------------------------------------------------------------------------------------------
NOTE:
the session must be started before the include file. The comments will not work if the session has not been started. 

-------------------------------------------------------------------------------------------------------------------
DELETING COMMENTS:
in order to delete a page with comments, go to 

-------------------------------------------------------------------------------------------------------------------
EXAMPLE:

<?php
session_start();

//website code

$width = 600;
$inputSize = "50";
$textAreaCols = "45";
$textAreaRows = "10";
$subject = "This is a sample subjet";
$message_body = "This is a sample message body";

$_SESSION['site'] = "http://" . $_SERVER['SERVER_NAME'];
include("/Library/WebServer/commontools/threaded_comments.php");

?>


-------------------------------------------------------------------------------------------------------------------
*/

//connect to database
include("/Library/WebServer/commontools/mysql_link.inc");

//select database
mysql_select_db("threaded_comments", $DBlink)
	or die(mysql_error());

// Form data cleaning function
function cleanFormData($formData) {
	$formData = trim($formData); // removes leading and trailing spaces
	$formData = strip_tags($formData); // strips HTML style tags

		if(get_magic_quotes_gpc()){ // prevents duplicate backslashes if magic quotes is enabled in php install
			$formData = stripslashes($formData);
		}
		
	$formData = mysql_real_escape_string($formData);
	return $formData;

} // END FUNCTION

//go into this block of code if the comment is a top level comment
if(isset($_POST['submit_top_level']) && $_POST['comment'] != $_SESSION['comment']) {
	
	$name = cleanFormData($_POST['name']);
	$email = cleanFormData($_POST['email']);
	$comment = cleanFormData($_POST['comment']);
	//save comment in a session variable so that we can compare to post variables. 
	//prevents duplicate entries if the page is refreshed
	$_SESSION['comment'] = $_POST['comment'];
	$parent_id = 0; //top level comments do not have a parent
	$time = date("h:i:s A");
	$date = date("F j, Y");
	
	$query_string = $_SERVER['QUERY_STRING'];
	
	if($query_string == "") {
		$url = $_SERVER['PHP_SELF'];
	} elseif (substr($query_string, 0, 9) == "parent_id"){
		//if url contains ?parent_id= then we dont want to add that as a new page into the DB
		$url = $_SERVER['PHP_SELF'];
	} else {
		//PHP_SELF does not grab variables if there are variables being passed in the url, but QUERY_STRING does...so if there
		//are variables in the url, append them to PHP_SELF
		$url = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];
	}
	
	$sql = mysql_query("SELECT page_id FROM comment_pages WHERE page='" . $url . "'");
	$row = mysql_fetch_array($sql);
	$page_id = $row['page_id'];
	
	$sql = "INSERT INTO comments (parent_id, name, email, comment, time_of_post, date_of_post, page_id)
			VALUES ('$parent_id','$name','$email','$comment','$time','$date','$page_id')";
			
	if (!mysql_query($sql,$DBlink)) {
		die('Error: ' . mysql_error());
	} else {
		
		// kmb [12/1/09]: name and email vars were losing assigned value below and causing problems when logging new/duplicate subscribers
		$newEmail = $_POST['email'];
		$newName = $_POST['name'];

		unset($_POST['name']);
		unset($_POST['email']);
		unset($_POST['comment']);
		
	}
	
	//if the user wants to subscribe to a comment thread the include file will do that. 
	//It also sends out the emails when a new comment is posted. 
	include("/Library/WebServer/commontools/threaded_comments/threaded_comments_subscriptions.php");
	
}

//go into this block of code if the comment is a nested comment
// kmb [11/24/09]: changed the parent_id to a hidden POST var to avoid the error of having two GET vars in the URL on a nested comment
if(isset($_POST['parent_id']) && $_POST['comment'] != $_SESSION['comment']){
//if(isset($_GET['parent_id']) && $_POST['comment'] != $_SESSION['comment']){
	
	$name = cleanFormData($_POST['name']);
	$email = cleanFormData($_POST['email']);
	$comment = cleanFormData($_POST['comment']);
	//save comment in a session variable so that we can compare to post variables. 
	//prevents duplicate entries if the page is refreshed
	$_SESSION['comment'] = $_POST['comment']; 

//	kmb [11/24/09]: changed this to a hidden POST var to avoid the error of having two GET vars in the URL on a nested comment
	$parent_id = $_POST['parent_id']; //if nested comment, the page will be refreshed and the parent_id will be passed in the url
//	$parent_id = $_GET['parent_id']; //if nested comment, the page will be refreshed and the parent_id will be passed in the url

	$time = date("h:i:s A");
	$date = date("F j, Y");
	
	$query_string = $_SERVER['QUERY_STRING'];
	
	if($query_string == "") {
		$url = $_SERVER['PHP_SELF'];
	} elseif (substr($query_string, 0, 9) == "parent_id"){
		//if url contains ?parent_id= then we dont want to add that as a new page into the DB
		$url = $_SERVER['PHP_SELF'];
	} else {
		//PHP_SELF does not grab variables if there are variables being passed in the url, but QUERY_STRING does...so if there
		//are variables in the url, append them to PHP_SELF
		$url = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];
	}
	
	$sql = mysql_query("SELECT page_id FROM comment_pages WHERE page='" . $url . "'");
	$row = mysql_fetch_array($sql);
	$page_id = $row['page_id'];
	
	$sql = "INSERT INTO comments (parent_id, name, email, comment, time_of_post, date_of_post, page_id)
			VALUES ('$parent_id','$name','$email','$comment','$time','$date','$page_id')";
			
	if (!mysql_query($sql,$DBlink)) {
		die('Error: ' . mysql_error());
	} else {
		
		// kmb [12/1/09]: name and email vars were losing assigned value below and causing problems when logging new/duplicate subscribers
		$newEmail = $_POST['email'];
		$newName = $_POST['name'];

		unset($_POST['name']);
		unset($_POST['email']);
		unset($_POST['comment']);
		unset($_POST['parent_id']);
		
	}
	
	//if the user wants to subscribe to a comment thread the include file will do that. 
	//It also sends out the emails when a new comment is posted. 
	include("/Library/WebServer/commontools/threaded_comments/threaded_comments_subscriptions.php");
	
}

$query_string = $_SERVER['QUERY_STRING'];
	
if($query_string == "") {
	$url = $_SERVER['PHP_SELF'];
} elseif (substr($query_string, 0, 9) == "parent_id"){
	//if url contains ?parent_id= then we dont want to add that as a new page into the DB
	$url = $_SERVER['PHP_SELF'];
} else {
	//PHP_SELF does not grab variables if there are variables being passed in the url, but QUERY_STRING does...so if there
	//are variables in the url, append them to PHP_SELF
	$url = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];
}


//calling the display_comments() function. start with comments that have a parent_id of zero 
//indentation of zero

echo "<div style=\"width:" . $width . "px;\">";
display_comments(0,0);
echo "</div>";


?>
<div id="comment-form">
<form name="top_level_comment" method="POST" onsubmit="return validate_form(this);" action="<?php echo $url; ?>">
<table>
	<tr>
		<td colspan="2"><strong>Add a new comment</strong></td>
	</tr>
	<tr>
    	<td>Name:</td>
        <td><input type="text" name="name" size="<?php if(isset($inputSize)) { echo $inputSize; } else { echo "35"; }?>"></td>
    </tr>
    <tr>
    	<td>Email:</td>
        <td><input type="text" name="email" size="<?php if(isset($inputSize)) { echo $inputSize; } else { echo "35"; }?>"></td>
    </tr>
    <tr>
    	<td valign="top">Comment:</td>
        <td><textarea name="comment" cols="<?php if(isset($textAreaCols)) { echo $textAreaCols; } else { echo "30"; }?>" rows="<?php if(isset($textAreaRows)) { echo $textAreaRows; } else { echo "5"; }?>"></textarea></td>
    </tr>
	<tr>
		<td>Subscribe:</td>
		<td><input type="checkbox" name="allComments" value="allComments" class="commentSubscribe" /> (to this comment page)</td>
		<td></td>
	</tr>
    <tr>
    	<td></td>
        <td><input type="submit" value="Submit" name="submit_top_level" class="commentSubmit" /></td>
    </tr>
</table>
</form>
</div>
<?php 

/*
The display_comments function prints out a comment thread onto the screen when it is included in the code. 
It takes two parameters--parent_id and the level. Each comment has 2 ID's. It has its own comment ID and a parent ID. 
Top level comments will have a parent ID of zero because they sit on the top level. A nested comment will have a parent ID of the comment that 
it is a reply to. a comment can have a parent_ID that points to a comment with another parent_ID and so on. 

The level indicates how far the nested comment should be indented. When determining how for to indent, the level is multiplied by 50 pixels. 
Top level comments have a level of zero, so there are not indented at all (0*50 = 0). A nested comment will have a level of 1 and will
be indented by 50 pixels (1*50 = 50). A level 2 comment will be indented 100 pixels etc.
display_comments
*/

function display_comments($parent, $level) {
	
	  $query_string = $_SERVER['QUERY_STRING'];
	
	  if($query_string == "") {
		  $url = $_SERVER['PHP_SELF'];
	  } elseif (substr($query_string, 0, 9) == "parent_id"){
		  //if url contains ?parent_id= then we dont want to add that as a new page into the DB
		  $url = $_SERVER['PHP_SELF'];
	  } else {
		  //PHP_SELF does not grab variables if there are variables being passed in the url, but QUERY_STRING does...so if there
		  //are variables in the url, append them to PHP_SELF
		  $url = $_SERVER['PHP_SELF'] . "?" . $_SERVER['QUERY_STRING'];
	  }
	  	  
	  $sql = mysql_query("SELECT page_id FROM comment_pages WHERE page='" . $url . "'");
	  $row = mysql_fetch_array($sql);
	  $page_id = $row['page_id'];
	  
	  //if the url has the unsubscribe variable, unsubscribe the user from the thread
	  if(substr_count($url, 'unsubscribe') != 0){
		  include("/Library/WebServer/commontools/threaded_comments/threaded_comments_subscriptions.php");
		  exit();
	  } elseif($page_id == "") {
		  //if the page has not been inserted into the database and given a page_id, insert it in the
	  	  //database and give it a page_id
		  $site = $_SESSION['site'];
		  mysql_query("INSERT INTO comment_pages (site, page) VALUES('$site','$url')") or die(mysql_error());
	  }

	  $sql = mysql_query("SELECT * FROM comments WHERE parent_id='" . $parent . "' AND page_id='" . $page_id . "' ORDER BY comment_id");
	  
	  while($row = mysql_fetch_array($sql)) {
	  $comment_id = $row['comment_id'];
	  
	  echo "<div style=\"width:" . $width . "px;\">
		  <table class=\"comments-table\" style='margin-left:" . (50*$level) . "px;'>
			  <tr>
			  	  <td>Posted By: </td>
				  <td><strong>" . $row['name'] . "</strong> on " . $row['date_of_post'] . " at " . $row['time_of_post'] . "</td>
			  </tr>
		  </table>
		  <!--I put the comment in its own table because when you click reply the form messes up the whole format of the comment. 
		  a seperate table fixes this-->
		  <table class=\"comments-table\" style='margin-left:" . (50*$level) . "px;'>
			  <tr>
				  <td colspan='3'>" . $row['comment'] . "</td>
				  <td></td>
			  </tr>
		  </table>
		  <table class=\"comments-table\" style='margin-left:" . (50*$level) . "px;'>
			  <tr>
				  <td><a href=\"javascript:toggle_display(" . $comment_id . ")\">Reply</a>
				  
					  <span id=\"" . $comment_id . "\" style='display:none;'>
					  
						<!-- kmb [11/24/09] took the parent_id GET var out of the form and put the fundID in so as to avoid the blank screen on loading only the parent_id GET var. the parent_id is now passed as a hidden field a little further down -->				  
						<!--<form name=\"nested_comment\" method=\"POST\" onsubmit=\"return validate_form(this);\" action=\"" . $_SERVER['PHP_SELF'] . "?parent_id=" . $comment_id . "\">-->
					  
						<form name=\"nested_comment\" method=\"POST\" onsubmit=\"return validate_form(this);\" action=\"" . $url . "\">
						  <table>
							  <tr>
									<td colspan=\"2\"><strong>Reply to an existing comment</strong></td>
							  </tr>
							  <tr>
								  <td>Name:</td>
								  <td><input type=\"text\" name=\"name\" size=\"35\"></td>
							  </tr>
							  <tr>
								  <td>Email:</td>
								  <td><input type=\"text\" name=\"email\" size=\"35\"></td>
							  </tr>
							  <tr>
								  <td valign=\"top\">Comment:</td>
								  <td><textarea name=\"comment\" cols=\"30\" rows=\"5\"></textarea></td>
							  </tr>
								<tr>
									<td>Subscribe:</td>
									<td><input type=\"checkbox\" name=\"allComments\" value=\"allComments\" class=\"commentSubscribe\"> (to this comment page)</td>
									<td></td>
								</tr>
								<tr>
								  <td><input type=\"hidden\" name=\"stage\" value=\"1\" />
								  
								  <!-- kmb [11/24/09]: added parent_id as a hidden field so that we can access it as a POST var -->
								  <input type=\"hidden\" name=\"parent_id\" value=\"". $comment_id ."\" />
								  
								  </td>
								  <td><input type=\"submit\" value=\"Submit\" name=\"submit_nested\" class=\"commentSubmit\" /></td>
							  </tr>
						  </table>
						  </form>
					  </span>
				  </td>
			  </tr>
		  </table>
		  </div>
		  <hr>";
		  
		  //call the function recursively. Use the current comment ID as the parent_id and indent the new comments 1 level. 
		  display_comments($comment_id, $level+1);
	  
	  } // end while($row = mysql_fetch_array($sql)) 
	    
} //end display_comments() {

?>

<script type="text/javascript">
	 
function toggle_display(i){
	if(document.getElementById(i).style.display == 'none') {
		document.getElementById(i).style.display = 'block';
	} else {
		document.getElementById(i).style.display = 'none';
	}
}
	 
function validate_form(thisform)
{
with (thisform)
  {
	  if (validate_required(name,"Please enter your name.") == false) {
		  name.focus();
		  return false;
	  }
	  if (validate_required(email,"Please enter your email address.") == false || validate_email(email, "Please enter a valid email address.") == false ) {
		  email.focus();
		  return false;
	  }
	  if (validate_required(comment,"Please enter a comment.") == false || validate_comment_length(comment, "Comments must be at least 10 characters long.") == false) {
		  comment.focus();
		  return false;
	  }
  }
}
	 

function validate_required(field,alerttxt)
{
	with (field) {
		if (value==null||value=="") {
			alert(alerttxt);
			return false;
		} else if (field == "comment" && value.length < 10) {
			alert("Comments must be at least 10 characters long.");
			return false;
		} else {
		  	return true;
		}
	}
}

function validate_comment_length(field,alerttxt)
{
	with (field) {
		if (value.length < 10) {
			alert(alerttxt);
			return false;
		} else {
		  	return true;
		}
	}
}

function validate_email(field,alerttxt)
{
	with (field) {
		if (check_email(value) == false) {
			alert(alerttxt);
			return false;
		} else {
		  	return true;
		}
	}
}

function check_email(str) {

	var at="@";
	var dot=".";
	var lat=str.indexOf(at);
	var lstr=str.length;
	var ldot=str.indexOf(dot);
	
	if (str.indexOf(at)==-1){
	   return false;
	}
  
	if (str.indexOf(at)==-1 || str.indexOf(at)==0 || str.indexOf(at)==lstr){
	   return false;
	}
  
	if (str.indexOf(dot)==-1 || str.indexOf(dot)==0 || str.indexOf(dot)==lstr){
		return false;
	}
  
	 if (str.indexOf(at,(lat+1))!=-1){
		return false;
	 }
  
	 if (str.substring(lat-1,lat)==dot || str.substring(lat+1,lat+2)==dot){
		return false;
	 }
  
	 if (str.indexOf(dot,(lat+2))==-1){
		return false;
	 }
	
	 if (str.indexOf(" ")!=-1){
		return false;
	 }
  
	 return true;					
}

</script>