<?php
require_once('/Library/WebServer/commontools/mysql_link.inc');
mysql_select_db('cardtaker');

//////////////////////////////////////////////////////////////////
//																//
//				SAVE DATA SENT BACK FROM CYBERSOURCE			//
//																//
//////////////////////////////////////////////////////////////////

	//if this is the first attempt to submit payment
	if($_POST['paymentID'] == 0){
		
		//get app id based on form page
		$result = mysql_query('select ID from applications where form_page="'.$_POST['form_page'].'"');
		$app = mysql_fetch_assoc($result);
		
		//save whatever data was returned to database
		$query = "insert into payments set "
					.'appID ='. $app['ID'].', '
					.'billTo_firstName ="'. $_POST['billTo_firstName'].'", '
					.'billTo_lastName ="'. $_POST['billTo_lastName'].'", '
					.'billTo_street1 ="'. $_POST['billTo_street1'].'", '
					.'billTo_city ="'. $_POST['billTo_city'].'", '
					.'billTo_state ="'. $_POST['billTo_state'].'", '
					.'billTo_postalCode ="'. $_POST['billTo_postalCode'].'", '
					.'card_cardType ="'. $_POST['card_cardType'].'", '
					.'card_expirationMonth ="'. $_POST['card_expirationMonth'].'", '
					.'card_expirationYear ="'. $_POST['card_expirationYear'].'", '
					.'ccAuthReply_cvCode ="'. $_POST['ccAuthReply_cvCode'].'", '
					.'billTo_phoneNumber ="'. $_POST['billTo_phoneNumber'].'", '
					.'billTo_email ="'. $_POST['billTo_email'].'", '
					.'orderAmount ='. $_POST['orderAmount'].', '
					.'orderNumber ="'. $_POST['orderNumber'].'", '
					.'reasonCode ='. $_POST['reasonCode'].', '
					.'decision ="'. $_POST['decision'].'", '
					.'card_accountNumber ="'. substr($_POST['card_accountNumber'], -4, 4).'" ';
		mysql_query($query);
		//pass back id to data so app my retieve data
		$_POST['paymentID'] = mysql_insert_id();
	}
	//if resubmission attempt
	else{
		//update db with newest data
		$query = "update payments set "
					.'billTo_firstName ="'. $_POST['billTo_firstName'].'", '
					.'billTo_lastName ="'. $_POST['billTo_lastName'].'", '
					.'billTo_street1 ="'. $_POST['billTo_street1'].'", '
					.'billTo_city ="'. $_POST['billTo_city'].'", '
					.'billTo_state ="'. $_POST['billTo_state'].'", '
					.'billTo_postalCode ="'. $_POST['billTo_postalCode'].'", '
					.'card_cardType ="'. $_POST['card_cardType'].'", '
					.'card_expirationMonth ="'. $_POST['card_expirationMonth'].'", '
					.'card_expirationYear ="'. $_POST['card_expirationYear'].'", '
					.'ccAuthReply_cvCode ="'. $_POST['ccAuthReply_cvCode'].'", '
					.'billTo_phoneNumber ="'. $_POST['billTo_phoneNumber'].'", '
					.'billTo_email ="'. $_POST['billTo_email'].'", '
					.'orderAmount ='. $_POST['orderAmount'].', '
					.'reasonCode ='. $_POST['reasonCode'].', '
					.'decision ="'. $_POST['decision'].'", '
					.'card_accountNumber ="'.substr($_POST['card_accountNumber'], -4, 4).'" '
					.'where ID='.$_POST['paymentID'];
		mysql_query($query);
		//print mysql_error();
	}
	
	
	
	
	
//////////////////////////////////////////////////////////////////
//																//
//			REDIRECT TO CORRECT PAGE BASED ON DECISION			//
//																//
//////////////////////////////////////////////////////////////////	
	
	
	
	
	
	//was payment accepted
	if($_POST['decision'] == 'ACCEPT'){
		//if so grab thankyou page for app from db pased on form appid and payment id
		$result = mysql_query('select thankyou_page from applications join payments on applications.ID=payments.appID where payments.ID='.$_POST['paymentID']);
		$app = mysql_fetch_assoc($result);
		
		//load session that was possibly started in different domain so id can be accessed by app
		session_id($_POST['session_id']);
		session_start();
		$_SESSION = unserialize(stripslashes(htmlspecialchars_decode($_POST['session'])));
		
		//clear errors
		unset($_SESSION['cterror']);
		
		//save id to app session 
		$_SESSION['paymentID'] = $_POST['paymentID'];
		
		//redirect to app thankyou page
		header("Location:https://".$_POST['host'].$app['thankyou_page']);	
	}
	else{
		//on failure to process
		
		//load app session so it can retieve payment info throught payment id
		session_id($_POST['session_id']);
		session_start();
		$_SESSION = unserialize(stripslashes(htmlspecialchars_decode($_POST['session'])));
		
		//clear any previous errors
		unset($_SESSION['cterror']);
		
		//save paymentiID to app session so it can load data
		$_SESSION['paymentID'] = $_POST['paymentID'];
		
		//set errors for any missing fields
		$i=0;
		while(isset($_POST['MissingField'.$i])){
			$_SESSION['cterror'][$_POST['MissingField'.$i++]] = 1;	
		}
		
		//return to form so user may correct and resubmit
		header("Location:https://".$_POST['host'].$_POST['form_page']);	
	}
	
	?>