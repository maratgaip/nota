<?php
/*
Plugin Name: BL Social Counter
Description: Get the social reach of your pages
Author: Bluthemes
Version: 1
Author URI: http://www.bluthemes.com
*/
class bl_social_counter extends WP_Widget {

	function bl_social_counter(){
		$widget_ops = array('classname' => 'bl_social_counter', 'description' => 'Get the social reach of your pages' );
		$this->WP_Widget('bl_social_counter', 'Bluthemes - Social Counter', $widget_ops);
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, 
			array( 
				'title' => '', 
				'facebook' => '',
				'twitter' => '',
				'google-plus' => '',
				'instagram' => ''
			) 
		); 
		$title 	= esc_textarea($instance['title']);
		$author_username  = !empty($instance['author_username']) ? $instance['author_username'] : 'no';
		$facebook 	= esc_textarea($instance['facebook']);
		$twitter 	= esc_textarea($instance['twitter']);
		$googleplus = esc_textarea($instance['google-plus']);
		$instagram 	= esc_textarea($instance['instagram']); ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'bluth_admin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
	  	<strong>Instructions</strong>
	  	<ol>
	    	<li>Fill in the Twitter API in Theme Options -> Social to use Twitter Count</li>
	    	<li>Put in your username below <strong>or</strong> check the "Get authors username" box and place it in the Post Sidebar (and configure in Theme Options -> Users)</li>
	  	</ol>
	  	<p>
	    	<label for="<?php echo $this->get_field_id('author_username'); ?>">Get authors username (ignores options below)</label><br>
	    		<select id="<?php echo $this->get_field_id('author_username'); ?>" name="<?php echo $this->get_field_name('author_username'); ?>" style="width: 100%;">
		      	<option value="no" <?php echo ($author_username == 'no') ? 'selected' : ''; ?>>No</option>
		      	<option value="yes" <?php echo ($author_username == 'yes') ? 'selected' : ''; ?>>Yes</option>
		    </select>
	  	</p>
	    <p>
      		<label for="<?php echo $this->get_field_id('facebook'); ?>">Facebook Page:</label><br />
			<input type="text" class="widefat" value="<?php echo $facebook; ?>" id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" placeholder="bluthemes">
	    </p>
	    <p>
      		<label for="<?php echo $this->get_field_id('twitter'); ?>">Twitter (set up Twitter API in Theme Options -> Social):</label><br />
			<input type="text" class="widefat" value="<?php echo $twitter; ?>" id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" placeholder="bluthemes">
	    </p>
	    <p>
      		<label for="<?php echo $this->get_field_id('google-plus'); ?>">Google+:</label><br />
			<input type="text" class="widefat" value="<?php echo $googleplus; ?>" id="<?php echo $this->get_field_id('google-plus'); ?>" name="<?php echo $this->get_field_name('google-plus'); ?>" placeholder="116124120939614234044">
	    </p>
	    <p>
      		<label for="<?php echo $this->get_field_id('instagram'); ?>">Instagram:</label><br />
			<input type="text" class="widefat" value="<?php echo $instagram; ?>" id="<?php echo $this->get_field_id('instagram'); ?>" name="<?php echo $this->get_field_name('instagram'); ?>" placeholder="bluthdesign">
	    </p>

	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] 		= strip_tags($new_instance['title']);
		$instance['author_username']    = strip_tags($new_instance['author_username']);
		$instance['facebook']	=  $new_instance['facebook'];
		$instance['twitter'] 	=  $new_instance['twitter'];
		$instance['google-plus']=  $new_instance['google-plus'];
		$instance['instagram'] 	=  $new_instance['instagram'];
		$instance['filter'] = isset($new_instance['filter']);
 		
 		delete_transient( 'bl_social_counter' );

		return $instance;
	}

	function widget( $args, $instance ) {
		extract($args);

		$title 	= apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
		// if author username is checked, then override everything with the options in the Theme Options
		if($instance['author_username'] == 'yes'){
			$instance['facebook'] = !(of_get_option('author_facebook_username_'.get_the_author_meta('ID'))) ? $instance['facebook'] : of_get_option('author_facebook_username_'.get_the_author_meta('ID'));
			$instance['twitter'] =  !(of_get_option('author_twitter_username_'.get_the_author_meta('ID'))) ? $instance['twitter'] : of_get_option('author_twitter_username_'.get_the_author_meta('ID'));
			$instance['google-plus'] = !(of_get_option('author_google_username_'.get_the_author_meta('ID'))) ? $instance['google-plus'] : of_get_option('author_google_username_'.get_the_author_meta('ID'));
			$instance['instagram'] = !(of_get_option('author_instagram_username_'.get_the_author_meta('ID'))) ? $instance['instagram'] : of_get_option('author_instagram_username_'.get_the_author_meta('ID'));
			$author_id = get_the_author_meta('ID');
		}else{
			$author_id = 0;
		}
		$social_sites = wp_parse_args( (array) $instance, array( 'facebook' => '', 'google-plus' => '', 'twitter' => '', 'instagram' => '') );
		// var_dump($social_sites);
		$social_sites = array_filter($social_sites);

		echo $before_widget;
		echo !empty($title) ? $before_title.$title.$after_title : ''; ?>
		
		<div class="widget-body"> <?php 

			$social_authority = blu_get_social_counter($social_sites, $author_id);

				$social_locale = array(
					'facebook' => array( 'suffix' => __('likes', 'bluth'), 'url' => 'http://www.facebook.com/'),
					'twitter' => array( 'suffix' => __('followers', 'bluth'), 'url' => 'http://www.twitter.com/'),
					'google-plus' => array( 'suffix' => __('people', 'bluth'), 'url' => 'https://plus.google.com/'),
					'instagram' => array( 'suffix' => __('followers', 'bluth'), 'url' => 'http://www.instagram.com/'),
				); 
			?>


			<ul class="list-unstyled clearfix"><?php
			foreach ($social_authority as $key => $value) { 
				if($key == 'twitter' and !of_get_option('twitter_api_key')){  
					if(is_super_admin()){ ?>
						<li class="<?php echo $key; ?>" style="padding:5px;"><small style="font-size: 9px; color: #777777;">Configure Twitter API Key in Appearance -> Theme Options</small></li><?php
					}
				}else if($key == 'google-plus'){ ?>
					<li class="<?php echo $key; ?>"><a href="<?php echo $social_locale[$key]['url'].$social_sites[$key]; ?>?rel=author" target="_blank"><div class="bl_wrapper"><i class="fa fa-<?php echo $key; ?>"></i><h4><?php echo number_format((int)$value, 0, ',','.'); ?></h4><small><?php echo $social_locale[$key]['suffix']; ?></small></div></a></li><?php
				}else{ ?>
					<li class="<?php echo $key; ?>"><a href="<?php echo $social_locale[$key]['url'].$social_sites[$key]; ?>" target="_blank"><div class="bl_wrapper"><i class="fa fa-<?php echo $key; ?>"></i><h4><?php echo number_format((int)$value, 0, ',','.'); ?></h4><small><?php echo $social_locale[$key]['suffix']; ?></small></div></a></li><?php
				}
			} ?>

			</ul>
		</div><?php
		
		echo $after_widget;
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("bl_social_counter");') );

add_action('admin_enqueue_scripts', 'blu_admin_scripts');
