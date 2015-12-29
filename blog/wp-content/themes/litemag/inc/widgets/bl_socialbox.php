<?php
/*
Plugin Name: bl Socialbox
Description: Box with links to social sites like facebook and google+
Author: Ivar Rafn
Version: 1
Author URI: http://www.bluth.is/
*/
class bl_socialbox extends WP_Widget
{
  function bl_socialbox(){
    $widget_ops = array('classname' => 'bl_socialbox', 'description' => 'Displays links to social networks in a stylish manner' );
    $this->WP_Widget('bl_socialbox', 'Bluthemes - Socialbox', $widget_ops);
  }
 
  function form($instance){

    $instance = wp_parse_args( (array) $instance, array( 'title' => '' ) );
    

    $title 			= !empty($instance['title']) ? $instance['title'] : '';
    $facebook 		= !empty($instance['facebook']) ? $instance['facebook'] : '';
    $twitter 		= !empty($instance['twitter']) ? $instance['twitter'] : '';
    $googleplus 	= !empty($instance['googleplus']) ? $instance['googleplus'] : '';
    $linkedin 		= !empty($instance['linkedin']) ? $instance['linkedin'] : '';
    $youtube 		= !empty($instance['youtube']) ? $instance['youtube'] : '';
    $rss 			= !empty($instance['rss']) ? $instance['rss'] : '';
    $flickr 		= !empty($instance['flickr']) ? $instance['flickr'] : '';
    $vimeo 			= !empty($instance['vimeo']) ? $instance['vimeo'] : '';
    $pinterest 		= !empty($instance['pinterest']) ? $instance['pinterest'] : '';
    $dribbble 		= !empty($instance['dribbble']) ? $instance['dribbble'] : '';
    $tumblr 		= !empty($instance['tumblr']) ? $instance['tumblr'] : '';
    $instagram 		= !empty($instance['instagram']) ? $instance['instagram'] : '';
	?>
	<p>
		<label for="<?php echo $this->get_field_id('title'); ?>">Title</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>">
	</p>
	<hr style="border:none;height:1px;background:#BFBFBF">
	<p>Insert the URL's to your social networks</p>
	<p>
		<label for="<?php echo $this->get_field_id('facebook'); ?>">Facebook</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('facebook'); ?>" name="<?php echo $this->get_field_name('facebook'); ?>" value="<?php echo $facebook; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('twitter'); ?>">Twitter</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('twitter'); ?>" name="<?php echo $this->get_field_name('twitter'); ?>" value="<?php echo $twitter; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('googleplus'); ?>">Google+</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('googleplus'); ?>" name="<?php echo $this->get_field_name('googleplus'); ?>" value="<?php echo $googleplus; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('linkedin'); ?>">LinkedIn</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('linkedin'); ?>" name="<?php echo $this->get_field_name('linkedin'); ?>" value="<?php echo $linkedin; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('youtube'); ?>">Youtube</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('youtube'); ?>" name="<?php echo $this->get_field_name('youtube'); ?>" value="<?php echo $youtube; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('rss'); ?>">RSS</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('rss'); ?>" name="<?php echo $this->get_field_name('rss'); ?>" value="<?php echo $rss; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('flickr'); ?>">Flickr</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('flickr'); ?>" name="<?php echo $this->get_field_name('flickr'); ?>" value="<?php echo $flickr; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('vimeo'); ?>">Vimeo</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('vimeo'); ?>" name="<?php echo $this->get_field_name('vimeo'); ?>" value="<?php echo $vimeo; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('pinterest'); ?>">Pinterest</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('pinterest'); ?>" name="<?php echo $this->get_field_name('pinterest'); ?>" value="<?php echo $pinterest; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('dribbble'); ?>">Dribbble</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('dribbble'); ?>" name="<?php echo $this->get_field_name('dribbble'); ?>" value="<?php echo $dribbble; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('tumblr'); ?>">Tumblr</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('tumblr'); ?>" name="<?php echo $this->get_field_name('tumblr'); ?>" value="<?php echo $tumblr; ?>">
	</p>
	<p>
		<label for="<?php echo $this->get_field_id('instagram'); ?>">Instagram</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('instagram'); ?>" name="<?php echo $this->get_field_name('instagram'); ?>" value="<?php echo $instagram; ?>">
	</p>

	<?php
  }
 
