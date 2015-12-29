<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

add_action( 'gd_quicksetup_install_done', 'gd_quicksetup_dismiss_pointers' );

/**
 * Turn off the "Do you want to run the quick setup wizard?" banner
 */
function gd_quicksetup_dismiss_pointers() {
	$dismissed   = array_filter( explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) ) );
	$dismissed[] = 'gd-quicksetup-start-wizard';
	update_user_meta( get_current_user_id(), 'dismissed_wp_pointers', implode( ',', $dismissed ) );
}
