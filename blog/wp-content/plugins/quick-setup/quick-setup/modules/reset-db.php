<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

add_action( 'gd_quicksetup_install', 'gd_quicksetup_reset_db' );
add_action( 'gd_quicksetup_install_done', 'gd_quicksetup_restore_object_cache' );

/**
 * Reset the database back to pristine condition
 * Reset the blog name / privacy setting
 * Reactivate our plugin, and whatever other plugins are caught in the filter
 * Save the db salts (to prevent loggint he user out)
 * Save the users (all non-subscribers)
 * Remove the default password nag (it isn't the default pw, it's just a clean DB)
 * Log the user back in
 * @filter gd_quick_setup_reactivate_plugins
 * @global mixed $wpdb
 * @global mixed $current_user
 */
function gd_quicksetup_reset_db() {
	global $wpdb, $current_user;

	require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

	// Uncache
	wp_cache_flush();
	if ( function_exists( 'wp_cache_init' ) ) {
		$GLOBALS['__wp_object_cache'] = $GLOBALS['wp_object_cache'];
		$GLOBALS['wp_object_cache']   = new GD_QuickSetup_ObjectCache();
	}

	// Don't send out the "your new WordPress site" e-mail
	add_filter( 'wp_mail', 'gd_quicksetup_cancel_new_site_email' );

	// Save the blog options
	$blogname    = get_option( 'blogname' );
	$blog_public = get_option( 'blog_public' );
	
	// Save the plugins
	$active_plugins = apply_filters( 'gd_quick_setup_reactivate_plugins', array() );
	$tmp = array();
	foreach ( $active_plugins as $plugin ) {
		if ( is_plugin_active( $plugin ) ) {
			$tmp[] = $plugin;
		}
	}
	$active_plugins = $tmp;

	// Save the salts
	$logged_in_salt = get_site_option( 'logged_in_salt' );
	$auth_salt = get_site_option( 'auth_salt' );

	// Save the admin user
	if ( $current_user->user_login != 'admin' ) {
		$user = get_user_by( 'login', 'admin' );
	}
	if ( ! isset( $user ) || $user->user_level < 10 ) {
		$user = $current_user;
	}
	
	// Save additional users
	$users = array();
	foreach ( get_users() as $_user ) {
		if ( $_user->ID == $user->ID ) {
			continue;
		}
		if ( user_can( $_user, 'edit_posts' ) ) {
			$users[] = $_user;
		}
	}

	// Nuke the DB
	$prefix = str_replace( '_', '\_', $wpdb->prefix );
	$tables = $wpdb->get_col( "SHOW TABLES LIKE '{$prefix}%'" );
	foreach ( $tables as $table ) {
		$wpdb->query( "DROP TABLE $table" );
	}

	// Reinstall
	$result = wp_install( $blogname, $user->user_login, $user->user_email, $blog_public );
	extract( $result, EXTR_SKIP );

	// Re-insert the admin user
	$query = $wpdb->prepare( "UPDATE $wpdb->users SET user_pass = %s, user_activation_key = '' WHERE ID = %d", $user->user_pass, $user->ID );
	$wpdb->query( $query );

	// Reset the salts
	update_site_option( 'logged_in_salt', $logged_in_salt );
	update_site_option( 'auth_salt', $auth_salt );

	// Disable the "you're using the default password" message
	if ( get_user_meta( $user->ID, 'default_password_nag' ) ) {
		update_user_meta( $user->ID, 'default_password_nag', false );
	}
	if ( get_user_meta( $user->ID, $wpdb->prefix . 'default_password_nag' ) ) {
		update_user_meta( $user->ID, $wpdb->prefix . 'default_password_nag', false );
	}
	
	// Re-insert the other users && disable the "you're using the default password" message
	foreach ( $users as $_user ) {
		$_user_id = wp_insert_user(
						array(
							'user_login' => $_user->user_login,
							'user_pass'  => $_user->user_pass,
							'user_email' => $_user->user_email,
						)
		);
		if ( is_wp_error( $_user_id ) ) {
			continue;
		}
		$query = $wpdb->prepare( "UPDATE $wpdb->users SET user_pass = %s, user_activation_key = '' WHERE ID = %d", $_user->user_pass, $_user_id );
		$wpdb->query( $query );
		update_user_meta( $_user_id, $wpdb->prefix . 'capabilities', $_user->caps );
		update_user_meta( $_user_id, $wpdb->prefix . 'user_level', gd_quicksetup_translate_role_level( $_user ) );
		if ( get_user_meta( $_user_id, 'default_password_nag' ) ) {
			update_user_meta( $_user_id, 'default_password_nag', false );
		}
		if ( get_user_meta( $_user_id, $wpdb->prefix . 'default_password_nag' ) ) {
			update_user_meta( $_user_id, $wpdb->prefix . 'default_password_nag', false );
		}
	}
	
	// Reset the salts
	update_site_option( 'logged_in_salt', $logged_in_salt );
	update_site_option( 'auth_salt', $auth_salt );
	
	// Remove sample content
	wp_delete_comment( 1, true );
	wp_delete_post( 1, true );
	wp_delete_post( 2, true );
	
	// Log the user back in
	// wp_clear_auth_cookie();
	wp_set_current_user( $user->ID );
	// wp_set_auth_cookie( $user_id );
	
	// Reactivate the plugins
	foreach ( $active_plugins as $plugin ) {
		activate_plugin( $plugin );
		$tmp  = explode( '/', $plugin );
		do_action( 'gd_quicksetup_installed_plugin-' . $tmp[0] );
	}
}

/**
 * Don't send the welcome e-mail
 * @param type $args
 * @return array
 */
function gd_quicksetup_cancel_new_site_email( $args ) {
	if ( __( 'New WordPress Site' ) === $args['subject'] ) {
		$args = array(
			'to'          => '',
			'subject'     => '',
			'message'     => '',
			'headers'     => '',
			'attachments' => array()
		);
	}
	return $args;
}

/**
 * Translate the user level to an int
 * @param WP_User $user
 * @return int
 */
function gd_quicksetup_translate_role_level( $user ) {
	if ( $user->has_cap( 'administrator' ) ) {
		return 10;
	} elseif ( $user->has_cap( 'editor' ) ) {
		return 7;
	} elseif ( $user->has_cap( 'author' ) ) {
		return 4;
	} if ( $user->has_cap( 'contributor' ) ) {
		return 1;
	} if ( $user->has_cap( 'subscriber' ) ) {
		return 0;
	}
	return 0;
}

/**
 * Restore the object cache, but flush it
 */
function gd_quicksetup_restore_object_cache() {
	if ( isset( $GLOBALS['__wp_object_cache'] ) && $GLOBALS['__wp_object_cache'] instanceof WP_Object_Cache ) {
		$GLOBALS['wp_object_cache'] = $GLOBALS['__wp_object_cache'];
		wp_cache_flush();
	}
}
