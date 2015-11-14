<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Homepage: Module C Widget
	Plugin URI: http://www.bloompixel.com
	Description: A widget that displays module C posts.
	Version: 1.0
	Author: Simrandeep Singh
	Author URI: http://www.simrandeep.com

-----------------------------------------------------------------------------------*/

add_action( 'widgets_init', 'bloompixel_module_c_widget' );

function bloompixel_module_c_widget() {
	register_widget( 'bloompixel_module_c' );
}

class bloompixel_module_c extends WP_Widget {
	function bloompixel_module_c() {				
		$widget_ops = array( 'classname' => 'module-c', 'description' => __('A widget that displays the module C posts ', 'bloompixel') );
		$control_ops = array( 'id_base' => 'module-c-widget' );
		$this->WP_Widget( 'module-c-widget', __('Lineza Home: Module C Widget', 'bloompixel'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		
		$title = apply_filters('widget_title', $instance['title'] );
		$cats = $instance['cats'];
		$posts = $instance['posts'];
		$excerpt_length = $instance['excerpt_length'];
		$show_excerpt = (int) $instance['show_excerpt'];
		$heading_background = $instance['heading_background'];
		$background = $instance['background'];
		$show_cat = (int) $instance['show_cat'];
		$show_author = (int) $instance['show_author'];
		$show_date = (int) $instance['show_date'];
		$show_comments = (int) $instance['show_comments'];

		echo $before_widget;

		// Display the widget title 
		if ( $title )
			echo "<div class='$heading_background'>" . $before_title . $title . $after_title . "</div>";

		//Display the name 
		/* if ( $cat )
			printf( '<p>' . __('Hey their Sailor! My name is %1$s.', 'example') . '</p>', $cat );

		if ( $show_info )
			printf( $name ); */
			
		?>
		<!-- START WIDGET -->
		<div class="module-box module-c-box <?php echo $background; ?>">
			<?php
				$pcount = 1;
				$module_c = new WP_Query("cat=".$cats."&orderby=date&order=DESC&showposts=".$posts);
			?>
			<?php if($module_c->have_posts()) : while ($module_c->have_posts()) : $module_c->the_post(); ?>
				<?php if ($pcount == 1) { ?>
				<?php include(trailingslashit( get_template_directory() ).'inc/review.php'); ?>
					<div class="col2 col-top">	
						<div class="col-post-med">
							<?php /** If Video Post **/
								if ( has_post_format( 'video' )) {
									$video_id = get_post_meta(get_the_ID(), 'video_id', true);
									$video_host = get_post_meta(get_the_ID(), 'video_host', true);				
									if($video_id != '') {
										if($video_host == 'youtube') 