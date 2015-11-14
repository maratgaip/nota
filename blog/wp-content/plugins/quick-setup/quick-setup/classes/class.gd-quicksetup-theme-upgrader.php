<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

/**
 * Go Daddy Quick Setup Plugin Upgrader
 *
 * This extends the default plugin upgrader to ensure that only wordpress.org
 * packages are allowed to comply with repository guidelines.
 */
class GD_QuickSetup_Theme_Upgrader extends Theme_Upgrader {

	/**
	 * If a requested theme/plugin is from wordpress.org -OR- from us, let it through
	 * @param array $options
	 */
	public function run( $options ) {
		$_options = get_option( 'gd_quicksetup_options' );
		if ( preg_match( '/(?:(\.)?wordpress\.org|' . preg_quote( parse_url( $_options['api_url'], PHP_URL_HOST ) ) . ')$/', parse_url( $options['package'], PHP_URL_HOST ) ) ) {
			
			// Some older servers don't have our SSL certs in their bundle
			add_filter('https_ssl_verify', '__return_false');
			parent::run( $options );
			remove_filter('https_local_ssl_verify', '__return_false');
		}
	}

	/**
	 * Shortcut to switch_theme.  May be wrapped later.
	 * Calls the gd_quicksetup_switched_plugin-$theme_slug hook
	 * Beware ... this hook is a bit useless since the theme isn't included yet, best
	 * to hook gd_quicksetup_after_install_done instead
	 * @param string $theme_slug
	 */
	public function switch_theme() {
		$args = func_get_args();
		call_user_func_array( 'switch_theme', $args );
		$slug = func_get_arg( 0 );
		$tmp  = explode( '/', $slug );
		do_action( 'gd_quicksetup_switched_theme-' . $tmp[0] );
	}
}
