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

?>