<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Facebook Like Box Widget
	Plugin URI: http://www.bloompixel.com
	Description: A widget to display Facebook like box for your website.
	Version: 1.0
	Author: Simrandeep Singh
	Author URI: http://www.simrandeep.com

-----------------------------------------------------------------------------------*/

add_action( 'widgets_init', create_function( '', 'register_widget("bpxl_facebook_widget");' ) );

/**
 * Create the widget class and extend from the WP_Widget
 */
class bpxl_facebook_widget extends WP_Widget {
	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
			'bpxl_facebook_widget',		// Base ID
			'Lineza Facebook Like Box',		// Name
			array(
				'classname'		=>	'bpxl_facebook_widget',
				'description'	=>	__('A widget that displays a facebook like box from your facebook page.', 'framework')
			)
		);
	} // end constructor
	
	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		extract( $args );
		/* Our variables from the widget settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$this->color_scheme = $instance['color_scheme'];
		$this->facebook_username = $instance['page_name'];
		$this->facebook_width = $instance['width'];
		$this->facebook_show_faces = ($instance['show_faces'] == "1" ? "true" : "false");
		$this->facebook_stream = ($instance['show_stream'] == "1" ? "true" : "false");
		$this->facebook_header = ($instance['show_header'] == "1" ? "true" : "false");
		add_action('wp_footer', array(&$this,'add_js'));
		
		// Before Widget
		echo $before_widget;
		
		/* Display the widget title if one was input (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;
		/* Like Box */
		?>
			<div class="fb-like-box"
				data-href="http://www.facebook.com/<?php echo $this->facebook_username; ?>"
				data-colorscheme="<?php echo $this->color_scheme; ?>"
				data-width="<?php echo $this->facebook_width; ?>"
				data-show-faces="<?php echo $this->facebook_show_faces; ?>"
				data-stream="<?php echo $this->facebook_stream; ?>"
				data-header="<?php echo $this->facebook_header; ?>"></div>
		<?php
		
		// After Widget
		echo $after_widget;
	}
	
	/**
	 * Add Facebook javascripts
	 */
	public function add_js() {
		echo '<div id="fb-root"></div>
			<script>(function(d, s, id) {
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) return;
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, \'script\', \'facebook-jssdk\'));</script>';
	}
	
	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		/* Strip tags for title and name to remove HTML (important for text inputs). */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['color_scheme'] = strip_tags( $new_instance['color_scheme'] );
		$instance['page_name'] = strip_tags( $new_instance['page_name'] );
		$instance['width'] = strip_tags( $new_instance['width'] );
		$instance['show_faces'] = (bool)$new_instance['show_faces'];
		$instance['show_stream'] = (bool)$new_instance['show_stream'];
		$instance['show_header'] = (bool)$new_instance['show_header'];
		return $instance;
	}
	
	/**
	 * Create the form for the Widget admin
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults = array(
			'title' => '',
			'color_scheme' => 'light',
			'page_name' => '',
			'width' => '300',
			'show_faces' => '',
			'show_stream' => '',
			'show_header' => ''
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
		<!-- Widget Title: Text Input -->
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'framework') ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" /></p>
		<!-- Page name: Text Input -->
		<p><label for="<?php echo $this->get_field_id( 'page_name' ); ?>"><?php _e('Page name (http://www.facebook.com/[page_name])', 'framework') ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'page_name' ); ?>" name="<?php echo $this->get_field_name( 'page_name' ); ?>" value="<?php echo $instance['page_name']; ?>" /></p>
		<!-- Width: Text Input -->
		<p><label for="<?php echo $this->get_field_id( 'width' ); ?>"><?php _e('Width', 'framework') ?></label>
		<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'width' ); ?>" name="<?php echo $this->get_field_name( 'width' ); ?>" value="<?php echo $instance['width']; ?>" /></p>
		<!-- Color Scheme: Select -->
		<p><label for="<?php echo $this->get_field_id( 'color_scheme' ); ?>"><?php _e('Color Scheme', 'framework') ?></label>
		<select id="<?php echo $this->get_field_id('color_scheme'); ?>" name="<?php echo $this->get_field_name('color_scheme'); ?>" class="widefat" style="width:100%;">
			<option <?php if ('light' == $instance['color_scheme']) echo 'selected="selected"'; ?>>light</option>
			<option <?php if ('dark' == $instance['color_scheme']) echo 'selected="selected"'; ?>>dark</option>
		</select>
		<!-- Show Faces: Checkbox -->
		<p><label for="<?php echo $this->get_field_id( 'show_faces' ); ?>"><?php _e('Show Faces', 'framework') ?></label>
		<input type="checkbox" class="widefat" id="<?php echo $this->get_field_id( 'show_faces' ); ?>" name="<?php echo $this->get_field_name( 'show_faces' ); ?>" value="1" <?php echo ($instance['show_faces'] == "true" ? "checked='checked'" : ""); ?> /></p>
		<!-- Show Stream: Checkbox -->
		<p><label for="<?php echo $this->get_field_id( 'show_stream' ); ?>"><?php _e('Show Stream', 'framework') ?></label><input type="checkbox" class="widefat" id="<?php echo $this->get_field_id( 'show_stream' ); ?>" name="<?php echo $this->get_field_name( 'show_stream' ); ?>" value="1" <?php echo ($instance['show_stream'] == "true" ? "checked='checked'" : ""); ?> /></p>
		<!-- Show Header: Checkbox -->
		<p><label for="<?php echo $this->get_field_id( 'show_header' ); ?>"><?php _e('Show Header', 'framework') ?></label>
		<input type="checkbox" class="widefat" id="<?php echo $this->get_field_id( 'show_header' ); ?>" name="<?php echo $this->get_field_name( 'show_header' ); ?>" value="1" <?php echo ($instance['show_header'] == "true" ? "checked='checked'" : ""); ?> /></p>
		<?php
	}
}