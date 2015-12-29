<?php

/*-----------------------------------------------------------------------------------

	Plugin Name: Homepage: Featured Posts Widget
	Plugin URI: http://www.bloompixel.com
	Description: A widget that displays featured type 1 posts.
	Version: 1.0
	Author: Simrandeep Singh
	Author URI: http://www.simrandeep.com

-----------------------------------------------------------------------------------*/

add_action( 'widgets_init', 'bloompixel_featured_a_widget' );

function bloompixel_featured_a_widget() {
	register_widget( 'bloompixel_featured_a' );
}

class bloompixel_featured_a extends WP_Widget {
	function bloompixel_featured_a() {
		$widget_ops = array( 'classname' => 'featured-a', 'description' => __('A widget that displays the featured posts ', 'bloompixel') );
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'featured-a-widget' );
		$this->WP_Widget( 'featured-a-widget', __('Lineza Home: Featured Posts', 'bloompixel'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		$bp_options = get_option('revista');
		extract( $args );
		
		$title = apply_filters('widget_title', $instance['title'] );
		$cats = $instance['cats'];
		$heading_background = $instance['heading_background'];
		$featured_style = $instance['featured_style'];
		$show_cat = (int) $instance['show_cat'];

		echo $before_widget;

		// Display the widget title 
		if ( $title )
			echo "<div class='$heading_background'>" . $before_title . $title . $after_title . "</div>";	
		?>
		<!-- START WIDGET -->
		<div class="featured-section-1-container">
			<div class="featured-section-content">
				<?php if ( $featured_style == "style-1" ) { ?>
					<?php
						$fcount = 1;
						$featured_a = new WP_Query("cat=".$cats."&orderby=date&order=DESC&showposts=5");
					?>
					<?php if($featured_a->have_posts()) : while ($featured_a->have_posts()) : $featured_a->the_post(); ?>
					<?php include(trailingslashit( get_template_directory() ).'inc/review.php'); ?>
						<?php if($fcount == 1){ ?> 
						<div class="featured-left">
							<div class="featured-post featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured7');
										} else {
											echo '<img width="251" height="255" src="' . get_stylesheet_directory_uri() . '/images/251x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.post -->
						<?php } elseif($fcount == 2) { ?>
							<div class="featured-post featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured7');
										} else {
											echo '<img width="251" height="255" src="' . get_stylesheet_directory_uri() . '/images/251x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost--> 
							</div>
						<?php } elseif($fcount == 3) { ?>
							<div class="featured-post featured-post-big featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured8');
										} else {
											echo '<img width="693" height="510" src="' . get_stylesheet_directory_uri() . '/images/693x510.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title24 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost-->
						<?php } elseif($fcount == 4) { ?>
						<div class="featured-left">
							<div class="featured-post featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured7');
										} else {
											echo '<img width="251" height="255" src="' . get_stylesheet_directory_uri() . '/images/251x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost-->
						<?php } elseif($fcount == 5) { ?>
							<div class="featured-post featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured7');
										} else {
											echo '<img width="251" height="255" src="' . get_stylesheet_directory_uri() . '/images/251x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost-->
						</div>
					<?php } $fcount++; endwhile; ?>
					<?php endif; ?>
				<?php }
					/*********** STYLE 2
					***************************************/
					if ( $featured_style == "style-2" ) { ?>
					<?php
						$fcount = 1;
						$featured_a = new WP_Query("cat=".$cats."&orderby=date&order=DESC&showposts=5");
					?>
					<?php if($featured_a->have_posts()) : while ($featured_a->have_posts()) : $featured_a->the_post(); ?>
					<?php include(trailingslashit( get_template_directory() ).'inc/review.php'); ?>
						<?php if($fcount == 1){ ?> 
						<div class="featured-left">
							<div class="featured-post featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured7');
										} else {
											echo '<img width="251" height="255" src="' . get_stylesheet_directory_uri() . '/images/251x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.post -->
						<?php } elseif($fcount == 2) { ?>
							<div class="featured-post featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured7');
										} else {
											echo '<img width="251" height="255" src="' . get_stylesheet_directory_uri() . '/images/251x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost--> 
						</div>
						<?php } elseif($fcount == 3) { ?>
						<div class="featured-left">
							<div class="featured-post featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured7');
										} else {
											echo '<img width="251" height="255" src="' . get_stylesheet_directory_uri() . '/images/251x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost-->
						<?php } elseif($fcount == 4) { ?>
							<div class="featured-post featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured7');
										} else {
											echo '<img width="251" height="255" src="' . get_stylesheet_directory_uri() . '/images/251x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost-->
						</div>
						<?php } elseif($fcount == 5) { ?>
							<div class="featured-post featured-post-big featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured8');
										} else {
											echo '<img width="693" height="510" src="' . get_stylesheet_directory_uri() . '/images/693x510.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title24 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost-->
					<?php } $fcount++; endwhile; ?>
					<?php endif; ?>
				<?php }
					/*********** STYLE 3
					***************************************/
					if ( $featured_style == "style-3" ) { ?>
					<?php
						$fcount = 1;
						$featured_a = new WP_Query("cat=".$cats."&orderby=date&order=DESC&showposts=5");
					?>
					<?php if($featured_a->have_posts()) : while ($featured_a->have_posts()) : $featured_a->the_post(); ?>
					<?php include(trailingslashit( get_template_directory() ).'inc/review.php'); ?>
						<?php if($fcount == 1){ ?> 
							<div class="featured-post featured-post-big featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured8');
										} else {
											echo '<img width="693" height="510" src="' . get_stylesheet_directory_uri() . '/images/693x510.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title24 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.post -->
						<?php } elseif($fcount == 2) { ?>
						<div class="featured-left">
							<div class="featured-post featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured7');
										} else {
											echo '<img width="251" height="255" src="' . get_stylesheet_directory_uri() . '/images/251x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost--> 
						<?php } elseif($fcount == 3) { ?>
							<div class="featured-post featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured7');
										} else {
											echo '<img width="251" height="255" src="' . get_stylesheet_directory_uri() . '/images/251x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost-->
						</div>
						<?php } elseif($fcount == 4) { ?>
						<div class="featured-left">
							<div class="featured-post featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured7');
										} else {
											echo '<img width="251" height="255" src="' . get_stylesheet_directory_uri() . '/images/251x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost-->
						<?php } elseif($fcount == 5) { ?>
							<div class="featured-post featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured7');
										} else {
											echo '<img width="251" height="255" src="' . get_stylesheet_directory_uri() . '/images/251x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost-->
						</div>
					<?php } $fcount++; endwhile; ?>
					<?php endif; ?>
				<?php }
					/*********** STYLE 4
					***************************************/
					if ( $featured_style == "style-4" ) { ?>
					<?php
						$fcount = 1;
						$featured_a = new WP_Query("cat=".$cats."&orderby=date&order=DESC&showposts=5");
					?>
					<?php if($featured_a->have_posts()) : while ($featured_a->have_posts()) : $featured_a->the_post(); ?>
					<?php include(trailingslashit( get_template_directory() ).'inc/review.php'); ?>
						<?php if($fcount == 1) { ?> 
							<div class="featured-post featured-big featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured8');
										} else {
											echo '<img width="693" height="510" src="' . get_stylesheet_directory_uri() . '/images/693x510.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title24 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.post -->
						<?php } elseif($fcount == 2) { ?>
							<div class="featured-post featured-502 featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured11');
										} else {
											echo '<img width="502" height="255" src="' . get_stylesheet_directory_uri() . '/images/502x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost--> 
						<?php } elseif($fcount == 3) { ?>
							<div class="featured-post featured-502 featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured11');
										} else {
											echo '<img width="502" height="255" src="' . get_stylesheet_directory_uri() . '/images/502x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost-->
						<?php } $fcount++; endwhile; ?>
					<?php endif; ?>
				<?php }
					/*********** STYLE 5
					***************************************/
					if ( $featured_style == "style-5" ) { ?>
					<?php
						$fcount = 1;
						$featured_a = new WP_Query("cat=".$cats."&orderby=date&order=DESC&showposts=5");
					?>
					<?php if($featured_a->have_posts()) : while ($featured_a->have_posts()) : $featured_a->the_post(); ?>
					<?php include(trailingslashit( get_template_directory() ).'inc/review.php'); ?>
						<?php if($fcount == 1) { ?>
						<div class="featured-502">
							<div class="featured-post featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured11');
										} else {
											echo '<img width="502" height="255" src="' . get_stylesheet_directory_uri() . '/images/502x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.post -->
						<?php } elseif($fcount == 2) { ?>
							<div class="featured-post featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured11');
										} else {
											echo '<img width="502" height="255" src="' . get_stylesheet_directory_uri() . '/images/502x255.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title16 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost-->
						</div>
						<?php } elseif($fcount == 3) { ?>
							<div class="featured-post featured-big featured-post-<?php echo $fcount; ?>">
								<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail">
									<?php
										if ( has_post_thumbnail() ) {
											the_post_thumbnail('featured8');
										} else {
											echo '<img width="693" height="510" src="' . get_stylesheet_directory_uri() . '/images/693x510.png" />';
										}
									?>
									<header>
										<?php if ( $show_cat == 1 ) { ?>
											<div class="post-cats uppercase">
												<?php
													$category = get_the_category();
													if ($category) {
													  echo '<span>' . $category[0]->name.'</span> ';
													}
												?>
											</div>
										<?php } ?>
										<h2 class="title title24 uppercase">
											<?php the_title(); ?>
										</h2>
									</header><!--.header-->
								</a>
							</div><!--.smallpost-->
						<?php } $fcount++; endwhile; ?>
					<?php endif; ?>
				<?php }
					/*********** SLIDER
					***************************************/
					if ( $featured_style == "slider" ) { ?>
					<div class="featuredslider loading">
						<ul class="slides">
							<?php
								$featured_a = new WP_Query("cat=".$cats."&orderby=date&order=DESC&showposts=5");
							?>
							<?php if($featured_a->have_posts()) : while ($featured_a->have_posts()) : $featured_a->the_post(); ?>
								<li>
									<a href="<?php the_permalink() ?>" title="<?php the_title_attribute(); ?>" class="featured-thumbnail featured-widgetslider">
										<?php
											if ( has_post_thumbnail() ) {
												the_post_thumbnail('slider');
											} else {
												echo '<img src="' . get_stylesheet_directory_uri() . '/images/1195x510.png" />';
											}
										?>
										<div class="post-inner textcenter">
											<?php if($bp_options['bp_post_meta_options']['3'] == '1') { ?>
												<div class="post-cats uppercase">
													<?php
														$category = get_the_category();
														if ($category) {
														  echo '<span>' . $category[0]->name.'</span> ';
														}
													?>
												</div>
											<?php } ?>
											<header>
												<h2 class="title title22 uppercase">
													<?php the_title(); ?>
												</h2>
											</header><!--.header-->
										</div>
									</a>
								</li>
								<?php endwhile; ?>
							<?php endif; ?>
						</ul>
					</div>
				<?php }
					/*********** STYLE 8
					***************************************/
					if ( $featured_style == "style-8" ) { ?>
					
				<?php } ?>
			</div><!-- .featured-section-content -->
		</div><!-- .featured-section-1-container -->
		<!-- END WIDGET -->
		<?php

		echo $after_widget;

	}
	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['cats'] = implode(',' , $new_instance['cats']  );
		$instance['heading_background'] = strip_tags( $new_instance['heading_background'] );
		$instance['featured_style'] = strip_tags( $new_instance['featured_style'] );
		$instance['show_cat'] = intval( $new_instance['show_cat'] );

		return $instance;
	}
	