  function update($new_instance, $old_instance){

    $instance = $old_instance;
    $instance['title'] 			= strip_tags($new_instance['title']);
    $instance['facebook'] 		= esc_url($new_instance['facebook']);
    $instance['twitter'] 		= esc_url($new_instance['twitter']);
    $instance['googleplus'] 	= esc_url($new_instance['googleplus']);
    $instance['linkedin'] 		= esc_url($new_instance['linkedin']);
    $instance['youtube'] 		= esc_url($new_instance['youtube']);
    $instance['rss'] 			= esc_url($new_instance['rss']);
    $instance['flickr'] 		= esc_url($new_instance['flickr']);
    $instance['vimeo'] 			= esc_url($new_instance['vimeo']);
    $instance['pinterest'] 		= esc_url($new_instance['pinterest']);
    $instance['dribbble'] 		= esc_url($new_instance['dribbble']);
    $instance['tumblr'] 		= esc_url($new_instance['tumblr']);
    $instance['instagram'] 		= esc_url($new_instance['instagram']);
    return $instance;
  }
 
  function widget($args, $instance){

    extract($args, EXTR_SKIP);

    echo $before_widget; 
    $title 	= apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
    ?>
  		<?php echo !empty($title) ? $before_title.$title.$after_title : ''; ?>
    	<div class="widget-body">
    		<ul class="clearfix">
	    	<?php echo !empty($instance['facebook']) 	? '<li><a target="_blank" data-title="Facebook" class="bl_icon_facebook" href="'.$instance['facebook'].'"><i class="fa fa-facebook"></i><span class="social-type">Facebook</span></a></li>' : '' ?>
	    	<?php echo !empty($instance['twitter']) 	? '<li><a target="_blank" data-title="Twitter" class="bl_icon_twitter" href="'.$instance['twitter'].'"><i class="fa fa-twitter"></i><span class="social-type">Twitter</span></a></li>' : '' ?>
	    	<?php echo !empty($instance['googleplus']) 	? '<li><a target="_blank" data-title="Google+" class="bl_icon_googleplus" href="'.$instance['googleplus'].'?rel=author"><i class="fa fa-google-plus"></i><span class="social-type">Google</span></a></li>' : '' ?>
	    	<?php echo !empty($instance['linkedin']) 	? '<li><a target="_blank" data-title="Linkedin" class="bl_icon_linkedin" href="'.$instance['linkedin'].'"><i class="fa fa-linkedin"></i><span class="social-type">Linkedin</span></a></li>' : '' ?>
	    	<?php echo !empty($instance['youtube']) 	? '<li><a target="_blank" data-title="Youtube" class="bl_icon_youtube" href="'.$instance['youtube'].'"><i class="fa fa-youtube"></i><span class="social-type">Youtube</span></a></li>' : '' ?>
	    	<?php echo !empty($instance['rss']) 		? '<li><a target="_blank" data-title="Rss" class="bl_icon_rss" href="'.$instance['rss'].'"><i class="fa fa-rss"></i><span class="social-type">Rss</span></a></li>' : '' ?>
	    	<?php echo !empty($instance['flickr']) 		? '<li><a target="_blank" data-title="Flickr" class="bl_icon_flickr" href="'.$instance['flickr'].'"><i class="fa fa-flickr"></i><span class="social-type">Flickr</span></a></li>' : '' ?>
	    	<?php echo !empty($instance['vimeo']) 		? '<li><a target="_blank" data-title="Vimeo" class="bl_icon_vimeo" href="'.$instance['vimeo'].'"><i class="fa fa-vimeo-square"></i><span class="social-type">Vimeo</span></a></li>' : '' ?>
	    	<?php echo !empty($instance['pinterest']) 	? '<li><a target="_blank" data-title="Pinterest" class="bl_icon_pinterest" href="'.$instance['pinterest'].'"><i class="fa fa-pinterest"></i><span class="social-type">Pinterest</span></a></li>' : '' ?>
	    	<?php echo !empty($instance['dribbble']) 	? '<li><a target="_blank" data-title="Dribbble" class="bl_icon_dribbble" href="'.$instance['dribbble'].'"><i class="fa fa-dribbble"></i><span class="social-type">Dribbble</span></a></li>' : '' ?>
	    	<?php echo !empty($instance['tumblr']) 		? '<li><a target="_blank" data-title="Tumblr" class="bl_icon_tumblr" href="'.$instance['tumblr'].'"><i class="fa fa-tumblr"></i><span class="social-type">Tumblr</span></a></li>' : '' ?>
	    	<?php echo !empty($instance['instagram']) 	? '<li><a target="_blank" data-title="Instagram" class="bl_icon_instagram" href="'.$instance['instagram'].'"><i class="fa fa-instagram"></i><span class="social-type">Instagram</span></a></li>' : '' ?>
    		</ul>
    	</div>
    <?php
	echo $after_widget;
  }
}
add_action( 'widgets_init', create_function('', 'return register_widget("bl_socialbox");') );