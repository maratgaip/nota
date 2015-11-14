<?php
/*
Plugin Name: Bl Post Slider
Description: Display posts
Author: Ivar Rafn
Version: 1
Author URI: http://www.bluthemes.com
*/
class bl_posts_slider extends WP_Widget {

	function bl_posts_slider(){
		$widget_ops = array('classname' => 'bl_posts_slider', 'description' => 'Display posts in slider' );
		$this->WP_Widget('bl_posts_slider', 'Bluthemes - Posts Slider', $widget_ops);
	}


	function widget( $args, $instance ) {

		extract($args);
		extract($instance);
		echo $before_widget;
		
		
		# THE TITLE
		if(!empty($title)){

			$title 	= apply_filters( 'widget_title', empty( $title ) ? '' : $title, $instance, $this->id_base );
			echo $before_title.$title.$after_title;
		
		}
		
		#	THE QUERY
	
		// categories
		$cat_posts 		= (empty($cat_posts) or (is_array($cat_posts) and in_array('0', $cat_posts))) ? '0' : $cat_posts;
		$tag_posts 		= (empty($tag_posts) or (is_array($tag_posts) and in_array('0', $tag_posts))) ? '0' : $tag_posts;
		$posts_per_page = empty($posts_per_page) ? 3 : $posts_per_page;
		$slider_width 	= empty($slider_width) ? 'normal' : $slider_width;
		$orderby 		= empty($orderby) ? 'date' : $orderby;
		$order 			= empty($order) ? 'desc' : $order;
	    $args = array(
	    	'offset' 				=> 0,
			'display_author' 		=> empty($display_author) ? 'true' : $display_author,
			'display_date' 			=> empty($display_date) ? 'true' : $display_date,
			'posts_per_page' 		=> $posts_per_page,
    		'ignore_sticky_posts'	=> 1, 
    		'category__in'			=> $cat_posts,
    		'orderby' 				=> $orderby, 
    		'order' 				=> $order, 
    	);
    	if(!empty($tag_posts)) $args['tag_slug__in'] = $tag_posts;

	    if(!empty($cat_posts) and is_array($cat_posts)){
			foreach($cat_posts as $cat_id) {
			 	$cat_name = get_cat_name($cat_id);
				$cat_menu[$cat_id] = $cat_name;
			} 
	    }

	    $post_meta_array = array('categories', 'comments');
	    $post_tab_meta_array = array('comments', 'author', 'categories');
	    
	    if($args['display_date'] == 'false'){
	    	array_push($post_meta_array, 'date');
	    	array_push($post_tab_meta_array, 'date');
	    }
	    if($args['display_author'] == 'false'){
	    	array_push($post_meta_array, 'author');
	    }

	    // store the global posts
	    global $posts_displayed;

	    $post_array = array();
	    $q = new WP_Query($args);
	    if($q->have_posts()){
	    	while($q->have_posts()){ $q->the_post();
	    		$post_array[] = $q->posts;
	    		array_push($posts_displayed, get_the_ID());
	    	}
	    }

	    $tabscounter = 0;
		echo '  <a class="arrow-left" href="#"></a>';
    	echo '	<a class="arrow-right" href="#"></a>';

        echo '    <div class="tabs">';
	    foreach($post_array[0] as $p){
	    if($tabscounter == 0)
	    		echo '		<a href="#" class="active">';
	    	else
	    		echo '		<a href="#">';

	    echo 			'<h4>' . $p->post_title . '</h4>';
        echo 			blu_get_meta_info($p->ID, $post_tab_meta_array);
	    echo '		</a>';
	    $tabscounter++;
	    }
        echo '    </div>';

        echo '    <div class="swiper-container ' . $slider_width . '">';
        echo '        <div class="swiper-wrapper">';


		foreach($post_array[0] as $p){
		
		// get the post image
		$post_image = blu_get_post_image( $p->ID, 'custom', false, false, array('width' => 1920, 'height' => 500));

		if(!is_array($post_image)){
			$post_image = array('url' => $post_image);
		}

        echo '            <div class="swiper-slide">';
        echo '            		<div class="slide-image" style="background-image:url('.$post_image['url'].');"></div>';
        echo '                <div class="container content-slide">';
        echo 						blu_get_meta_info($p->ID, array('comments', 'author', 'date'));
        echo '                   	<h2 class="slide-title">';
        echo '							<a href="' . get_the_permalink($p->ID) . '">' . $p->post_title . '</a>';
        echo '						</h2>';
        if(get_post_meta( $p->ID, 'blu_post_subtitle', true )){
			echo ' 					<h3 class="slide-sub-title">' . get_post_meta( $p->ID, 'blu_post_subtitle', true ) . '</h3>';
		}
        echo '						<div class="slide-author-info">';
		if($args['display_author'] != 'false'){
        echo ' 							<img class="slide-author-image" src="' . blu_get_avatar_url( get_avatar( $p->post_author , '40' ), $p->post_author ). '">';
    	}
        echo 							blu_get_meta_info($p->ID, $post_meta_array);
        echo '						</div>';
        echo '                </div>';
        echo '            </div>';
	    }

        echo '        </div>';
        echo '    </div>';
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		
		$instance = $old_instance;
		foreach($new_instance as $key => $value){
			$instance[$key] = $value;
		}
		return $instance;
	}

