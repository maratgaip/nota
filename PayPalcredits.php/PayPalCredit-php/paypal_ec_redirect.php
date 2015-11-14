<?php
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

include("paypal_functions.php");

//Call to SetExpressCheckout using the shopping parameters collected from the shopping form on index.php and few from config.php 
@session_start ();
if(isset($_GET["id"])){
//echo $_GET["id"];die;
$song_id = $_GET["id"];    
$_SESSION['song_id'] = $song_id;

 $currenturl = $_GET['currenturl'];
 $_SESSION['currenturl'] = $currenturl;
 
  $price = $_GET['price'];
 
//$returnURL = $_SESSION['currenturl'];  
$returnURL = 'http://kiandastream.globusapps.com/PayResult.php';
$cancelURL = $_SESSION['currenturl'];
$data = array(
    'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
    'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD',
    'PAYMENTREQUEST_0_DESC' => 'test EC payment',
    'PAYMENTREQUEST_0_PAYMENTREQUESTID' => '111',
    'L_PAYMENTREQUEST_0_NAME0' => 'songs',
    'L_PAYMENTREQUEST_0_AMT0' => "$price",
    'L_PAYMENTREQUEST_0_NUMBER0' => 'ABC123',
    'L_PAYMENTREQUEST_0_QTY0' => '1',
);
$resArray = CallShortExpressCheckout($data, $returnURL, $cancelURL);
//echo '<pre>';
//print_r($resArray);
//echo '<pre>';
//die;
$token = $resArray['TOKEN'];

if ($response) {
    $setExpressCheckout = $this->_redirect("https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=" . $token);
}
$ack = strtoupper($resArray["ACK"]);
if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {  //if SetExpressCheckout API call is successful
    RedirectToPayPal($resArray["TOKEN"]);
} else {
    //Display a user friendly Error on the page using any of the following error information returned by PayPal
    $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
    $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
    $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
    $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

    echo "SetExpressCheckout API call failed. ";
    echo "Detailed Error Message: " . $ErrorLongMsg;
    echo "Short Error Message: " . $ErrorShortMsg;
    echo "Error Code: " . $ErrorCode;
    echo "Error Severity Code: " . $ErrorSeverityCode;
}
}

elseif(isset($_GET["album_id"])){
//     echo $album_id;
    $album_id = $_GET["album_id"];
    $_SESSION['album_id'] = $album_id;
    
    $album_title = $_GET['title'];
    $_SESSION['title'] = $album_title;

   $currenturl = $_GET['currenturl'];
   $_SESSION['currenturl'] = $currenturl;
     $price = $_GET['price'];
   
$returnURL = 'http://kiandastream.globusapps.com/PayResult1.php';
$cancelURL = $_SESSION['currenturl'];
$data = array(
    'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
    'PAYMENTREQUEST_0_CURRENCYCODE' => 'USD',
    'PAYMENTREQUEST_0_DESC' => 'test EC payment',
    'PAYMENTREQUEST_0_PAYMENTREQUESTID' => '111',
    'L_PAYMENTREQUEST_0_NAME0' => 'album',
    'L_PAYMENTREQUEST_0_AMT0' => $price,
    'L_PAYMENTREQUEST_0_NUMBER0' => 'ABC123',
    'L_PAYMENTREQUEST_0_QTY0' => '1',
);

$resArray = CallShortExpressCheckout($data, $returnURL, $cancelURL);
//echo '<pre>';
//print_r($resArray);
//echo '<pre>';
//die;
$token = $resArray['TOKEN'];

if ($response) {
    $setExpressCheckout = $this->_redirect("https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=" . $token);
}
$ack = strtoupper($resArray["ACK"]);
if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {  //if SetExpressCheckout API call is successful
    RedirectToPayPal($resArray["TOKEN"]);
} else {
    //Display a user friendly Error on the page using any of the following error information returned by PayPal
    $ErrorCode = urldecode($resArray["L_ERRORCODE0"]);
    $ErrorShortMsg = urldecode($resArray["L_SHORTMESSAGE0"]);
    $ErrorLongMsg = urldecode($resArray["L_LONGMESSAGE0"]);
    $ErrorSeverityCode = urldecode($resArray["L_SEVERITYCODE0"]);

    echo "SetExpressCheckout API call failed. ";
    echo "Detailed Error Message: " . $ErrorLongMsg;
    echo "Short Error Message: " . $ErrorShortMsg;
    echo "Error Code: " . $ErrorCode;
    echo "Error Severity Code: " . $ErrorSeverityCode;
}
}
?>