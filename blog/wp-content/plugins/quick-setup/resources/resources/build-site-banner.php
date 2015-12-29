<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

?>
<div id="gd_quicksetup_start_wizard" class="updated q-setup-wrap clear-fix">
	
	<div class="q-setup-img"></div>
	
	<div class="q-setup-text-wrap">
		<h3><?php _e( 'Go Daddy Quick Setup &trade;', 'gd_quicksetup' ); ?></h3>
		<h2>
			<?php _e( 'Build your WordPress site in just a few clicks.', 'gd_quicksetup' ); ?>
		</h2>
		<a href="<?php echo esc_url( admin_url( 'tools.php?page=gd_quicksetup-wizard' ) ); ?>" class="g-btn-prg g-btn-lg"><?php _e( 'Get Started', 'gd_quicksetup' ); ?></a> &nbsp;&nbsp;<a href="javascript:;" id="gd_quicksetup_wizard_dismiss" class="q-setup-link"><?php _e( "No thanks, I'm good.", 'gd_quicksetup' ); ?></a>
	</div>
	
</div>
<script type="text/javascript">
jQuery(document).ready( function() { 
	jQuery('#gd_quicksetup_wizard_dismiss').on( 'click', function() {
		jQuery.post( ajaxurl, {
			pointer: 'gd-quicksetup-start-wizard',
			action: 'dismiss-wp-pointer'
		});
		jQuery("#gd_quicksetup_start_wizard").fadeOut();
	});
});	
</script>
