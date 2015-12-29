<?php
/*
Plugin Name: bl Html
Description: HTML box with some settings
Author: Ivar Rafn
Version: 1
Author URI: http://www.bluth.is/
*/
class bl_html extends WP_Widget {

	function bl_html(){
		$widget_ops = array('classname' => 'bl_html', 'description' => 'Add HTML or Text' );
		$this->WP_Widget('bl_html', 'Bluthemes - HTML', $widget_ops);
	}


	function widget( $args, $instance ) {
		extract($args);
		$title 	= apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$text 	= apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );

		echo $before_widget; 
			echo !empty($title) ? $before_title.$title.$after_title : ''; ?>
			<div class="widget-body<?php echo $instance['white_bg'] != 'false' ? ' box' : ''; ?><?php echo $instance['add_padding'] != 'false' ? ' pad-xs-10 pad-sm-15 pad-md-20' : ''; ?>"><?php echo !empty( $instance['filter'] ) ? wpautop( $text ) : $text; ?></div>
		<?php
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] 			= strip_tags($new_instance['title']);
		$instance['white_bg'] 		= strip_tags($new_instance['white_bg']);
		$instance['add_padding'] 	= strip_tags($new_instance['add_padding']);

		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) );
		$instance['filter'] = isset($new_instance['filter']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'white_bg' => 'true', 'add_padding' => 'true' ) );
		$title = strip_tags($instance['title']);
		$text = esc_textarea($instance['text']);
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'bluth_admin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('white_bg'); ?>">Add white background</label><br>
			<select style="width:216px" id="<?php echo $this->get_field_id('white_bg'); ?>" name="<?php echo $this->get_field_name('white_bg'); ?>">
			  	<option value="true" <?php echo ($instance['white_bg'] == 'true') ? 'selected=""' : ''; ?>>Yes</option> 
			  	<option value="false" <?php echo ($instance['white_bg'] == 'false') ? 'selected=""' : ''; ?>>No</option> 
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('add_padding'); ?>">Add padding to the widget</label><br>
			<select style="width:216px" id="<?php echo $this->get_field_id('add_padding'); ?>" name="<?php echo $this->get_field_name('add_padding'); ?>">
			  	<option value="true" <?php echo ($instance['add_padding'] == 'true') ? 'selected=""' : ''; ?>>Yes</option> 
			  	<option value="false" <?php echo ($instance['add_padding'] == 'false') ? 'selected=""' : ''; ?>>No</option> 
			</select>
		</p>
		<textarea class="widefat" rows="16" cols="20" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>"><?php echo $text; ?></textarea>

		<p><input id="<?php echo $this->get_field_id('filter'); ?>" name="<?php echo $this->get_field_name('filter'); ?>" type="checkbox" <?php checked(isset($instance['filter']) ? $instance['filter'] : 0); ?> />&nbsp;<label for="<?php echo $this->get_field_id('filter'); ?>"><?php _e('Automatically add paragraphs', 'bluth_admin'); ?></label></p>
<?php
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("bl_html");') );