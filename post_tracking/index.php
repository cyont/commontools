<?php

	$query = '<TrackRequest USERID="774UNIVE0611"><TrackID ID="EJ123456780US"></TrackID></TrackRequest>';
	
	$url = "http://production.shippingapis.com/ShippingAPITest.dll?API=TrackV2&XML=$query";
	
	echo $url."<br><br>";
	
	$result = file_get_contents($url);
	echo "<pre>".$result."</pre>";
?>