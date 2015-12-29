<?php
/*
Plugin Name: bl category
Description: Category list with some settings
Author: Ivar Rafn
Version: 1
Author URI: http://www.bluth.is/
*/
class bl_category extends WP_Widget {

	function bl_category(){
		$widget_ops = array('classname' => 'bl_category', 'description' => 'List of categories and sub-categories' );
		$this->WP_Widget('bl_category', 'Bluthemes - Categories', $widget_ops);
	}


	function widget( $args, $instance ) {
		extract($args);
		$title 	= apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		echo $before_widget;
			if ( !empty( $title ) ) { echo $before_title . $title . $after_title; }
		
		$catargs = array(
			'show_count'	=> 1,
			'hide_empty'    => 0,
			'title_li' => ''
		); ?>
		<ul><?php
			wp_list_categories( $catargs ); ?>
		</ul><?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] 			= strip_tags($new_instance['title']);

		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
		echo $before_title . $title . $after_title;
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'bluth_admin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>

<?php
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("bl_category");') );