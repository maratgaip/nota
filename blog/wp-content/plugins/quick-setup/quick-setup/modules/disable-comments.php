<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

add_action( 'gd_quicksetup_installed_plugin-disable-comments', 'gd_quicksetup_setup_disable_comments' );

/**
 * Set up the disable-comments plugin
 * Don't allow comments on pages
 */
function gd_quicksetup_setup_disable_comments() {
	
	// Set up initial options
	$plugin = new Disable_Comments();
	
	// Disable comments on pages
	$option = get_option( 'disable_comments_options' );
	$option['disabled_post_types'] = array( 'page' );
	update_option( 'disable_comments_options', $option );
}
