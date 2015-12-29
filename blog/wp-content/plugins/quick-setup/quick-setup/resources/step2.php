<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

?>
<form action="<?php echo esc_url( add_query_arg( array( 'step' => '3' ) ) ); ?>" method="post" id="form-step2">
	<input type="hidden" name="theme_slug" id="theme_slug" value="" />
	<input type="hidden" name="site_type" id="site_type" value="<?php echo esc_attr( $_POST['site_type'] ); ?>" />
	<?php wp_nonce_field( 'quick_setup_step3' ); ?>

	<?php
		
	$result = $this->api->get_themes( $_POST['site_type'] );

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
		
		<div class="q-setup-steps q-setup-step-2"></div>
		<ul class="q-setup-steps-text">
			<li class="q-setup-step-1-text"><?php _e( 'Site', 'gd_quicksetup' ); ?></li>
			<li class="q-setup-step-2-text current-step-text"><?php _e( 'Theme', 'gd_quicksetup' ); ?></li>
			<li class="q-setup-step-3-text"><?php _e( 'Content', 'gd_quicksetup' ); ?></li>
		</ul>
		
		<div class="q-setup-step-title">
			<?php _e( '2. Pick a Theme', 'gd_quicksetup' ); ?>
		</div>
		
		<ul class="q-setup-theme-list clear-fix">
			<?php foreach ( $result as $theme ) : ?>
			
			<li>
				<div class="q-setup-theme-name"><?php echo esc_html( $theme['name'] ); ?></div>
				<div class="q-setup-theme <?php if ( $theme['slug'] === wp_get_theme()->get_stylesheet() ) : ?>q-setup-theme-selected<?php endif; ?>">
					<img src="<?php echo esc_attr( $theme['thumb'] ); ?>" />

					<div class="q-setup-theme-pick-overlay">
						<a href="#TB_inline?width=<?php echo esc_attr( $theme['width'] ) + 10; ?>&height=<?php echo esc_attr( $theme['height'] ) + 10; ?>&inlineId=qs_<?php echo md5( $theme['screenshot'] ); ?>" title="<?php echo esc_attr( $theme['name'] ); ?>" class="thickbox back button show-thickbox-image" data-lazy-load-target="lazy-load-<?php echo md5( $theme['screenshot'] ); ?>"><?php _e( 'Preview', 'gd_quicksetup' ); ?></a> &nbsp;
						<a href="javascript:;" class="back button form2-submit" data-theme-slug="<?php echo esc_attr( $theme['slug'] ); ?>"><?php _e( 'Select', 'gd_quicksetup' ); ?></a>
					</div>
				</div>
			</li>
			
			<?php endforeach ;?>
		</ul>
		
	</div>
	
	<span class="theme-note"><?php _e( 'Note: You can change your theme at any time, even after your site has launched, with just a few clicks.', 'gd_quicksetup' ); ?></span>

	<ul class="q-setup-btn-wrap clear-fix">
		<li>
			<a href="<?php echo esc_url( admin_url( 'tools.php?page=gd_quicksetup-wizard' ) ); ?>"><?php _e( 'Start over', 'gd_quicksetup' ); ?></a>
		</li>
	</ul>
</form>

<?php foreach ( $result as $theme ) : ?>
	<div class="q-setup-screenshot" id="qs_<?php echo md5( $theme['screenshot'] ); ?>" style="display: none;">
		<img id="lazy-load-<?php echo md5( $theme['screenshot'] ); ?>" src="<?php echo esc_attr( plugin_dir_url( 'quick-setup/images/loading-snake.gif' ) . 'loading-snake.gif' ); ?>" data-src="<?php echo esc_attr( $theme['screenshot'] ); ?>" data-width="<?php echo esc_attr( $theme['width'] ); ?>" data-height="<?php echo esc_attr( $theme['height'] ); ?>" />
	</div>
<?php endforeach; ?>