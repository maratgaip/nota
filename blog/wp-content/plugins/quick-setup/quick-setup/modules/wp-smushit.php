<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

// smushit can quit unexpectedly causing the last step of quick setup to hang indefinitely
// this has been disabled until a fix can be found

// add_action( 'gd_quicksetup_install_done',     'gd_quicksetup_setup_wp_smushit' );  // Run after content has been created
add_action( 'gd_quicksetup_installed_plugin-wp-smushit', 'gd_quicksetup_disable_smushit' );

/**
 * Disable smush it with an option
 */
function gd_quicksetup_disable_smushit() {
	update_option( 'wp_smushit_smushit_auto', time() + 300 );
}

/**
 * Smush attachments (e.g. gallery images that were just uploaded)
 */
function gd_quicksetup_setup_wp_smushit() {	
	$attachments = get_posts(
					array(
						'numberposts'    => -1,
						'post_type'      => 'attachment',
						'post_mime_type' => 'image',
					)
	);
	foreach ( $attachments as $attachment ) {
		$original_meta = wp_get_attachment_metadata( $attachment->ID, true );
		$meta = wp_smushit_resize_from_meta_data( $original_meta, $attachment->ID, false );
		wp_update_attachment_metadata( $attachment->ID, $meta );
		usleep( 500000 ); // 500ms
	}
}
