<?php
/**
 * Valenti Recent Replies For bbPress
 */

if ( ! class_exists( 'CB_Widget_bbp_recent_replies' ) ) {

    class CB_Widget_bbp_recent_replies extends WP_Widget {

        function __construct() {
            $widget_ops = array('classname' => 'widget-cb-bbp-recent-replies', 'description' => __( 'The latest bbPress replies with avatars' ) );
            parent::__construct('widget-cb-bbp-recent-replies', __('Valenti bbPress: Recent Replies'), $widget_ops);
            $this->alt_option_name = 'widget_cb_bbp_recent_replies';
        }

        function widget( $args, $instance ) {

            extract($args, EXTR_SKIP);

            $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Comments' );
            $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
            $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;

            if ( ! $number ) {
                $number = 5;
            }

            $cb_qry = new WP_Query( array( 'post_type' => bbp_get_reply_post_type(), 'post_status' => array( bbp_get_public_status_id(), bbp_get_closed_status_id() ), 'posts_per_page' => $number, 'ignore_sticky_posts' => true, 'no_found_rows' => true ) );

            echo $before_widget;

            echo $before_title . $title . $after_title;
?>
            <ul class="cb-bbp-recent-replies">

                <?php while ( $cb_qry->have_posts() ) : $cb_qry->the_post();  ?>

                    <li>

                        <?php

                        $cb_reply_id   = bbp_get_reply_id( $cb_qry->post->ID );
                        $cb_reply_url = '<a class="bbp-reply-topic-title" href="' . esc_url( bbp_get_reply_url( $cb_reply_id ) ) . '" title="' . esc_attr( bbp_get_reply_excerpt( $cb_reply_id, 50 ) ) . '">' . bbp_get_reply_topic_title( $cb_reply_id ) . '</a>';

                        $cb_author_avatar = bbp_get_reply_author_link( array( 'post_id' => $cb_reply_id, 'type' => 'avatar', 'size' => 60 ) );
                        $cb_author_name = bbp_get_reply_author_link( array( 'post_id' => $cb_reply_id, 'type' => 'name' ) );

                        echo $cb_author_avatar . '<div class="cb-bbp-meta">' . $cb_author_name . ' <i class="fa fa-long-arrow-right"></i> ' . $cb_reply_url  .'<div class="cb-bbp-recent-replies-time">' . bbp_get_time_since( get_the_time( 'U' ) ) . '</div></div>';

                        ?>

                    </li>

                <?php endwhile; ?>

            </ul>

        <?php echo $after_widget;

        // Reset the $post global
        wp_reset_postdata();
        }

        function update( $new_instance, $old_instance ) {
            $instance = $old_instance;
            $instance['title'] = strip_tags($new_instance['title']);
            $instance['number'] = absint( $new_instance['number'] );

            return $instance;
        }

        function form( $instance ) {
            $title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
            $number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
    ?>
            <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

            <p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of comments to show:' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
    <?php
        }
    }
}

if ( ! function_exists( 'cb_bbp_recent_replies' ) ) {
    function cb_bbp_recent_replies () {
        register_widget( 'CB_Widget_bbp_recent_replies' );
    }
    add_action( 'widgets_init', 'cb_bbp_recent_replies' );
}
?>