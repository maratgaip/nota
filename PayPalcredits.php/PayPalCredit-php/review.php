<?php 
	/*
	* Call to GetExpressCheckoutDetails and DoExpressCheckoutPayment APIs
	*/

	require_once ("paypal_functions.php"); 

	/*
	* The paymentAmount is the total value of the shopping cart(in real apps), here it was set 
    * in paypalfunctions.php in a session variable 
	*/
	
	
	$_SESSION['payer_id'] =	$_GET['PayerID'];


	// Check to see if the Request object contains a variable named 'token'	
	$token = "";

	if (isset($_REQUEST['token']))
	{
		$token = $_REQUEST['token'];
               
	}

	// If the Request object contains the variable 'token' then it means that the user is coming from PayPal site.	
	if ( $token != "" )
	{

		/*
		* Calls the GetExpressCheckoutDetails API call
		*/
		$resArrayGetExpressCheckout = GetShippingDetails( $token );
		$ackGetExpressCheckout = strtoupper($resArrayGetExpressCheckout["ACK"]);	 
		if( $ackGetExpressCheckout == "SUCCESS" || $ackGetExpressCheckout == "SUCESSWITHWARNING") 
		{
			/*
			* The information that is returned by the GetExpressCheckoutDetails call should be integrated by the partner into his Order Review 
			* page		
			*/
			$email 				= $resArrayGetExpressCheckout["EMAIL"]; // ' Email address of payer.
			$payerId 			= $resArrayGetExpressCheckout["PAYERID"]; // ' Unique PayPal customer account identification number.
			$payerStatus		= $resArrayGetExpressCheckout["PAYERSTATUS"]; // ' Status of payer. Character length and limitations: 10 single-byte alphabetic characters.
			$firstName			= $resArrayGetExpressCheckout["FIRSTNAME"]; // ' Payer's first name.
			$lastName			= $resArrayGetExpressCheckout["LASTNAME"]; // ' Payer's last name.
			$cntryCode			= $resArrayGetExpressCheckout["COUNTRYCODE"]; // ' Payer's country of residence in the form of ISO standard 3166 two-character country codes.
			$shipToName			= $resArrayGetExpressCheckout["PAYMENTREQUEST_0_SHIPTONAME"]; // ' Person's name associated with this address.
			$shipToStreet		= $resArrayGetExpressCheckout["PAYMENTREQUEST_0_SHIPTOSTREET"]; // ' First street address.
			$shipToCity			= $resArrayGetExpressCheckout["PAYMENTREQUEST_0_SHIPTOCITY"]; // ' Name of city.
			$shipToState		= $resArrayGetExpressCheckout["PAYMENTREQUEST_0_SHIPTOSTATE"]; // ' State or province
			$shipToCntryCode	= $resArrayGetExpressCheckout["PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE"]; // ' Country code. 
			$shipToZip			= $resArrayGetExpressCheckout["PAYMENTREQUEST_0_SHIPTOZIP"]; // ' U.S. Zip code or other country-specific postal code.
			$addressStatus 		= $resArrayGetExpressCheckout["ADDRESSSTATUS"]; // ' Status of street address on file with PayPal 
			$totalAmt   		= $resArrayGetExpressCheckout["PAYMENTREQUEST_0_AMT"]; // ' Total Amount to be paid by buyer
			$currencyCode       = $resArrayGetExpressCheckout["CURRENCYCODE"]; // 'Currency being used 
			/*
			* Add check here to verify if the payment amount stored in session is the same as the one returned from GetExpressCheckoutDetails API call
			* Checks whether the session has been compromised
			*/
			if($_SESSION["Payment_Amount"] != $totalAmt || $_SESSION["currencyCodeType"] != $currencyCode)
			exit("Parameters in session do not match those in PayPal API calls");
		} 
		else  
		{
			//Display a user friendly Error on the page using any of the following error information returned by PayPal
			$ErrorCode = urldecode($resArrayGetExpressCheckout["L_ERRORCODE0"]);
			$ErrorShortMsg = urldecode($resArrayGetExpressCheckout["L_SHORTMESSAGE0"]);
			$ErrorLongMsg = urldecode($resArrayGetExpressCheckout["L_LONGMESSAGE0"]);
			$ErrorSeverityCode = urldecode($resArrayGetExpressCheckout["L_SEVERITYCODE0"]);

			echo "GetExpressCheckoutDetails API call failed. ";
			echo "Detailed Error Message: " . $ErrorLongMsg;
			echo "Short Error Message: " . $ErrorShortMsg;
			echo "Error Code: " . $ErrorCode;
			echo "Error Severity Code: " . $ErrorSeverityCode;
		}
	}

include('header.php');
?>
<div class="span4">
</div>
<div class="span5">
<div class="hero-unit">
<h4>Shipping Address</h4>
			<p><?php echo $shipToName;		?><br>
			   <?php echo $shipToStreet;	?><br>
			   <?php echo $shipToCity;		?><br>
			   <?php echo $shipToState;	?><br>
			   <?php echo $shipToCntryCode;?><br>
			   <?php echo $shipToZip;		?><br></p>
<h4>Billing Address</h4>
			<p><?php echo $shipToName;		?><br>
			   <?php echo $shipToStreet;	?><br>
			   <?php echo $shipToCity;		?><br>
			   <?php echo $shipToState;	?><br>
			   <?php echo $shipToCntryCode;?><br>
			   <?php echo $shipToZip;		?></p>
			
			<p>Total Amount: <?php echo $totalAmt ?></p>
			<p>Currency Code:<?php echo $currencyCode ?></p>
			<form action="confirm.php" name="order_confirm" method="POST">
				Shipping methods: <select name="shipping_method" id="shipping_method" style="width: 250px;" class="required-entry">
					<optgroup label="United Parcel Service" style="font-style:normal;">
					<option value="ups_XPD">
					Worldwide Expedited - $8.00</option>
					<option value="ups_WXS" selected="selected">
					Worldwide Express Saver - $5.00</option>
					</optgroup>
					<optgroup label="Flat Rate" style="font-style:normal;">
					<option value="flatrate_flatrate">
					Fixed - $2.00</option>
					</optgroup>
					</select><br>
				<input type="submit" class ="btn btn-primary btn-large" value="Place Order"></input>
			</form>
			
</div>
</div>
<div class="span3">
</div>
<?php include('footer.php'); ?>