	function form( $instance ) {
		wp_enqueue_script( 'suggest' );
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'subtitle' => '', 'posts_per_page' => 3, 'slider_width' => 'normal', 'display_author' => true, 'display_date' => true, 'cat_posts' => '', 'tag_posts' => '', 'orderby' => 'date', 'order' => 'desc' ) );

		extract($instance); ?>
		
		<div class="full" style="margin-top: 20px; margin-bottom: 10px;">
			<div class="half">
				<label class="customlabel" for="<?php echo $this->get_field_id('Title'); ?>">
					<?php _e('Title:', 'bluth_admin'); ?>
					<small><?php _e('Title (optional)', 'bluth_admin'); ?></small>
				</label>
			</div>
			<div class="half">
				<input class="normal" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</div>
		</div>
		<div class="full" style="margin-bottom: 10px;">
			<div class="half">
				<label class="customlabel" for="<?php echo $this->get_field_id('subtitle'); ?>">
					<?php _e('Sub-Title:', 'bluth_admin'); ?>
					<small><?php _e('Extra Title (optional)', 'bluth_admin'); ?></small>
				</label>
			</div>
			<div class="half">
				<input class="normal" id="<?php echo $this->get_field_id('subtitle'); ?>" name="<?php echo $this->get_field_name('subtitle'); ?>" type="text" value="<?php echo esc_attr($subtitle); ?>" />
			</div>
		</div>

		<!-- POSTS PER PAGE -->
		<div class="full" style="margin-bottom: 10px;">
			<div class="half">
				<label class="customlabel" for="<?php echo $this->get_field_id('posts_per_page'); ?>">
					<?php _e('Posts Per Load:', 'bluth_admin'); ?>
					<small><?php _e('How many posts to load', 'bluth_admin'); ?></small>
				</label>
			</div>
			<div class="half">
				<input class="normal" id="<?php echo $this->get_field_id('posts_per_page'); ?>" name="<?php echo $this->get_field_name('posts_per_page'); ?>" type="text" value="<?php echo esc_attr($posts_per_page); ?>" />
			</div>
		</div>
		<!-- POSTS PER PAGE -->
		<div class="full" style="margin-bottom: 10px;">
			<div class="half">
				<label class="customlabel" for="<?php echo $this->get_field_id('slider_width'); ?>">
					<?php _e('Slider Width:', 'bluth_admin'); ?>
					<small><?php _e('How many posts to load', 'bluth_admin'); ?></small>
				</label>
			</div>
			<div class="half">
				<select class="normal" id="<?php echo $this->get_field_id('slider_width'); ?>" name="<?php echo $this->get_field_name('slider_width'); ?>" value="<?php echo esc_attr($slider_width); ?>" >
					<option value="normal" <?php echo $slider_width == 'normal' ? 'selected' : ''; ?>>Normal</option>
					<option value="full-width" <?php echo $slider_width == 'full-width' ? 'selected' : ''; ?>>Full Width</option>
				</select>
			</div>
		</div>
		<!-- DISPLAY AUTHOR -->
		<div class="full" style="margin-bottom: 10px;">
			<div class="half">
				<label class="customlabel" for="<?php echo $this->get_field_id('display_author'); ?>">
					<?php _e('Display Author:', 'bluth_admin'); ?>
					<small><?php _e('Do you want to display the author in the footer?', 'bluth_admin'); ?></small>
				</label>
			</div>
			<div class="half">
				<select class="normal" id="<?php echo $this->get_field_id('display_author'); ?>" name="<?php echo $this->get_field_name('display_author'); ?>">
					<option value="true"<?php selected($instance['display_author'], 'true') ?>>On</option>
					<option value="false"<?php selected($instance['display_author'], 'false') ?>>Off</option>
				</select>
			</div>
		</div>
		<!-- DISPLAY DATE -->
		<div class="full" style="margin-bottom: 10px;">
			<div class="half">
				<label class="customlabel" for="<?php echo $this->get_field_id('display_date'); ?>">
					<?php _e('Display Date:', 'bluth_admin'); ?>
					<small><?php _e('Do you want to display the date in the footer?', 'bluth_admin'); ?></small>
				</label>
			</div>
			<div class="half">
				<select class="normal" id="<?php echo $this->get_field_id('display_date'); ?>" name="<?php echo $this->get_field_name('display_date'); ?>">
					<option value="true"<?php selected($instance['display_date'], 'true') ?>>On</option>
					<option value="false"<?php selected($instance['display_date'], 'false') ?>>Off</option>
				</select>
			</div>
		</div>
		<div class="full" style="margin-bottom: 10px;">

			<!-- CATEGORIES -->
			<!-- CATEGORIES -->
			<!-- CATEGORIES -->
			<div class="half">
				<label class="customlabel" for="<?php echo $this->get_field_id('cat_posts'); ?>">
					<?php _e('Only Show Categories:', 'bluth_admin'); ?>
					<small><?php _e('Hold CTRL for multiple', 'bluth_admin'); ?></small>
				</label>
				<select style="min-height: 108px;" class="normal" id="<?php echo $this->get_field_id('cat_posts'); ?>" name="<?php echo $this->get_field_name('cat_posts'); ?>[]" multiple><?php 
					if(is_array($cat_posts) and in_array('0', $cat_posts)){ ?>
						<option value="0" selected>All</option><?php 
					}else{ ?>
						<option value="0">All</option><?php
					} 
					// $category_ids = get_all_category_ids();
					$categories = get_categories( array( 'hide_empty' => 1 ) );
					foreach($categories as $category) {
					 	echo (is_array($cat_posts) and in_array($category->term_id, $cat_posts)) ? '<option value="'.(int)$category->term_id.'" selected>'.$category->name.'</option>' : '<option value="'.$category->term_id.'">'.$category->name.'</option>';
					} ?>
				</select>
			</div>
			<div class="half">

				<!-- TAGS -->
				<!-- TAGS -->
				<!-- TAGS -->
				<div class="full" style="margin-bottom: 10px;">
					
					<label class="customlabel" for="<?php echo $this->get_field_id('tag_posts'); ?>">
						<?php _e('Only Show Tags', 'bluth_admin'); ?>
						<small><?php _e('Separated by comma', 'bluth_admin'); ?></small>
					</label><?php

				   	  	$tags = $tag_posts;

				   	  	if(is_array($tag_posts)){
				   	  		$tags = array();
				   	  		foreach ($tag_posts as $tag) {
				   	  			$tag_obj = get_tag($tag);
				   	  			if($tag_obj){
				   	  				$tags[] = $tag_obj->name;
				   	  			}
				   	  		}
							$tags = implode(',', $tags);
				   	  	}				
				   	?>
					<input class="normal" id="<?php echo $this->get_field_id('tag_posts'); ?>" name="<?php echo $this->get_field_name('tag_posts'); ?>" type="text" value="<?php echo esc_attr($tags); ?>" onfocus="setSuggestTags('<?php echo $this->get_field_id('tag_posts'); ?>');" />
				</div>

				<div class="full" style="margin-bottom: 10px;">	

					<!-- ORDER BY -->
					<!-- ORDER BY -->
					<!-- ORDER BY -->
					<label class="customlabel" for="<?php echo $this->get_field_id('orderby'); ?>">
						<?php _e('Order By:', 'bluth_admin'); ?>
						<small><?php _e('How to order the posts', 'bluth_admin'); ?></small>
					</label>
					<select class="normal" id="<?php echo $this->get_field_id('orderby'); ?>" name="<?php echo $this->get_field_name('orderby'); ?>">
						<option value="date"<?php echo $orderby == 'date' ? ' selected' : ''; ?>>Date</option>
						<option value="name"<?php echo $orderby == 'name' ? ' selected' : ''; ?>>Name</option>
					</select>
				</div>
				<div class="full" style="margin-bottom: 10px;">	

					<!-- ORDER -->
					<!-- ORDER -->
					<!-- ORDER -->
					<label class="customlabel" for="<?php echo $this->get_field_id('orderby'); ?>">
						<?php _e('Order Sort:', 'bluth_admin'); ?>
						<small><?php _e('How to order the posts', 'bluth_admin'); ?></small>
					</label>
					<select class="normal" id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
						<option value="desc"<?php echo $order == 'desc' ? ' selected' : ''; ?>>Descending</option>
						<option value="asc"<?php echo $order == 'asc' ? ' selected' : ''; ?>>Ascending</option>
					</select>
				</div>	
			</div>
		</div>
		<br>
		<hr>
		<br>

		<script type="text/javascript" >
		    // Function to add auto suggest
		    if (typeof(setSuggestTags) !== "function") { 
			    function setSuggestTags(id) {
			        jQuery('#' + id).suggest("<?php echo site_url(); ?>/wp-admin/admin-ajax.php?action=ajax-tag-search&tax=post_tag", {multiple:true, multipleSep: ","});
			    }
		    }
	    </script>

<?php
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("bl_posts_slider");') );