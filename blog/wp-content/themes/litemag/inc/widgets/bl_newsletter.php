<?php
/*
Plugin Name: bl Instagram
Description: Box with your recent tweets
Author: Ivar Rafn
Version: 1
Author URI: http://www.bluth.is/
*/
class bl_newsletter extends WP_Widget
{
  function bl_newsletter(){
    $widget_ops = array('classname' => 'bl_newsletter', 'description' => 'Mailchimp newsletter signup' );
    $this->WP_Widget('bl_newsletter', 'Bluthemes - Newsletter', $widget_ops);

/*    if ( is_active_widget(false, false, $this->id_base) ){
    	wp_enqueue_script( 'content-mailchimp', get_template_directory_uri() . '/assets/js/mailchimp.js', array('jquery'), BLISS_VERSION, true );
    }*/
  }
 
  function form($instance){

	    $instance = wp_parse_args( (array) $instance, array( 
	    	'title' => 'Newsletter', 
	    	'sub_title' => 'WRITE DOWN YOUR EMAIL', 
	    	'text' => 'Enter your email address below to subscribe to our newsletter.', 
	    	'button_text' => 'SUBSCRIBE', 
	    	'list_id' => '',
	    	));
	    
	    $title  	= apply_filters('title', $instance['title']);
	    $sub_title  = apply_filters('sub_title', $instance['sub_title']);
	    $text		= apply_filters('text', $instance['text']);
	    $button_text= apply_filters('button_text', $instance['button_text']);
	    $api_key	= bl_utilities::get_option('mailchimp_api_key');
	    $list_id	= apply_filters('list_id', $instance['list_id']);

	if(empty($api_key)){ ?>

	  <strong>Instructions</strong>
	  <p>You need to have an account at Mailchimp.com to get an API key. Mailchimp has a free plan available.</p>
	  <ol>
	    <li>Create an account at <a href="http://eepurl.com/AfnZT" target="_blank">MailChimp</a></li>
	    <li>Login to Mailchimp and go to Account -> API keys</li>
	    <li>Add a key and copy it</li>
	    <li>Paste the API key in <a href="themes.php?page=options-framework">Theme Options -> Social</a></li>
	  </ol>
	  <hr>
	<?php } ?>

		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>">Title</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('sub_title'); ?>">Sub-Title</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('sub_title'); ?>" name="<?php echo $this->get_field_name('sub_title'); ?>" value="<?php echo $sub_title; ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('text'); ?>">Text</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('text'); ?>" name="<?php echo $this->get_field_name('text'); ?>" value="<?php echo $text; ?>">
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('button_text'); ?>">Button Text</label><br>
		<input type="text" style="width:216px" id="<?php echo $this->get_field_id('button_text'); ?>" name="<?php echo $this->get_field_name('button_text'); ?>" value="<?php echo $button_text; ?>">
		</p>
	<?php 
	if(!empty($api_key)){ 

		require_once(get_template_directory().'/inc/mailchimp/Mailchimp.php');

		
		if(strpos($api_key,'-')){
			$mailchimp = new MailChimp($api_key);

			$lists = $mailchimp->call('lists/list');

			if (!$lists){
				echo "<strong>Unable to load lists!</strong>";
				echo "<p>ERROR MESSAGE</p>";	
			}else{
				if(isset($lists['total']) and $lists['total'] == 0){
					echo '<p>You need to create a list first on mailchimp.com</p>';
				
				}elseif(isset($lists['error'])){
					echo '<p>Error: ' . $lists['error'] . '</p>';
				}else{ ?>
					<p>
						<label for="<?php echo $this->get_field_id('list'); ?>">Select mail list</label><br>
						<select style="width:216px" id="<?php echo $this->get_field_id('list_id'); ?>" name="<?php echo $this->get_field_name('list_id'); ?>">
						  	<?php
							  foreach ($lists['data'] as $mail_list) {
							  	if($list_id == $mail_list['id']){

							 	echo '<option value="'.$mail_list['id'].'" selected="">'.$mail_list['name'].'</option>'; 	
							  	}else{
							  		
							 	echo '<option value="'.$mail_list['id'].'">'.$mail_list['name'].'</option>'; 	
							  	}
							  }
							?>  
						</select>
					</p>
				<?php
				}
			}
		}else{
			echo 'Error: Mailchimp API key is not correct';
		}
	}
  }
 
  function update($new_instance, $old_instance){

	    $instance = $old_instance;
	    $instance['title']		= strip_tags($new_instance['title']);
	    $instance['sub_title']		= strip_tags($new_instance['sub_title']);
	    $instance['text']       = strip_tags($new_instance['text']);
	    $instance['button_text']= strip_tags($new_instance['button_text']);
	    $instance['api_key']    = strip_tags($new_instance['api_key']);
	    $instance['list_id']    = strip_tags($new_instance['list_id']);
	    return $instance;
  }
 
  function widget($args, $instance){

    extract($args, EXTR_SKIP);
    echo $before_widget;
    $title 	= apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
    ?>
	<?php echo !empty($title) ? $before_title.$title.$after_title : ''; ?>
	<div class="widget-body">
	<?php echo !empty($instance['sub_title']) ? '<h5>'.$instance['sub_title'].'</h5>' : '' ?>
	<?php echo !empty($instance['text']) ? '<p>'.$instance['text'].'</p>' : '' ?>
	<div class="input-append">
	    <input type="text" class="bl_newsletter_email" type="text" placeholder="<?php _e('Email address', 'bluth'); ?>">
	    <button data-list="<?php echo $instance['list_id']; ?>" class="btn btn-success btn-block" type="button"><?php echo $instance['button_text'] ?></button>
	</div>
	</div>
    <script type="text/javascript">
		window.locale = {
	        "no_email_provided": '<?php _e('No email provided!', 'bluth_admin'); ?>', 
	        "thank_you_for_subscribing": '<?php _e('Thank you for subscribing!', 'bluth_admin'); ?>', 
		};
    </script>
    <?php
    echo $after_widget;
  }
}
add_action( 'widgets_init', create_function('', 'return register_widget("bl_newsletter");') );