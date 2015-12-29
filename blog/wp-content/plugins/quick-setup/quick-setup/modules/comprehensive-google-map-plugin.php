<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

add_action( 'gd_quicksetup_installed_plugin-comprehensive-google-map-plugin',  'gd_quicksetup_setup_comprehensive_google_map_plugin' );

/**
 * Set up the comprehensive-google-maps plugin and corresponding "Location" page
 * Don't include a map if there's no address
 * @return null
 */
function gd_quicksetup_setup_comprehensive_google_map_plugin() {
	global $gd_quicksetup_plugin;
	$options = $gd_quicksetup_plugin->get_current_plugin_options();

	// Address
	$address = '';
	foreach ( (array) $_POST['type'] as $k => $v ) {
		if ( !$_POST['enabled'][$k] || 'false' === $_POST['enabled'][$k] ) {
			continue;
		}
		if ( 'contact' === $v ) {
			$name   = stripslashes_deep( $_POST['contact_name'][$k] );
			$street = stripslashes_deep( $_POST['contact_address'][$k] );
			$city   = stripslashes_deep( $_POST['contact_city'][$k] );
			$state  = stripslashes_deep( $_POST['contact_state'][$k] );
			$zip    = stripslashes_deep( $_POST['contact_zip'][$k] );
			$phone  = stripslashes_deep( $_POST['contact_phone'][$k] );
			if ( strlen( $street.$city.$state.$zip ) > 0 ) {
				$address = "$street $city, $state $zip";
			}
			break;
		}
	}
	if ( empty( $address ) && empty( $name ) && empty( $phone ) ) {
		return;
	}

	// Create a Location page
	$postid = wp_insert_post(
					array(
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
						'post_content'   => '',
						'post_name'      => 'location',
						'post_title'     => ( isset( $options['page_title'] ) ? $options['page_title'] : __( 'Location', 'gd_quicksetup' ) ),
						'post_type'      => 'page',
						'post_status'    => 'publish',
						'menu_order'     => 700,
					)
	);

	// Create a map shortcode
	if ( !empty( $address ) ) {
		$map_str = sprintf(
						"\n<p>[google-map-v3 width=\"500\" height=\"350\" zoom=\"14\" maptype=\"roadmap\" mapalign=\"left\" directionhint=\"false\" language=\"default\" poweredby=\"false\" maptypecontrol=\"true\" pancontrol=\"true\" zoomcontrol=\"true\" scalecontrol=\"true\" streetviewcontrol=\"true\" scrollwheelcontrol=\"false\" draggable=\"true\" tiltfourtyfive=\"false\" addmarkermashupbubble=\"false\" addmarkermashupbubble=\"false\" addmarkerlist=\"%s{}1-default.png{}%s\" bubbleautopan=\"true\" showbike=\"false\" showtraffic=\"false\" showpanoramio=\"false\"]</p>",
						$address,
						$name
		);
	}

	// Create a Location page
	wp_update_post(
					array(
						'ID'           => $postid,
						'post_content' => ( !empty( $name ) ? "<p><strong>$name</strong></p>\n" : '' ) . ( !empty( $address ) ? "<p>$street</p>\n<p>$city, $state $zip</p>\n" : '' ) . '<p>' . __( 'Phone:', 'gd_quicksetup' ) . ' ' . $phone . "</p>\n" . $map_str,
					)
	);
}
