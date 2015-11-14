<?php  /* 125 Ads - Valenti Widget */

if ( !class_exists ( 'cb_wp_125_ads_widget' ) ) {
 class cb_wp_125_ads_widget extends WP_Widget {

    	function __construct() {
    		$widget_ops = array('classname' => 'ads-125-widget clearfix', 'description' =>  "Show up to ten 125px x 125px ads" );
    		parent::__construct('ads-125', 'Valenti 125px x 125px Ads', $widget_ops);
    		$this->alt_option_name = 'widget_125_ads';

    		add_action( 'save_post', array($this, 'flush_widget_cache') );
    		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
    		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
    	}

    	function widget($args, $instance) {
    		$cache = wp_cache_get('widget_125_ads', 'widget');

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
    		$title = apply_filters('widget_title', empty($instance['title']) ? NULL : $instance['title'], $instance, $this->id_base);
    		$ad_url_1 = apply_filters('ad_url_1', empty($instance['ad_url_1']) ? NULL : $instance['ad_url_1'], $instance, $this->id_base);
    		$ad_url_2 = apply_filters('ad_url_2', empty($instance['ad_url_2']) ? NULL : $instance['ad_url_2'], $instance, $this->id_base);
    		$ad_url_3 = apply_filters('ad_url_3', empty($instance['ad_url_3']) ? NULL : $instance['ad_url_3'], $instance, $this->id_base);
    		$ad_url_4 = apply_filters('ad_url_4', empty($instance['ad_url_4']) ? NULL : $instance['ad_url_4'], $instance, $this->id_base);
    		$ad_url_5 = apply_filters('ad_url_5', empty($instance['ad_url_5']) ? NULL : $instance['ad_url_5'], $instance, $this->id_base);
            $ad_url_6 = apply_filters('ad_url_6', empty($instance['ad_url_6']) ? NULL : $instance['ad_url_6'], $instance, $this->id_base);
            $ad_url_7 = apply_filters('ad_url_7', empty($instance['ad_url_7']) ? NULL : $instance['ad_url_7'], $instance, $this->id_base);
            $ad_url_8 = apply_filters('ad_url_8', empty($instance['ad_url_8']) ? NULL : $instance['ad_url_8'], $instance, $this->id_base);
            $ad_url_9 = apply_filters('ad_url_9', empty($instance['ad_url_9']) ? NULL : $instance['ad_url_9'], $instance, $this->id_base);
            $ad_url_10 = apply_filters('ad_url_10', empty($instance['ad_url_10']) ? NULL : $instance['ad_url_10'], $instance, $this->id_base);
    		$cb_output = NULL;

            if ( is_home() || is_category() || is_tag() || is_singular() || is_archive() ) {
        		$cb_output = '<ul class="cb-125-ads">';
        		if ( isset( $ad_url_1 ) ) { $cb_output .= '<li>'. $ad_url_1 .'</li>'; }
                if ( isset( $ad_url_2 ) ) { $cb_output .= '<li>'. $ad_url_2 .'</li>'; }
                if ( isset( $ad_url_3 ) ) { $cb_output .= '<li>'. $ad_url_3 .'</li>'; }
                if ( isset( $ad_url_4 ) ) { $cb_output .= '<li>'. $ad_url_4 .'</li>'; }
                if ( isset( $ad_url_5 ) ) { $cb_output .= '<li>'. $ad_url_5 .'</li>'; }
                if ( isset( $ad_url_6 ) ) { $cb_output .= '<li>'. $ad_url_6 .'</li>'; }
                if ( isset( $ad_url_7 ) ) { $cb_output .= '<li>'. $ad_url_7 .'</li>'; }
                if ( isset( $ad_url_8 ) ) { $cb_output .= '<li>'. $ad_url_8 .'</li>'; }
                if ( isset( $ad_url_9 ) ) { $cb_output .= '<li>'. $ad_url_9 .'</li>'; }
                if ( isset( $ad_url_10 ) ) { $cb_output .= '<li>'. $ad_url_10 .'</li>'; }
                $cb_output .= '</ul>';
            }

    		echo $before_widget;

    		if ( $title ) echo $before_title . $title . $after_title;

            echo $cb_output;

    		echo $after_widget;

    		$cache[$args['widget_id']] = ob_get_flush();
    		wp_cache_set('widget_125_ads', $cache, 'widget');
    	}

    	function update( $new_instance, $old_instance ) {
    		$instance = $old_instance;
    		$instance['title'] = strip_tags($new_instance['title']);
    		$instance['ad_url_1'] = ($new_instance['ad_url_1']);
    		$instance['ad_url_2'] = ($new_instance['ad_url_2']);
    		$instance['ad_url_3'] = ($new_instance['ad_url_3']);
    		$instance['ad_url_4'] = ($new_instance['ad_url_4']);
    		$instance['ad_url_5'] = ($new_instance['ad_url_5']);
            $instance['ad_url_6'] = ($new_instance['ad_url_6']);
            $instance['ad_url_7'] = ($new_instance['ad_url_7']);
            $instance['ad_url_8'] = ($new_instance['ad_url_8']);
            $instance['ad_url_9'] = ($new_instance['ad_url_9']);
            $instance['ad_url_10'] = ($new_instance['ad_url_10']);

    		$this->flush_widget_cache();

    		$alloptions = wp_cache_get( 'alloptions', 'options' );
    		if ( isset($alloptions['widget_125_ads']) )
    			delete_option('widget_125_ads');

    		return $instance;
    	}

    	function flush_widget_cache() {
    		wp_cache_delete('widget_125_ads', 'widget');
    	}

    	function form( $instance ) {
    		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
    		$ad_url_1    = isset( $instance['ad_url_1'] ) ? esc_attr( $instance['ad_url_1'] ) : '';
    		$ad_url_2    = isset( $instance['ad_url_2'] ) ? esc_attr( $instance['ad_url_2'] ) : '';
    		$ad_url_3    = isset( $instance['ad_url_3'] ) ? esc_attr( $instance['ad_url_3'] ) : '';
    		$ad_url_4    = isset( $instance['ad_url_4'] ) ? esc_attr( $instance['ad_url_4'] ) : '';
    		$ad_url_5    = isset( $instance['ad_url_5'] ) ? esc_attr( $instance['ad_url_5'] ) : '';
            $ad_url_6    = isset( $instance['ad_url_6'] ) ? esc_attr( $instance['ad_url_6'] ) : '';
            $ad_url_7    = isset( $instance['ad_url_7'] ) ? esc_attr( $instance['ad_url_7'] ) : '';
            $ad_url_8    = isset( $instance['ad_url_8'] ) ? esc_attr( $instance['ad_url_8'] ) : '';
            $ad_url_9    = isset( $instance['ad_url_9'] ) ? esc_attr( $instance['ad_url_9'] ) : '';
            $ad_url_10    = isset( $instance['ad_url_10'] ) ? esc_attr( $instance['ad_url_10'] ) : '';

    ?>
    		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>


            <p><label for="<?php echo $this->get_field_id( 'ad_url_1' ); ?>">Ad 1 Code</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'ad_url_1' ); ?>" name="<?php echo $this->get_field_name( 'ad_url_1' ); ?>" type="text" value="<?php echo $ad_url_1; ?>" size="3" /></p>

            <p><label for="<?php echo $this->get_field_id( 'ad_url_2' ); ?>">Ad 2 Code</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'ad_url_2' ); ?>" name="<?php echo $this->get_field_name( 'ad_url_2' ); ?>" type="text" value="<?php echo $ad_url_2; ?>" size="3" /></p>

            <p><label for="<?php echo $this->get_field_id( 'ad_url_3' ); ?>">Ad 3 Code</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'ad_url_3' ); ?>" name="<?php echo $this->get_field_name( 'ad_url_3' ); ?>" type="text" value="<?php echo $ad_url_3; ?>" size="3" /></p>

            <p><label for="<?php echo $this->get_field_id( 'ad_url_4' ); ?>">Ad 4 Code</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'ad_url_4' ); ?>" name="<?php echo $this->get_field_name( 'ad_url_4' ); ?>" type="text" value="<?php echo $ad_url_4; ?>" size="3" /></p>

            <p><label for="<?php echo $this->get_field_id( 'ad_url_5' ); ?>">Ad 5 Code</label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'ad_url_5' ); ?>" name="<?php echo $this->get_field_name( 'ad_url_5' ); ?>" type="text" value="<?php echo $ad_url_5; ?>" size="3" /></p>

            <p><label for="<?php echo $this->get_field_id( 'ad_url_6' ); ?>">Ad 6 Code</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'ad_url_6' ); ?>" name="<?php echo $this->get_field_name( 'ad_url_6' ); ?>" type="text" value="<?php echo $ad_url_6; ?>" size="3" /></p>

            <p><label for="<?php echo $this->get_field_id( 'ad_url_7' ); ?>">Ad 7 Code</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'ad_url_7' ); ?>" name="<?php echo $this->get_field_name( 'ad_url_7' ); ?>" type="text" value="<?php echo $ad_url_7; ?>" size="3" /></p>

            <p><label for="<?php echo $this->get_field_id( 'ad_url_8' ); ?>">Ad 8 Code</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'ad_url_8' ); ?>" name="<?php echo $this->get_field_name( 'ad_url_8' ); ?>" type="text" value="<?php echo $ad_url_8; ?>" size="3" /></p>

            <p><label for="<?php echo $this->get_field_id( 'ad_url_9' ); ?>">Ad 9 Code</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'ad_url_9' ); ?>" name="<?php echo $this->get_field_name( 'ad_url_9' ); ?>" type="text" value="<?php echo $ad_url_9; ?>" size="3" /></p>

            <p><label for="<?php echo $this->get_field_id( 'ad_url_10' ); ?>">Ad 10 Code</label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'ad_url_10' ); ?>" name="<?php echo $this->get_field_name( 'ad_url_10' ); ?>" type="text" value="<?php echo $ad_url_10; ?>" size="3" /></p>

    <?php
    	}
    }
}
if ( ! function_exists( 'cb_125_ads_loader ' ) ) {

    function cb_125_ads_loader () {
        register_widget( 'cb_wp_125_ads_widget' );
    }

    add_action( 'widgets_init', 'cb_125_ads_loader' );
}
?>