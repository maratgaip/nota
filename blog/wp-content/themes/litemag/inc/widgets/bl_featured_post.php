<?php
/*
Plugin Name: bl Featured Post
Description: Display posts tagged with "featured"
Author: Ivar Rafn
Version: 1
Author URI: http://www.bluth.is/
*/
class bl_featured_post extends WP_Widget {

	function bl_featured_post(){
		$widget_ops = array('classname' => 'bl_featured_post', 'description' => 'Display posts tagged with "featured" or the selected tag in Theme Options' );
		$this->WP_Widget('bl_featured_post', 'Bluthemes - Featured Post', $widget_ops);
	}


	function widget( $args, $instance ) {
		extract($args);
		$title 	= apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		$text 	= apply_filters( 'widget_text', empty( $instance['text'] ) ? '' : $instance['text'], $instance );
		$post_offset 	= (isset($instance['post_offset']) and is_numeric($instance['post_offset'])) ? $instance['post_offset'] : 0;
		$tag_to_get = empty( $instance['tag'] ) ? 'featured' : $instance['tag'];
		echo $before_widget;
	    global $post;

	    $args = array(
			'tag' 					=> $tag_to_get,
			'numberposts' 			=> 10,
			'offset' 				=> $post_offset, 
    		'ignore_sticky_posts'	=> 1, 
    		'order' 				=> 'DESC' );

		if($instance['order'] == 'popular'){
			$args['meta_key'] = 'blu_post_views_count';
			$args['orderby'] = 'blu_post_views_count';
		}

		echo !empty($title) ? $before_title.$title.$after_title : '';
	    $myposts = get_posts( $args );
	    $views_array = array(); ?>
		<div class="swiper-container swiper-container-featured">
		    <a class="arrow-left" href="#"></a>
			<a class="arrow-right" href="#"></a>
    		<div class="swiper-pagination"></div>
			<div class="swiper-wrapper"><?php 
				foreach( $myposts as $post ){  ?> 
					<div class="swiper-slide swiper-slide-large" style="width:763px;">
			        	<a href="<?php echo $post->guid; ?>" style="background-image: url('<?php echo blu_get_post_image( $post->ID, 'featured_post' ); ?>')">
			        		<h3 class="post-title">
			        			<?php echo $post->post_title; ?><?php
				        		if(!empty($instance['excerpt'])){ ?>
				        			<p><?php echo blu_truncate($post->post_content, 65, ' '); ?></p><?php
				        		} ?>
			        		</h3>
			        	</a>
					</div><?php 
				} ?>
			</div> <!-- swiper-wrapper -->
		</div> <!-- swiper-container --><?php
		wp_reset_postdata();
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] 			= strip_tags($new_instance['title']);
		$instance['tag'] 			= strip_tags($new_instance['tag']);
		$instance['post_offset'] 	= strip_tags($new_instance['post_offset']);
		$instance['order'] 			= $new_instance['order'];

		if ( current_user_can('unfiltered_html') )
			$instance['text'] =  $new_instance['text'];
		else
			$instance['text'] = stripslashes( wp_filter_post_kses( addslashes($new_instance['text']) ) );
		$instance['video'] = isset($new_instance['video']);
		$instance['excerpt'] = isset($new_instance['excerpt']);
		return $instance;
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => '', 'text' => '', 'order' => '', 'tag' => 'featured' ) );
		$title = strip_tags($instance['title']);
		$tag = strip_tags($instance['tag']);
		$text = esc_textarea($instance['text']);
		$order = $instance['order'];
		$post_offset = empty($instance['post_offset']) ? 0 : $instance['post_offset']; ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'bluth_admin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Tag to get:', 'bluth_admin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" name="<?php echo $this->get_field_name('tag'); ?>" type="text" value="<?php echo esc_attr($tag); ?>" />
		</p>
		<p>
			<input id="<?php echo $this->get_field_id('excerpt'); ?>" name="<?php echo $this->get_field_name('excerpt'); ?>" type="checkbox" <?php checked(isset($instance['excerpt']) ? $instance['excerpt'] : 0); ?> />&nbsp;
			<label for="<?php echo $this->get_field_id('excerpt'); ?>"><?php _e('Display the excerpt', 'bluth_admin'); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('post_offset'); ?>">Post Offset:</label>
			<select id="<?php echo $this->get_field_id('post_offset'); ?>" name="<?php echo $this->get_field_name('post_offset'); ?>">
				<?php 
					$i = 0;
					while ($i <= 10) {
						echo ($i == $post_offset) ? '<option value="'.$i.'" selected="">'.$i.'</option>' : '<option value="'.$i.'">'.$i.'</option>';
						$i++;
					}
				?>
			</select>
		</p>	
		<p>
			<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Order By:', 'bluth_admin'); ?></label>
			<select style="width:216px" id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
			  	<option value="date" <?php echo ($instance['order'] == 'date') ? 'selected=""' : ''; ?>>Date</option> 
			  	<option value="popular" <?php echo ($instance['order'] == 'popular') ? 'selected=""' : ''; ?>>Popularity(View count)</option> 
			</select>

		</p>
<?php
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("bl_featured_post");') );