<?php
/**
 * Valenti Facebook Like Box
 */
if ( ! class_exists( 'CB_WP_Widget_facebook_box' ) ) {

    class CB_WP_Widget_facebook_box extends WP_Widget {

    	function __construct() {
    		$widget_ops = array('classname' => 'facebook-box-widget', 'description' =>  "Facebook Like Box" );
    		parent::__construct('facebook-box', 'Valenti Facebook Box', $widget_ops);
    		$this->alt_option_name = 'widget_facebook_box';

    		add_action( 'save_post', array($this, 'flush_widget_cache') );
    		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
    		add_action( 'switch_theme', array($this, 'flush_widget_cache') );

    		// Only load javascript if widget is active
    		if (is_active_widget('','','facebook-box')) {
    			add_action( 'wp_footer', array($this,'cb_fb_box' ));
    		}

    	}

    	function widget($args, $instance) {
    		$cache = wp_cache_get('widget_facebook_box', 'widget');

    		if ( !is_array($cache) )
    			$cache = array();

    		if ( ! isset( $args['widget_id'] ) )
    			$args['widget_id'] = $this->id;

    		if ( isset( $cache[ $args['widget_id'] ] ) ) {
    			echo $cache[ $args['widget_id'] ];
    			return;
    		}

    		ob_start();
    		extract($args);

    		$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
    		$this->appid = apply_filters('widget_appid', empty($instance['appid']) ? '' : $instance['appid'], $instance, $this->id_base);
    		$page = apply_filters('widget_page', empty($instance['page']) ? '' : $instance['page'], $instance, $this->id_base);

    		echo $before_widget;

    		   if ( $title ) echo $before_title . $title . $after_title;
    ?>
    		   <div class="fb-like-box"
                	data-href="http://www.facebook.com/<?php echo $page; ?>"
                	data-width="360"
                	data-show-faces="true"
                	data-stream="false"
                	data-header="false"
                	data-border-color="#fff">
               </div>
    <?php
    		echo $after_widget;

    		$cache[$args['widget_id']] = ob_get_flush();
    		wp_cache_set('widget_facebook_box', $cache, 'widget');
    	}

    	function update( $new_instance, $old_instance ) {
    		$instance = $old_instance;
    		$instance['title'] = strip_tags($new_instance['title']);
    		$instance['appid'] = strip_tags($new_instance['appid']);
    		$instance['page'] = strip_tags($new_instance['page']);
    		$this->flush_widget_cache();

    		$alloptions = wp_cache_get( 'alloptions', 'options' );
    		if ( isset($alloptions['widget_facebook_box']) )
    			delete_option('widget_facebook_box');

    		return $instance;

    	}

    	function cb_fb_box() {
    		 if (!isset($this->appid)) { $this->appid = NULL; }

             $cb_site_locale = get_locale();

    		  echo '<div id="fb-root"></div>
    				  <script>
    				  	  (function(d, s, id) {
    					  var js, fjs = d.getElementsByTagName(s)[0];
    					  if (d.getElementById(id)) return;
    					  js = d.createElement(s); js.id = id;
                          js.src = "//connect.facebook.net/' . $cb_site_locale . '/all.js#xfbml=1";
    						fjs.parentNode.insertBefore(js, fjs);
    					  }(document, \'script\', \'facebook-jssdk\'));
    				</script>';
    	}

    	function flush_widget_cache() {
    		wp_cache_delete('widget_facebook_box', 'widget');
    	}

    	function form( $instance ) {
    		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
    		$appid     = isset( $instance['appid'] ) ? esc_attr( $instance['appid'] ) : '';
    		$page     = isset( $instance['page'] ) ? esc_attr( $instance['page'] ) : '';

    ?>
    		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

            <p><label for="<?php echo $this->get_field_id( 'appid' ); ?>">App ID: (You can get one from https://developers.facebook.com/)</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'appid' ); ?>" name="<?php echo $this->get_field_name( 'appid' ); ?>" type="text" value="<?php echo $appid; ?>" /></p>

            <p><label for="<?php echo $this->get_field_id( 'page' ); ?>">Page name: (Without http://www.facebook.com/)</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'page' ); ?>" name="<?php echo $this->get_field_name( 'page' ); ?>" type="text" value="<?php echo $page; ?>" /></p>

    <?php
    	}
    }
}

if ( ! function_exists( 'cb_facebook_box_loader' ) ) {
    function cb_facebook_box_loader () {
        register_widget( 'CB_WP_Widget_facebook_box' );
    }
    add_action( 'widgets_init', 'cb_facebook_box_loader' );
}
?>