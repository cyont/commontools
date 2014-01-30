<?
//new functions 

function createValue($url){
	
	$new_url = str_replace('/',"",str_replace('/index.php',"" ,$url));

	return $new_url;

}
function getURL($url){
	
	$new_url = str_replace('/form1.php',"/formswitcher.php" ,$url);

	return $new_url;

}

function getDates(){

$date = date('ymdHis');

return $date;

}


function getCodes($res,$cur){
		
		//echo "<br/>". $cur." cur baby";
		
//array for error codes
$error_codes = array('102:<b>The following was invalid<br/>:</b><font color=\"#BE0F34\"><br/>','200:Address Verification Check failed, please make sure the address you entered matches the address your credit card is registered to. <br/>','202:Your card has expired, you can enter a different card and try again.<br/>','203:Your card has been declined, please verifiy your information and try again.<br/>','204:Your card has been declined for insufficient funds <br/>','205:Your card has been declined, please verifiy your information and try again.<br/>','210:Your card has been declined as \"Over the Limit\"<br/>','231:The Credit card number you entered is not valid, please correct and try again<br/>','240:The Credit card number you entered does not match the card type, please correct and try again <br/>');
	
	
	$size = sizeof($error_codes);
	
		while($size >=0){
			list($reason_code, $default)= split (":",$error_codes[$size]);
				
			if($res == $reason_code){
				//echo $reason_code."<br/>".$default;
				$payment_error_text .=$default;
				if($res == "102"){
				
					$i = 0;
					$curField = "InvalidField" . $i;
						while(isset($_POST[$curField])) {
							$payment_error_text .= ("&nbsp;&nbsp;&nbsp;" . $_POST[$curField] . "<br/>");
							$i++;
							$curField = "InvalidField" . $i;
						//echo $curField;
						//echo "here";
						}
						
			
					$i = 0;
					$curField = "MissingField" . $i;
				//	print_r($_POST);
						while(isset($_POST[$curField])) {
							$payment_error_text .= ("&nbsp;&nbsp;&nbsp;" . $_POST[$curField] . "<br/>");
							$i++;
							$curField = "MissingField" . $i;
							//echo $curField;
							//echo "or here";
							//echo $payment_error_text;
							//echo "test";
						}
							
			
					$payment_error_text .= "</font>";
					echo $payment_error_text;
					//echo "test this".$payment_error_text;
				}//end of 102 check
				else{
				
					echo $payment_error_text."<br/>";
			
				}		
			}
			$size--;
		}
}


function getErrors($res){


if($res == "102"){
					
					$i = 0;
					$curField = "InvalidField" . $i;
						while(isset($_POST[$curField])) {
							//$errors[$i] = $payment_error_text .= ("&nbsp;&nbsp;&nbsp;" . $_POST[$curField] . "<br>");
							$errors2 .= "&nbsp;&nbsp;&nbsp;" . $_POST[$curField] . ":";

							$i++;
							$curField = "InvalidField" . $i;
						//echo $curField;
					
						}
						
			
					$i = 0;
					$curField = "MissingField" . $i;
						while(isset($_POST[$curField])) {
							//$errors[$i] = $payment_error_text .= ("&nbsp;&nbsp;&nbsp;" . $_POST[$curField] . "<br>");
							$errors2 .=  $_POST[$curField] . ":";
							$i++;
							$curField = "MissingField" . $i;
							//echo $errors2;
							

						}
							
			
					
				//	echo $payment_error_text." maybe";
					//echo "test this".$payment_error_text;
				}//end

//echo $errors2."<br/>";
return $errors2;

}


function splitErrors($er){

$new_error .="<span style=\"font-family:arial, san-serif;color:#BE0F34;font-size:12px;\">";
$error =str_replace(":", "<br/>", $er);
$new_error .= $error."</span>";
// echo $new_error;
return $new_error;
}

?>