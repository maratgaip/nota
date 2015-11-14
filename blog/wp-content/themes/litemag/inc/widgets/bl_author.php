<?php
/*
Plugin Name: blu Author
Description: Box to show author information
Author: Ivar Rafn
Version: 1
Author URI: http://www.bluth.is/
*/
class blu_author extends WP_Widget {

	function blu_author(){
		$widget_ops = array('classname' => 'bl_author', 'description' => 'Display the blogs author(s)' );
		$this->WP_Widget('bl_author', 'Bluthemes - Author', $widget_ops);
	}

	function form($instance){

		$instance = wp_parse_args((array)$instance, array(
			'title' => '',
			'author_layout' => 'multi',
			'text' => '',
			'show_twitter' => true,
			'single_author_id' => '',
		));
		$blu_author_roles 				= empty($instance['blu_author_roles']) ? array() : $instance['blu_author_roles'];
		
		$title 				= strip_tags($instance['title']);
		// $author_name 		= strip_tags( $instance['author_name']);
		$text 				= esc_textarea($instance['text']);
		// $image_bg_uri 		= esc_url( $instance['image_bg_uri']);
		// $image_author_uri 	= esc_url( $instance['image_author_uri']);
	?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'bluth_admin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
	  	<p>
	    	<label for="<?php echo $this->get_field_id('author_layout'); ?>">Layout (single author or multiple)</label><br>
	    	<select class="author_layout" id="<?php echo $this->get_field_id('author_layout'); ?>" name="<?php echo $this->get_field_name('author_layout'); ?>" style="width: 100%;">
	      		<option value="multi" <?php selected($instance['author_layout'], 'multi') ?>>Show a list of authors</option>
		      	<option value="single" <?php selected($instance['author_layout'], 'single') ?>>Show a single author</option>
	    	</select>
  		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked($instance['show_twitter'], true) ?> id="<?php echo $this->get_field_id('show_twitter'); ?>" name="<?php echo $this->get_field_name('show_twitter'); ?>" />
			<label for="<?php echo $this->get_field_id('show_twitter'); ?>"><?php _e('Show Twitter link', 'bluth'); ?></label><br />  	
		</p>
		
  		<div id="blu_author_multi" data-field-id="<?php echo $this->get_field_id('author_layout'); ?>"<?php echo $instance['author_layout'] == 'single' ? ' class="hidden"' : '' ?>>
  			<hr>
  			<p><strong>Options for all authors layout</strong></p>
			<p>
				<label for="<?php echo $this->get_field_id('blu_author_roles'); ?>">Select Author roles to display:</label><br />
				<select class="widefat" id="<?php echo $this->get_field_id('blu_author_roles'); ?>" name="<?php echo $this->get_field_name('blu_author_roles'); ?>[]" multiple>
					<option value="administrator" <?php echo (in_array('administrator', $blu_author_roles)) ? 'selected' : ''; ?>>Administrator</option>
					<option value="author" <?php echo (in_array('author', $blu_author_roles)) ? 'selected' : ''; ?>>Author</option>
					<option value="editor" <?php echo (in_array('editor', $blu_author_roles)) ? 'selected' : ''; ?>>Editor</option>
					<option value="contributor" <?php echo (in_array('contributor', $blu_author_roles)) ? 'selected' : ''; ?>>Contributor</option>
					<option value="subscriber" <?php echo (in_array('subscriber', $blu_author_roles)) ? 'selected' : ''; ?>>Subscriber</option>
			</select>
			<p style="font-size:12px;color:#888">Hold Ctrl/Command to select multiple options.</p>
			</p>
  		
  		</div>
  		<div id="blu_author_single" data-field-id="<?php echo $this->get_field_id('author_layout'); ?>"<?php echo $instance['author_layout'] == 'multi' ? ' class="hidden"' : '' ?>>
  			<hr>
  			<p><strong>Options for single author layout</strong></p>
			<p>
				<label for="<?php echo $this->get_field_id('single_author_id'); ?>">Select Author:</label><br />
		    	<select id="<?php echo $this->get_field_id('single_author_id'); ?>" name="<?php echo $this->get_field_name('single_author_id'); ?>" style="width: 100%;">
		    		<option value="dynamic" <?php echo ($instance['single_author_id'] == 'dynamic' ? ' selected="selected"' : ''); ?>>Get Post Author</option><?php 

					$users = get_users( array('who' => 'authors') );

					foreach($users as $user){

						echo '<option value="'.$user->ID.'"'.($instance['single_author_id'] == $user->ID ? ' selected="selected"' : '').'>'.$user->user_nicename.'</option>';
					}
				?>
				</select>
			</p>
  		</div>
  		<script type="text/javascript">
  			jQuery(function($){
  				$('.author_layout').unbind('change').change(function(){
  					if($(this).find('option:selected').val() == 'single'){
  						$('#blu_author_single[data-field-id="'+$(this).attr('id')+'"]').removeClass('hide');
  					}else{
  						$('#blu_author_single[data-field-id="'+$(this).attr('id')+'"]').addClass('hide');
  					}
  				});
  			});
  		</script><?php
	}

