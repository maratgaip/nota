<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

/**
 * Upgrade plugins from a third party system
 */
class GD_QuickSetup_Upgrader {

	/**
	 * The target plugin's slug, according to WordPress
	 * @var string
	 */
	protected $_slug = '';
	
	/**
	 * Plugin data, fetched from an external API
	 * @var mixed
	 */
	protected $_api_data = null;
	
	/**
	 * Plugin data, read from get_plugin_data
	 * @var array
	 */
	protected $_plugin_data = null;
	
	/**
	 * A safe key for variants
	 * @var string
	 */
	protected $_key = '';

	/**
	 * API communicator
	 * @var GD_QuickSetup_API
	 */
	protected $_api = '';

	/**
	 * Class Constructor
	 * @return GD_QuickSetup_Upgrader
	 */
	public function __construct( $slug = '' ) {

		// Add data
		$this->_slug = $slug;
		$this->_key  = $slug;
		if ( false !== strpos( $this->_key, '/' ) ) {
			$tmp = explode( '/', $this->_key );
			$tmp = array_pop( $tmp );
			$this->_key = basename( strtolower( $tmp ), '.php' );
		}

		// Add hooks
		add_filter( 'admin_init', array( $this, 'init' ) );
		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'api_check' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
	}

	/**
	 * Initialize, fetch current plugin data and new plugin data
	 * @return void
	 */
	public function init() {
		$data = null;
		$path = GD_QUICKSETUP_DIR . '/quick-setup.php';
		if ( file_exists( $path ) ) {
			$data = get_plugin_data( $path );
		}
		$this->_plugin_data = $data;
		$this->_api_data    = $this->get_api_data( $data );
	}

	/**
	 * Set the API communicator
	 * @param GD_QuickSetup $api
	 */
	public function set_api( $api ) {
		$this->_api = $api;
	}

	/**
	 * Plugin information
	 * @param bool $false always false
	 * @param string $action the API function being performed
	 * @param object $args plugin arguments
	 * @return object $response the plugin info
	 */
	public function plugin_info( $false, $action, $args ) {
		if ( !isset( $args->slug ) || !isset( $this->_key ) || $args->slug != $this->_key ) {
			return $false;
		}
		if ( !isset( $this->_api_data ) ) {
			$this->init();
		}
		$response               = new stdClass();
		$response->slug         = $this->_key;
		$response->plugin_name  = $this->_api_data->plugin_name;
		$response->version      = $this->_api_data->new_version;
		$response->author       = $this->_api_data->author;
		$response->homepage     = $this->_api_data->homepage;
		$response->requires     = $this->_api_data->requires;
		$response->tested       = $this->_api_data->tested;
		$response->downloaded   = 0;
		$response->last_updated = $this->_api_data->last_updated;
		if ( isset( $this->_api_data->sections ) && !empty( $this->_api_data->sections ) ) {
			$response->sections = array_merge(
							array(
								'description' => $this->_api_data->description,
							),
							(array) $this->_api_data->sections
			);
		} else {
			$response->sections = array(
				'description' => $this->_api_data->description,
			);			
		}
		$response->download_link = $this->_api_data->package;		
		return $response;
	}
	
	/**
	 * Get latest plugin data from API
	 * Cached in a transient for 6 hours
	 * @return mixed
	 */
	protected function get_api_data() {
		$api_data = get_site_transient( $this->_key . '_api_data' );
		if ( empty( $api_data ) ) {
			$response = $this->_api->get_self_update();
			if ( is_wp_error( $response ) ) {
				$api_data = new stdClass();
			} else {
				$api_data = json_decode( $response['body'] );
			}
			set_site_transient( $this->_key . '_api_data', $api_data, 60 * 60 * 6 );
		}
		return $api_data;
	}

	/**
	 * Hook into the plugin update check
	 * @param mixed $transient
	 * @return mixed
	 */
	public function api_check( $transient ) {
		if ( !isset( $this->_api_data ) ) {
			$this->init();
		}
		if ( isset( $this->_api_data ) && isset( $this->_api_data->new_version ) && isset( $this->_plugin_data ) && isset( $this->_plugin_data['Version'] ) && 1 === version_compare( $this->_api_data->new_version, $this->_plugin_data['Version'] ) ) {
			$transient->response[ $this->_slug ] = $this->_api_data;
		}
		return $transient;
	}
}
