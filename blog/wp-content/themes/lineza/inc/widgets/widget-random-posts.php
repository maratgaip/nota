<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Random Posts Widget
	Plugin URI: http://www.bloompixel.com
	Description: A widget that displays random posts.
	Version: 1.0
	Author: Simrandeep Singh
	Author URI: http://www.simrandeep.com

-----------------------------------------------------------------------------------*/

add_action( 'widgets_init', 'bpxl_random_posts_widget' );  

// Register Widget
function bpxl_random_posts_widget() {
    register_widget( 'bpxl_random_widget' );
}

// Widget Class
class bpxl_random_widget extends WP_Widget {

	// Widget Setup 
	function bpxl_random_widget() {
		$widget_ops = array( 'classname' => 'random_posts', 'description' => __('A widget that displays the random posts of your blog', 'bloompixel') );
		$control_ops = array( 'id_base' => 'random_widget' );
		$this->WP_Widget( 'random_widget', __('Lineza Random Posts', 'bloompixel'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		
		//Our variables from the widget settings.
		$title = apply_filters('widget_title', $instance['title'] );
		$posts = $instance['posts'];
		$heading_background = $instance['heading_background'];
		$show_thumb = (int) $instance['show_thumb'];
		$show_cat = (int) $instance['show_cat'];
		$show_author = (int) $instance['show_author'];
		$show_date = (int) $instance['show_date'];
		$show_comments = (int) $instance['show_comments'];
		
		// Before Widget
		echo $before_widget;
		
		// Display the widget title  
		if ( $title )
			echo "<div class='$heading_background'>" . $before_title . $title . $after_title . "</div>";
		
		?>
		<!-- START WIDGET -->
		<ul class="random-posts">
			<?php
				$randomposts = new WP_Query('showposts='.$posts.'&orderby=rand&ignore_sticky_posts=1');
			?>
			<?php if($randomposts->have_posts()) : while ($randomposts->have_posts()) : $randomposts->the_post(); ?>
				<li>
					<?php if ( $show_thumb == 1 ) { ?>
						<div class="thumbnail">
							<?php if(has_post_thumbnail()): ?>
								<a class="widgetthumb" href='<?php the_permalink(); ?>'><?php the_post_thumbnail(); ?></a>
							<?php else: ?>
								<a class="widgetthumb" href='<?php the_permalink(); ?>'><img src="<?php echo get_template_directory_uri(); ?>/images/100x100.png" alt="<?php the_title(); ?>"  width='100' height='100' class="wp-post-image" /></a>
							<?php endif; ?>
						</div>
					<?php } ?>
					<div class="info">
						<?php if ( $show_cat == 1 ) { ?>
							<div class="post-cats uppercase">
								<i class="fa fa-tag"></i> <?php the_category(', '); ?>
							</div>
						<?php } ?>
						<span class="widgettitle"><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></span>
						<span class="meta">
							<?php if ( $show_author == 1 ) { ?>
								<span class="post-author"><i class="fa fa-user"></i> <?php the_author_posts_link(); ?></span>
							<?php } ?>
							<?php if ( $show_date == 1 ) { ?>
								<time datetime="<?php the_time('Y-m-j'); ?>"><i class="fa fa-calendar"></i> <?php the_time(get_option( 'date_format' )); ?></time>
							<?php } ?>
							<?php if ( $show_comments == 1 ) { ?>
								<span class="post-comments"><i class="fa fa-comments"></i> <?php comments_popup_link( '0', '1', '%', 'comments-link', 'Comments are off for this post'); ?></span>
							<?php } ?>
						</span>
					</div>
				</li>
			<?php endwhile; ?>
			<?php endif; ?>
		</ul>
		<!-- END WIDGET -->
		<?php
		
		// After Widget
		echo $after_widget;
	}
	
	// Update the widget
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['posts'] = $new_instance['posts'];
		$instance['heading_background'] = strip_tags( $new_instance['heading_background'] );
		$instance['show_thumb'] = intval( $new_instance['show_thumb'] );
		$instance['show_cat'] = intval( $new_instance['show_cat'] );
		$instance['show_author'] = intval( $new_instance['show_author'] );
		$instance['show_date'] = intval( $new_instance['show_date'] );
		$instance['show_comments'] = intval( $new_instance['show_comments'] );
		return $instance;
	}


	//Widget Settings
	function form( $instance ) {
		//Set up some default widget settings.
		$defaults = array(
			'title' => __('Random Posts', 'bloompixel'),
			'posts' => 4,
			'show_thumb' => 1,
			'show_cat' => 0,
			'show_author' => 0,
			'show_date' => 1,
			'show_comments' => 0,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$heading_background = isset( $instance['heading_background'] ) ? esc_attr( $instance['heading_background'] ) : '';
		$show_thumb = isset( $instance[ 'show_thumb' ] ) ? esc_attr( $instance[ 'show_thumb' ] ) : 1;
		$show_cat = isset( $instance[ 'show_cat' ] ) ? esc_attr( $instance[ 'show_cat' ] ) : 1;
		$show_author = isset( $instance[ 'show_author' ] ) ? esc_attr( $instance[ 'show_author' ] ) : 1;
		$show_date = isset( $instance[ 'show_date' ] ) ? esc_attr( $instance[ 'show_date' ] ) : 1;
		$show_comments = isset( $instance[ 'show_comments' ] ) ? esc_attr( $instance[ 'show_comments' ] ) : 1;

		// Widget Title: Text Input
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'example'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php if(!empty($instance['title'])) { echo $instance['title']; } ?>" class="widefat" type="text" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'posts' ); ?>"><?php _e('Number of posts to show:','bloompixel'); ?></label>
			<input id="<?php echo $this->get_field_id( 'posts' ); ?>" name="<?php echo $this->get_field_name( 'posts' ); ?>" value="<?php echo $instance['posts']; ?>" class="widefat" type="text" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id("show_thumb"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_thumb"); ?>" name="<?php echo $this->get_field_name("show_thumb"); ?>" value="1" <?php if (isset($instance['show_thumb'])) { checked( 1, $instance['show_thumb'], true ); } ?> />
				<?php _e( 'Show Thumbnails', 'bloompixel'); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id("show_cat"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_cat"); ?>" name="<?php echo $this->get_field_name("show_cat"); ?>" value="1" <?php if (isset($instance['show_cat'])) { checked( 1, $instance['show_cat'], true ); } ?> />
				<?php _e( 'Show Categories', 'bloompixel'); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id("show_author"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_author"); ?>" name="<?php echo $this->get_field_name("show_author"); ?>" value="1" <?php if (isset($instance['show_author'])) { checked( 1, $instance['show_author'], true ); } ?> />
				<?php _e( 'Show Post Author', 'bloompixel'); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id("show_date"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_date"); ?>" name="<?php echo $this->get_field_name("show_date"); ?>" value="1" <?php if (isset($instance['show_date'])) { checked( 1, $instance['show_date'], true ); } ?> />
				<?php _e( 'Show Post Date', 'bloompixel'); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id("show_comments"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_comments"); ?>" name="<?php echo $this->get_field_name("show_comments"); ?>" value="1" <?php if (isset($instance['show_comments'])) { checked( 1, $instance['show_comments'], true ); } ?> />
				<?php _e( 'Show Post Comments', 'bloompixel'); ?>
			</label>
		</p>
		<?php
	}
}
?>