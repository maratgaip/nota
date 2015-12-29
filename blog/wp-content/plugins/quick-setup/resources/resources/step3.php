<?php

/**
 * Copyright 2013 Go Daddy Operating Company, LLC. All Rights Reserved.
 */

// Make sure it's wordpress
if ( !defined( 'ABSPATH' ) )
	die( 'Forbidden' );

?>
<form action="<?php echo esc_url( add_query_arg( array( 'step' => '4' ) ) ); ?>" method="post" id="form-step3" enctype="multipart/form-data">
	<input type="hidden" name="theme_slug" id="theme_slug" value="<?php echo esc_attr( $_POST['theme_slug'] ); ?>" />
	<input type="hidden" name="site_type" id="site_type" value="<?php echo esc_attr( $_POST['site_type'] ); ?>" />
	<?php wp_nonce_field( 'quick_setup_step4' ); ?>

	<?php

	$result = $this->api->get_features( $_POST['site_type'], $_POST['theme_slug'] );

	if ( is_wp_error( $result ) ) {
		wp_die( __( 'There was a problem fetching the data', 'gd_quicksetup' ) );
	} else {
		$result = json_decode( $result['body'], true );
		if ( !is_array( $result ) ) {
			wp_die( __( 'There was a problem fetching the data', 'gd_quicksetup' ) );
		}
	}

	/**
	 * What did the user enter last time?
	 * @staticvar array $data cached option
	 * @param string $key
	 * @param string $field
	 * @return boolean|string
	 */
	function gd_quicksetup_get_last_value( $key, $field, $default = '' ) {
		static $data = null;
		if ( null === $data ) {
			$data = get_option( 'gd_quicksetup_last_post' );
		}

		// Look up saved values
		if ( isset( $data[$field] ) && isset( $data[$field][$key] ) && !empty( $data[$field][$key] ) ) {
			return $data[$field][$key];
		}

		// If there's no saved value, but this is a checkbox, return false
		if ( in_array( $field, array( 'share', 'extra_plugins' ) ) ) {
			
			// If there's no data, this is the first time we've hit the page, so checkboxes should default to true
			if ( empty( $data ) ) {
				return 'on';
			}
			return false;
		}

		// Lastly, return empty string
		return $default;
	}

	/**
	 * Did the user dis/enable the page last time?
	 * @staticvar array $data cached option
	 * @param string $key
	 * @param string $default
	 * @return boolean
	 */
	function gd_quicksetup_page_is_enabled( $key, $default ) {
		static $data = null;
		if ( null === $data ) {
			$data = get_option( 'gd_quicksetup_last_post' );
		}
		if ( isset( $data['enabled'] ) && isset( $data['enabled'][$key] ) ) {
			return $data['enabled'][$key];
		}
		if ( isset( $data['enabled'] ) && is_array( $data['enabled'] ) ) {
			return false;
		}
		return $default;
	}

	?>
	
	<div class="q-setup-steps-wrap"> <!-- Rounded corners start -->
		
		<div class="q-setup-steps q-setup-step-3"></div>
		<ul class="q-setup-steps-text">
			<li class="q-setup-step-1-text"><?php _e( 'Site', 'gd_quicksetup' ); ?></li>
			<li class="q-setup-step-2-text"><?php _e( 'Theme', 'gd_quicksetup' ); ?></li>
			<li class="q-setup-step-3-text current-step-text"><?php _e( 'Content', 'gd_quicksetup' ); ?></li>
		</ul>
		
		<div class="q-setup-step-title">
			<?php _e( '3. Select Pages &amp; Create Content', 'gd_quicksetup' ); ?>
		</div>

		<?php $idx = 0; ?>
		<?php foreach ( $result['pages'] as $key => $page ) : ?>
			<?php if ( 'page' === $page['type'] ) : ?>
				<div class="q-setup-item clear-fix page-container" data-index="<?php echo esc_attr( $idx ); ?>" id="q-setup-panel-<?php echo esc_attr( $idx ); ?>">
					<input type="hidden" name="type[<?php echo esc_attr( $key ); ?>]" value="page" />
					<input type="hidden" name="enabled[<?php echo esc_attr( $key ); ?>]" value="true" />
					<input type="hidden" name="home[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $page['home'] ); ?>" />
					<div class="q-setup-icn q-setup-<?php echo esc_attr( $page['class'] ); ?>-icn"></div>
					<div class="q-setup-pg-desc">
						<div class="q-setup-pg-name"><?php echo esc_html( $page['title'] ); ?></div>
						<p>
							<?php echo esc_html( $page['description'] ); ?>
						</p>
						<?php if ( $page['removable'] ) : ?>
							<div class="">
								<a href="javascript:;" class="back button qs-add-remove"><?php _e( 'Remove', 'gd_quicksetup' ); ?></a> &nbsp;
								<a href="javascript:;" class="q-setup-info-icn" title="<?php esc_attr_e( 'The plugins that support your selected settings are automatically installed.', 'gd_quicksetup' ); ?>"></a>
							</div>
						<?php endif; ?>
					</div>
					<ul class="q-setup-page-info">
						<li>
							<input type="text" class="q-setup-pg-name-input" name="title[<?php echo esc_attr( $key ); ?>]" placeholder="<?php echo esc_attr( $page['page_title'] ); ?>" value="<?php echo esc_attr( gd_quicksetup_get_last_value( $key, 'title', $page['page_title'] ) ); ?>" />
						</li>
						<li>
							<textarea class="q-setup-pg-desc-textarea" name="content[<?php echo esc_attr( $key ); ?>]" placeholder="<?php echo esc_attr_e( $page['suggestion'] ); ?>"><?php echo esc_textarea( gd_quicksetup_get_last_value( $key, 'content' ) ); ?></textarea>
						</li>
					</ul>
					<?php if ( !gd_quicksetup_page_is_enabled( $key, $page['enabled'] ) ) : ?>
						<script type="text/javascript">
							jQuery(document).ready( function( $ ) {
								$(this).find("#q-setup-panel-<?php echo esc_attr( $idx ); ?> a.qs-add-remove").trigger("click");
							});
						</script>
					<?php endif; ?>
				</div>
			<?php elseif ( 'blog' === $page['type'] ) : ?>
				<div class="q-setup-item clear-fix page-container" data-index="<?php echo esc_attr( $idx ); ?>" id="q-setup-panel-<?php echo esc_attr( $idx ); ?>">
					<input type="hidden" name="type[<?php echo esc_attr( $key ); ?>]" value="blog" />
					<input type="hidden" name="enabled[<?php echo esc_attr( $key ); ?>]" value="true" />
					<input type="hidden" name="home[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $page['home'] ); ?>" />
					<div class="q-setup-icn q-setup-<?php echo esc_attr( $page['class'] ); ?>-icn"></div>
					<div class="q-setup-pg-desc">
						<div class="q-setup-pg-name"><?php echo esc_html( $page['title'] ); ?></div>
						<p>
							<?php echo esc_html( $page['description'] ); ?>
						</p>
						<?php if ( $page['removable'] ) : ?>
							<div class="">
								<a href="javascript:;" class="back button qs-add-remove"><?php _e( 'Remove', 'gd_quicksetup' ); ?></a> &nbsp;
								<a href="javascript:;" class="q-setup-info-icn" title="<?php esc_attr_e( 'The plugins that support your selected settings are automatically installed.', 'gd_quicksetup' ); ?>"></a>
							</div>
						<?php endif; ?>
					</div>
					<ul class="q-setup-page-info">
						<li>
							<input type="text" class="q-setup-pg-name-input" name="title[<?php echo esc_attr( $key ); ?>]" placeholder="<?php echo esc_attr( $page['page_title'] ); ?>" value="<?php echo esc_attr( gd_quicksetup_get_last_value( $key, 'title', $page['page_title'] ) ); ?>" />
						</li>
						<li>
							<textarea class="q-setup-pg-desc-textarea" name="content[<?php echo esc_attr( $key ); ?>]" placeholder="<?php echo esc_attr( $page['suggestion'] ); ?>"><?php echo esc_textarea( gd_quicksetup_get_last_value( $key, 'content' ) ); ?></textarea>
						</li>
					</ul>
					<?php if ( !gd_quicksetup_page_is_enabled( $key, $page['enabled'] ) ) : ?>
						<script type="text/javascript">
							jQuery(document).ready( function( $ ) {
								$(this).find("#q-setup-panel-<?php echo esc_attr( $idx ); ?> a.qs-add-remove").trigger("click");
							});
						</script>
					<?php endif; ?>
				</div>
			<?php elseif ( 'gallery' === $page['type'] ) : ?>
				<div class="q-setup-item clear-fix page-container" data-index="<?php echo esc_attr( $idx ); ?>" id="q-setup-panel-<?php echo esc_attr( $idx ); ?>">
					<input type="hidden" name="type[<?php echo esc_attr( $key ); ?>]" value="gallery" />
					<input type="hidden" name="enabled[<?php echo esc_attr( $key ); ?>]" value="true" />
					<input type="hidden" name="home[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $page['home'] ); ?>" />
					<div class="q-setup-icn q-setup-<?php echo esc_attr( $page['class'] ); ?>-icn"></div>
					<div class="q-setup-pg-desc">
						<div class="q-setup-pg-name"><?php echo esc_html( $page['title'] ); ?></div>
						<p>
							<?php echo esc_html( $page['description'] ); ?>
						</p>
						<?php if ( $page['removable'] ) : ?>
							<div class="">
								<a href="javascript:;" class="back button qs-add-remove"><?php _e( 'Remove', 'gd_quicksetup' ); ?></a> &nbsp;
								<a href="javascript:;" class="q-setup-info-icn" title="<?php esc_attr_e( 'The plugins that support your selected settings are automatically installed.', 'gd_quicksetup' ); ?>"></a>
							</div>
						<?php endif; ?>
					</div>
					<ul class="q-setup-page-info">
						<?php for ( $i = 0 ; $i < 3 ; $i++ ) : ?>
							<li>
								<label for="upload_image_<?php echo esc_attr( $i ); ?>">
									<input id="upload_image_<?php echo esc_attr( $key ); ?>_<?php echo esc_attr( $i ); ?>" name="upload_image_<?php echo esc_attr( $key ); ?>_<?php echo esc_attr( $i ); ?>" data-idx="<?php echo esc_attr( $key ); ?>" data-index="<?php echo esc_attr( $i ); ?>" type="file" size="36" value="" />
								</label>
								<a href="javascript:;" class="q-setup-remove-gallery-file"><?php _e( 'Remove', 'gd_quicksetup' ); ?></a>
							</li>
						<?php endfor; ?>
						<li>
							<a href="javascript:;" class="button button-primary qs-add-images"><?php _e( 'Add Images', 'gd_quicksetup' ); ?></a>
						</li>
					</ul>
					<?php if ( !gd_quicksetup_page_is_enabled( $key, $page['enabled'] ) ) : ?>
						<script type="text/javascript">
							jQuery(document).ready( function( $ ) {
								$(this).find("#q-setup-panel-<?php echo esc_attr( $idx ); ?> a.qs-add-remove").trigger("click");
							});
						</script>
					<?php endif; ?>
				</div>
			<?php elseif ( 'contact' === $page['type'] ) : ?>
				<div class="q-setup-item clear-fix page-container" data-index="<?php echo esc_attr( $idx ); ?>" id="q-setup-panel-<?php echo esc_attr( $idx ); ?>">
					<input type="hidden" name="type[<?php echo esc_attr( $key ); ?>]" value="contact" />
					<input type="hidden" name="enabled[<?php echo esc_attr( $key ); ?>]" value="true" />
					<input type="hidden" name="home[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $page['home'] ); ?>" />
					<div class="q-setup-icn q-setup-<?php echo esc_attr( $page['class'] ); ?>-icn"></div>
					<div class="q-setup-pg-desc">
						<div class="q-setup-pg-name"><?php echo esc_html( $page['title'] ); ?></div>
						<p>
							<?php echo esc_html( $page['description'] ); ?>
						</p>
						<?php if ( $page['removable'] ) : ?>
							<div class="">
								<a href="javascript:;" class="back button qs-add-remove"><?php _e( 'Remove', 'gd_quicksetup' ); ?></a> &nbsp;
								<a href="javascript:;" class="q-setup-info-icn"  title="<?php esc_attr_e( 'The plugins that support your selected settings are automatically installed.', 'gd_quicksetup' ); ?>"></a>
							</div>
						<?php endif; ?>
					</div>
					<ul class="q-setup-page-info">
						<li>
							<input type="text" name="contact_name[<?php echo esc_attr( $key ); ?>]" class="q-setup-pg-name-input" placeholder="<?php esc_attr_e( 'Name or Business', 'gd_quicksetup' ); ?>" value="<?php echo esc_attr( gd_quicksetup_get_last_value( $key, 'contact_name' ) ); ?>" />
						</li>
						<li>
							<input type="text" name="contact_address[<?php echo esc_attr( $key ); ?>]"class="q-setup-pg-name-input" placeholder="<?php esc_attr_e( 'Address', 'gd_quicksetup' ); ?>" value="<?php echo esc_attr( gd_quicksetup_get_last_value( $key, 'contact_address' ) ); ?>" />
						</li>

						<li class="q-setup-fl">
							<input type="text" name="contact_city[<?php echo esc_attr( $key ); ?>]"class="q-setup-pg-city-input" placeholder="<?php esc_attr_e( 'City', 'gd_quicksetup' ); ?>" value="<?php echo esc_attr( gd_quicksetup_get_last_value( $key, 'contact_city' ) ); ?>" />
						</li>
						<li class="q-setup-fl">
							<input type="text" name="contact_state[<?php echo esc_attr( $key ); ?>]"class="q-setup-pg-state-input" placeholder="<?php esc_attr_e( 'State', 'gd_quicksetup' ); ?>" value="<?php echo esc_attr( gd_quicksetup_get_last_value( $key, 'contact_state' ) ); ?>" />
						</li>
						<li class="q-setup-fl">
							<input type="text" name="contact_zip[<?php echo esc_attr( $key ); ?>]"class="q-setup-pg-zip-input" placeholder="<?php esc_attr_e( 'Zip', 'gd_quicksetup' ); ?>" value="<?php echo esc_attr( gd_quicksetup_get_last_value( $key, 'contact_zip' ) ); ?>" />
						</li>
						<li>
							<input type="text" name="contact_phone[<?php echo esc_attr( $key ); ?>]" class="q-setup-pg-name-input" placeholder="<?php esc_attr_e( '555-555-5555', 'gd_quicksetup' ); ?>" value="<?php echo esc_attr( gd_quicksetup_get_last_value( $key, 'contact_phone' ) ); ?>" />
						</li>
						<li>
							<input type="text" name="contact_email[<?php echo esc_attr( $key ); ?>]"class="q-setup-pg-name-input" placeholder="<?php $user = wp_get_current_user(); echo esc_attr( $user->user_email ); ?>" value="<?php echo esc_attr( gd_quicksetup_get_last_value( $key, 'contact_email' ) ); ?>" />
						</li>
						<li>
							<label class="q-setup-block-label"><?php _e( 'Social networks', 'gd_quicksetup' ); ?></label>
							<input type="text" name="contact_twitter[<?php echo esc_attr( $key ); ?>]" class="q-setup-pg-name-input" placeholder="<?php esc_attr_e( '@twitterhandle', 'gd_quicksetup' ); ?>" value="<?php echo esc_attr( gd_quicksetup_get_last_value( $key, 'contact_twitter' ) ); ?>" />
						</li>
						<li>
							<input type="text" name="contact_facebook[<?php echo esc_attr( $key ); ?>]"class="q-setup-pg-name-input" placeholder="<?php esc_attr_e( 'http://facebook.com/user.name', 'gd_quicksetup' ); ?>" value="<?php echo esc_attr( gd_quicksetup_get_last_value( $key, 'contact_facebook' ) ); ?>" />
						</li>
						<li>
							<input type="text" name="contact_googleplus[<?php echo esc_attr( $key ); ?>]"class="q-setup-pg-name-input" placeholder="<?php esc_attr_e( 'https://plus.google.com/u/0/0000000000000000/', 'gd_quicksetup' ); ?>" value="<?php echo esc_attr( gd_quicksetup_get_last_value( $key, 'contact_googleplus' ) ); ?>" />
						</li>
					</ul>
					<?php if ( !gd_quicksetup_page_is_enabled( $key, $page['enabled'] ) ) : ?>
						<script type="text/javascript">
							jQuery(document).ready( function( $ ) {
								$(this).find("#q-setup-panel-<?php echo esc_attr( $idx ); ?> a.qs-add-remove").trigger("click");
							});
						</script>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<?php $idx++; ?>
		<?php endforeach; ?>

		<?php $data = get_option( 'gd_quicksetup_last_post' ); ?>
		<?php if ( isset( $data['type'] ) && is_array( $data['type'] ) && !empty( $data['type'] ) ) : ?>
			<?php foreach ( $data['type'] as $key => $type ) : ?>
				<?php if ( 0 === strpos( $key, 'custom_page_' ) && false === strpos( $key, '{{idx}}' ) && 'page' === $type ) : ?>
					<div class="q-setup-item clear-fix page-container" data-index="<?php echo esc_attr( $idx ); ?>" id="q-setup-panel-<?php echo esc_attr( $idx ); ?>">
						<input type="hidden" name="type[<?php echo esc_attr( $key ); ?>]" value="page" />
						<input type="hidden" name="enabled[<?php echo esc_attr( $key ); ?>]" value="true" />
						<input type="hidden" name="home[<?php echo esc_attr( $key ); ?>]" value="false" />
						<div class="q-setup-icn q-setup-custom-icn"></div>
						<div class="q-setup-pg-desc">
							<div class="q-setup-pg-name"><?php _e( 'Custom', 'gd_quicksetup' ); ?></div>
							<p>
								<?php _e( 'Create custom pages for your unique needs.', 'gd_quickseutp' ); ?>
							</p>
							<div class="">
								<a href="javascript:;" class="back button qs-add-remove"><?php _e( 'Remove', 'gd_quicksetup' ); ?></a> &nbsp;
								<a href="javascript:;" class="q-setup-info-icn" title="<?php esc_attr_e( 'The plugins that support your selected settings are automatically installed.', 'gd_quicksetup' ); ?>"></a>
							</div>
						</div>
						<ul class="q-setup-page-info">
							<li>
								<input type="text" class="q-setup-pg-name-input" name="title[<?php echo esc_attr( $key ); ?>]" placeholder="<?php echo esc_attr( $page['page_title'] ); ?>" value="<?php echo esc_attr( gd_quicksetup_get_last_value( $key, 'title' ) ); ?>" />
							</li>
							<li>
								<textarea class="q-setup-pg-desc-textarea" name="content[<?php echo esc_attr( $key ); ?>]" placeholder="<?php echo esc_attr_e( $page['suggestion'] ); ?>"><?php echo esc_textarea( gd_quicksetup_get_last_value( $key, 'content' ) ); ?></textarea>
							</li>
						</ul>
						<?php if ( !gd_quicksetup_page_is_enabled( $key, $page['enabled'] ) ) : ?>
							<script type="text/javascript">
								jQuery(document).ready( function( $ ) {
									$(this).find("#q-setup-panel-<?php echo esc_attr( $idx ); ?> a.qs-add-remove").trigger("click");
								});
							</script>
						<?php endif; ?>
					</div>
					<?php $idx++; ?>
				<?php endif; ?>
			<?php endforeach; ?>
		<?php endif; ?>

		<div class="q-setup-item clear-fix">
			<div class="q-setup-pg-desc">
				<div class="q-setup-pg-name"><?php _e( 'Custom Pages', 'gd_quicksetup' ); ?></div>
			</div>
			<div class="q-setup-extra-wrap">
				<a href="javascript:;" class="qs-add-custom-page"><?php _e( 'Add custom page', 'gd_quicksetup' ); ?></a>
			</div>
		</div>
		
		<div id="qs-custom-page-template" style="display: none;">
			<div class="q-setup-item clear-fix page-container" data-index="{{idx}}" id="q-setup-panel-{{idx}}" style="display: none;">
				<input type="hidden" name="type[custom_page_{{idx}}]" value="page" />
				<input type="hidden" name="enabled[custom_page_{{idx}}]" value="true" />
				<input type="hidden" name="home[custom_page_{{idx}}]" value="" />
				<div class="q-setup-icn q-setup-custom-icn"></div>
				<div class="q-setup-pg-desc">
					<div class="q-setup-pg-name"><?php _e( 'Custom', 'gd_quicksetup' ); ?></div>
					<p>
						<?php _e( 'Create custom pages for your unique needs.', 'gd_quickseutp' ); ?>
					</p>
					<div class="">
						<a href="javascript:;" class="back button qs-add-remove"><?php _e( 'Remove', 'gd_quicksetup' ); ?></a> &nbsp;
						<a href="javascript:;" class="q-setup-info-icn" title="<?php esc_attr_e( 'The plugins that support your selected settings are automatically installed.', 'gd_quicksetup' );?>"></a>
					</div>
				</div>
				<ul class="q-setup-page-info">
					<li>
						<input type="text" class="q-setup-pg-name-input" name="title[custom_page_{{idx}}]" placeholder="<?php echo esc_attr_e( 'Page title', 'gd_quicksetup' ); ?>" />
					</li>
					<li>
						<textarea class="q-setup-pg-desc-textarea" name="content[custom_page_{{idx}}]" placeholder="<?php esc_attr_e( 'Page content goes here ...', 'gd_quicksetup' ); ?>"></textarea>
					</li>
				</ul>
			</div>
		</div>
		
		<div class="q-setup-item q-setup-item-last clear-fix">
			<div class="q-setup-pg-desc">
				<div class="q-setup-pg-name">Social <!--&nbsp;<a href="#" class="q-setup-info-icn"></a>--></div>
				<p>
					<?php _e( 'Allow visitors to share your site on their favorite social networks.', 'gd_quicksetup' ); ?>
				</p>
			</div>
			<div class="q-setup-extra-wrap">
				<ul>
					<li><label><input type="checkbox" <?php checked( gd_quicksetup_get_last_value( 'facebook', 'share' ) === 'on' ); ?> name="share[facebook]" value="on" id="qSetupFBIcn"/>&nbsp;<span class="q-setup-fb-icn" for="qSetupFBIcn"></span></label></li>
					<li><label><input type="checkbox" <?php checked( gd_quicksetup_get_last_value( 'twitter', 'share' ) === 'on' ); ?> name="share[twitter]" value="on" id="qSetupTWIcn"/>&nbsp;<span class="q-setup-tw-icn" for="qSetupTWIcn"></span></label></li>
					<li><label><input type="checkbox" <?php checked( gd_quicksetup_get_last_value( 'googleplus', 'share' ) === 'on' ); ?> name="share[googleplus]" value="on"	id="qSetupGPIcn"/>&nbsp;<span class="q-setup-gp-icn" for="qSetupGPIcn"></span></label></li>
				</ul>
			</div>
		</div>
		
	</div>
	
	<div class="q-setup-optionals-wrap">
		<div class="q-setup-optionals-title">
			<a href="javascript:;" class="q-setup-expand"></a> <span><?php _e( 'Plug-in Settings (optional)', 'gd_quicksetup' ); ?></span>
		</div>
		<ul class="q-setup-optionals-list" style="display: none;">
			<?php foreach ( $result['plugins'] as $plugin ) : ?>
				<li>
					<label><input type="checkbox" <?php checked( gd_quicksetup_get_last_value( $plugin['slug'], 'extra_plugins' ) === 'on' ); ?> name="extra_plugins[<?php echo esc_attr( $plugin['slug'] ); ?>]" value="on" /> <?php echo do_shortcode( esc_html( $plugin['description'] ) ); ?></label> &nbsp;<a href="javascript:;" class="q-setup-info-icn" title="<?php echo esc_attr( $plugin['tip'] ); ?>"></a>
				</li>
			<?php endforeach; ?>			
		</ul>
	</div>
	
	<input type="checkbox" id="q-setup-final-warning"> <label for="q-setup-final-warning"><?php _e( 'I understand that clicking "Finish" will cause my database to be overwritten.', 'gd_quicksetup' ); ?></label>

	<ul class="q-setup-btn-wrap clear-fix">
		<li>
			<a href="javascript:;" class="button-primary gd-quicksetup-wizard-submit button-disabled" disabled><?php _e( 'Finish', 'gd_quicksetup' ); ?></a>
		</li>
		<li>
			<a href="<?php echo esc_url( admin_url( 'tools.php?page=gd_quicksetup-wizard' ) ); ?>" class="gd-quicksetup-wizard-start-over"><?php _e( 'Start over', 'gd_quicksetup' ); ?></a>
		</li>
	</ul>
</form>
