<?php

//THIS IS THE FACEBOOK GRAPH API VERSION 2.0, THE MOST RECENT IS THE VERSION 2.1 I WAS HAVING A LOT OF TROUBLE WITH IT SO I'M USING THE 2.0
//HOWEVER, IS WORKING PERFECTLY. I'LL KEEP WORKING ON HAVING THE SDK 4.0 AND THE GRAPH API 2.1
require 'src/facebook.php';  // This is the facebook SDK

require 'functions.php'; //check user functions

$facebook = new Facebook(array(
  'appId'  => '305513519653069',   // Facebook App ID - THIS IS A TEST APP ID, CHANGE WITH PLANIT APP ID
  'secret' => '7d15bdfc44197c88c765c44dfd2d7acf',  // Facebook App Secret - THIS IS A TEST APP SECRET, CHANGE WITH PLANIT APP SECRET 
  'cookie' => true, 
));
$user = $facebook->getUser(); //getting the user

if ($user) {
  try {
    $user_profile = $facebook->api('/me');
    $fbuid = $user_profile['id'];                 // getting user id from facebook api
    $fbuname = $user_profile['username'];  // gettin user username from facebook api
    $fbfullname = $user_profile['name']; // getting user full name (name) from facebook api
    $fbemail = $user_profile['email'];    // getting user email from facebook api
  
  // PUTTING VARIABLES ON THE SESSION
      $_SESSION['fb_id'] = $fbuid;           
      $_SESSION['fb_username'] = $fbuname;
      $_SESSION['fb_name'] = $fbfullname;
      $_SESSION['fb_email'] =  $fbemail;
      checkuser($fbuid,$fbfullname,$fbemail);
    
  } catch (FacebookApiException $e) {
    error_log($e);
   $user = null;
  }
}

//IF THE USER IS LOGS IN SUCESSFULLY REDIRECT IT TO THE MAIN PAGE
if ($user) {

  header("Location: index.php");

}
//ELSE PROVIDE THE LOGIN URL / THIS ALSO RUNS WHEN THE USER CLICKS THE LOGIN BUTTON. 
else { 

 $loginUrl = $facebook->getLoginUrl(array('scope'=>'email',)); 
 header("Location: ".$loginUrl);

}
?>


