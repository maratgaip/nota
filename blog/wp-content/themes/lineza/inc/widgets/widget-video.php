<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Video Widget
	Plugin URI: http://www.bloompixel.com
	Description: A widget that displays video.
	Version: 1.0
	Author: Simrandeep Singh
	Author URI: http://www.simrandeep.com

-----------------------------------------------------------------------------------*/

add_action( 'widgets_init', 'bpxl_videos_widget' );  

// Register Widget
function bpxl_videos_widget() {
    register_widget( 'bpxl_video_widget' );
}

// Widget Class
class bpxl_video_widget extends WP_Widget {

	// Widget Setup 
	function bpxl_video_widget() {
		$widget_ops = array( 'classname' => 'video_widget', 'description' => __('A widget that displays the video', 'bloompixel') );
		$control_ops = array( 'id_base' => 'video_widget' );
		$this->WP_Widget( 'video_widget', __('Lineza Video Widget', 'bloompixel'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		
		//Our variables from the widget settings.
		$title = apply_filters('widget_title', $instance['title'] );
		$id = $instance['id'];
		$host = $instance['host'];
		
		// Before Widget
		echo $before_widget;
		
		?>
		<!-- START WIDGET -->
		<div id="video-widget">
			<?php		
				// Display the widget title  
				if ( 