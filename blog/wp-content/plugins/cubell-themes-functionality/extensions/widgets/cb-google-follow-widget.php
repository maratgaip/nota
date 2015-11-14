<?php 
/**
 * Valenti Google+ Follow widget
 */

if ( ! class_exists( 'cb_google_follow_widget' ) ) {
    class cb_google_follow_widget extends WP_Widget {
    
    	function __construct() {
    		$widget_ops = array('classname' => 'cb-google-follow-widget clearfix', 'description' =>  "Google+ Follow widget" );
    		parent::__construct('google-follow', 'Valenti Google+ Follow Widget', $widget_ops);
    		$this->alt_option_name = 'widget_google_follow_badge';
    
    		add_action( 'save_post', array($this, 'flush_widget_cache') );
    		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
    		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
    	}
    
    	function widget($args, $instance) {
    		$cache = wp_cache_get('widget_google_follow_badge', 'widget');
    
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
    		$googleplus = apply_filters('widget_title', empty($instance['googleplus']) ? '' : $instance['googleplus'], $instance, $this->id_base);
    		
            echo $before_widget; 
            if ( $title ) echo $before_title . $title . $after_title; 
    
    		
            if ( $googleplus != NULL ) {
    
                 echo '<div class="g-page" data-width="360"  data-layout="landscape"  data-href="'. $googleplus .'"  data-showtagline="false" data-showcoverphoto="false" data-rel="publisher"></div> 
                 <script type="text/javascript">
                                  (function() {
                                    var po = document.createElement("script"); po.type = "text/javascript"; po.async = true;
                                    po.src = "https://apis.google.com/js/plusone.js";
                                    var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s);
                                  })();
                                </script>';
            }
    
            echo $after_widget; 
    
    		// Reset the global $the_post as this query will have stomped on it
    		wp_reset_postdata();
    
    
    		$cache[$args['widget_id']] = ob_get_flush();
    		wp_cache_set('widget_google_follow_badge', $cache, 'widget');
    	}
    
    	function update( $new_instance, $old_instance ) {
    		$instance = $old_instance;
    		$instance['googleplus'] = strip_tags($new_instance['googleplus']);
    		$instance['title'] = strip_tags($new_instance['title']);
    		$this->flush_widget_cache();
    
    		$alloptions = wp_cache_get( 'alloptions', 'options' );
    		if ( isset($alloptions['widget_google_follow_badge']) )
    			delete_option('widget_google_follow_badge');
    
    		return $instance;
    	}
    
    	function flush_widget_cache() {
    		wp_cache_delete('widget_recent_posts', 'widget');
    	}
    
    	function form( $instance ) {
    
    		$googleplus     = isset( $instance['googleplus'] ) ? esc_attr( $instance['googleplus'] ) : '';
    		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
    ?>      
            
            <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cubell' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
    		
    		<p><label for="<?php echo $this->get_field_id( 'googleplus' ); ?>">Google+ Page URL:</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'googleplus' ); ?>" name="<?php echo $this->get_field_name( 'googleplus' ); ?>" type="text" value="<?php echo $googleplus; ?>" /></p>        
            
            
                   
    	
         <?php
    	}
    }
}

if ( ! function_exists( 'cb_google_follow_widget' ) ) {
    
    function cb_google_follow_widget () {
        register_widget( 'cb_google_follow_widget' );
    }
        add_action( 'widgets_init', 'cb_google_follow_widget' );
}
?>