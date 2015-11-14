<?php 
/**
 * Valienti Multi-Widget
 */

if ( ! class_exists( 'CB_WP_Widget_tabs' ) ) {
    class CB_WP_Widget_tabs extends WP_Widget {
    
    	function __construct() {
    		$widget_ops = array('classname' => 'cb-multi-widget', 'description' =>  "Show multiple widgets in one widget." );
    		parent::__construct('multi-widget', 'Valenti Multi-Widget', $widget_ops);
    		$this->alt_option_name = 'widget_tabs';
    	}
    
    	function widget($args, $instance) {
    		ob_start();
    		extract($args);
    
    		 echo $before_widget; 
    		 	 if  (is_active_sidebar('cb_multi_widgets') ) { ?>
    		<div class="tabber">
         	  <?php dynamic_sidebar('cb_multi_widgets'); ?>
    		</div>
            
            <?php }
    		
    		echo $after_widget;
    	
    	}
    
    	function form( $instance ) {
    		
    		echo '<p>Add a couple of widgets to the "Valenti Multi-Widget" Widget area below.</p>';    
    	
    	}
    }
}

if ( ! function_exists( 'cb_tabs_loader' ) ) {
    function cb_tabs_loader (){
        register_widget( 'CB_WP_widget_tabs' );
    }
    add_action( 'widgets_init', 'cb_tabs_loader' );
}
?>