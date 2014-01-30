<?

	//require_once('webauth/webauth.inc');
	session_start();
//	webauth::forceLogin($webauth_service, $webauth_splash);

	//if(is_object($_SESSION['webauth']))
		//$_SESSION['webauth'] = get_object_vars($_SESSION['webauth']);
	
	//check wether or not they have already been logged in via webauth
	if(!isset($_SESSION['webauth']['netID']) || $_SESSION['webauth']['netID']==''){
		
		//check if page to return has been specified otherwise use current page as the return to
		if($webauth_service == '' || !isset($webauth_service))	{
			$_SESSION['webauth_service'] = $_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
		}else {
			$_SESSION['webauth_service'] = $_SERVER['SERVER_NAME'].$webauth_service;
		}
		
		//save possibly set splash page to session so splash.php can grab it and display it
		$_SESSION['webauth_splash'] = $webauth_splash;
		
		//redirect to webauth so user can sign in allowing the host to be a variable so all domains can use this same file
		//header("Location: https://webauth.arizona.edu/webauth/login?service=https://".$_SERVER['SERVER_NAME']."/commontools/webauth/redirect.php");
	//	$port = $_SERVER['SERVER_PORT'];
	//	if($port == '80' || $port =='443') {
			header("Location:http://elvis.sunion.arizona.edu:8088/webauth/redirect.php?sid=".session_id());
	//	}else{
	//		header("Location:http://elvis.sunion.arizona.edu:8088/webauth/redirect.php?sid=".session_id()."&port=".$port);
	//	}
	
		exit();
		
		//after beign sent to webauth they will be redirected to redirect.php
	}
	
	function create_logout_link($text){
		//session_destroy();
		return 'https://webauth.arizona.edu/webauth/logout?logout_href=http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'].'&logout_text='.$text;
	}
?>
