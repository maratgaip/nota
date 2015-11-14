<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Subscribe Widget
	Plugin URI: http://www.bloompixel.com
	Description: A widget that displays subscription box.
	Version: 1.0
	Author: Simrandeep Singh
	Author URI: http://www.simrandeep.com

-----------------------------------------------------------------------------------*/

add_action( 'widgets_init', 'bpxl_subscription_widget' );  

// Register Widget
function bpxl_subscription_widget() {
    register_widget( 'bpxl_subscribe_widget' );
}

// Widget Class
class bpxl_subscribe_widget extends WP_Widget {

	// Widget Setup 
	function bpxl_subscribe_widget() {
		$widget_ops = array( 'classname' => 'subscribe_widget', 'description' => __('A widget that displays the subscribe box', 'bloompixel') );
		$control_ops = array( 'id_base' => 'subscribe_widget' );
		$this->WP_Widget( 'subscribe_widget', __('Lineza Subscribe Widget', 'bloompixel'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		
		//Our variables from the widget settings.
		$title = apply_filters('widget_title', $instance['title'] );
		$id = $instance['id'];
		$desc = $instance['desc'];
		
		// Before Widget
		echo $before_widget;
		
		?>
		<!-- START WIDGET -->
		<div id="subscribe-widget">
			<?php		
				// Display the widget title  
				if ( $title )
					echo $before_title . $title . $after_title;
			?>
			<p><?php echo $desc; ?></p>
			<form style="" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $id; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true" _lpchecked="1">
				<input type="text" value="" placeholder="Email Address" name="email">
				<input type="hidden" value="<?php echo $id; ?>" name="uri"><input type="hidden" name="loc" value="en_US"><input type="submit" value="Subscribe">
			</form>
		</div>
		<!-- END WIDGET -->
		<?php
		
		// After Widget
		echo $after_widget;
	}
	
	// Update the widget
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['id'] = stripslashes( $new_instance['id']);
		$instance['desc'] = $new_instance['desc'];
		return $instance;
	}


	//Widget Settings
	function form( $instance ) {
		//Set up some default widget settings.
		$defaults = array( 'title' => __('Subscribe', 'bloompixel'), 'id' => '', 'desc' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults );

		// Widget Title: Text Input
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" type="text" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'id' ); ?>">Feedburner ID:</label>
			<input id="<?php echo $this->get_field_id( 'id' ); ?>" name="<?php echo $this->get_field_name( 'id' ); ?>" value="<?php echo $instance['id']; ?>" class="widefat" type="text" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'desc' ); ?>">Subscribe Text:</label>
			<input id="<?php echo $this->get_field_id( 'desc' ); ?>" name="<?php echo $this->get_field_name( 'desc' ); ?>" value="<?php echo $instance['desc']; ?>" class="widefat" type="text" />
		</p>
		<?php
	}
}
?>