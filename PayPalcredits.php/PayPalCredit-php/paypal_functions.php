<?php

/* * ******************************************
  Module contains calls to PayPal APIs
 * ****************************************** */

if (session_id() == "")
    session_start();

require('paypal_config.php');

// Use values from config.php
$PROXY_HOST = PROXY_HOST;
$PROXY_PORT = PROXY_PORT;
$SandboxFlag = SANDBOX_FLAG;


if ($SandboxFlag) {  //API Credentials and URLs for Sandbox
    $API_UserName = PP_USER_SANDBOX;
    $API_Password = PP_PASSWORD_SANDBOX;
    $API_Signature = PP_SIGNATURE_SANDBOX;
    $API_Endpoint = PP_NVP_ENDPOINT_SANDBOX;
    $PAYPAL_URL = PP_CHECKOUT_URL_SANDBOX;
}
// else {  // API Credentials and URLs for Live
//   $API_UserName = PP_USER;
//   $API_Password = PP_PASSWORD;
 //  $API_Signature = PP_SIGNATURE;
//   $API_Endpoint = PP_NVP_ENDPOINT_LIVE;
//   $PAYPAL_URL = PP_CHECKOUT_URL_LIVE;
//}
// BN Code 	is only applicable for partners
$sBNCode = SBN_CODE;

$version = API_VERSION;


/*
 * Purpose: 	Prepares the parameters for the SetExpressCheckout API Call.
 * Inputs:  
 * 		parameterArray:     the item details, prices and taxes
 * 		returnURL:			the page where buyers return to after they are done with the payment review on PayPal
 * 		cancelURL:			the page where buyers return to when they cancel the payment review on PayPal
 */

function CallShortExpressCheckout($data, $returnURL, $cancelURL) {
    //------------------------------------------------------------------------------------------------------------------------------------
    // Construct the parameter string that describes the SetExpressCheckout API call in the shortcut implementation
    
//    die;
    $nvpstr = "&RETURNURL=" . $returnURL;
    $nvpstr = $nvpstr . "&CANCELURL=" . $cancelURL;
    $nvpstr = $nvpstr . "&ALLOWNOTE=1";
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_PAYMENTACTION=" . $data['PAYMENTREQUEST_0_PAYMENTACTION'];
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $data['PAYMENTREQUEST_0_CURRENCYCODE'];
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_AMT=" . $data['L_PAYMENTREQUEST_0_AMT0'];
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_ITEMAMT=" . $data['L_PAYMENTREQUEST_0_AMT0'];
    $nvpstr = $nvpstr . "&L_PAYMENTREQUEST_0_QTY0=" . $data['L_PAYMENTREQUEST_0_QTY0'];
    $nvpstr = $nvpstr . "&L_PAYMENTREQUEST_0_AMT0=" . $data['L_PAYMENTREQUEST_0_AMT0'];
    $nvpstr = $nvpstr . "&L_PAYMENTREQUEST_0_NAME0=" . $data['L_PAYMENTREQUEST_0_NAME0'];


    //'--------------------------------------------------------------------------------------------------------------- 
    //' Make the API call to PayPal
    //' If the API call succeded, then redirect the buyer to PayPal to begin to authorize payment.  
    //' If an error occured, show the resulting errors
    //'---------------------------------------------------------------------------------------------------------------
    $resArray = hash_call("SetExpressCheckout", $nvpstr);
    $ack = strtoupper($resArray["ACK"]);
//    echo '<pre>';
//    print_r($resArray);
//    die;
    if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
        return $resArray;
    }
}

/* Purpose: 	
 * Prepares the parameters for the GetExpressCheckoutDetails API Call.
 * Inputs:  None
 * Returns: The NVP Collection object of the GetExpressCheckoutDetails Call Response.
 */

function GetShippingDetails($token) {
    /*
     * Build a second API request to PayPal, using the token as the
     *  ID to get the details on the payment authorization
     */
    $nvpstr = "&TOKEN=" . $token;

    /*
     * Make the API call and store the results in an array.  
     * If the call was a success, show the authorization details, and provide an action to complete the payment.  
     * If failed, show the error
     */
    $resArray = hash_call("GetExpressCheckoutDetails", $nvpstr);
    $ack = strtoupper($resArray["ACK"]);
    if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
        $_SESSION['payer_id'] = $resArray['PAYERID'];
    }
    return $resArray;
}

/*
 * Purpose: 	Prepares the parameters for the DoExpressCheckoutPayment API Call.
 * Inputs:   FinalPaymentAmount:	The total transaction amount.
 * Returns: 	The NVP Collection object of the DoExpressCheckoutPayment Call Response.
 */

