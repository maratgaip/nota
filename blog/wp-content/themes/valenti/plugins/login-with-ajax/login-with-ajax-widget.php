<?php
class LoginWithAjaxWidget extends WP_Widget {
    public $defaults;
    
    /** constructor */
    function LoginWithAjaxWidget() {
    	$this->defaults = array(
    		'title' => __('Log In','login-with-ajax'),
    		'title_loggedin' => __( 'Hi', 'login-with-ajax' ).' %username%',
    		'template' => 'default',
    		'profile_link' => 1,
    		'registration' => 1,
    		'remember' => 1
    	);
    	$widget_ops = array('description' => __( "Login widget with AJAX capabilities.", 'login-with-ajax') );
        parent::WP_Widget(false, $name = 'Login With Ajax', $widget_ops);	
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
    	$instance = array_merge($this->defaults, $instance);
        echo $args['before_widget'];
    	if( !is_user_logged_in() && !empty($instance['title']) ){
		    echo $args['before_title'];
		    echo '<span class="lwa-title">';
		    echo apply_filters('widget_title',$instance['title'], $instance, $this->id_base);
		    echo '</span>';
		    echo $args['after_title'];
    	}elseif( is_user_logged_in() && !empty($instance['title_loggedin']) ) {
		    echo $args['before_title'];
		    echo '<span class="lwa-title">';
		    echo str_replace('%username%', LoginWithAjax::$current_user->display_name, $instance['title_loggedin']);
		    echo '</span>';
		    echo $args['after_title'];
    	}
    	LoginWithAjax::widget($instance);
	    echo $args['after_widget'];
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance) {
    	foreach($this->defaults as $key => $value){
    		if( !isset($new_instance[$key]) ){
    			$new_instance[$key] = false;
    		}
    	}
    	return $new_instance;
    }

    /** @see WP_Widget::form */
    function form($instance) {
    	$instance = array_merge($this->defaults, $instance);
        ?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php echo sprintf(__('Title (%s)', 'login-with-ajax'),__('Logged In','login-with-ajax')); ?>: </label>
				<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('title_loggedin'); ?>"><?php echo sprintf(__('Title (%s)', 'login-with-ajax'),__('Logged Out','login-with-ajax')); ?>: </label>
				<input type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title_loggedin'); ?>" value="<?php echo esc_attr($instance['title_loggedin']); ?>" />
			</p>
            <p>
            	<label for="<?php echo $this->get_field_id('profile_link'); ?>"><?php _e('Show profile link?', 'login-with-ajax'); ?> </label>
                <input id="<?php echo $this->get_field_id('profile_link'); ?>" name="<?php echo $this->get_field_name('profile_link'); ?>" type="checkbox" value="1" <?php echo !empty($instance['profile_link']) ? 'checked="checked"':""; ?> />
			</p>
            <p>
            	<label for="<?php echo $this->get_field_id('remember'); ?>"><?php _e('Recover Password?', 'login-with-ajax'); ?> </label>
                <input id="<?php echo $this->get_field_id('remember'); ?>" name="<?php echo $this->get_field_name('remember'); ?>" type="checkbox" value="1" <?php echo !empty($instance['remember']) ? 'checked="checked"':""; ?> />
			</p>
            <p>
            	<label for="<?php echo $this->get_field_id('registration'); ?>"><?php _e('AJAX Registration?', 'login-with-ajax'); ?> </label>
                <input id="<?php echo $this->get_field_id('registration'); ?>" name="<?php echo $this->get_field_name('registration'); ?>" type="checkbox" value="1" <?php echo !empty($instance['registration']) ? 'checked="checked"':""; ?> />
			</p>
			<?php if( count(LoginWithAjax::$templates) > 1 ): ?>
			<p>
            	<label for="<?php echo $this->get_field_id('template'); ?>"><?php _e('Template', 'login-with-ajax'); ?> </label>
            	<select id="<?php echo $this->get_field_id('template'); ?>" name="<?php echo $this->get_field_name('template'); ?>" >
            		<?php foreach( array_keys(LoginWithAjax::$templates) as $template ): ?>
            		<option <?php echo ($instance['template'] == $template) ? 'selected="selected"':""; ?>><?php echo $template ?></option>
            		<?php endforeach; ?>
            	</select>
			</p>
			<?php endif; ?>
        <?php 
    }

}
?>