<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

add_action( 'gd_quicksetup_installed_plugin-bad-behavior', 'gd_quicksetup_setup_bad_behavior' );
add_action( 'gd_quicksetup_install_plugins', 'gd_quicksetup_disable_bad_behavior' );

/**
 * Disable bad behavior by turning on the BB2_TEST constant
 */
function gd_quicksetup_disable_bad_behavior() {
	define( 'BB2_TEST', true );	
}

/**
 * Set up bad behavior, install the user's IP into the whitelist
 */
function gd_quicksetup_setup_bad_behavior() {
	$ip = $_SERVER['REMOTE_ADDR'];
	if ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif ( !empty( $_SERVER['HTTP_X_REAL_IP'] ) ) {
		$ip = $_SERVER['HTTP_X_REAL_IP'];
	}
	update_option(
					'bad_behavior_whitelist',
					array(
						'ip' => array( $ip ),
						'url' => array(),
						'useragent' => array(),
					)
	);
}
