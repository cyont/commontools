<?php
	session_start();
///*	
$pageURL .= "https://trinity.sunion.arizona.edu/computerassets/jquery.php";



////////////////////////////////////////////////////////////////////////////
//					get either netid or email pass combo				  //
////////////////////////////////////////////////////////////////////////////

	if(isset($_GET['ticket'])){
		$tix = $_GET['ticket'];
		
		$url = '"https://webauth.arizona.edu/webauth/serviceValidate?ticket='.$tix.'&service='.$pageURL.'"';
		
		exec("curl -m 120 $url " ,$return_message_array, $return_number);
		
		//check to make sure ticket was valid
		if($return_message_array[1] == "	<cas:authenticationSuccess>"){
						
			$netID = $return_message_array[2];		
			$netID = trim(str_replace("<cas:user>","",str_replace("</cas:user>","", $netID)));
			
			$_SESSION['netID'] = $netID;
			
		}
		
		//if not send back to webauth to get valid ticket
		else{
			header("Location: https://webauth.arizona.edu/webauth/login?service=".$pageURL);
			exit;
		}
	}
	else{
		header("Location: https://webauth.arizona.edu/webauth/login?service=".$pageURL);
		exit;	
	}//*/
	//$_SESSION['netID']="nbischof";
$_SESSION['webauth']['netID'] = $_SESSION['netID'];
$admins=Array('nbischof','sanorris','jmasson');
if (in_array($_SESSION['webauth']['netID'],$admins)) {
  $_SESSION['computerassets']['authorized'] = TRUE;
  $user = $_SESSION['webauth']['netID'];
  echo '<html>'.
  '<head>'.
  '<title>Computer Assets - Computers</title>'.
  '<link rel="stylesheet" type="text/css" href="asset_styles.css" />'.
  '<script src="jquery-1.2.6.min.js"></script>'.
  '<script src="asset_functions.js"></script>'.
  '</head>'.
  '<body onload="loadComputersList();">';
  include("header.php");
  echo '<div id="content">'.
  '<div id="machine_list"></div>'.
  '<div id="machine_details"><br /> &larr; Select a Computer/Device</div>';
  include("assets_commonelements.php");
  echo '</div>'.
  '</body>'.
  '</html>';
}
else {
  echo 'permission denied';
  die;
}
?>