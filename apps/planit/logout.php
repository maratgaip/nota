<?php 
session_start();
session_unset();
    $_SESSION['fb_token'] = NULL;
    $_SESSION['logout'] = NULL;
header("Location: index.php");        // you can enter home page here ( Eg : header("Location: " ."http://www.krizna.com"); 
?>
