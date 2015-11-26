<?php
$key = 'ODIz-MzVl-NzVh-ODFl-NTcy-OThm-ZmJi-M2Ix-MDMx-MTI2-MzM2';
$mobile = '52693435;
$message = urlencode("Let's code tonight!;)"); // make the phrase URL friendly
$sUrl = "http://ecuanota.com/api-send-sms"; // point to this URL
$sLink = $sUrl."?key=".$key."&mobile=".$mobile."&message=".$message; // create the SMS
file_get_contents($sLink); // send the SMS
// echo file_get_contents($sLink); // to see the JSON response from the server
?>