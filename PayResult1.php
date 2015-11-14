<?php

@session_start();

@ob_start ();

@ob_implicit_flush ( 0 );

@error_reporting ( E_ALL ^ E_NOTICE );

@ini_set ( 'display_errors', true );

@ini_set ( 'html_errors', false );

@ini_set ( 'error_reporting', E_ALL ^ E_NOTICE );

define ( 'ROOT_DIR', dirname ( __FILE__ ) );

define ( 'INCLUDE_DIR', ROOT_DIR . '/includes' );

include (INCLUDE_DIR . '/config.inc.php');

require_once INCLUDE_DIR . '/class/_class_mysql.php';

require_once INCLUDE_DIR . '/db.php';

require_once INCLUDE_DIR . '/member.php';

include("PayPalcredits.php/PayPalCredit-php/paypal_functions.php");

$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$final_actual_link = explode("=", $actual_link);
$token = explode("&", $final_actual_link[1]);
$finalToken = $token[0];

$shippingDetails = GetShippingDetails($finalToken);
$finalAmount = $shippingDetails['AMT'];
$payToken = $shippingDetails['TOKEN'];
$payerId = $shippingDetails['PAYERID'];
$confirmPayment = ConfirmPayment($finalAmount, $payToken, $payerId);
if ($confirmPayment['PAYMENTINFO_0_PAYMENTSTATUS'] == "Completed") {
    
       $db->query("INSERT INTO vass_transaction (trans_id,user_id,amount,created_on) VALUES ('".$confirmPayment['PAYMENTINFO_0_TRANSACTIONID']."','$payerId','".$confirmPayment['PAYMENTINFO_0_AMT']."','".$confirmPayment['PAYMENTINFO_0_ORDERTIME']."')"); 
    
     $myalbum = trim($_SESSION['title']);
    $myalbumfolder = $_SESSION['album_id'];
       $json['success'] = true;
       $json['album_id'] = $myalbumfolder;
       $json['album_title'] = $myalbum;
       
            print '
				<html>
                                     <head>
				        <title>Kiandastream</title>
				        <script language="Javascript" type="text/javascript">
                                    if(window.opener != null && !window.opener.closed)
                                       {
                                            window.opener.DownloadPayement.AlbumDownload(' . json_encode($json) . ');
                                                window.close();
                                                  }
                                             
				        </script>
                                        </head>
                                        <body></body>
                                        </html> ';
       
}
else{
       $json['success'] = false;
            print '
				<html>
                                     <head>
				        <title>Kiandastream</title>
				        <script language="Javascript" type="text/javascript">
                                    if(window.opener != null && !window.opener.closed)
                                       {
                                            window.opener.DownloadPayement.AlbumDownload(' . json_encode($json) . ');
                                                window.close();
                                                  }
                                             
				        </script>
                                        </head>
                                        <body></body>
                                        </html> ';
       
}
?>
