<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

add_action( 'gd_quicksetup_modules_init', 'gd_quicksetup_set_permalinks' );

/**
 * Set up permalinks, flush rewrite rules
 * @global type $wp_rewrite
 */
function gd_quicksetup_set_permalinks() {	
	global $wp_rewrite;
	$wp_rewrite->set_permalink_structure( '/%year%/%monthnum%/%postname%/' );
	flush_rewrite_rules();
}
