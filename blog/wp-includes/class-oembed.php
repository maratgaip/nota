<?php
/**
 * API for fetching the HTML to embed remote content based on a provided URL.
 * Used internally by the {@link WP_Embed} class, but is designed to be generic.
 *
 * @link http://codex.wordpress.org/oEmbed oEmbed Codex Article
 * @link http://oembed.com/ oEmbed Homepage
 *
 * @package WordPress
 * @subpackage oEmbed
 */

/**
 * oEmbed class.
 *
 * @package WordPress
 * @subpackage oEmbed
 * @since 2.9.0
 */
class WP_oEmbed {
	public $providers = array();
	public static $early_providers = array();

	/**
	 * Constructor
	 *
	 * @since 2.9.0
	 */
	public function __construct() {
		$providers = array(
			'#http://(www\.)?youtube\.com/watch.*#i'              => array( 'http://www.youtube.com/oembed',                      true  ),
			'#https://(www\.)?youtube\.com/watch.*#i'             => array( 'http://www.youtube.com/oembed?scheme=https',         true  ),
			'#http://(www\.)?youtube\.com/playlist.*#i'           => array( 'http://www.youtube.com/oembed',                      true  ),
			'#https://(www\.)?youtube\.com/playlist.*#i'          => array( 'http://www.youtube.com/oembed?scheme=https',         true  ),
			'#http://youtu\.be/.*#i'                              => array( 'http://www.youtube.com/oembed',                      true  ),
			'#https://youtu\.be/.*#i'                             => array( 'http://www.youtube.com/oembed?sch