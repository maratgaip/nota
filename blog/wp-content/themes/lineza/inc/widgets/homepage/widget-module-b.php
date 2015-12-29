<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Homepage: Module B Widget
	Plugin URI: http://www.bloompixel.com
	Description: A widget that displays module B posts.
	Version: 1.0
	Author: Simrandeep Singh
	Author URI: http://www.simrandeep.com

-----------------------------------------------------------------------------------*/

add_action( 'widgets_init', 'bloompixel_module_b_widget' );

function bloompixel_module_b_widget() {
	register_widget( 'bloompixel_module_b' );
}

class bloompixel_module_b extends WP_Widget {
	function bloompixel_module_b() {
		$widget_ops = array( 'classname' => 'module-b', 'description' => __('A widget that displays the module B posts ', 'bloompixel') );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'module-b-widget' );
		$this->WP_Widget( 'module-b-widget', __('Lineza Home: Module B Widget', 'bloompixel'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );
		
		$title = apply_filters('widget_title', $instance['title'] );
		$posts = $instance['posts'];
		$excerpt_length = $instance['excerpt_length'];
		$cat = $instance['cat'];
		$heading_background = $instance['heading_background'];
		$background = $instance['background'];
		$show_thumb = (int) $instance['show_thumb'];
		$show_excerpt = (int) $instance['show_excerpt'];
		$show_cat = (int) $instance['show_cat'];
		$show_author = (int) $instance['show_author'];
		$show_date = (int) $instance['show_date'];
		$show_comments = (int) $instance['show_comments'];

		echo $before_widget;

		// Display the widget title 
		if ( $title )
			echo "<div class='$heading_background'>" . $before_title . $title . $after_title . "</div>";
		?>
		<!-- START WIDGET -->
		<div class="module-box module-b-box <?php echo $background; ?>">
			<?php
				$module_b = new WP_Query("cat=".$cat."&orderby=date&order=DESC&showposts=".$posts);
			?>
			<?php if($module_b->have_posts()) : while ($module_b->have_posts()) : $module_b->the_post(); ?>
				<?php include(trailingslashit( get_template_directory() ).'inc/review.php'); ?>
				<div class="col-post col-post-small">
					<?php if ( $show_thumb == 1 ) { ?>
						<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail featured-thumbnail-small">
							<?php
								if ( has_post_thumbnail() ) {
									the_post_thumbnail('featured');
								} else {
									echo '<img width="280" height="150" src="' . get_stylesheet_directory_uri() . '/images/280x150.png" />';
								}
							?>
						</a>
					<?php } ?>
					<div class="module-b-content">
						<?php if ( $show_cat == 1 ) { ?>
							<div class="post-cats uppercase">
								<i class="fa fa-tag"></i> <?php the_category(', '); ?>
							</div>
						<?php } ?>
						<header>
							<h2 class="title title18">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" rel="bookmark"><?php the_title(); ?></a>
							</h2>
						</header><!--.header-->
						
						<?php if ($review_enable != '' ) { ?>
							<div class="rating-star rating-star-bottom rating-home" title="<?php echo "Rating: " . $finalrating; ?>">
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<i class="fa fa-star"></i>
								<div class="rating-star-top" style="width:<?php echo $finalpercent; ?>%;">
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
									<i class="fa fa-star"></i>
								</div>
							</div>
						<?php } ?>
						
						<?php if ( $show_excerpt == 1 ) { ?>
							<div class="post-content">
								<?php echo excerpt($excerpt_length);?>
							</div>
						<?php } ?>
						
						<div class="post-meta">
							<?php if ( $show_author == 1 ) { ?>
								<span class="post-author"><i class="fa fa-user"></i> <?php the_author_posts_link(); ?></span>
							<?php } ?>
							<?php if ( $show_date == 1 ) { ?>
								<time datetime="<?php the_time('Y-m-j'); ?>"><i class="fa fa-calendar"></i> <?php the_time(get_option( 'date_format' )); ?></time>
							<?php } ?>
							<?php if ( $show_comments == 1 ) { ?>
								<span class="post-comments"><i class="fa fa-comments"></i> <?php comments_popup_link( '0', '1', '%', 'comments-link', 'Comments are off for this post'); ?></span>
							<?php } ?>
						</div><!-- .post-meta -->
					</div>
				</div><!-- .col-post-small -->
			<?php endwhile; ?>
			<?php endif; ?>
		</div><!--End .module-b-box-->
		<!-- END WIDGET -->
		<?php

		echo $after_widget;

	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['posts'] = strip_tags( $new_instance['posts'] );
		$instance['excerpt_length'] = strip_tags( $new_instance['excerpt_length'] );
		$instance['cat'] = strip_tags( $new_instance['cat'] );
		$instance['heading_background'] = strip_tags( $new_instance['heading_background'] );
		$instance['background'] = strip_tags( $new_instance['background'] );
		$instance['show_thumb'] = intval( $new_instance['show_thumb'] );
		$instance['show_excerpt'] = intval( $new_instance['show_excerpt'] );
		$instance['show_cat'] = intval( $new_instance['show_cat'] );
		$instance['show_author'] = intval( $new_instance['show_author'] );
		$instance['show_date'] = intval( $new_instance['show_date'] );
		$instance['show_comments'] = intval( $new_instance['show_comments'] );

		return $instance;
	}
	
