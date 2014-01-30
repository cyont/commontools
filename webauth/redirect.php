<?php
require_once('/srv/www/htdocs/commontools/webauth/webauth.inc');
	
	//if coming from staging site capture session from reffering domain then continue on to webauth
	if(isset($_GET['sid'])){
		session_id($_GET['sid']);
		session_start();
		//var_dump($_SESSION);
		//if(isset($_GET['port'])) {	
		//	header("Location: https://webauth.arizona.edu/webauth/login?service=http://elvis.sunion.arizona.edu:8088/webauth/redirect.php?port=".$_GET['port']);
		//}else{
			header("Location: https://webauth.arizona.edu/webauth/login?service=http://elvis.sunion.arizona.edu:8088/webauth/redirect.php");
		//}
		//header("Location: https://webauth.arizona.edu/webauth/login?service=http://elvis.sunion.arizona.edu:8088/webauth/redirect.php");
		exit();
	}
	//else if we got a tichet from webauth
	else if(isset($_GET['ticket'])){
		session_start();
		//var_dump($_SESSION);
		//create validation url using the ticket and this page as the service
		// if(isset($_GET['port'])) {
		// 	$url_parts = parse_url($page_url);
		// 	$page_url = $url_parts['scheme'] . "://" . $url_parts['host'] . ":" . $_GET['port'] . $url_parts['path'];
		// 	echo $page_url;
		// 	exit();
		// }
		
		if($_SERVER['SERVER_ENV'] == 'staging')
			$url = 'https://webauth.arizona.edu/webauth/serviceValidate?ticket='.$_GET['ticket'].'&service=http://elvis.sunion.arizona.edu:8088/webauth/redirect.php';
		else 
			$url = '"https://webauth.arizona.edu/webauth/serviceValidate?ticket='.$_GET['ticket'].'&service='.($_SERVER['HTTPS']?'https://':'http://').$_SERVER['HTTP_HOST'].'/commontools/webauth/redirect.php"';
		
		//setup curl to webauth
		$curlHandle = curl_init();
		$options = array(
			CURLOPT_HEADER => false,
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FORBID_REUSE => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_TIMEOUT => 10
		);
		curl_setopt_array($curlHandle, $options);
		$temp = curl_exec($curlHandle);
		
		//create xml parser
		$xml_parser = xml_parser_create();
		
		//parse xml into an array of values and an array of indexes
		xml_parse_into_struct($xml_parser, $temp, &$struct, &$index);
		
		//check if ticket was authenticated successfully
		if(isset($index['CAS:AUTHENTICATIONSUCCESS'])){
			
			//save response to session
			$_SESSION['webauth']['netID'] = $struct[$index['CAS:USER'][0]]['value'];
			$_SESSION['webauth']['studentID'] = $struct[$index['CAS:STUDENTID'][0]]['value'];
			$_SESSION['webauth']['activeemployee'] = $struct[$index['CAS:ACTIVEEMPLOYEE'][0]]['value'];
			$_SESSION['webauth']['activestudent'] = $struct[$index['CAS:ACTIVESTUDENT'][0]]['value'];
			$_SESSION['webauth']['employeeID'] = $struct[$index['CAS:EMPLOYEEID'][0]]['value'];
			$_SESSION['webauth']['emplid'] = $struct[$index['CAS:EMPLID'][0]]['value'];
			$_SESSION['webauth']['ua_id'] = $struct[$index['CAS:DBKEY'][0]]['value'];
			
			//make sure this doesn't get used by another app that uses webauth			
			$_SESSION['webauth']['splash'] = NULL;
			
			//error_log("Login Success");
			//error_log(print_r($_SESSION, true));
			//error_log(print_r($_SESSION["webauth_service"], true));
			
			//redirect back to page that was set in include.php
			
			
			if(strpos($_SESSION['webauth_service'],'http') !== false)
				header("Location: $_SESSION[webauth_service]");
			else
				header("Location:http://$_SESSION[webauth_service]");
			exit();
		}
		//if ticket authentication failed for some reason return user to webauth
		else{
			if($_SERVER['SERVER_ENV'] == 'staging')
				header("Location: https://webauth.arizona.edu/webauth/login?service=http://elvis.sunion.arizona.edu:8088/webauth/redirect.php");
			else 
				header("Location: https://webauth.arizona.edu/webauth/login?service=".($_SERVER['HTTPS']?'https://':'http://').$_SERVER['HTTP_HOST']."/commontools/webauth/redirect.php");
			
			exit();
		}
	
	}
	else{
		if($_SERVER['SERVER_ENV'] == 'staging')
			header("Location: https://webauth.arizona.edu/webauth/login?service=http://elvis.sunion.arizona.edu:8088/webauth/redirect.php");
		else 
			header("Location: https://webauth.arizona.edu/webauth/login?service=".($_SERVER['HTTPS']?'https://':'http://').$_SERVER['HTTP_HOST']."/commontools/webauth/redirect.php");
			
		exit();
	}