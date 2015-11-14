<?php


$email = $_REQUEST['email'];
$myFile = "email-ids.csv";

$fh = fopen($myFile, 'a') or die("can't open file");
$stringData = $email . ",\n";
fwrite($fh, $stringData);
fclose($fh);

?>