<?php

// Form data cleaning function
function cleanFormData($formData) {
	$formData = trim($formData); // removes leading and trailing spaces
	$formData = strip_tags($formData); // strips HTML style tags

		if(get_magic_quotes_gpc()){ // prevents duplicate backslashes if magic quotes is enabled in php install
			$formData = stripslashes($formData);
		}

	return $formData;
}


## NOTE: This function ALSO handles the task of form_cleaner.php. No need to use both on a file upload.
// File name cleaning function
function cleanFileName($fileName) {
	
	## Replace troublesome characters
	// define filename filters
	$search = array("#", ":", " ", "/", "'", "%22");
	$replace = array("_", "");

	// clean the name
	$fileName = trim($fileName); // removes leading and trailing spaces
	$fileName = str_replace($search, $replace, $fileName);


	## Handle magic quotes differences between test and live servers
	// helps in handling quotes in file names
	$fileName = trim($fileName); // removes leading and trailing spaces
	$fileName = strip_tags($fileName); // strips HTML style tags

	if(get_magic_quotes_gpc()){ // prevents duplicate backslashes if magic quotes is enabled in php install
		$fileName = stripslashes($fileName);
	}

	return $fileName;
}

?>