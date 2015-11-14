<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

add_action( 'gd_quicksetup_installed_plugin-simple-social-buttons', 'gd_quicksetup_setup_simple_social_buttons' );

/**
 * Set up twitter/facebook/google+ buttons at the bottom of each post/page
 * @global SimpleSocialButtonsPR_Admin $_ssb_pr
 */
function gd_quicksetup_setup_simple_social_buttons() {
	global $_ssb_pr;
	
	if ( !isset( $_ssb_pr ) ) {
		$_ssb_pr = new SimpleSocialButtonsPR_Admin();
	}

	// Get twitter handle
	$twitter = '';
	foreach ( (array) $_POST['type'] as $k => $v ) {
		if ( !$_POST['enabled'][$k] || 'false' === $_POST['enabled'][$k] ) {
			continue;
		}
		if ( 'contact' === $v ) {
			$twitter = stripslashes_deep( $_POST['contact_twitter'][$k] );
			break;
		}
	}
	$twitter = ltrim( $twitter, '@' );
	
	$_ssb_pr->update_settings(
					array(
						'googleplus'      => ( 'on' === $_POST['share']['googleplus'] ) ? 1 : '',
						'fblike'          => ( 'on' === $_POST['share']['facebook'] )   ? 2 : '',
						'twitter'         => ( 'on' === $_POST['share']['twitter'] )? 3 : '',
						'pinterest'       => '',
						'beforepost'      => null,
						'afterpost'       => 1,
						'beforepage'      => null,
						'afterpage'       => 1,
						'beforearchive'   => null,
						'afterarchive'    => 1,
						'showfront'       => 1,
						'showcategory'    => 1,
						'showarchive'     => 1,
						'showtag'         => 1,
						'override_css'    => null,
						'twitterusername' => $twitter,
					)
	);
}
