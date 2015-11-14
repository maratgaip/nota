<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

add_action( 'gd_quicksetup_installed_plugin-jetpack', 'gd_quicksetup_restore_options' );
add_filter( 'gd_quick_setup_reactivate_plugins',      'gd_quicksetup_save_jetpack' );

/**
 * Restore the jetpack settings to the DB
 */
function gd_quicksetup_restore_jetpack_settings() {
	if ( is_plugin_active( 'jetpack/jetpack.php' ) ) {
		foreach ( $GLOBALS['gd_quicksetup_jetpack_settings'] as $key => $val ) {
			update_option( $key, $val );
		}
	}
}

/**
 * Add jetpack to the active plugins list if it's currently active
 * @param array $active_plugins
 * @return array
 */
function gd_quicksetup_save_jetpack( $active_plugins ) {
	$GLOBALS['gd_quicksetup_jetpack_settings'] = array();
	if ( is_plugin_active( 'jetpack/jetpack.php' ) ) {
		foreach ( array( 'jetpack_active_modules', 'jetpack_options', 'jetpack_activated' ) as $key ) {
			$GLOBALS['gd_quicksetup_jetpack_settings'][ $key ] = get_option( $key );
		}
		$active_plugins[] = 'jetpack/jetpack.php';
	}
	return $active_plugins;
}
