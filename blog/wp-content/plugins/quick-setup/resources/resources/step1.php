<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

?>
<form action="<?php echo esc_url( add_query_arg( array( 'step' => '2' ) ) ); ?>" method="post" id="form-step1">
	<input type="hidden" name="site_type" id="site_type" value="" />
	<?php wp_nonce_field( 'quick_setup_step2' ); ?>

	<?php
	
	$result = $this->api->get_site_types();
	
	if ( is_wp_error( $result ) ) {
		wp_die( __( 'There was a problem fetching the data', 'gd_quicksetup' ) );
	} else {
		$result = json_decode( $result['body'], true );
		if ( !is_array( $result ) ) {
			wp_die( __( 'There was a problem fetching the data', 'gd_quicksetup' ) );
		}
	}

	?>
	
	<div class="q-setup-steps-wrap"> <!-- Rounded corners start -->
		
		<div class="q-setup-steps q-setup-step-1"></div>
		<ul class="q-setup-steps-text">
			<li class="q-setup-step-1-text current-step-text"><?php _e( 'Site', 'gd_quicksetup' ); ?></li>
			<li class="q-setup-step-2-text"><?php _e( 'Theme', 'gd_quicksetup' ); ?></li>
			<li class="q-setup-step-3-text"><?php _e( 'Content', 'gd_quicksetup' ); ?></li>
		</ul>
		
		<div class="q-setup-step-title">
			<?php _e( '1. Choose a Site Type', 'gd_quicksetup' ); ?>
		</div>
		
		<ul class="q-setup-site-type clear-fix">
			<!-- Personal Website -->
			<li class="personal-type">
				<div class="q-setup-site-type-inner">
					<div class="q-setup-site-type-text">
						<strong><?php echo esc_html( $result['personal']['label1'] ); ?></strong><br />
						<?php echo esc_html( $result['personal']['desc'] ); ?>
					</div>
					<div class="q-setup-pers-ws-img"></div>
				</div>
				
				<div class="q-setup-site-type-overlay">
					<div class="q-setup-site-type-overlay-title"><?php echo esc_html( $result['personal']['label2'] ); ?></div>
					<p class="personal-full">
						<?php echo esc_html( $result['personal']['full'] ); ?>
					</p>
					<p class="personal-great">
						<strong><?php _e( 'Great for:', 'gd_quicksetup' ); ?></strong> <?php echo esc_html( $result['personal']['great'] ); ?>
					</p>
					<div class="q-setup-profile-btn">
						<input type="submit" value="<?php esc_attr_e( 'Select', 'gd_quicksetup' ); ?>" class="back button select-pers-btn" data-site-type="personal" />
					</div>
				</div>
			</li>
			<!-- End Personal Website -->
			
			<!-- Organization Website -->
			<li class="company-type">
				<div class="q-setup-site-type-inner">
					<div class="q-setup-site-type-text">
						<strong><?php echo esc_html( $result['company']['label1'] ); ?></strong><br />
						<?php echo esc_html( $result['company']['desc'] ); ?>
					</div>
					<div class="q-setup-org-ws-img"></div>
				</div>
				
				<div class="q-setup-site-type-overlay">
					<div class="q-setup-site-type-overlay-title"><?php echo esc_html( $result['company']['label2'] ); ?></div>
					<p class="company-full">
						<?php echo esc_html( $result['company']['full'] ); ?>
					</p>
					<p class="company-great">
						<strong><?php _e( 'Great for:', 'gd_quicksetup' ); ?></strong> <?php echo esc_html( $result['company']['great'] ); ?>
					</p>
					<div class="q-setup-profile-btn">
						<input type="submit" value="<?php esc_attr_e( 'Select', 'gd_quicksetup' ); ?>" class="back button select-org-btn" data-site-type="company" />
					</div>
				</div>
			</li>
			<!-- End Organization Website -->
			
			<!-- Gallery Website -->
			<li class="gallery-type">
				<div class="q-setup-site-type-inner">
					<div class="q-setup-site-type-text">
						<strong><?php echo esc_html( $result['gallery']['label1'] ); ?></strong>
						<?php echo esc_html( $result['gallery']['desc'] ); ?>
					</div>
					<div class="q-setup-gallery-ws-img"></div>
				</div>
				
				<div class="q-setup-site-type-overlay">
					<div class="q-setup-site-type-overlay-title"><?php echo esc_html( $result['gallery']['label2'] ); ?></div>
					<p class="gallery-full">
						<?php echo esc_html( $result['gallery']['full'] ); ?>
					</p>
					<p class="gallery-great">
						<strong><?php _e( 'Great for:', 'gd_quicksetup' ); ?></strong> <?php echo esc_html( $result['gallery']['great'] ); ?>
					</p>
					<div class="q-setup-profile-btn">
						<input type="submit" value="<?php esc_attr_e( 'Select', 'gd_quicksetup' ); ?>" class="back button select-gal-btn" data-site-type="gallery" />
					</div>
				</div>
			</li>
			<!-- End Gallery Website -->
			
			<!-- Blog Website -->
			<li class="blog-type">
				<div class="q-setup-site-type-inner">
					<div class="q-setup-site-type-text">
						<strong><?php echo esc_html( $result['blog']['label1'] ); ?></strong>
						<?php echo esc_html( $result['blog']['desc'] ); ?>
					</div>
					<div class="q-setup-blog-ws-img"></div>
				</div>
				
				<div class="q-setup-site-type-overlay">
					<div class="q-setup-site-type-overlay-title"><?php echo esc_html( $result['blog']['label2'] ); ?></div>
					<p class="blog-full">
						<?php echo esc_html( $result['blog']['full'] ); ?>
					</p>
					<p class="blog-great">
						<strong><?php _e( 'Great for:', 'gd_quicksetup' ); ?></strong> <?php echo esc_html( $result['blog']['great'] ); ?>
					</p>
					<div class="q-setup-profile-btn">
						<input type="submit" value="<?php esc_attr_e( 'Select', 'gd_quicksetup' ); ?>" class="back button select-blog-btn" data-site-type="blog" />
					</div>
				</div>
			</li>
			<!-- End Blog Website -->
		</ul>
	
	</div>
</form>
