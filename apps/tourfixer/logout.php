<?php 
session_start();
session_unset();
    $_SESSION['logout'] = NULL;
    $_SESSION['fb_id'] = NULL;           
    $_SESSION['fb_username'] = NULL;
    $_SESSION['fb_name'] = NULL;
    $_SESSION['fb_email'] =  NULL;
header("Location: index.php");        // you can enter home page here ( Eg : header("Location: " ."http://www.krizna.com"); 
?>
