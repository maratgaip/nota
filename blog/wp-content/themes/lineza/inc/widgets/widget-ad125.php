<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: 125 Ad Widget
	Plugin URI: http://www.bloompixel.com
	Description: A widget that displays 125x125px ad.
	Version: 1.0
	Author: Simrandeep Singh
	Author URI: http://www.simrandeep.com

-----------------------------------------------------------------------------------*/

add_action( 'widgets_init', 'bp_125_ad_widget' );  

// Register Widget
function bp_125_ad_widget() {
    register_widget( 'bp_125_widget' );
}

// Widget Class
class bp_125_widget extends WP_Widget {

	// Widget Setup 
	function bp_125_widget() {
		$widget_ops = array( 'classname' => 'bp_125_widget', 'description' => __('A widget that displays 125x125 ad', 'bloompixel') );
		$control_ops = array( 'width' => '125', 'height' => '125', 'id_base' => 'bp_125_widget' );
		$this->WP_Widget( 'bp_125_widget', __('Lineza 125x125 Ad Widget', 'bloompixel'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		
		//Our variables from the widget settings.
		$banner1 = $instance['banner1'];
		$banner2 = $instance['banner2'];
		$banner3 = $instance['banner3'];
		$banner4 = $instance['banner4'];
		$link1 = $instance['link1'];
		$link2 = $instance['link2'];
		$link3 = $instance['link3'];
		$link4 = $instance['link4'];
		
		// Before Widget
		//echo $before_widget;
		
		// Display the widget title  
		/* if ( $title )
			echo $before_title . $title . $after_title;
		 */
		?>
		<!-- START WIDGET -->
		<li class="widget ad-125-widget">
			<ul>
			<?php
				// Ad1
				if ( $link1 )
					echo '<li class="adleft"><a href="' . $link1 . '"><img src="' . $banner1 . '" width="125" height="125" alt="" /></a></li>';
					
				elseif ( $banner1 )
					echo '<li class="adleft"><img src="' . $banner1 . '" width="125" height="125" alt="" /></li>';
					
				// Ad2
				if ( $link2 )
					echo '<li class="adright"><a href="' . $link2 . '"><img src="' . $banner2 . '" width="125" height="125" alt="" /></a></li>';
					
				elseif ( $banner2 )
					echo '<li class="adright"><img src="' . $banner2 . '" width="125" height="125" alt="" /></li>';
					
				// Ad3
				if ( $link3 )
					echo '<li class="adleft"><a href="' . $link3 . '"><img src="' . $banner3 . '" width="125" height="125" alt="" /></a></li>';
					
				elseif ( $banner3 )
					echo '<li class="adleft"><img src="' . $banner3 . '" width="125" height="125" alt="" /></li>';
					
				// Ad4
				if ( $link4 )
					echo '<li class="adright"><a href="' . $link4 . '"><img src="' . $banner4 . '" width="125" height="125" alt="" /></a></li>';
					
				elseif ( $banner4 )
					echo '<li class="adright"><img src="' . $banner4 . '" width="125" height="125" alt="" /></li>';
			?>
			</ul>
		</li>
		<!-- END WIDGET -->
		<?php
		
		// After Widget
		//echo $after_widget;
	}
	
	// Update the widget
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['link1'] = $new_instance['link1'];
		$instance['link2'] = $new_instance['link2'];
		$instance['link3'] = $new_instance['link3'];
		$instance['link4'] = $new_instance['link4'];
		$instance['banner1'] = $new_instance['banner1'];
		$instance['banner2'] = $new_instance['banner2'];
		$instance['banner3'] = $new_instance['banner3'];
		$instance['banner4'] = $new_instance['banner4'];
		return $instance;
	}


	//Widget Settings
	function form( $instance ) {
		//Set up some default widget settings.
		$defaults = array( 
			'link1' => 'http://bloompixel.com/',
			'banner1' => get_template_directory_uri()."/images/125x125.png",
			'link2' => 'http://bloompixel.com/',
			'banner2' => get_template_directory_uri()."/images/125x125.png",
			'link3' => 'http://bloompixel.com/',
			'banner3' => get_template_directory_uri()."/images/125x125.png",
			'link4' => 'http://bloompixel.com/',
			'banner4' => get_template_directory_uri()."/images/125x125.png",
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		// Widget Title: Text Input
		?>
		<!-- Ad1 Link URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'link1' ); ?>"><?php _e('Ad1 Link URL:', 'bloompixel') ?></label>
			<input id="<?php echo $this->get_field_id( 'link1' ); ?>" name="<?php echo $this->get_field_name( 'link1' ); ?>" value="<?php echo $instance['link1']; ?>" style="width:100%;" type="text" />
		</p>
		<!-- Ad1 Banner URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'banner1' ); ?>"><?php _e('Ad1 Banner URL:', 'bloompixel') ?></label>
			<input id="<?php echo $this->get_field_id( 'banner1' ); ?>" name="<?php echo $this->get_field_name( 'banner1' ); ?>" value="<?php echo $instance['banner1']; ?>" style="width:100%;" type="text" />
		</p>
		
		<!-- Ad2 Link URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'link2' ); ?>"><?php _e('Ad2 Link URL:', 'bloompixel') ?></label>
			<input id="<?php echo $this->get_field_id( 'link2' ); ?>" name="<?php echo $this->get_field_name( 'link2' ); ?>" value="<?php echo $instance['link2']; ?>" style="width:100%;" type="text" />
		</p>
		<!-- Ad2 Banner URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'banner2' ); ?>"><?php _e('Ad2 Banner URL:', 'bloompixel') ?></label>
			<input id="<?php echo $this->get_field_id( 'banner2' ); ?>" name="<?php echo $this->get_field_name( 'banner2' ); ?>" value="<?php echo $instance['banner2']; ?>" style="width:100%;" type="text" />
		</p>
		
		<!-- Ad3 Link URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'link3' ); ?>"><?php _e('Ad3 Link URL:', 'bloompixel') ?></label>
			<input id="<?php echo $this->get_field_id( 'link3' ); ?>" name="<?php echo $this->get_field_name( 'link3' ); ?>" value="<?php echo $instance['link3']; ?>" style="width:100%;" type="text" />
		</p>
		<!-- Ad3 Banner URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'banner3' ); ?>"><?php _e('Ad3 Banner URL:', 'bloompixel') ?></label>
			<input id="<?php echo $this->get_field_id( 'banner3' ); ?>" name="<?php echo $this->get_field_name( 'banner3' ); ?>" value="<?php echo $instance['banner3']; ?>" style="width:100%;" type="text" />
		</p>
		
		<!-- Ad4 Link URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'link4' ); ?>"><?php _e('Ad4 Link URL:', 'bloompixel') ?></label>
			<input id="<?php echo $this->get_field_id( 'link4' ); ?>" name="<?php echo $this->get_field_name( 'link4' ); ?>" value="<?php echo $instance['link4']; ?>" style="width:100%;" type="text" />
		</p>
		<!-- Ad4 Banner URL -->
		<p>
			<label for="<?php echo $this->get_field_id( 'banner4' ); ?>"><?php _e('Ad4 Banner URL:', 'bloompixel') ?></label>
			<input id="<?php echo $this->get_field_id( 'banner4' ); ?>" name="<?php echo $this->get_field_name( 'banner4' ); ?>" value="<?php echo $instance['banner4']; ?>" style="width:100%;" type="text" />
		</p>
		<?php
	}
}
?>