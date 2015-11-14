<?php

//if posting only
if(isset($_POST['submit'])) {
	$return = array('type' => 'error');
	
	$to = 'enteryouremail@gmail.com'; // Change this line to your email.
	
	$name = isset($_POST['name']) ? trim($_POST['name']) : '';
	$email = isset($_POST['email']) ? trim($_POST['email']) : '';
	$message = isset($_POST['message']) ? trim($_POST['message']) : '';
	$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
	$subject = isset($_POST['subject']) && $_POST['subject'] ? trim($_POST['subject']) : 'Contact Form Submission';
	
	if($name && $email && $message && filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= "From: {$name} <{$email}>\r\n";
		
		if($phone) {
			$message .= ' <br /> Phone: ' . $phone;
		}
		
		@$send = mail($to, $subject, $message, $headers);
		
		if($send) {
			$return['type'] = 'success';
			$return['message'] = 'Email successfully sent.';
		} else {
			$return['message'] = 'Error sending email.';
		}
	} else {
		$return['message'] = 'Error validating email.';
	}
	
	die(json_encode($return));
}

?>