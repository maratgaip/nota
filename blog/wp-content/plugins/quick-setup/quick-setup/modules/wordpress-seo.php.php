<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

add_action( 'gd_quicksetup_installed_plugin-wordpress-seo', 'gd_quicksetup_setup_wordpress_seo' );

/**
 * Set the site's twitter username
 * @return null
 */
function gd_quicksetup_setup_wordpress_seo() {

	// Get twitter handle
	$twitter = '';
	foreach ( (array) $_POST['type'] as $k => $v ) {
		if ( !$_POST['enabled'][$k] || 'false' === $_POST['enabled'][$k] ) {
			continue;
		}
		if ( 'contact' === $v ) {
			$twitter    = stripslashes_deep( $_POST['contact_twitter'][$k] );
			$googleplus = stripslashes_deep( $_POST['contact_googleplus'][$k] );
			break;
		}
	}
	$twitter = ltrim( $twitter, '@' );

	// User id
	$user_id = wp_get_current_user()->ID;
	
	// Save the twitter settings in WP SEO and in the user's profile
	if ( !empty( $twitter ) ) {
		$options                 = get_option( 'wpseo_social', array() );
		$options['twitter']      = 'on';
		$options['twitter_site'] = $twitter;
		update_option( 'wpseo_social', $options );
		update_user_meta( $user_id, 'twitter', $twitter );
	}

	// Save the google plus settings in the user's profile, this will
	// enable the rel="author" meta tag
	if ( !empty( $googleplus ) ) {
		update_user_meta( $user_id, 'googleplus', $googleplus );
	}
}
