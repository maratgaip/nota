<?php 
/**
 * Valenti Social Media widget
 */
if ( ! class_exists( 'cb_social_media_icons_widget' ) ) {
     class cb_social_media_icons_widget extends WP_Widget {
    
    	function __construct() {
    		$widget_ops = array('classname' => 'cb-social-media-widget clearfix', 'description' =>  "Social media icon widget" );
    		parent::__construct('social-media-icons', 'Valenti Social Media Icons', $widget_ops);
    		$this->alt_option_name = 'widget_social_media';
    
    		add_action( 'save_post', array($this, 'flush_widget_cache') );
    		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
    		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
    	}
    
    	function widget($args, $instance) {
    		$cache = wp_cache_get('widget_social_media', 'widget');
    
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
    		$facebook = apply_filters('widget_title', empty($instance['facebook']) ? '' : $instance['facebook'], $instance, $this->id_base);
    		$twitter = apply_filters('widget_title', empty($instance['twitter']) ? '' : $instance['twitter'], $instance, $this->id_base);
    		$googleplus = apply_filters('widget_title', empty($instance['googleplus']) ? '' : $instance['googleplus'], $instance, $this->id_base);
    		$youtube = apply_filters('widget_title', empty($instance['youtube']) ? '' : $instance['youtube'], $instance, $this->id_base);
    		$rss = apply_filters('widget_title', empty($instance['rss']) ? '' : $instance['rss'], $instance, $this->id_base);
    		
            echo $before_widget; 
            if ( $title ) echo $before_title . $title . $after_title; 
    		$i = 0;
    		
            if ($rss != '') {$i++; ?><a href="<?php echo $rss;?>" target="_blank" class="cb-social-media-icon cb-rss icon-<?php echo $i; ?>"></a><?php } 
            if ($twitter != '') {$i++; ?><a href="<?php echo $twitter;?>" target="_blank" class="cb-social-media-icon cb-twitter icon-<?php echo $i; ?>"></a><?php } 
            if ($facebook != '') {$i++; ?><a href="<?php echo $facebook;?>" target="_blank" class="cb-social-media-icon cb-facebook icon-<?php echo $i; ?>"></a><?php } 
            if ($googleplus != '') {$i++; ?><a href="<?php echo $googleplus;?>" target="_blank" class="cb-social-media-icon cb-googleplus icon-<?php echo $i; ?>"></a><?php }
            if ($youtube != '') {$i++; ?><a href="<?php echo $youtube;?>" target="_blank" class="cb-social-media-icon cb-youtube icon-<?php echo $i; ?>"></a><?php } 
    
            echo $after_widget; 
    
    		// Reset the global $the_post as this query will have stomped on it
    		wp_reset_postdata();
    
    
    		$cache[$args['widget_id']] = ob_get_flush();
    		wp_cache_set('widget_social_media', $cache, 'widget');
    	}
    
    	function update( $new_instance, $old_instance ) {
    		$instance = $old_instance;
    		$instance['facebook'] = strip_tags($new_instance['facebook']);
    		$instance['twitter'] = strip_tags($new_instance['twitter']);
    		$instance['googleplus'] = strip_tags($new_instance['googleplus']);
    		$instance['title'] = strip_tags($new_instance['title']);
    		$instance['youtube'] = strip_tags($new_instance['youtube']);
    		$instance['rss'] = strip_tags($new_instance['rss']);
    		$this->flush_widget_cache();
    
    		$alloptions = wp_cache_get( 'alloptions', 'options' );
    		if ( isset($alloptions['widget_social_media']) )
    			delete_option('widget_social_media');
    
    		return $instance;
    	}
    
    	function flush_widget_cache() {
    		wp_cache_delete('widget_social_media', 'widget');
    	}
    
    	function form( $instance ) {
    		$facebook     = isset( $instance['facebook'] ) ? esc_attr( $instance['facebook'] ) : '';
    		$twitter     = isset( $instance['twitter'] ) ? esc_attr( $instance['twitter'] ) : '';
    		$googleplus     = isset( $instance['googleplus'] ) ? esc_attr( $instance['googleplus'] ) : '';
    		$youtube     = isset( $instance['youtube'] ) ? esc_attr( $instance['youtube'] ) : '';
    		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
    		$rss     = isset( $instance['rss'] ) ? esc_attr( $instance['rss'] ) : '';
    ?>      
            <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cubell' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>
            
    		<p><label for="<?php echo $this->get_field_id( 'twitter' ); ?>">Twitter URL:</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'twitter' ); ?>" name="<?php echo $this->get_field_name( 'twitter' ); ?>" type="text" value="<?php echo $twitter; ?>" /></p>
            
    		<p><label for="<?php echo $this->get_field_id( 'facebook' ); ?>">Facebook URL:</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'facebook' ); ?>" name="<?php echo $this->get_field_name( 'facebook' ); ?>" type="text" value="<?php echo $facebook; ?>" /></p>
            
    		<p><label for="<?php echo $this->get_field_id( 'googleplus' ); ?>">Google+ Page URL:</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'googleplus' ); ?>" name="<?php echo $this->get_field_name( 'googleplus' ); ?>" type="text" value="<?php echo $googleplus; ?>" /></p>        
    		
    		<p><label for="<?php echo $this->get_field_id( 'youtube' ); ?>">YouTube URL:</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'youtube' ); ?>" name="<?php echo $this->get_field_name( 'youtube' ); ?>" type="text" value="<?php echo $youtube; ?>" /></p>
            
    		<p><label for="<?php echo $this->get_field_id( 'rss' ); ?>">RSS URL:</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'rss' ); ?>" name="<?php echo $this->get_field_name( 'rss' ); ?>" type="text" value="<?php echo $rss; ?>" /></p> 
                   
    	
         <?php
    	}
    }
}
if ( ! function_exists( 'cb_social_media_widget' ) ) {
    function cb_social_media_widget (){
     register_widget( 'cb_social_media_icons_widget' );
    }
     add_action( 'widgets_init', 'cb_social_media_widget' );
}
?>