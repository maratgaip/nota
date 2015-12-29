<?php 

@session_start ();

@ob_start ();

@ob_implicit_flush ( 0 );



//include_once 'download.php';
$file = $_POST['file'];
$file = realpath('/static/songs/');
echo $file+1;
header ("Content-type: octet/stream");
//header ("Content-disposition: attachment; filename=".$file.";");
header ("Content-disposition: attachment; filename='/static/songs/995.mp3'");
header("Content-Length: ".filesize("/static/songs/995.mp3"));
readfile("/static/songs/995.mp3");
exit;


?>
