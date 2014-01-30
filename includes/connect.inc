<?php
/*
 * Class used to implement Soap API for connect 
 */
class connect{
	private $clientName = 'arizonaug';
	private $passKey = 'Ar1z*0N*aU5';
	public $client;
	
	function __construct(){
		$this->client = new SoapClient('https://arizonaug.askadmissions.net/ws/bridge.asmx?wsdl', array('trace'=>1));
	}
	
	function getContacts($criteria=array(), $attributes, $order=array(), $start=1, $end=1){
			
		$params = array(
			'ClientName'=>$this->clientName,
			'PassKey'=>$this->passKey,
			'StartingRecord'=>$start,
			'EndingRecord'=>$end,
			'SearchCriteriaXml'=> new SoapVar($this->createAttributesXML($criteria),XSD_STRING),
			'AttributesXml'=> new SoapVar($this->createAttributesXML($attributes),XSD_STRING),
			'OrderByXml'=> new SoapVar($this->createAttributesXML($order),XSD_STRING)
		);
		
		
		$response = $this->client->getContacts($params);
		return simplexml_load_string($response->GetContactsResult, NULL, LIBXML_NOCDATA);
		
	}
	function createContact($attributes){
		$params = array(
			'ClientName'=>$this->clientName,
			'PassKey'=>$this->passKey,
			'AttributesXml'=> new SoapVar($this->createAttributesXML($attributes),XSD_STRING)
		);
		
		$response = $this->client->createContact($params);
		return simplexml_load_string($response->CreateContactResult, NULL, LIBXML_NOCDATA);;
	}
	function updateContact($id, $attributes){
			
		$params = array(
			'ClientName'=>$this->clientName,
			'PassKey'=>$this->passKey,
			'ContactID'=>$id,
			'AttributesXml'=> new SoapVar($this->createAttributesXML($attributes),XSD_STRING)
		);
		
		$response = $this->client->updateContact($params);
		return simplexml_load_string($response->UpdateContactResult, NULL, LIBXML_NOCDATA);;
		
	}
	
	function createAttributesXml($attributesArray){
		
		if(sizeof($attributesArray) == 0 ){
			return '';
		}
		
		$xmlString = '<attributes>';
		foreach($attributesArray as $attribute){
			$xmlString .= '<attribute><name>'.$attribute['name'].'</name>';
			$xmlString .= $attribute['value'] ? '<value><![CDATA['.$attribute['value'].']]></value>' : '';
			$xmlString .= $attribute['operator'] ? '<operator>'.$attribute['operator'].'</operator>': '';
			$xmlString .= $attribute['order'] ? '<order>'.$attribute['order'].'</order>': '';
			$xmlString .= '</attribute>';
		}
		$xmlString .= '</attributes>';
		
		return $xmlString;
	}
}

/*
 * Class containing functions to implement needed UASavvy/UA Social functionality 
 */
 
class UASavvy{
		
	//function to set the innerHTML for the UA Savvy emails sent out by connect to opted-in students	
	static function updateUASocialEmailHTML($html){
		
		$connect = new connect();
		
		try{
			$attributes = array(array("name" => "text4435", "value" => $html), array("name" => "sourcecode", "value" => 'UA Social - Email Content'));
			$response = $connect->updateContact('436892', $attributes);
		}catch(Exception $e){
			var_dump($e);
		}
		
		var_dump($response);
	}
	
	//function to opt-in students to recieaving the ua savvy deal emails
	static function addStudentToEmailList($emplId, $netID, $firstName, $lastName, $dob, $email){
			
		$connect = new connect();
		
		try{
			$searchCriteria = array(array("name" => "text2895", "value" => $emplId, "operator"=> "equals"));
			$attributes = array(array("name" => "ID"));
			$response = $connect->getContacts($searchCriteria, $attributes);
		}catch(Exception $e){
			var_dump($e);
		}
		
		$count = $response->returndata->count.'';
		
		if($count == 0){
			try{
				$attributes = array(
								array("name" => "text2895", "value" => $emplId),
								array("name" => "text3595", "value" => $netID),
								array("name" => "firstname", "value" => $firstName),
								array("name" => "lastname", "value" => $lastName),
								array("name" => "dob", "value" => $dob),
								array("name" => "email", "value" => $email),
								array("name" => "emailoptout", "value" => 'no'),
								array("name" => "sourcecode", "value" => 'UA Social - New Sign-Up')
							  );
							  
				$response = $connect->createContact($attributes);
			}catch(Exception $e){
				var_dump($e);
			}
			var_dump($response);
		}
		else{
			$id = $response->returndata->contacts->contact->attribute->value.'';
			
			try{
				$attributes = array(
								array("name" => "email", "value" => $email),
								array("name" => "emailoptout", "value" => 'no'),
								array("name" => "sourcecode", "value" => 'UA Social - New Sign-Up')
							  );
				$response = $connect->updateContact($id,$attributes);
			}catch(Exception $e){
				var_dump($e);
			}
			var_dump($response);
		}
	}
}


