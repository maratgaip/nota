<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Respond with code 2 for ajax requests
if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest' ) {
	die('2');
}

// Rescue param
if ( isset( $_GET['EMERGENCY'] ) || isset( $_GET['emergency'] ) ) {	
	$file = rtrim( ABSPATH, '/' ) . '/.maintenance';
	if ( unlink( $file ) ) {
		unlink( __FILE__ );
	}
	$url = preg_replace( '/[?&]emergency=([^&])/i', '', $_SERVER['REQUEST_URI'] );
	$url = preg_replace( '/[?&]r=([^&]*)/i', '', $url );
	if ( false === strpos( $url, '?' ) ) {
		$url .= '?r=' . mt_rand();
	} else {
		$url .= '&r=' . mt_rand();
	}

	header( 'Location: ' . $url, true, 302 );
	die();
}

$protocol = $_SERVER['SERVER_PROTOCOL'];
if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol )
	$protocol = 'HTTP/1.0';
header( "$protocol 503 Service Unavailable", true, 503 );
header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Maintenance</title>

</head>
<body>
    <h1>Briefly unavailable for scheduled maintenance. Check back in a minute.</h1>
	<p>
		<?php if ( '%REMOTE_ADDR%' === $_SERVER['REMOTE_ADDR'] ) : ?>
			Click <a href="%emergency_url%&r=<?php echo mt_rand(); ?>">here</a> if your site gets stuck
		<?php endif; ?>
	</p>
</body>
</html>
<?php die(); ?>
