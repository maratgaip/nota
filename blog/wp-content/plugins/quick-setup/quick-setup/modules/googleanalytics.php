<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

add_action( 'gd_quicksetup_installed_plugin-googleanalytics', 'gd_quicksetup_setup_googleanalytics' );

/**
 * Set the google analytics tracking key
 * @return null
 */
function gd_quicksetup_setup_googleanalytics() {

	if ( !isset( $_POST['google_analytics_key'] ) || !preg_match( '/^UA\-[0-9]+\-[0-9]+$/', $_POST['google_analytics_key'] ) ) {
		deactivate_plugins( 'googleanalytics/googleanalytics.php' );
		return;
	}

	update_option( 'web_property_id', $_POST['google_analytics_key'] );
}