	function form( $instance ) {
		//Set up some default widget settings.
		$defaults = array(
			'cats' => 1,
			'show_cat' => 1,
		);
		$instance = wp_parse_args( (array) $instance, $defaults );
		$cat = isset( $instance[ 'cat' ] ) ? intval( $instance[ 'cat' ] ) : 0;
		$heading_background = isset( $instance['heading_background'] ) ? esc_attr( $instance['heading_background'] ) : '';
		$featured_style = isset( $instance['featured_style'] ) ? esc_attr( $instance['featured_style'] ) : '';
		$show_cat = isset( $instance[ 'show_cat' ] ) ? esc_attr( $instance[ 'show_cat' ] ) : 1;
		
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
		<p>
			<label for="<?php echo $this->get_field_id( 'featured_style' ); ?>"><?php _e( 'Featured Posts Style:','bloompixel' ); ?></label> 
			<select id="<?php echo $this->get_field_id( 'featured_style' ); ?>" name="<?php echo $this->get_field_name( 'featured_style' ); ?>" style="width:100%;" >
				<option value="style-1" <?php if ($featured_style == 'style-1') echo 'selected="selected"'; ?>><?php _e( 'Style 1','bloompixel' ); ?></option>
				<option value="style-2" <?php if ($featured_style == 'style-2') echo 'selected="selected"'; ?>><?php _e( 'Style 2','bloompixel' ); ?></option>
				<option value="style-3" <?php if ($featured_style == 'style-3') echo 'selected="selected"'; ?>><?php _e( 'Style 3','bloompixel' ); ?></option>
				<option value="style-4" <?php if ($featured_style == 'style-4') echo 'selected="selected"'; ?>><?php _e( 'Style 4','bloompixel' ); ?></option>
				<option value="style-5" <?php if ($featured_style == 'style-5') echo 'selected="selected"'; ?>><?php _e( 'Style 5','bloompixel' ); ?></option>
				<option value="slider" <?php if ($featured_style == 'slider') echo 'selected="selected"'; ?>><?php _e( 'Slider','bloompixel' ); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id("show_cat"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("show_cat"); ?>" name="<?php echo $this->get_field_name("show_cat"); ?>" value="1" <?php if (isset($instance['show_cat'])) { checked( 1, $instance['show_cat'], true ); } ?> />
				<?php _e( 'Show Categories', 'bloompixel'); ?>
			</label>
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