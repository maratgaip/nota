<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Homepage: Featured Type 2 Widget
	Plugin URI: http://www.bloompixel.com
	Description: A widget that displays featured type 2 posts.
	Version: 1.0
	Author: Simrandeep Singh
	Author URI: http://www.simrandeep.com

-----------------------------------------------------------------------------------*/

add_action( 'widgets_init', 'bloompixel_featured_b_widget' );

function bloompixel_featured_b_widget() {
	register_widget( 'bloompixel_featured_b' );
}

class bloompixel_featured_b extends WP_Widget {
	function bloompixel_featured_b() {
		$widget_ops = array( 'classname' => 'featured-b', 'description' => __('A widget that displays the module D posts ', 'bloompixel') );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'featured-b-widget' );
		$this->WP_Widget( 'featured-b-widget', __('Lineza Home: Module D Widget', 'bloompixel'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		$bp_options = get_option('revista');
		extract( $args );
		
		$title = apply_filters('widget_title', $instance['title'] );
		$posts = $instance['posts'];
		$excerpt_length = $instance['excerpt_length'];
		$heading_background = $instance['heading_background'];
		$show_excerpt = (int) $instance['show_excerpt'];
		$show_cat = (int) $instance['show_cat'];
		$show_author = (int) $instance['show_author'];
		$show_date = (int) $instance['show_date'];
		$show_comments = (int) $instance['show_comments'];
		$cats = $instance['cats'];

		echo $before_widget;

		// Display the widget title 
		if ( $title )
			echo "<div class='$heading_background'>" . $before_title . $title . $after_title . "</div>";	
		?>
		<!-- START WIDGET -->
		<div class="module-d-widget">
			<?php
				$j = 1;
				$featured_b = new WP_Query("cat=".$cats."&orderby=date&order=DESC&showposts=".$posts);
			?>
			<?php if($featured_b->have_posts()) : while ($featured_b->have_posts()) : $featured_b->the_post(); ?>
			<?php include(trailingslashit( get_template_directory() ).'inc/review.php'); ?>
				<div class="featured-item<?php if($j == 4){echo ' last';} ?>">
					<div class="featured-content">
						<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail featured-thumbnail-list">
							<?php
								if ( has_post_thumbnail() ) {
									the_post_thumbnail('featured3');
								} else {
									echo '<img width="277" height="260" src="' . get_stylesheet_directory_uri() . '/images/277x260.png" />';
								}
							?>
						</a>
						<div class="post-inner">
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
					</div><!-- .featured-content -->
				</div>
			<?php $j++; endwhile; ?>
			<?php endif; ?>
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
		$instance['excerpt_length'] = strip_tags( $new_instance['excerpt_length'] );
		$instance['heading_background'] = strip_tags( $new_instance['heading_background'] );
		$instance['show_excerpt'] = intval( $new_instance['show_excerpt'] );
		$instance['show_cat'] = intval( $new_instance['show_cat'] );
		$instance['show_author'] = intval( $new_instance['show_author'] );
		$instance['show_date'] = intval( $new_instance['show_date'] );
		$instance['show_comments'] = intval( $new_instance['show_comments'] );
		$instance['cats'] = implode(',' , $new_instance['cats']  );

		return $instance;
	}
	
	function form( $instance ) {
		//Set up some default widget settings.
		$defaults = array(
			'posts' => 4,
			'excerpt_length' => 15,
			'show_excerpt' => 1,
			'show_cat' => 1,
			'show_author' => 1,
			'show_date' => 1,
			'show_comments' => 0,
			'cats' => 1,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$heading_background = isset( $instance['heading_background'] ) ? esc_attr( $instance['heading_background'] ) : '';
		$show_cat = isset( $instance[ 'show_excerpt' ] ) ? esc_attr( $instance[ 'show_excerpt' ] ) : 1;
		$show_cat = isset( $instance[ 'show_cat' ] ) ? esc_attr( $instance[ 'show_cat' ] ) : 1;
		$show_author = isset( $instance[ 'show_author' ] ) ? esc_attr( $instance[ 'show_author' ] ) : 1;
		$show_date = isset( $instance[ 'show_date' ] ) ? esc_attr( $instance[ 'show_date' ] ) : 1;
		$show_comments = isset( $instance[ 'show_comments' ] ) ? esc_attr( $instance[ 'show_comments' ] ) : 1;
		
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
		<?php
	}
}

?>