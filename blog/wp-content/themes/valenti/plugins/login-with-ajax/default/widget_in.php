<?php
    	global $current_user;
    	get_currentuserinfo();
        $cb_author_id = $current_user->ID;
        $cb_author_posts = count_user_posts( $cb_author_id );
        $cb_nav_style = ot_get_option('cb_menu_style', 'cb_dark');
        if ($cb_nav_style == 'cb_light') {
            $cb_menu_color = 'cb-light-menu';
        } else {
             $cb_menu_color = 'cb-dark-menu';
        }

?>

<div class="cb-login-modal clearfix <?php echo $cb_menu_color; ?>">
    <div class="lwa cb-logged-in clearfix">

<?php
            if ( class_exists('buddypress') ) {

                global $bp;

                $cb_buddypress_user_profile_link = $cb_buddypress_user_profile_link = NULL;

                $cb_buddypress_current_user_id = $bp->loggedin_user->id;

                if ( function_exists( 'bp_loggedin_user_domain' ) ) {
                    $cb_buddypress_user_profile_link = bp_loggedin_user_domain();
                }

                if ( isset( $bp->profile->slug ) ) {
                    $cb_buddypress_user_profile_slug = $cb_buddypress_user_profile_link . $bp->profile->slug;
                }

                if ( function_exists( 'bp_get_groups_root_slug' ) ) {
                    $cb_buddypress_user_group_link = $cb_buddypress_user_profile_link . bp_get_groups_root_slug();
                }

                if ( function_exists( 'bp_get_messages_slug' ) ) {
                    $cb_buddypress_user_message_link = $cb_buddypress_user_profile_link . bp_get_messages_slug();
                }

                if ( function_exists( 'bp_get_activity_slug' ) ) {
                    $cb_buddypress_user_activity_link = $cb_buddypress_user_profile_link . bp_get_activity_slug();
                }

                $cb_buddypress_user_avatar = bp_core_fetch_avatar( array( 'item_id' => $cb_buddypress_current_user_id, 'type' => 'full', 'width' => 120, 'height' => 120 ) );
                $cb_buddypress_mystery_man = 'mystery-man.jpg';
                $cb_buddypress_avatar_check = strpos($cb_buddypress_user_avatar, $cb_buddypress_mystery_man);

                if ( $cb_buddypress_avatar_check === false ) {
                    $cb_buddypress_final_avatar = $cb_buddypress_user_avatar;
                } else {
                    $cb_buddypress_final_avatar = get_avatar( $cb_author_id, $size = '150' );
                }
?>

        <div class="cb-header">
                <div class="cb-title"><?php echo  $current_user->display_name;  ?></div>
                <div class="cb-close"><span class="cb-close-modal"><i class="fa fa-times"></i></span></div>
        </div>
        <div class="cb-lwa-profile">

            <div class="cb-avatar">
                    <?php echo $cb_buddypress_final_avatar; ?>
            </div>

            <div class="cb-user-data clearfix">

                <?php if ( function_exists( 'bp_get_activity_slug' ) ) { ?>

                <div class="cb-block"><i class="fa fa-user"></i>
                    <a class="url fn n" href="<?php echo $cb_buddypress_user_activity_link; ?>"><?php echo __( 'Activity', 'buddypress' ) ?></a>
                </div>

                <?php } ?>

                <?php if ( isset( $cb_buddypress_user_profile_slug ) ) { ?>

                <div class="cb-block"><i class="fa fa-user"></i>
                    <a href="<?php echo $cb_buddypress_user_profile_slug; ?>"><?php echo __( 'Profile', 'buddypress' ) ?></a>
                </div>

                <?php } ?>

                <?php if ( function_exists( 'bp_get_groups_root_slug' ) ) { ?>

                <div class="cb-block"><i class="fa fa-bookmark"></i>
                    <a href="<?php echo $cb_buddypress_user_group_link; ?>"><?php echo __( 'Memberships', 'buddypress' ) ?></a>
                </div>

                <?php } ?>

                <?php if ( function_exists( 'bp_get_messages_slug' ) ) { ?>

                <div class="cb-block"><i class="fa fa-envelope"></i>
                    <a href="<?php echo $cb_buddypress_user_message_link; ?>"><?php echo __( 'Messages', 'buddypress' ) ?></a>
                </div>

                <?php } ?>

                <?php if ( class_exists('bbpress') ) { ?>

                    <div class="cb-block"><i class="fa fa-comment"></i>
                        <a href="<?php bbp_user_topics_created_url($cb_author_id); ?>"><?php _e( 'Topics Started', 'bbpress' ); ?></a>
                    </div>

                    <div class="cb-block"><i class="fa fa-comments"></i>
                        <a href="<?php bbp_subscriptions_permalink($cb_author_id); ?>"><?php _e( 'Subscriptions', 'bbpress' ); ?></a>
                    </div>

                <?php } ?>

                <div class="cb-block cb-last-block"><i class="fa fa-sign-out"></i>
                    <a class="wp-logout" href="<?php echo wp_logout_url() ?>"><?php esc_html_e( 'Log Out' ,'login-with-ajax') ?></a>
                </div>

            </div>

        </div>

        <?php } elseif ( class_exists('bbpress') ) { ?>

        <div class="cb-header">
                <div class="cb-title"><?php echo  $current_user->display_name;  ?></div>
                <div class="cb-close"><span class="cb-close-modal"><i class="fa fa-times"></i></span></div>
        </div>
        <div class="cb-lwa-profile">

            <div class="cb-avatar">
                    <?php echo get_avatar( $cb_author_id, $size = '150' );  ?>
            </div>

            <div class="cb-user-data clearfix">


                <div class="cb-block"><i class="fa fa-user"></i>
                    <a class="url fn n" href="<?php bbp_user_profile_url($cb_author_id); ?>" rel="me"><?php _e( 'Profile', 'bbpress' ); ?></a>
                </div>

                <div class="cb-block"><i class="fa fa-comment"></i>
                    <a href="<?php bbp_user_topics_created_url($cb_author_id); ?>"><?php _e( 'Topics Started', 'bbpress' ); ?></a>
                </div>

                <div class="cb-block"><i class="fa fa-comments"></i>
                    <a href="<?php bbp_user_replies_created_url($cb_author_id); ?>"><?php _e( 'Replies Created', 'bbpress' ); ?></a>
                </div>

                <div class="cb-block"><i class="fa fa-heart"></i>
                    <a href="<?php bbp_favorites_permalink($cb_author_id); ?>"><?php _e( 'Favorites', 'bbpress' ); ?></a>
                </div>

                <div class="cb-block"><i class="fa fa-bookmark"></i>
                    <a href="<?php bbp_subscriptions_permalink($cb_author_id); ?>"><?php _e( 'Subscriptions', 'bbpress' ); ?></a>
                </div>

                <div class="cb-block cb-last-block"><i class="fa fa-sign-out"></i>
                    <a class="wp-logout" href="<?php echo wp_logout_url() ?>"><?php esc_html_e( 'Log Out' ,'login-with-ajax') ?></a>
                </div>

            </div>

        </div>

        <?php } else { ?>

        <div class="cb-header">
                <div class="cb-title"><?php echo  $current_user->display_name;  ?></div>
                <div class="cb-close"><span class="cb-close-modal"><i class="fa fa-times"></i></span></div>
        </div>
        <div class="cb-lwa-profile">

             <div class="cb-avatar">
                    <?php echo get_avatar( $cb_author_id, $size = '150' );  ?>
            </div>

            <div class="cb-user-data clearfix">

                <div class="cb-block"><i class="fa fa-user"></i>
                    <a href="<?php echo get_edit_user_link($cb_author_id); ?>"><?php esc_html_e("Edit Profile", 'cubell'); ?></a>
                </div>

                <div class="cb-block"><i class="fa fa-sign-out"></i>
                    <a class="wp-logout" href="<?php echo wp_logout_url() ?>"><?php esc_html_e( 'Log Out' ,'login-with-ajax') ?></a>
                </div>

            </div>
        </div>


        <?php } ?>

    </div>
</div>