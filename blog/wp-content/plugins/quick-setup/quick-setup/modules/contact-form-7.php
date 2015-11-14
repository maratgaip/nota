<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

add_action( 'gd_quicksetup_installed_plugin-contact-form-7', 'gd_quicksetup_setup_contact_form_7' );

/**
 * Set up the contact form 7 plugin.
 * Set up the default contact form with the user's e-mail address, and create a
 * contact page to contain the form.
 * @global type $gd_quicksetup_plugin
 * @return null
 */
function gd_quicksetup_setup_contact_form_7() {
	global $gd_quicksetup_plugin;
	$options = $gd_quicksetup_plugin->get_current_plugin_options();

	// Get email address
	$email = '';
	foreach ( (array) $_POST['type'] as $k => $v ) {
		if ( !$_POST['enabled'][$k] || 'false' === $_POST['enabled'][$k] ) {
			continue;
		}
		if ( 'contact' === $v ) {
			$email = sanitize_email( stripslashes_deep( $_POST['contact_email'][$k] ) );
			break;
		}
	}
	if ( empty( $email ) ) {
		return;
	}

	// Update the contact form
	$post_content = '';
	$posts = get_posts(
					array( 
						'post_type'   => 'wpcf7_contact_form',
						'numberposts' => 1,
					)
	);
	if ( class_exists( 'WPCF7_ContactForm' ) && is_array( $posts ) && !empty( $posts[0] ) && $posts[0] instanceof WP_Post ) {

		// Use Contact Form 7's API
		$post = $posts[0];
		$contact_form = new WPCF7_ContactForm( $post );

		// Add CAPTCHA
		$search = '[textarea your-message] </p>';
		$cid = rand( 0, 1000 );
		$contact_form->form = str_replace( $search, $search . "\n\n<p>Please enter the text below<br />\n    [captchac captcha-$cid]<br />\n    [captchar captcha-$cid]<br /></p>", $contact_form->form );

		// Change title
		$contact_form->title = ( isset( $options['page_title'] ) ? $options['page_title'] : __( 'Contact', 'gd_quicksetup' ) );
		
		if ( !empty( $email ) ) {
			$contact_form->mail['recipient'] = $email;
		}

		// Save
		$contact_form->save();

		// New tag for the contact page
		$post_content = '[contact-form-7 id="' . $post->ID . '" title="' . $post->post_title . '"]';
	}

	// Create a Contact page
	wp_insert_post(
					array(
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
						'post_content'   => $post_content,
						'post_name'      => 'contact',
						'post_title'     => ( isset( $options['page_title'] ) ? $options['page_title'] : __( 'Contact Us', 'gd_quicksetup' ) ),
						'post_type'      => 'page',
						'post_status'    => 'publish',
						'menu_order'     => 800,
					)
	);
}
