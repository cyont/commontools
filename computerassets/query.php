<?php

require('commontools/mysql_link.inc');
	
mysql_select_db("computerAssets", $DBlink);

$query=$_POST['query'];
$query = stripslashes($query);
//$query = 'select * from Computer';

$result = mysql_query($query);

//print $query;

 $i=0;
 
 $obje="";
 
 while ($row = mysql_fetch_assoc($result)) {
   $obje[$i++] = $row;
}

$jsonobje = json_encode($obje);

print $jsonobje;


?>
