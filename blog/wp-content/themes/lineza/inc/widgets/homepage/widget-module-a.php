<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Homepage: Module A Widget
	Plugin URI: http://www.bloompixel.com
	Description: A widget that displays module A posts.
	Version: 1.0
	Author: Simrandeep Singh
	Author URI: http://www.simrandeep.com

-----------------------------------------------------------------------------------*/

add_action( 'widgets_init', 'bloompixel_module_a_widget' );

function bloompixel_module_a_widget() {
	register_widget( 'bloompixel_module_a' );
}

class bloompixel_module_a extends WP_Widget {
	function bloompixel_module_a() {		
        // This is where we add the style and script
        add_action( 'load-widgets.php', array($this, 'my_custom_load') );
		
		$widget_ops = array( 'classname' => 'module-a', 'description' => __('A widget that displays the module A posts ', 'bloompixel') );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'module-a-widget' );
		$this->WP_Widget( 'module-a-widget', __('Lineza Home: Module A Widget', 'bloompixel'), $widget_ops, $control_ops );
	}

	function my_custom_load() {
        wp_enqueue_style( 'wp-color-picker' );        
        wp_enqueue_script( 'wp-color-picker' );    
    }
	
	function widget( $args, $instance ) {
		$bp_options = get_option('revista');
		extract( $args, EXTR_SKIP );
		
		$title = apply_filters('widget_title', $instance['title'] );
		$cats = $instance['cats'];
		$posts = $instance['posts'];
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
		<div class="module-box module-a-box <?php echo $background; ?>">
			<?php
				$pcount = 1;
				$module_c = new WP_Query("cat=".$cats."&orderby=date&order=DESC&showposts=".$posts);
			?>
			<?php if($module_c->have_posts()) : while ($module_c->have_posts()) : $module_c->the_post(); ?>
				<?php if ($pcount == 1) { ?>
				<?php include(trailingslashit( get_template_directory() ).'inc/review.php'); ?>
					<div class="col-top">	
						<div class="col-post col-post-med">
							<?php /** If Video Post **/
				