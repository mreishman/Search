<?php
include("core/php/commonFunctions.php");
echo json_encode(getDirContents(escapeshellarg($_POST['dir'])));
?>