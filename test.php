<?php
session_start();
//$_SESSION['test']='test"""""\'\'dad\'dad"4';
var_dump($_SESSION);
mysql_session_test();
print '<br />';
print session_id();
require("db/mysqli.inc");
$db = new db_mysqli("session");
//$db->query("delete from session");
$result = $db->query("select * from session");
while($row = $result->fetch_assoc()){
print $row["session_id"].'<br />';
}