function ConfirmPayment($FinalPaymentAmt, $token, $payerID) {
    /* Gather the information to make the final call to finalize the PayPal payment.  The variable nvpstr
     * holds the name value pairs
     */

    //mandatory parameters in DoExpressCheckoutPayment call
    $token = urlencode($token);
    $paymentType = 'Sale';
    $currencyCodeType = 'USD';
    $payerID = urlencode($payerID);

    $serverName = urlencode($_SERVER['SERVER_NAME']);

    $nvpstr = '&TOKEN=' . $token . '&PAYERID=' . $payerID . '&PAYMENTREQUEST_0_PAYMENTACTION=' . $paymentType . '&PAYMENTREQUEST_0_AMT=' . $FinalPaymentAmt;
    $nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE=' . $currencyCodeType . '&IPADDRESS=' . $serverName;

    /* Make the call to PayPal to finalize payment
      If an error occured, show the resulting errors
     */
    $resArray = hash_call("DoExpressCheckoutPayment", $nvpstr);
    $ack = strtoupper($resArray["ACK"]);
    if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
//            print"<pre>";
//            print_r($resArray);
//            print"</pre>";
//            die;

        return $resArray;
    }
}

/*
 * hash_call: Function to perform the API call to PayPal using API signature
 * @methodName is name of API  method.
 * @nvpStr is nvp string.
 * returns an associtive array containing the response from the server.
 */

function hash_call($methodName, $nvpStr) {
    //declaring of global variables
    global $API_Endpoint, $version, $API_UserName, $API_Password, $API_Signature;
    global $USE_PROXY, $PROXY_HOST, $PROXY_PORT;
    global $gv_ApiErrorURL;
    global $sBNCode;
    //setting the curl parameters.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);

    //turning off the server and peer verification(TrustManager Concept).
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);

    //if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
    //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php 
    if ($USE_PROXY)
        curl_setopt($ch, CURLOPT_PROXY, $PROXY_HOST . ":" . $PROXY_PORT);

    //NVPRequest for submitting to server
    $nvpreq = "METHOD=" . urlencode($methodName) . "&VERSION=" . urlencode($version) . "&PWD=" . urlencode($API_Password) . "&USER=" . urlencode($API_UserName) . "&SIGNATURE=" . urlencode($API_Signature) . $nvpStr . "&BUTTONSOURCE=" . urlencode($sBNCode);

    //setting the nvpreq as POST FIELD to curl
    curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

    //getting response from server
    $response = curl_exec($ch);

    //convrting NVPResponse to an Associative Array
    $nvpResArray = deformatNVP($response);
    $nvpReqArray = deformatNVP($nvpreq);
    //print_r($nvpResArray);die;
    $_SESSION['nvpReqArray'] = $nvpReqArray;

    if (curl_errno($ch)) {
        // moving to display page to display curl errors
        $_SESSION['curl_error_no'] = curl_errno($ch);
        $_SESSION['curl_error_msg'] = curl_error($ch);

        //Execute the Error handling module to display errors. 
    } else {
        //closing the curl
        curl_close($ch);
    }

    return $nvpResArray;
}

/*
 * Purpose: Redirects to PayPal.com site.
 * Inputs:  NVP string.
 *  Returns: 
 */

function RedirectToPayPal($token) {
    global $PAYPAL_URL;

    // Redirect to paypal.com here
    // With useraction=commit user will see "Pay Now" on Paypal website and when user clicks "Pay Now" and returns to our website we can call DoExpressCheckoutPayment API without asking the user
    $payPalURL = $PAYPAL_URL . $token;

    header("Location:" . $payPalURL
    );
    exit;
}

/*
 * This function will take NVPString and convert it to an Associative Array and it will decode the response.
 * It is usefull to search for a particular key and displaying arrays.
 * @nvpstr is NVPString.
 * @nvpArray is Associative Array.
 */

function deformatNVP($nvpstr) {
    $intial = 0;
    $nvpArray = array();

    while (strlen($nvpstr)) {
        //postion of Key
        $keypos = strpos($nvpstr, '=');
        //position of value
        $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);

        /* getting the Key and Value values and storing in a Associative Array */
        $keyval = substr($nvpstr, $intial, $keypos);
        $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);
        //decoding the respose
        $nvpArray[urldecode($keyval)] = urldecode($valval);
        $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
    }
    return $nvpArray;
}

?>
