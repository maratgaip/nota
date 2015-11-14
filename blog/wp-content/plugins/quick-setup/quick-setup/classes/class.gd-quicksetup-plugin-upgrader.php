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
class GD_QuickSetup_Plugin_Upgrader extends Plugin_Upgrader {

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
	 * Shortcut to activate_plugin.
	 * Calls the gd_quicksetup_installed_plugin-$plugin_slug hook
	 * @param string $plugin_slug
	 */
	public function activate_plugin() {
		$args = func_get_args();
		ob_start();
		call_user_func_array( 'activate_plugin', $args );
		$out = ob_get_contents();
		ob_end_clean();
		$slug = func_get_arg( 0 );
		$tmp  = explode( '/', $slug );
		do_action( 'gd_quicksetup_installed_plugin-' . $tmp[0], $out );
	}
}
