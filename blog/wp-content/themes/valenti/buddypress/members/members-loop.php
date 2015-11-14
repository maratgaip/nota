<?php

/**
 * BuddyPress - Members Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_legacy_theme_object_filter()
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */
	
	$cb_buddypress_sidebar = ot_get_option('cb_buddypress_sidebar', 'sidebar');
	$i = 0;

	if ( $cb_buddypress_sidebar == 'nosidebar' ) {
		$cb_line_amount = 4; 
	} else {
		$cb_line_amount = 3;
	}

?>

<?php do_action( 'bp_before_members_loop' ); ?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="member-dir-count-top">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-top">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

	<?php do_action( 'bp_before_directory_members_list' ); ?>

	<div id="members-list" class="item-list clearfix" role="main">
		<div class="cb-bp-members-line clearfix">

<?php 
			while ( bp_members() ) {
			
				bp_the_member();

				if ( ( $i % $cb_line_amount == 0 ) && ( $i != 0 ) ) { 
					echo '</div><div class="cb-bp-members-line clearfix">'; 
				}
?>

				<div class="cb-member-list-box">
					<div class="item-avatar">
						<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar( 'type=full&height=260&width=262' ); ?></a>
					</div>

					<div class="item">
						<div class="item-title">
							<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>

						</div>

						<?php do_action( 'bp_directory_members_item' ); ?>

					</div>

					<div class="action">

						<?php do_action( 'bp_directory_members_actions' ); ?>

					</div>

				</div>

			<?php $i++; ?>

		<?php } ?>

		</div>

	</div>

	<?php do_action( 'bp_after_directory_members_list' ); ?>

	<?php bp_member_hidden_fields(); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-dir-count-bottom">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-bottom">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( "Sorry, no members were found.", 'buddypress' ); ?></p>
	</div>

<?php endif; ?>

<?php do_action( 'bp_after_members_loop' ); ?>
