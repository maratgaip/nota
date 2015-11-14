<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

// This fires via ajax, after the theme has been switched.  Since it's
// on a second page load, the theme has been included
add_action( 'gd_quicksetup_after_install_done', 'gd_quicksetup_setup_cyberchimps_theme', 10 );
add_action( 'gd_quicksetup_after_install_done', 'gd_quicksetup_change_cyberchimps_skin', 20 );
add_action( 'gd_quicksetup_after_install_done', 'gd_quicksetup_change_theme_support_link', 20 );

// Theme specific calls
add_action( 'gd_quicksetup_after_install_done', 'gd_quicksetup_setup_bizcard_theme', 30 );
add_action( 'gd_quicksetup_after_install_done', 'gd_quicksetup_setup_gallery_theme', 30 );
add_action( 'gd_quicksetup_after_install_done', 'gd_quicksetup_setup_vintage_theme', 30 );
add_action( 'gd_quicksetup_after_install_done', 'gd_quicksetup_setup_featured_image', 30 );

/**
 * Set up the theme.  If it's not a cyberchimps theme, just continue on.
 * @return null
 */
function gd_quicksetup_setup_cyberchimps_theme() {	

	// Make sure we're using a cyberchimps theme
	if ( !function_exists( 'cyberchimps_get_default_values' ) ) {
		return;
	}

	// Get $_POST data back
	$data = get_option( 'gd_quicksetup_last_post' );
	if ( empty( $data ) ) {
		return;
	}
	
	// Get default options
	$option_defaults = get_option( 'cyberchimps_options' );
	if ( empty( $option_defaults ) ) {
		$option_defaults = cyberchimps_get_default_values();
	}
	
	// Set up social buttons
	if ( !isset( $option_defaults['theme_backgrounds'] ) || empty( $option_defaults['theme_backgrounds'] ) ) {
		$option_defaults['theme_backgrounds'] = 'default';
	}
	$option_defaults['blog_section_order'] = array( 'blog_post_page' );
	$option_defaults['social_twitter']     = '';
	$option_defaults['social_facebook']    = '';
	$option_defaults['social_google']      = '';
	
	// Add user selected social networks
	foreach ( (array) $data['type'] as $k => $v ) {
		if ( !$data['enabled'][$k] || 'false' === $_POST['enabled'][$k] ) {
			continue;
		}
		if ( 'contact' === $v ) {
			if ( !empty( $data['contact_twitter'][$k] ) ) {
				$option_defaults['social_twitter'] = '1';
				$option_defaults['twitter_url']    = 'http://twitter.com/' . ltrim( stripslashes_deep( $data['contact_twitter'][$k], '@' ) );
			}
			if ( !empty( $data['contact_facebook'][$k] ) ) {
				$option_defaults['social_facebook'] = '1';
				$option_defaults['facebook_url']    = stripslashes_deep( $data['contact_facebook'][$k] );
			}
			if ( !empty( $data['contact_googleplus'][$k] ) ) {
				$option_defaults['social_google'] = '1';
				$option_defaults['google_url']    = stripslashes_deep( $data['contact_googleplus'][$k] );
			}
			if ( !empty( $data['contact_email'][$k] ) ) {
				$option_defaults['profile_picture'] = 'http://www.gravatar.com/avatar/' . md5( strtolower( trim( $data['contact_email'][$k] ) ) ) . '.jpg?s=250&r=g';
			}
			break;
		}
	}
	
	// Header section
	$option_defaults['header_section_order'] = array(
		'cyberchimps_logo',
	);

	// Toggle blog description off
	$option_defaults['description_toggle'] = '';

	// Turn off footer widgets
	$option_defaults['footer_show_toggle'] = '';

	// Save options
	update_option( 'cyberchimps_options', $option_defaults );
	
	// Set all posts of type "page" to full width, except for business card
	if ( 'gdbizcard' === wp_get_theme()->get_stylesheet() ) {
		return;
	}
	$posts = get_posts(
				array(
					'post_type'   => 'page',
					'post_status' => 'any',
					'numberposts' => -1
				)
	);
	foreach ( (array) $posts as $post ) {
		update_post_meta( $post->ID, 'cyberchimps_page_sidebar', 'full_width' );
	}
}

/**
 * Set up the business card profile for the gdbizcard theme.
 * If it's not the correct theme, just continue on.
 * @return null
 */
