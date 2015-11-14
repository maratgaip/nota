<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

?>
<div id="gd_quicksetup_site_setup" class="updated q-setup-wrap clear-fix">
	
	<div class="q-setup-done-img"></div>
	
	<div class="q-setup-text-wrap">
		<h2><?php _e( 'Keep your site fresh with new or updated content.', 'gd_quicksetup' ); ?></h2>
		
		
		<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=page' ) ); ?>" class="g-btn-prg g-btn-lg"><?php _e( 'Edit Pages', 'gd_quicksetup' ); ?></a> &nbsp;
		<a href="<?php echo esc_url( admin_url( 'post-new.php' ) ); ?>" class="g-btn-prg g-btn-lg"><?php _e( 'Write Post', 'gd_quicksetup' ); ?></a>
		<br /><br />
		
		<a href="<?php echo home_url(); ?>" class="q-setup-link"><?php _e( 'View my site', 'gd_quicksetup' ); ?></a> &nbsp;
		<a href="javascript:;" id="gd_quicksetup_wizard_complete_dismiss" class="welcome-panel-close"><?php _e( 'Dismiss', 'gd_quicksetup' ); ?></a>
			
	</div>
	
</div>

<script type="text/javascript">
jQuery(document).ready( function() { 
	jQuery('#gd_quicksetup_wizard_complete_dismiss').on( 'click', function() {
		jQuery.post( ajaxurl, {
			pointer: 'gd-quicksetup-wizard-complete',
			action: 'dismiss-wp-pointer'
		});
		jQuery("#gd_quicksetup_site_setup").fadeOut();
	});
});	
</script>
