<?php

if (function_exists('register_sidebar')) {
	
	$before_widget = '<div id="%1$s" class="%2$s single-widget box pad-xs-10 pad-sm-10 pad-md-10 pad-lg-20 clearfix">';
	$after_widget = '</div>';
	$before_title = '<h3 class="widget-head clearfix"><span>';
	$after_title = '</span></h3>';
	

	/* Home Sidebar */
	register_sidebar(
		array(				
			'id' => 'home_sidebar', 					
			'name' => 'Home Sidebar',				
			'description' => 'Sidebar that displays on your home/blog/front-paget', 
			'before_widget' => $before_widget,	
			'after_widget' => $after_widget,	
			'before_title' => $before_title,	
			'after_title' => $after_title
		)
	);
	/* Home Sidebar (Sticky) */
	register_sidebar(
		array(				
			'id' => 'home_sidebar_sticky', 					
			'name' => 'Home Sidebar (Sticky)',				
			'description' => 'Sidebar that displays on your home/blog/front-page, Elements in here will stick to the top when scrolled to', 
			'before_widget' => $before_widget,	
			'after_widget' => $after_widget,	
			'before_title' => $before_title,	
			'after_title' => $after_title
		)
	);

	/* Home Top */
	register_sidebar(
		array(				
			'id' => 'home_top', 					
			'name' => 'Home Top',				
			'description' => 'The top of the front page (above the sidebar) good for placing Bluth Slider for example', 
			'before_widget' => $before_widget,	
			'after_widget' => $after_widget,	
			'before_title' => $before_title,	
			'after_title' => $after_title
		)
	);
	
	/* Home Layout */
	register_sidebar(
		array(				
			'id' => 'home_layout', 					
			'name' => 'Home Layout',				
			'description' => 'The layout of the front page (use the Bluth Posts widget here)', 
			'before_widget' => $before_widget,	
			'after_widget' => $after_widget,	
			'before_title' => $before_title,	
			'after_title' => $after_title
		)
	);
	/* Post Sidebar */
	register_sidebar(
		array(				
			'id' => 'post_sidebar', 					
			'name' => 'Post Sidebar',				
			'description' => 'Sidebar that displays with a single post', 
			'before_widget' => $before_widget,	
			'after_widget' => $after_widget,	
			'before_title' => $before_title,	
			'after_title' => $after_title
		)
	);
	/* Post Sidebar (Sticky) */
	register_sidebar(
		array(				
			'id' => 'post_sidebar_sticky', 					
			'name' => 'Post Sidebar (Sticky)',				
			'description' => 'Sidebar that displays with a single post, Elements in here will stick to the top when scrolled to', 
			'before_widget' => $before_widget,	
			'after_widget' => $after_widget,	
			'before_title' => $before_title,	
			'after_title' => $after_title
		)
	);
	/* Post Bottom */
	register_sidebar(
		array(				
			'id' => 'post_bottom', 					
			'name' => 'Post Bottom',				
			'description' => 'Widget area to display at the bottom of each post', 
			'before_widget' => $before_widget,	
			'after_widget' => $after_widget,	
			'before_title' => $before_title,	
			'after_title' => $after_title
		)
	);
	/* Page Sidebar */
	register_sidebar(
		array(				
			'id' => 'page_sidebar', 					
			'name' => 'Page Sidebar',				
			'description' => 'Sidebar that displays on a single page', 
			'before_widget' => $before_widget,	
			'after_widget' => $after_widget,	
			'before_title' => $before_title,	
			'after_title' => $after_title
		)
	);
	/* Page Sidebar (Sticky) */
	register_sidebar(
		array(				
			'id' => 'page_sidebar_sticky', 					
			'name' => 'Page Sidebar (Sticky)',				
			'description' => 'Sidebar that displays on a single page, Elements in here will stick to the top when scrolled to', 
			'before_widget' => $before_widget,	
			'after_widget' => $after_widget,	
			'before_title' => $before_title,	
			'after_title' => $after_title
		)
	);
	/* Custom sidebar*/
	register_sidebar(
		array(				
			'id' => 'custom_sidebar_1', 					
			'name' => 'Custom Sidebar #1',				
			'description' => 'A custom sidebar to be used with plugins', 
			'before_widget' => $before_widget,	
			'after_widget' => $after_widget,	
			'before_title' => $before_title,	
			'after_title' => $after_title
		)
	);
	/* Custom sidebar*/
	register_sidebar(
		array(				
			'id' => 'custom_sidebar_2', 					
			'name' => 'Custom Sidebar #2',				
			'description' => 'A custom sidebar to be used with plugins', 
			'before_widget' => $before_widget,	
			'after_widget' => $after_widget,	
			'before_title' => $before_title,	
			'after_title' => $after_title
		)
	);
	/* Custom sidebar*/
	register_sidebar(
		array(				
			'id' => 'custom_sidebar_3', 					
			'name' => 'Custom Sidebar #3',				
			'description' => 'A custom sidebar to be used with plugins', 
			'before_widget' => $before_widget,	
			'after_widget' => $after_widget,	
			'before_title' => '<h3 class="widget-head">',	
			'after_title' => '',	
		)
	);

	/* Footer Widgets */
	$footer_widgets_num = wp_get_sidebars_widgets();
	$footer_widgets_num = (isset($footer_widgets_num['footer-widgets'])) ? count( $footer_widgets_num['footer-widgets']) : 0;

	switch ($footer_widgets_num) {
		case 1:
			$footer_widgets_num = '12';
		break;
		case 2:
			$footer_widgets_num = '6';
		break;
		case 3:
			$footer_widgets_num = '4';
		break;
		case 4:
			$footer_widgets_num = '3';
		break;
		case 5:
			$footer_widgets_num = '2';
		break;
		case 6:
			$footer_widgets_num = '2';
		break;
		case 7:
			$footer_widgets_num = '1';
		break;
		case 8:
			$footer_widgets_num = '1';
		break;
		case 11:
			$footer_widgets_num = '1';
		break;
		case 12:
			$footer_widgets_num = '1';
		break;
		default:
			$footer_widgets_num = '1';
		break;
	}

	register_sidebar(array(
	   'name' => __('Footer Widgets','bluth_admin' ),
	   'id'   => 'footer-widgets',
		'description'   => __( 'There are 4 slots available in the footer','bluth_admin' ),
		'before_widget' => '<div id="%1$s" class="single-widget col-md-'.$footer_widgets_num.' col-lg-'.$footer_widgets_num.' pad-md-10 pad-lg-10 %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => $before_title,
		'after_title'   => $after_title
   	));
}