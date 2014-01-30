<?php 
session_start();
session_destroy();
header("Location:https://webauth.arizona.edu/webauth/logout?logout_href=".$_SERVER['HTTP_REFERER']."&logout_text=".$_GET['logout_text']);
?>