	function form( $instance ) {
		//Set up some default widget settings.
		$defaults = array(
			'cat' => (isset($this->cat) ? $this->cat : false),
			'posts' => 4,
			'excerpt_length' => 15,
			'show_thumb' => 1,
			'show_excerpt' => 1,
			'show_cat' => 1,
			'show_author' => 1,
			'show_date' => 1,
			'show_comments' => 0,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$cat = isset( $instance[ 'cat' ] ) ? intval( $instance[ 'cat' ] ) : 0;
		$heading_background = isset( $instance['heading_background'] ) ? esc_attr( $instance['heading_background'] ) : '';
		$background = isset( $instance['background'] ) ? esc_attr( $instance['background'] ) : '';
		$show_cat = isset( $instance[ 'show_thumb' ] ) ? esc_attr( $instance[ 'show_thumb' ] ) : 1;
		$show_cat = isset( $instance[ 'show_excerpt' ] ) ? esc_attr( $instance[ 'show_excerpt' ] ) : 1;
		$show_cat = isset( $instance[ 'show_cat' ] ) ? esc_attr( $instance[ 'show_cat' ] ) : 1;
		$show_author = isset( $instance[ 'show_author' ] ) ? esc_attr( $instance[ 'show_author' ] ) : 1;
		$show_date = isset( $instance[ 'show_date' ] ) ? esc_attr( $instance[ 'show_date' ] ) : 1;
		$show_comments = isset( $instance[ 'show_comments' ] ) ? esc_attr( $instance[ 'show_comments' ] ) : 1;
		?>
		<?php // Widget Title: Text Input ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:', 'bloompixel'); ?></label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php if(!empty($instance['title'])) { echo $instance['title']; } ?>" class="widefat" type="text" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'cat' ); ?>"><?php _e( 'Category:','bloompixel' ); ?></label> 
			<?php wp_dropdown_categories( Array(
						'orderby'            => 'ID', 
						'order'              => 'ASC',
						'show_count'         => 1,
						'hide_empty'         => 1,
						'hide_if_empty'      => true,
						'echo'               => 1,
						'selected'           => $cat,
						'hierarchical'       => 1, 
						'name'               => $this->get_field_name( 'cat' ),
						'id'                 => $this->get_field_id( 'cat' ),
						'taxonomy'           => 'category',
					) ); ?>
		</p>

		<?php //Text Input ?>
		<p>
			<label for="<?php echo $this->get_field_id( 'posts' ); ?>"><?php _e('Number of posts:', 'bloompixel'); ?></label>
			<input id="<?php echo $this->get_field_id( 'posts' ); ?>" name="<?php echo $this->get_field_name( 'posts' ); ?>" value="<?php echo $instance['posts']; ?>" class="widefat" type="text" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id("show_thumb"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_thumb"); ?>" name="<?php echo $this->get_field_name("show_thumb"); ?>" value="1" <?php if (isset($instance['show_thumb'])) { checked( 1, $instance['show_thumb'], true ); } ?> />
				<?php _e( 'Show Thumbnail', 'bloompixel'); ?>
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
		<p>
			<label for="<?php echo $this->get_field_id("show_excerpt"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_excerpt"); ?>" name="<?php echo $this->get_field_name("show_excerpt"); ?>" value="1" <?php if (isset($instance['show_excerpt'])) { checked( 1, $instance['show_excerpt'], true ); } ?> />
				<?php _e( 'Show Excerpt', 'bloompixel'); ?>
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'excerpt_length' ); ?>"><?php _e('Excerpt Length:', 'bloompixel'); ?></label>
			<input id="<?php echo $this->get_field_id( 'excerpt_length' ); ?>" name="<?php echo $this->get_field_name( 'excerpt_length' ); ?>" value="<?php echo $instance['excerpt_length']; ?>" class="widefat" type="text" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'heading_background' ); ?>"><?php _e( 'Heading Background Color:','bloompixel' ); ?></label> 
			<select id="<?php echo $this->get_field_id( 'heading_background' ); ?>" name="<?php echo $this->get_field_name( 'heading_background' ); ?>" style="width:100%;" >
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