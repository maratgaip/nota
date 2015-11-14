<?php

/**
 * Copyright 2012 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

add_action( 'gd_quicksetup_install', 'gd_quicksetup_start_maintenance_mode', 0 );
add_action( 'gd_quicksetup_install_done', 'gd_quicksetup_stop_maintenance_mode', 999 );

/**
 * Put the site in maintenance mode
 * @global mixed $wp_filesystem
 */
function gd_quicksetup_start_maintenance_mode() {
	global $wp_filesystem;

	if ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	} elseif ( !empty ( $_SERVER['HTTP_X_REAL_IP'] ) ) {
		$ip = $_SERVER['HTTP_X_REAL_IP'];
	} else {
		$ip = $_SERVER['REMOTE_ADDR'];
	}
	
	$wp_filesystem->delete( $wp_filesystem->abspath() . '.maintenance' );
	$wp_filesystem->put_contents( $wp_filesystem->abspath() . '.maintenance', '<' . '?php $upgrading = ' . time()  . '; ?' . '>', FS_CHMOD_FILE );
	$maintenance = file_get_contents( GD_QUICKSETUP_DIR . '/resources/maintenance.php' );
	$maintenance = str_replace( '%REMOTE_ADDR%', $ip, $maintenance );
	$maintenance = str_replace( '%emergency_url%', add_query_arg( array( 'EMERGENCY' => '1' ), home_url() ), $maintenance );
	$wp_filesystem->put_contents( $wp_filesystem->wp_content_dir() . 'maintenance.php', $maintenance, FS_CHMOD_FILE );
}

/**
 * Take the site out of maintenance mode
 * @global mixed $wp_filesystem
 */
function gd_quicksetup_stop_maintenance_mode() {
	global $wp_filesystem;
	$wp_filesystem->delete( $wp_filesystem->abspath() . '.maintenance' );
	$wp_filesystem->delete( $wp_filesystem->wp_content_dir() . 'maintenance.php' );
}