function gd_quicksetup_setup_bizcard_theme() {

	// Make sure we're using the gdbizcard theme
	if ( 'gdbizcard' !== wp_get_theme()->get_stylesheet() ) {
		return;
	}

	// Get $_POST data back
	$data = get_option( 'gd_quicksetup_last_post' );
	if ( empty( $data ) ) {
		return;
	}
	
	// Get default options
	$option_defaults = get_option( 'cyberchimps_options' );
	
	// Copy the social sharing to the business card profile
	foreach ( (array) $data['type'] as $k => $v ) {
		if ( !$data['enabled'][$k] || 'false' === $_POST['enabled'][$k] ) {
			continue;
		}
		if ( 'contact' === $v ) {
			if ( !empty( $data['contact_name'][$k] ) ) {
				$option_defaults['profile_name'] = stripslashes_deep( $data['contact_name'][$k] );
			}
			if ( !empty( $data['contact_twitter'][$k] ) ) {
				$option_defaults['profile_twitter']     = '1';
				$option_defaults['profile_twitter_url'] = 'http://twitter.com/' . ltrim( stripslashes_deep( $data['contact_twitter'][$k], '@' ) );
			}
			if ( !empty( $data['contact_facebook'][$k] ) ) {
				$option_defaults['profile_facebook']     = '1';
				$option_defaults['profile_facebook_url'] = stripslashes_deep( $data['contact_facebook'][$k] );
			}
			if ( !empty( $data['contact_googleplus'][$k] ) ) {
				$option_defaults['profile_google']     = '1';
				$option_defaults['profile_google_url'] = stripslashes_deep( $data['contact_googleplus'][$k] );
			}
			if ( !empty( $data['contact_phone'][$k] ) ) {
				$option_defaults['profile_phone'] = stripslashes_deep( $data['contact_phone'][$k] );
			}
			if ( !empty( $data['contact_city'][$k] ) ) {
				$option_defaults['profile_location'] = stripslashes_deep( $data['contact_city'][$k] );
			}
			if ( !empty( $data['contact_state'][$k] ) ) {
				if ( empty( $option_defaults['profile_location'] ) ) {
					$option_defaults['profile_location'] = stripslashes_deep( $data['contact_state'][$k] );
				} else {
					$option_defaults['profile_location'] .= ', ' . stripslashes_deep( $data['contact_state'][$k] );
				}
			}
			break;
		}
	}
	
	// Website
	$option_defaults['profile_website'] = get_home_url();
	
	// Save options
	update_option( 'cyberchimps_options', $option_defaults );
	
	// Add the business card profile to the "About" page
	foreach ( (array) $data['type'] as $k => $v ) {
		if ( !$data['enabled'][$k] || 'false' === $_POST['enabled'][$k] ) {
			continue;
		}
		if ( 'page' === $v && 'about' === $k ) {
			$title = stripslashes_deep( $data['title'][$k] );
			if ( empty( $title ) ) {
				$title = __( 'Untitled', 'gd_quicksetup' );
			}
			$post = get_page_by_title( $title );
			update_post_meta( $post->ID, 'cyberchimps_page_section_order', array( 'profile', 'page_section' ) );
			break;
		}
	}
}

/**
 * Change the theme's skin
 */
function gd_quicksetup_change_cyberchimps_skin() {

	// Make sure we're using a cyberchimps theme
	if ( !function_exists( 'cyberchimps_get_default_values' ) ) {
		return;
	}

	// Get api result data back
	$data = get_transient( 'gd_quicksetup_last_api_response' );
	if ( empty( $data ) ) {
		return;
	}

	// Get default options
	$option_defaults = get_option( 'cyberchimps_options' );
	
	// Update skin
	if ( isset( $data['theme']['options'] ) && isset( $data['theme']['options']['skin'] ) ) {
		$skin = $data['theme']['options']['skin'];
		if ( file_exists( get_stylesheet_directory() . '/inc/css/skins/'.$skin.'.css' ) ) {
			$option_defaults['cyberchimps_skin_color'] = $skin;
			
		}
	}
	
	// Update typography
	if ( isset( $data['theme']['options'] ) && isset( $data['theme']['options']['typography_options'] ) ) {
		if ( isset( $data['theme']['options']['typography_options']['size'] ) ) {
			$option_defaults['typography_options']['size'] = stripslashes_deep( $data['theme']['options']['typography_options']['size'] );
		}
		if ( isset( $data['theme']['options']['typography_options']['face'] ) ) {
			$option_defaults['typography_options']['face'] = stripslashes_deep( $data['theme']['options']['typography_options']['face'] );
		}
		if ( isset( $data['theme']['options']['typography_options']['style'] ) ) {
			$option_defaults['typography_options']['style'] = stripslashes_deep( $data['theme']['options']['typography_options']['style'] );
		}
		if ( isset( $data['theme']['options']['typography_options']['color'] ) ) {
			$option_defaults['typography_options']['color'] = stripslashes_deep( $data['theme']['options']['typography_options']['color'] );
		}
	}
	
	// Save
	update_option( 'cyberchimps_options', $option_defaults );
}

/**
 * Change the theme's support link
 */
