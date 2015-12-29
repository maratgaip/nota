<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

/**
 * Download and install silently
 */
class GD_QuickSetup_Installer_Skin extends WP_Upgrader_Skin {
	public function before() {}
	public function after() {}
	public function header() {}
	public function footer() {}
	public function feedback() {}
}
