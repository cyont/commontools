<?php

	require_once("db.inc");
	$db = new db($database["host"], $database["user"], $database["password"], $database["db"]);
	
	$post = mysqlCleanValues($_POST);
	$get = mysqlCleanValues($_GET);
	
	session_start();
	
	require_once("centralAuth.inc");

	function printPretty($val)
	{
		print("<pre>".print_r($val, true)."</pre>");
	}
	
	function randomString($length = 20)
	{
	    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
	    $string = "";    
	    for ($p = 0; $p < $length; $p++) {
	        $string .= $characters[mt_rand(0, strlen($characters))];
	    }
	    return $string;
	}
	
	function enum_select($table , $field)
	{
		global $db;
		
		$result = $db->query("SHOW COLUMNS FROM $table LIKE '$field'");
		$row = mysqli_fetch_array($result);
		$regex = "/'(.*?)'/";
		preg_match_all( $regex , $row[1], $enum_array );
		$enum_fields = $enum_array[1];
		return($enum_fields);
	}
	
	function mysqlCleanValues($array)
	{
		global $db;
		
		if(is_array($array))
		{
			foreach($array as $key => $value)
			{
				if(is_array($array[$key]))
		            $array[$key] = mysqlCleanValues($array[$key]);

		        if(is_string($array[$key]))
		            $array[$key] = $db->real_escape_string($array[$key]);
		    }            
		}
		if(is_string($array))
		    $array = $db->real_escape_string($array);
		
		return $array;
	}

?>