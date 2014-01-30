<?php

		$param = array('intReportID'=>952);
	
		$client = new SoapClient("https://www.career.arizona.edu/apps/WS/CS_Webservices.asmx?WSDL", array('trace' => TRUE, 'soap_version'   => SOAP_1_2));
	
		$listing = $client->CS_Get_JobListings($param);
		
		var_dump($listing);
	
		