	function update($new_instance, $old_instance){

		$instance = $old_instance;
		$instance['title'] 				= strip_tags($new_instance['title']);
		$instance['author_layout']    	= in_array($new_instance['author_layout'], array('multi','single')) ? $new_instance['author_layout'] : 'multi';
		$instance['show_twitter'] 		= isset($new_instance['show_twitter']);
		$instance['single_author_id'] 	= is_numeric($new_instance['single_author_id']) ? $new_instance['single_author_id'] : 0;
    	$instance['blu_author_roles'] 	= $new_instance['blu_author_roles'];

		if(current_user_can('unfiltered_html')){
			$instance['text'] =  $new_instance['text'];
		}else{
			$instance['text'] = stripslashes(wp_filter_post_kses(addslashes($new_instance['text'])));
		}

		return $instance;
	}

	function widget($args, $instance){

		extract($args);

		$title 	= apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);

		echo $before_widget;
		echo !empty($title) ? $before_title.$title.$after_title : '';

		?>
		<div class="author-wrap author-layout-<?php echo $instance['author_layout'] ?>"><?php
	
			switch($instance['author_layout']){
				case 'single': 
					// var_dump($instance['single_author_id']);
						if($instance['single_author_id'] == 'dynamic'){
							$instance['single_author_id'] = get_the_author_meta('ID');
						}

						$user = get_user_by('id', $instance['single_author_id']);
						$text = get_the_author_meta('description', $user->ID);
						$job = bl_utilities::get_option('author_job_'.$user->ID);
						$twitter_link = bl_utilities::get_option('author_twitter_'.$user->ID);
						$twitter_username = bl_utilities::get_option('author_twitter_username_'.$user->ID);
						$cover = bl_utilities::get_option('author_box_image_'.$user->ID);
						
						echo '<div class="author-box-container">';
							echo '<div class="author-image">';
								echo '<img src="' . blu_get_avatar_url( get_avatar( $user->ID , '50' ), $user->ID ) . '">';
								// echo get_avatar($user->ID, '50');
							echo '</div>';
							echo '<div class="author-box-body">';
								echo '<div class="author-bio">';
									echo '<h4>';
									echo 	'<a href="' . $user->user_url . '">' . $user->display_name . '</a>';
									echo 	'<small>' . $job . '</small>';
									echo 	'<a class="author-twitter" href="' . $twitter_link . '"><i class="fa fa-twitter"></i> ' . $twitter_username . '</a>';
									echo '</h4>';
									// echo empty($text) ? '' : '<p class="muted">'.$text.'</p>';
								echo '</div>';
								echo '<div class="author-stats">';
									echo '<a href="' . get_author_posts_url($user->ID) . '" class="author-post-count">';
									echo 	'<span>' . count_user_posts( $user->ID ) . '</span>';
									echo 	__('Posts', 'bluth');
									echo '</a>';
								echo '</div>';
							echo '</div>';
						echo '</div>';

					break;
				case 'multi':
				default:

					$authors = array();
					
					if(empty($instance['blu_author_roles'])){
						$instance['blu_author_roles'] = array('author', 'administrator', 'editor', 'subscriber', 'composer');
					}

				    foreach ($instance['blu_author_roles'] as $role) :

				        $users_query = new WP_User_Query( array( 

				            'fields' => 'all_with_meta', 
				            'role' => $role, 
				            'orderby' => 'display_name'

				        ) );

				        $results = $users_query->get_results();
				    
				        if ($results) $authors = array_merge($authors, $results);
				    
				    endforeach;

					foreach($authors as $user){

						$user = get_user_by('id', $user->ID);
						$text = get_the_author_meta('description', $user->ID);
						$job = bl_utilities::get_option('author_job_'.$user->ID);
						$twitter_link = bl_utilities::get_option('author_twitter_'.$user->ID);
						$twitter_username = bl_utilities::get_option('author_twitter_username_'.$user->ID);
						$cover = bl_utilities::get_option('author_box_image_'.$user->ID);

						echo '<div class="author-box-container">';
							echo '<div class="author-image">';
								echo '<img src="' . blu_get_avatar_url( get_avatar( $user->ID , '50' ), $user->ID ) . '">';
								// echo get_avatar($user->ID, '50');
							echo '</div>';
							echo '<div class="author-box-body">';
								echo '<div class="author-bio">';
									echo '<h4>';
									echo 	'<a href="' . $user->user_url . '">' . $user->display_name . '</a>';
									echo 	'<small>' . $job . '</small>';
									echo 	'<a class="author-twitter" href="' . $twitter_link . '"><i class="fa fa-twitter"></i> ' . $twitter_username . '</a>';
									echo '</h4>';
									// echo empty($text) ? '' : '<p class="muted">'.$text.'</p>';
								echo '</div>';
								echo '<div class="author-stats">';
									echo '<a href="' . get_author_posts_url($user->ID) . '" class="author-post-count">';
									echo 	'<span>' . count_user_posts( $user->ID ) . '</span>';
									echo 	__('Posts', 'bluth');
									echo '</a>';
								echo '</div>';
							echo '</div>';
						echo '</div>';
					}
					
					break;
			} ?>
		</div><?php

		echo $after_widget;
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("blu_author");') );

add_action('admin_enqueue_scripts', 'blu_admin_scripts');