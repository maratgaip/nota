<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

?>
<script type="text/javascript">

function gd_quicksetup_poll_wizard() {
	var url = ajaxurl;
	if ( url.indexOf("?") > -1 ) {
		url += "&r=" + Math.random() ;
	} else {
		url += "?r=" + Math.random();
	}

	var data = {
		_wpnonce: '<?php echo wp_create_nonce( 'quick_setup_ajax_poll' ); ?>',
		action: 'quick_setup_ajax_poll'
	};
	jQuery.ajax({
		'data'   : data,
		'type'   : 'POST',
		'url'    : url,
		'cache'  : false
	}).done( function( response ) {
		if ( "2" != response ) {
			jQuery(".q-setup-building").hide();
			jQuery(".q-setup-modal").fadeIn();
		} else {
			setTimeout( 'gd_quicksetup_poll_wizard()' , 1000 ); 
		}
	}).fail(function (xhr, ajaxOptions, thrownError) {
		setTimeout( 'gd_quicksetup_poll_wizard()' , 1000 ); 
	});
}

setTimeout( 'gd_quicksetup_poll_wizard()' , 1000 ); 

</script>

<div class="q-setup-steps-wrap">
	<div class="q-setup-building clear-fix">
		<img src="<?php echo home_url(); ?>/wp-content/plugins/quick-setup/images/loading-snake.gif" /> <?php _e( 'Building your site...', 'gd_quickseutp' ); ?>
		<div class="q-setup-building-caption">
			<?php _e( 'This may take up to a minute or two. Thanks for your patience.', 'gd_quicksetup' ); ?>
		</div>
	</div>

	<div class="q-setup-modal" style="display: none;">
		<h2 class="q-setup-contratulations-site-live"><?php _e( 'Congratulations, your website is live!', 'gd_quicksetup' ); ?></h2>
		<div class="q-setup-view-site">
			<div class="q-setup-modal-screen"></div>
			<div class="q-setup-screen-text">
				<?php _e( 'Check out your new home <br />on the Web.', 'gd_quicksetup' ); ?><br /> <br />
				<a href="<?php echo home_url(); ?>" class="back button-primary"><?php _e( 'View My Website', 'gd_quicksetup' ); ?></a>
			</div>
		</div>

		<div class="q-setup-plus"></div>

		<div class="q-setup-mobile-site">
			<div class="q-setup-mobile-opt"><div><?php _e( 'MOBILE OPTIMIZED', 'gd_quicksetup' ); ?></div></div>
			<div class="q-setup-mobile"></div>
			<div class="q-setup-mobile-text">
				<?php _e( "Don't forget your mobile-friendly site!", 'gd_quicksetup' ); ?><br />
				<?php _e( 'Grab your phone and go to', 'gd_quicksetup' ); ?><br />
				<?php echo preg_replace( '/http[s]?:\/\//', '', home_url() ); ?>
			</div>
		</div>

		<div class="q-setup-modal-footer">
			<a href="<?php echo esc_url( admin_url() ); ?>"><?php _e( 'Go to My Dashboard', 'gd_quicksetup' ); ?></a> &nbsp;|&nbsp; <a href="http://x.co/quickwp" target="_blank"><?php _e( 'Tell Us What You Think', 'gd_quicksetup' ); ?></a> &nbsp;|&nbsp; <a href="http://x.co/quicksetup" target="_blank"><?php _e( 'Get Support', 'gd_quicksetup' ); ?></a>
		</div>
	</div>
</div>