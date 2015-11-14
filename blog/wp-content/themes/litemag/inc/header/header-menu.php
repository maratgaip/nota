<?php
$menu_layout = bl_utilities::get_option('header_layout');
$header_html = bl_utilities::get_option('header_html');

?>
<div id="menu-main" class="menu-main clearfix">
	<div class="brand">
		<div class="logo-area <?php echo $menu_layout; ?> clearfix"><?php 

			$logo = bl_utilities::get_option('logo');
			if ( !empty( $logo ) ) { ?>
				<a class="menu-brand brand-image" href="<?php echo home_url(); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><img src="<?php echo $logo; ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>"><?php
					// we won't display the description if it's in the header area or if it's disabled
					if(bl_utilities::get_option('header_description')){ ?>
						<span class="brand-description"><?php bloginfo( 'description' ); ?></span><?php 
					} ?>
					
				</a><?php 
			}else{ ?>
				<a class="menu-brand brand-text" href="<?php echo home_url(); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home"><h1><?php echo blu_colorsplit(get_bloginfo( 'name' )); ?></h1><?php
				
					// we won't display the description if it's in the header area or if it's disabled
					if(bl_utilities::get_option('header_description')){ ?>
						<span class="brand-description"><?php bloginfo( 'description' ); ?></span><?php 
					} ?>
					
				</a><?php 
			}

			// if there's a left aligned header then display the HTML box here
			if($menu_layout == 'header_left_with_ad'){ ?>
				<div class="header_adspot pull-right"><?php echo $header_html; ?></div><?php
			} ?>
		</div>
		 <button type="button" class="navbar-toggle visible-xs" data-toggle="collapse" data-target=".blu-top-header">
		    <span class="sr-only">Toggle navigation</span>
		    <i class="fa fa-bars"></i>
		</button>
		<nav role="navigation"> 
			<a class="mini-logo-link" href="<?php echo home_url(); ?>"><img class="mini-logo" src="<?php echo bl_utilities::get_option('mini_logo'); ?>"></a><?php
			if(has_nav_menu('primary')){
	            wp_nav_menu( array(
	                'theme_location'    => 'primary',
	                'depth'             => 4,
	                'container'         => 'div',
	                'container_class' 	=> 'blu-top-header collapse navbar-collapse',
	                // 'fallback_cb'       => 'wp_bootstrap_navwalker::fallback',
	                'walker'            => new Bluth_Menu())
	            ); 
	    	} ?>
	    </nav>
	</div>
</div>