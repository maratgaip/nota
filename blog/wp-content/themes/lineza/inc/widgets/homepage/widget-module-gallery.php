<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Homepage: Module Gallery Widget
	Plugin URI: http://www.bloompixel.com
	Description: A widget that displays module gallery posts.
	Version: 1.0
	Author: Simrandeep Singh
	Author URI: http://www.simrandeep.com

-----------------------------------------------------------------------------------*/

add_action( 'widgets_init', 'bloompixel_module_gallery_widget' );

function bloompixel_module_gallery_widget() {
	register_widget( 'bloompixel_module_gallery' );
}

class bloompixel_module_gallery extends WP_Widget {
	function bloompixel_module_gallery() {
		$widget_ops = array( 'classname' => 'module-gallery', 'description' => __('A widget that displays the module gallery posts ', 'bloompixel') );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'module-gallery-widget' );
		$this->WP_Widget( 'module-gallery-widget', __('Lineza Home: Module Gallery Widget', 'bloompixel'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		$bp_options = get_option('revista');
		extract( $args );
		
		$title = apply_filters('widget_title', $instance['title'] );
		$posts = $instance['posts'];
		$cats = $instance['cats'];
		$heading_background = $instance['heading_background'];
		$background = $instance['background'];

		echo $before_widget; ?>
		<div class="carousel <?php echo $background; ?>">
		<?php // Display the widget title 
		if ( $title )
			echo "<div class='$heading_background'>" . $before_title . $title . $after_title . "</div>";	
		?>
		<!-- START WIDGET -->		
		<ul class="slides">
			<?php
				$module_b = new WP_Query("cat=".$cats."&orderby=date&order=DESC&showposts=".$posts);
			?>
			<?php if($module_b->have_posts()) : while ($module_b->have_posts()) : $module_b->the_post(); ?>
				<li>
					<a class="featured-thumbnail" href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>">
						<?php
							if ( has_post_thumbnail() ) {
								the_post_thumbnail('featured3');
							} else {
								echo '<img width="277" height="260" src="' . get_stylesheet_directory_uri() . '/images/277x260.png" />';
							}
						?>
						<div class="carousel-content">
							<h3 class="title title16 carousel-title uppercase"><?php the_title(); ?></h3>
							<p><?php echo excerpt(10);?></p>
						</div>
					</a>
				</li>
			<?php endwhile; ?>
			<?php endif; ?>
		</ul>
		</div>
		<!-- END WIDGET -->
		<?php

		echo $after_widget;

	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['posts'] = strip_tags( $new_instance['posts'] );
		$instance['cats'] = implode(',' , $new_instance['cats']  );
		$instance['heading_background'] = strip_tags( $new_instance['heading_background'] );
		$instance['background'] = strip_tags( $new_instance['background'] );

		return $instance;
	}
	
	function form( $instance ) {
		//Set up some default widget settings.
		$defaults = array(
			'cats' => 1,
			'posts' => 10,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$heading_background = isset( $instance['heading_background'] ) ? esc_attr( $instance['heading_background'] ) : '';
		$background = isset( $instance['background'] ) ? esc_attr( $instance['background'] ) : '';
		
		$categories_all = get_categories();
		$bpxl_categories = array();

		foreach ($categories_all as $bpxl_cat) {
			$bpxl_categories[$bpxl_cat->cat_ID] = $bpxl_cat->cat_name;
		}
		?>
		<?php // Widget Title: Text Input ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php if(!empty($instance['title'])) { echo $instance['title']; } ?>" class="widefat" type="text" />
		</p>

		<?php //Text Input ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'posts' ); ?>"><?php _e('Number of posts:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'posts' ); ?>" name="<?php echo $this->get_field_name( 'posts' ); ?>" value="<?php echo $instance['posts']; ?>" class="widefat" type="text" />
		</p>
		<p>
			<?php $cats = explode ( ',' , $instance['cats'] ) ; ?>
			<label for="<?php echo $this->get_field_id( 'cats' ); ?>"><?php _e('Category: ','bloompixel'); ?></label>
			<select multiple="multiple" id="<?php echo $this->get_field_id( 'cats' ); ?>[]" name="<?php echo $this->get_field_name( 'cats' ); ?>[]" class="widefat" >
				<?php foreach ($bpxl_categories as $bpxl_category => $bpxl_option) { ?>
				<option value="<?php echo $bpxl_category ?>" <?php if ( in_array( $bpxl_category , $cats ) ) { echo ' selected="selected"' ; } ?>><?php echo $bpxl_option; ?></option>
				<?php } ?>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'heading_background' ); ?>"><?php _e( 'Heading Background Color:','bloompixel' ); ?></label> 
			<select id="<?php echo $this->get_field_id( 'heading_background' ); ?>" name="<?php echo $this->get_field_name( 'heading_background' ); ?>" style="width:100%;">
				<option value="default-bg" <?php if ($heading_background == 'default-bg') echo 'selected="selected"'; ?>><?php _e( 'Default','bloompixel' ); ?></option>
				<option value="blue-bg" <?php if ($heading_background == 'blue-bg') echo 'selected="selected"'; ?>><?php _e( 'Blue','bloompixel' ); ?></option>
				<option value="carrot-bg" <?php if ($heading_background == 'carrot-bg') echo 'selected="selected"'; ?>><?php _e( 'Carrot','bloompixel' ); ?></option>
				<option value="black-bg" <?php if ($heading_background == 'black-bg') echo 'selected="selected"'; ?>><?php _e( 'Black','bloompixel' ); ?></option>
				<option value="emerald-bg" <?php if ($heading_background == 'emerald-bg') echo 'selected="selected"'; ?>><?php _e( 'Emerald','bloompixel' ); ?></option>
				<option value="gray-bg" <?php if ($heading_background == 'gray-bg') echo 'selected="selected"'; ?>><?php _e( 'Gray','bloompixel' ); ?></option>
				<option value="lemon-bg" <?php if ($heading_background == 'lemon-bg') echo 'selected="selected"'; ?>><?php _e( 'Lemon','bloompixel' ); ?></option>
				<option value="midlight-bg" <?php if ($heading_background == 'midlight-bg') echo 'selected="selected"'; ?>><?php _e( 'Midlight Blue','bloompixel' ); ?></option>
				<option value="pomegranate-bg" <?php if ($heading_background == 'pomegranate-bg') echo 'selected="selected"'; ?>><?php _e( 'Pomegranate','bloompixel' ); ?></option>
				<option value="purple-bg" <?php if ($heading_background == 'purple-bg') echo 'selected="selected"'; ?>><?php _e( 'Purple','bloompixel' ); ?></option>
				<option value="yellow-bg" <?php if ($heading_background == 'yellow-bg') echo 'selected="selected"'; ?>><?php _e( 'Yellow','bloompixel' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'background' ); ?>"><?php _e( 'Background:','bloompixel' ); ?></label> 
			<select id="<?php echo $this->get_field_id( 'background' ); ?>" name="<?php echo $this->get_field_name( 'background' ); ?>" style="width:100%;" >
				<option value="transparent-bg" <?php if ($background == 'transparent-bg') echo 'selected="selected"'; ?>><?php _e( 'White','bloompixel' ); ?></option>
				<option value="light-bg" <?php if ($background == 'light-bg') echo 'selected="selected"'; ?>><?php _e( 'Light','bloompixel' ); ?></option>
				<option value="dark-bg" <?php if ($background == 'dark-bg') echo 'selected="selected"'; ?>><?php _e( 'Dark','bloompixel' ); ?></option>
			</select>
		</p>
		<?php
	}
}

?>