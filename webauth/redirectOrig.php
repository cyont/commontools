<?php

	session_start();
	
	//make sure we are returning from webauth with the ticket set otherwise go back to webauth
	if(!isset($_GET['ticket'])){
	
		header("Location: https://webauth.arizona.edu/webauth/login?service=https://".$_SERVER['SERVER_NAME']."/commontools/webauth/redirect.php");
		exit();
		
	}
	//if we did get a tichet from webauth
	else if(isset($_GET['ticket'])){
		
		//create validation url using the ticket and this page as the service
		$url = '"https://webauth.arizona.edu/webauth/serviceValidate?ticket='.$_GET['ticket'].'&service=https://'.$_SERVER['SERVER_NAME'].'/commontools/webauth/redirect.php"';
		
		//send validation request to webauth
		exec("curl -m 120 $url " ,$return_message_array, $return_number);
		
		//turn the retured array into a string so that it can be parsed as xml
		$temp = implode('', $return_message_array);
		
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
			unset($_SESSION['webauth_splash']);
			
			//redirect back to page that was set in include.php
			header("Location:http://".$_SERVER['SERVER_NAME'].$_SESSION['webauth_service']);
			exit();
		}
		//if ticket authentication failed for some reason return user to webauth
		else{
			header("Location: https://webauth.arizona.edu/webauth/login?service=https://".$_SERVER['SERVER_NAME']."/commontools/webauth/redirect.php");
			exit();
		}
	
	}
?>


