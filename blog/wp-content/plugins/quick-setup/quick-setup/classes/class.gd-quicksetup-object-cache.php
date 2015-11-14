<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

/**
 * This is a mock object cache that gets substituted for the real object cache
 * when the site is re-created.  This prevents misbehaved object caches from
 * causing problems with the site being rebuilt properly.
 */
class GD_QuickSetup_ObjectCache {
	
	/**
	 * All calls return false
	 * @param string $method
	 * @param array $args
	 * @return boolean (always false)
	 */
	public function __call( $method, $args ) {
		return false;
	}
}
