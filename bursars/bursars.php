<?php
$ch = curl_init("http://ruby.ccit.arizona.edu/student_link/redirect-login.cgi");
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.5');
curl_setopt($ch, CURLOPT_REFERER, 'http://www.union.arizona.edu');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$return = curl_exec($ch);
curl_close($ch);

$tok = strtok($return, '/');
$host = strtok('/');
$host = 'test.sl.arizona.edu';

		$data = '<?xml version="1.0" encoding="UTF-16"?>'.chr(10);
		$data .= "<webPayPost_request>".chr(10);
		$data .= "<auth_info><user_id /></auth_info>";
		$data .= "<charges>".chr(10);
		$data .= "<charge>".chr(10);
		$data .= "<sid>".'000000012'."</sid>".chr(10);
		$data .= "<subcode>" . '20350' . "</subcode>".chr(10);
		$data .= "<cid>" . 'web' . "</cid>".chr(10);
		$data .= "<term>" . '041' . "</term>".chr(10);
		$data .= "<amount>" . '1.00' . "</amount>".chr(10);
		$data .= "<action>CHARGE</action>".chr(10);
		$data .= "<description>" . "MEAL PLAN WEB DEPOSIT" . "</description>".chr(10);
		$data .= "<reference_number>".'null'. "</reference_number>".chr(10);
		$data .= "</charge>".chr(10);
		$data .= "</charges>".chr(10);
		$data .= "</webPayPost_request>".chr(10);



$ch = curl_init("https://".$host.'/student_link/services/webpaypost.asp');
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.5');
curl_setopt($ch, CURLOPT_REFERER, 'http://www.union.arizona.edu');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, 'xmlmsg='.urlencode($data));
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
$return = curl_exec($ch);
var_dump($return);
var_dump(curl_error($ch));
?>