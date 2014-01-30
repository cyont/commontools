<?php

	// function to validate the email address
	function validateEmail($email)
	{
		// create the email validation regular expression
		$regexp = "/^([._a-z0-9-]+)(\.[._a-z0-9-])*@([._a-z0-9-]+)(\.[._a-z0-9-])*(\.[a-z]{2,6})$/i";
	
		// validate the syntax
		if (preg_match($regexp, $email)) return 1;
		else return 0;
	}
	
	// function to validate the name fields
	function validateName($nameField)
	{
		// create the name validation regular expression
		// upper and lower case letters,
		// comma, single quote, period, underline, dash
		// \s = any white space characters (space, tab & line break)
		$regexp2 = "/^[a-zA-Z,'._\-\s]*$/i";
	
		// validate the syntax
		if (preg_match($regexp2, $nameField)) return 1;
		else return 0;
	}
	
	// function to validate the comment field
	function validateComment($commentField)
	{
		// create the comment validation regular expression
		// \s = any white space characters (space, tab & line break)
		// \w = any alphanumeric characters (letters & numbers)
		// period, comma, underline, dash, single quote, exclamation point, 
		// question mark, ampersand, pipe and dollar sign.
		//---------------------------------------------------*
		// $regexp3 = "/^[0-9a-zA-Z,._;:!?|$&\-\s\'\"]*$/i";
		//---------------------------------------------------*
		$regexp3 = "/^[^*<>]*$/i";
	
		// validate the syntax
		if (preg_match($regexp3, $commentField)) return 1;
		else return 0;
	}
	
	// function to validate the street address field
	function validateAddress($addressField)
	{
		// create the street address validation regular expression
		// \s = any white space characters (space, tab & line break)
		// \w = any alphanumeric characters (letters & numbers)
		// underline, comma, pound sign, single quote, period and dash.
		$regexp4 = "/^[\w\s_,\#\'\.\-]*$/i";
	
		// validate the syntax
		if (preg_match($regexp4, $addressField)) return 1;
		else return 0;
	}
	
	// function to validate the date
	function validateDate($dateField)
	{
		// create the date validation regular expression.
		$regexp5 = "/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}$/";
	
		// validate the syntax
		if (preg_match($regexp5, $dateField)) return 1;
		else return 0;
	}
	
	// function to validate the zip or postal code
	function validateZip($zip)
	{
		// create the zip validation regular expression
		$regexp6 = "/(^\d{5}(-\d{4})?$)|(^[ABCEGHJKLMNPRSTVXY]{1}\d{1}[A-Z]{1} *\d{1}[A-Z]{1}\d{1}$)/i";
	
		// validate the syntax
		if (preg_match($regexp6, $zip)) return 1;
		else return 0;
	}
	
	// function to validate the phone number
	function validatePhone($phone)
	{
		// create the phone validation regular expression
		$regexp7 = "/^(?:(?:\+?1\s*(?:[.-]\s*)?)?(?:\(\s*([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9])\s*\)|([2-9]1[02-9]|[2-9][02-8]1|[2-9][02-8][02-9]))\s*(?:[.-]\s*)?)?([2-9]1[02-9]|[2-9][02-9]1|[2-9][02-9]{2})\s*(?:[.-]\s*)?([0-9]{4})(?:\s*(?:#|x\.?|ext\.?|extension)\s*(\d+))?$/i";
	
		// validate the syntax
		if (preg_match($regexp7, $phone)) return 1;
		else return 0;
	}
		
?>