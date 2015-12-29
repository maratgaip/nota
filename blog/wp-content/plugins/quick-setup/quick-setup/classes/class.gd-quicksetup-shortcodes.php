<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

/**
 * Set up some basic shortcodes
 */
class GD_QuickSetup_Shortcodes {
	
	/**
	 * Constructor.
	 * Add shortcodes support
	 */
	public function __construct() {
		add_shortcode( 'gdqs_input', array( $this, 'do_input' ) );
		add_shortcode( 'gdqs_link', array( $this, 'do_link' ) );
	}

	/**
	 * Replace [gdqs_link] shortcode
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public function do_link( $atts, $content ) {
		extract(
						shortcode_atts(
										array(
											'href' => '',
											'target' => '_self',
										),
										$atts
						)
		);
		return '<a href="' . esc_url( $href ) . '" target="' . esc_attr( $target ) . '">' . esc_html( $content ) . '</a>';
	}
	
	/**
	 * Replace [gdqs_input] shortcode
	 * @param array $atts
	 * @param string $content
	 * @return string
	 */
	public function do_input( $atts ) {
		$data = get_option( 'gd_quicksetup_last_post' );
		extract(
						shortcode_atts(
										array(
											'type'        => 'text',
											'placeholder' => '',
											'name'        => 'input1',
										),
										$atts
						)
		);
		$value = '';
		if ( isset( $data[$name] ) ) {
			$value = $data[$name];
		}
		return '<input type="' . esc_attr( $type ) . '" placeholder="' . esc_attr( $placeholder ) . '" name="' . esc_attr( $name ) . '" value="' . esc_attr( $value ) . '" />';
	}
}
