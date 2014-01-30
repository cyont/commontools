<?php
class webauth{
	
	static public function forceLogin($service = NULL, $splash=NULL){
		session_start();
		if(!$_SESSION['webauth'] || empty($_SESSION['webauth']['netID'])){
			
			if($service == NULL)
				$service = ($_SERVER['HTTPS']?'https://':'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			else
				$service = ($_SERVER['HTTPS']?'https://':'http://').$_SERVER['HTTP_HOST'].$service;
			
			if($splash != NULL)
				$splash = 'https://'.$_SERVER['HTTP_HOST'].$splash;
			
			$_SESSION['webauth']['service'] = $service;
			$_SESSION['webauth']['splash'] = $splash;
			
			if($_SERVER['SERVER_ENV'] == 'staging'){
				header("Location:http://elvis.sunion.arizona.edu:8088/webauth/redirect.php?sid=".session_id());
				exit();
			}
			else{
				header("Location:https://webauth.arizona.edu/webauth/login?service=".($_SERVER['HTTPS']?'https://':'http://').$_SERVER['HTTP_HOST']."/commontools/webauth/redirect.php");
				exit();
			}
		}
	}
	
	static function create_logout_link($return_text, $return_link = NULL){
		if($return_link)
			$uri = $return_link;
		else 
			$uri = $_SERVER['HTTP_HOST'];
		
		return '/commontools/webauth/logout.php?logout_href='.($_SERVER['HTTPS']?'https://':'http://').$uri.$_SERVER['PHP_SELF'].'&logout_text='.$return_text;
	}	
}