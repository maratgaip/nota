<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

?>
<script type="text/javascript">

function gd_quicksetup_poll_wizard($) {
	data = {
		_wpnonce: '<?php echo wp_create_nonce( 'quick_setup_ajax_poll' ); ?>',
		action: 'quick_setup_ajax_poll'
	};
	$.post( ajaxurl, data, function( response ) {
		if ( "2" != response ) {
			$(".q-setup-building").hide();
			$(".q-setup-modal").fadeIn();
			clearInterval( gd_timer );
		}
	});
}

gd_timer = setInterval( function() { gd_quicksetup_poll_wizard( jQuery ); }, 1000 ); 

</script>

<div class="q-setup-steps-wrap">
	<div class="q-setup-building clear-fix">
		<img src="<?php echo home_url(); ?>/wp-content/plugins/quick-setup/images/loading-snake.gif" /> <?php _e( 'Building your site...', 'gd_quickseutp' ); ?>
	</div>

	<div class="q-setup-modal" style="display: none;">
		<div class="q-setup-view-site">
			<div class="q-setup-modal-screen"></div>
			<div class="q-setup-screen-text">
				<?php _e( 'Check out your new home <br />on the Web.', 'gd_quicksetup' ); ?><br /> <br />
				<a href="<?php echo home_url(); ?>" class="back button-primary"><?php _e( 'View My Website', 'gd_quicksetup' ); ?></a>
			</div>
		</div>

		<div class="q-setup-plus"></div>

		<div class="q-setup-mobile-site">
			<div class="q-setup-mobile-opt"><?php _e( 'MOBILE OPTIMIZED', 'gd_quicksetup' ); ?></div>
			<div class="q-setup-mobile"></div>
			<div class="q-setup-mobile-text">
				<?php _e( "Don't forget your mobile-friendly site!", 'gd_quicksetup' ); ?><br />
				<?php _e( 'Grab your phone and go to', 'gd_quicksetup' ); ?><br />
				<?php echo esc_url( preg_replace( '/http[s]?:\/\//', '', home_url() ) ); ?>
			</div>
		</div>

		<div class="q-setup-modal-footer">
			<a href="<?php echo esc_url( admin_url() ); ?>"><?php _e( 'Go to My Dashboard', 'gd_quicksetup' ); ?></a>
		</div>
	</div>
</div>