<?php

@session_start ();
$countryavail = $_GET['countryavail'];

if($countryavail !== true){
$contryId = $_GET["countryid"];
//echo $countryId;

$_SESSION['country_Id'] = $contryId;
echo $_SESSION['country_Id'];
}
else{
    
 session_destroy('country_Id'); 
 
 
}