<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

/**
 * Talk to the API ... see what features, site types, etc. are available
 */
class GD_QuickSetup_API {

	/**
	 * Identify our plugin.
	 * Used in HTTP headers
	 * @var string
	 */
	private $_slug = 'quick-setup';

	/**
	 * List site types
	 * @return array|WP_Error
	 */
	public function get_site_types() {
		return $this->make_call( 'siteTypes' );
	}

	/**
	 * Check for an update to this plugin
	 * @param string $version
	 * @return array|WP_Error
	 */
	public function get_self_update() {
		return $this->make_call( 'updates/plugin/' . $this->_slug );
	}
	
	/**
	 * List themes
	 * @param string $site_type
	 * @return array|WP_Error
	 */
	public function get_themes( $site_type ) {
		return $this->make_call( 'themes/' . $site_type );
	}

	/**
	 * List features (step 3)
	 * @param string $site_type
	 * @param string $theme
	 * @return array|WP_Error
	 */
	public function get_features( $site_type, $theme ) {
		return $this->make_call( 'features/' . $site_type . '/' . $theme );
	}

	/**
	 * Get setup instructions (plugins, themes, etc.)
	 * @param string $site_type
	 * @param string $theme
	 * @return array|WP_Error
	 */
	public function get_setup_instructions( $site_type, $theme ) {
		
		// Create a list of pages the user has enabled
		$enabled = '';
		if ( isset( $_POST['enabled'] ) ) {
			$enabled = $_POST['enabled'];
			unset( $enabled['custom_page_{{idx}}'] );
			$enabled = implode( ',', array_keys( $enabled ) );
		}

		// Create a list of the extra plugins we need
		$plugins = array();
		if ( isset( $_POST['share'] ) && !empty( $_POST['share'] ) ) {
			$plugins[] = 'share';
		}
		if ( isset( $_POST['extra_plugins'] ) ) {
			foreach ( $_POST['extra_plugins'] as $k => $v ) {
				$plugins[] = $k;
			}
		}
		$plugins = implode( ',', $plugins );

		// Contact the API
		return $this->make_call( 'setupInstructions/' . $site_type . '/' . $theme . '/pages=' . $enabled . '/plugins=' . $plugins );
	}

	/**
	 * Get the arguments to pass into wp_remote_get or wp_remote_post
	 * @global string $wp_version
	 * @global mixed $wpdb
	 * @return array
	 */
	protected function get_args() {
		global $wp_version, $wpdb;
		$options = get_option( 'gd_quicksetup_options' );
		if ( !empty( $options['key'] ) ) {
			$skin = '';
			if ( function_exists( 'cyberchimps_get_option' ) ) {
				$skin = cyberchimps_get_option( 'cyberchimps_skin_color', '' );
			}
			return array(
				'headers'   => array(
					'X-Plugin-Api-Key'        => $options['key'],
					'X-Plugin-Theme'          => wp_get_theme()->get_stylesheet(),
					'X-Plugin-Theme-Version'  => wp_get_theme()->get( 'Version' ),
					'X-Plugin-Theme-Skin'     => $skin,
					'X-Plugin-URL'            => get_home_url(),
					'X-Plugin-WP-Version'     => $wp_version,
					'X-Plugin-Plugins'        => json_encode( get_option( 'active_plugins' ) ),
					'X-Plugin-MySQL-Version'  => $wpdb->db_version(),
					'X-Plugin-PHP-Version'    => PHP_VERSION,
					'X-Plugin-Locale'         => get_locale(),
					'X-Plugin-WP-Lang'        => ( defined( 'WP_LANG' ) ? WP_LANG : 'en_US' ),
					'X-Plugin-Version'        => get_option( 'gd_quicksetup_version' ),
					'X-Plugin-Slug'           => $this->_slug,
				)
			);
		}
		return array(
				'headers'   => array(
					'X-Plugin-Api-Key'        => '',
					'X-Plugin-Theme'          => '',
					'X-Plugin-Theme-Version'  => '',
					'X-Plugin-Theme-Skin'     => '',
					'X-Plugin-URL'            => '',
					'X-Plugin-WP-Version'     => $wp_version,
					'X-Plugin-Plugins'        => json_encode( array() ),
					'X-Plugin-MySQL-Version'  => $wpdb->db_version(),
					'X-Plugin-PHP-Version'    => PHP_VERSION,
					'X-Plugin-Locale'         => get_locale(),
					'X-Plugin-WP-Lang'        => ( defined( 'WP_LANG' ) ? WP_LANG : 'en_US' ),
					'X-Plugin-Version'        => get_option( 'gd_quicksetup_version' ),
					'X-Plugin-Slug'           => $this->_slug,
				)
			);
	}

	/**
	 * Talk to the API endpoint
	 * @param string $method
	 * @param array $args
	 * @param string $verb
	 * @return array|WP_Error
	 */
	protected function make_call( $method, $args = array(), $verb = 'GET' ) {
		$options     = get_option( 'gd_quicksetup_options' );
		$max_retries = 1;
		$retries     = 0;
		if ( !in_array( $verb, array( 'GET', 'POST' ) ) ) {
			return new WP_Error( 'gd_quicksetup_api_bad_verb', sprintf( __( 'Unknown verb: %s. Try GET or POST', 'gd_quicksetup' ), $verb ) );
		}
		while ( $retries <= $max_retries ) {
			$retries++;
			if ( 'GET' === $verb ) {
				$url = $options['api_url'] . $method;
				if ( !empty( $args ) ) {
					$url .= '?' . build_query( $args );
				}
				add_filter( 'https_ssl_verify', '__return_false' );
				$result = wp_remote_get( $url, $this->get_args() );
				remove_filter( 'https_ssl_verify', '__return_false' );
			} elseif ( 'POST' === $verb ) {
				$_args = $this->get_args();
				$_args['body'] = $args;
				add_filter( 'https_ssl_verify', '__return_false' );
				$result = wp_remote_post( $options['api_url'] . $method, $_args );
				remove_filter( 'https_ssl_verify', '__return_false' );
			}
			if ( is_wp_error( $result ) ) {
				break;
			} elseif ( self::_is_retryable_error( $result ) ) {	
				
				// The service is in a known maintenance condition, give a sec to recover
				sleep( apply_filters( 'gd_quicksetup_api_retry_delay', 1 ) );
				continue;
			} else {
				break;
			}
		}

		do_action( 'gd_quicksetup_api_debug_request', $options['api_url'] . $method, $args );
		do_action( 'gd_quicksetup_api_debug_response', $result );

		if ( !is_wp_error( $result ) && '200' != $result['response']['code'] ) {
			return new WP_Error( 'gd_quicksetup_api_bad_status', sprintf( __( 'API returned bad status: %d: %s', 'gd_quicksetup' ), $result['response']['code'], $result['response']['message'] ) );
		}

		return $result;
	}
	
	/**
	 * Check if the result of a wp_remote_* call is an error and should be retried
	 * @param array $result
	 * @return bool
	 */
	protected static function _is_retryable_error( $result ) {
		if ( is_wp_error( $result ) ) {
			return false;
		}
		if ( !isset( $result['response'] ) || !isset( $result['response']['code'] ) || 503 != $result['response']['code'] ) {
			return false;
		}
		$json = json_decode( $result['body'], true );
		if ( isset( $json['status'] ) && 503 == $json['status'] && isset( $json['type'] ) && 'error' == $json['type'] && isset( $json['code'] ) && 'RetryRequest' == $json['code'] ) {
			return true;
		}
		return false;
	}
}
