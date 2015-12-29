<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

// Save my settings, re-activate myself, restore my settings
add_filter( 'gd_quick_setup_reactivate_plugins', 'gd_quicksetup_setup_reactive_myself' );
add_action( 'gd_quicksetup_installed_plugin-quick-setup', 'gd_quicksetup_restore_options' );

/**
 * Add Go Daddy Quick Setup to the list of plugins to be re-activated after the database has been reset
 * @param $active_plugins
 * @return array
 */
function gd_quicksetup_setup_reactive_myself( $active_plugins ) {
	$GLOBALS['_gd_quicksetup_options_backup'] = get_option( 'gd_quicksetup_options' );
	$active_plugins[] = plugin_basename( trailingslashit( GD_QUICKSETUP_DIR ) . 'quick-setup.php' );
	return $active_plugins;
}

/**
 * Restore options
 */
function gd_quicksetup_restore_options() {
	update_option( 'gd_quicksetup_options', $GLOBALS['_gd_quicksetup_options_backup'] );
}

