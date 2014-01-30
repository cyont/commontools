<div id="popup" style="display:none; position:absolute"></div>
<div id="popup1" style="display:none; position:absolute"></div>
<div id="popup2" style="display:none; position:absolute"></div>
<div id="popup3" style="display:none; position:absolute"></div>
<?php
$currentPage = $_SERVER["PHP_SELF"];
$currentPage = Explode('/', $currentPage);
$currentPage = substr($currentPage[count($currentPage) - 1],0,-4);
echo '<script type="text/javascript">var curPage="'.$currentPage.'";</script>';
?>