function gd_quicksetup_change_theme_support_link() {

	// Make sure we're using a cyberchimps theme
	if ( !function_exists( 'cyberchimps_get_default_values' ) ) {
		return;
	}

	// Get default options
	$option_defaults = get_option( 'cyberchimps_options' );
	
	// Update support link
	$options = get_option( 'gd_quicksetup_options' );
	$url     = 'http://x.co/quicksetup';
	if ( isset( $option_defaults['admin'] ) ) {
		$option_defaults['admin']['support_url'] = $url;
		$option_defaults['admin']['key'] = $options['key'];
	} else {
		$option_defaults['admin'] = array(
			'support_url' => $url,
			'key'         => $options['key'],
		);
	}

	// Save
	update_option( 'cyberchimps_options', $option_defaults );
}

/**
 * Change the gallery style on the gallery template
 */
function gd_quicksetup_setup_gallery_theme() {

	// Make sure we're using the gdbizcard theme
	if ( 'gdgallery' !== wp_get_theme()->get_stylesheet() ) {
		return;
	}

	// Find the gallery page
	$post = get_page_by_title( __( 'Gallery', 'gd_quicksetup' ) );
	if ( !$post ) {
		$post = get_page_by_title( __( 'Home', 'gd_quicksetup' ) );
	}
	if ( !$post ) {
		return;
	}

	// 4 columns
	if ( preg_match( '/\[gallery\s+ids=\"([^"]+)\"\]/', $post->post_content, $matches ) ) {
		$post->post_content = str_replace( $matches[0], '[gallery columns=4 ids="' . $matches[1] . '"]', $post->post_content );
		wp_update_post( $post );
	}
}

/**
 * Change the header to "logo only" style on the vintage template
 */
function gd_quicksetup_setup_vintage_theme() {

	// Make sure we're using the gdbizcard theme
	if ( 'gdvintage' !== wp_get_theme()->get_stylesheet() ) {
		return;
	}

	// Get default options
	$option_defaults = get_option( 'cyberchimps_options' );
	
	// Header section
	$option_defaults['header_section_order'] = array(
		'cyberchimps_logo',
	);

	// Save
	update_option( 'cyberchimps_options', $option_defaults );	
}

/**
 * Add a featured post image to the user's blog post for some blog themes
 */
function gd_quicksetup_setup_featured_image() {

	// Get api result data back
	$response = get_transient( 'gd_quicksetup_last_api_response' );
	if ( empty( $response ) ) {
		return;
	}
	
	// Get the last post so we can figure out the site type
	$data = get_option( 'gd_quicksetup_last_post' );
	if ( empty( $data ) ) {
		return;
	}

	// Set featured post image for blog posts
	if ( isset( $response['theme']['options'] ) && isset( $response['theme']['options']['blog'] ) && isset( $response['theme']['options']['blog']['featured_image'] ) ) {
		$url = $response['theme']['options']['blog']['featured_image'];
		
		// Get the first blog post
		$posts = get_posts(
					array(
						'post_type'   => 'post',
						'post_status' => 'published',
						'numberposts' => 1
					)
		);

		// Set the featured image
		if ( is_array( $posts ) && !empty( $posts ) && $posts[0] instanceof WP_Post ) {
			gd_quicksetup_set_featured_image( $posts[0]->ID, $url );
		}
	}

	// Set featured post image for the home page
	if ( isset( $response['theme']['options'] ) && isset( $response['theme']['options']['home'] ) && isset( $response['theme']['options']['home']['featured_image'] ) ) {
		$url = $response['theme']['options']['home']['featured_image'];
		
		// Find the home page
		foreach ( (array) $data['home'] as $k => $v ) {
			if ( !$data['enabled'][$k] || 'false' === $data['enabled'][$k] ) {
				continue;
			}
			if ( 'page' === $data['type'][$k] && $v ) {
				$title = stripslashes_deep( $data['title'][$k] );
				if ( empty( $title ) ) {
					$title = __( 'Untitled', 'gd_quicksetup' );
				}
				$post = get_page_by_title( $title );
				if ( $post instanceof WP_Post ) {
					gd_quicksetup_set_featured_image( $post->ID, $url );
				}
				break;
			}
		}
	}
}

/**
 * Set a featured image for a post from a URL
 * @param int $post_id
 * @param string $url
 * @return true|WP_Error
 */
function gd_quicksetup_set_featured_image( $post_id, $url ) {

	// Don't complain about SSL cert errors
	add_filter( 'https_ssl_verify', '__return_false' );

	// Download file
	$tmp = download_url( $url );

	// Okay, start complaining about SSL cert errors again
	remove_filter( 'https_ssl_verify', '__return_false' );

	// Set variables for storage
	// fix file filename for query strings
	preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $url, $matches );
	$file_array['name']     = basename( $matches[0] );
	$file_array['tmp_name'] = $tmp;

	// If there's an error, bail
	if ( is_wp_error( $tmp ) ) {
		@unlink( $file_array['tmp_name'] );
		$file_array['tmp_name'] = '';
		return $tmp;
	}

	// Save it
	$thumb_id = media_handle_sideload( $file_array, 0 );
	if ( is_wp_error( $thumb_id ) ) {
		return $thumb_id;
	}

	// Set the featured image
	set_post_thumbnail( $post_id, $thumb_id );
	return true;
}
