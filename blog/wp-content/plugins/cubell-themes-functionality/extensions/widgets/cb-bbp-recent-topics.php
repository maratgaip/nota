<?php
/**
 * Valenti Recent Topics For bbPress
 */

if ( ! class_exists( 'CB_Widget_bbp_recent_topics' ) ) {

    class CB_Widget_bbp_recent_topics extends WP_Widget {

        function __construct() {
            $widget_ops = array('classname' => 'widget-cb-bbp-recent-topics', 'description' => __( 'The latest bbPress topics' ) );
            parent::__construct('widget-cb-bbp-recent-topics', __('Valenti bbPress: Recent Topics'), $widget_ops);
            $this->alt_option_name = 'widget_cb_bbp_recent_topics';
        }

        function widget( $args, $instance ) {

            extract($args, EXTR_SKIP);

            $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Topics' );
            $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
            $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
            $orderby = ( ! empty( $instance['number'] ) ) ? strip_tags( $instance['order_by'] ) : 'newness';

            if ( $orderby == 'newness') {
                $cb_meta_key = $cb_order_by = NULL;
            } elseif ( $orderby == 'popular' ) {
                $cb_meta_key =  '_bbp_reply_count';
                $cb_order_by = 'meta_value';
            } elseif ( $orderby == 'freshness') {
                $cb_meta_key =  '_bbp_last_active_time';
                $cb_order_by = 'meta_value';
            }

            if ( ! $number ) {
                $number = 5;
            }

            $cb_qry = new WP_Query( array( 'post_type' => bbp_get_topic_post_type(), 'post_status' => array( bbp_get_public_status_id(), bbp_get_closed_status_id() ), 'order'  => 'DESC', 'posts_per_page' => $number, 'ignore_sticky_posts' => true, 'no_found_rows' => true, 'meta_key' => $cb_meta_key, 'orderby' => $cb_order_by ) );

            echo $before_widget;

            echo $before_title . $title . $after_title;
?>
            <ul class="cb-bbp-recent-topics">

                <?php while ( $cb_qry->have_posts() ) : $cb_qry->the_post();  ?>

                    <li>

                        <?php

                        $cb_reply_id   = bbp_get_reply_id( $cb_qry->post->ID );
                        $cb_reply_url = '<a class="bbp-reply-topic-title" href="' . esc_url( bbp_get_reply_url( $cb_reply_id ) ) . '" title="' . esc_attr( bbp_get_reply_excerpt( $cb_reply_id, 50 ) ) . '">' . bbp_get_reply_topic_title( $cb_reply_id ) . '</a>';
                        $cb_number_replies = bbp_get_topic_reply_count($cb_reply_id);
                        $cb_author_avatar = bbp_get_reply_author_link( array( 'post_id' => $cb_reply_id, 'type' => 'avatar', 'size' => 60 ) );
                        $cb_author_name = bbp_get_reply_author_link( array( 'post_id' => $cb_reply_id, 'type' => 'name' ) );

                        echo $cb_author_avatar . '<div class="cb-bbp-meta">' . $cb_reply_url  .'<div class="cb-bbp-byline">' . __('Started by', 'cubell') . ' ' . $cb_author_name . ' <i class="fa fa-long-arrow-right"></i> ' . $cb_number_replies . ' replies</div></div>';

                        ?>

                    </li>

                <?php endwhile; ?>

            </ul>

<?php
            echo $after_widget;

            // Reset the $post global
            wp_reset_postdata();
        }

        function update( $new_instance, $old_instance ) {
            $instance = $old_instance;
            $instance['title'] = strip_tags($new_instance['title']);
            $instance['number'] = absint( $new_instance['number'] );
            $instance['order_by'] = strip_tags( $new_instance['order_by'] );

            return $instance;
        }

        function form( $instance ) {
            $title  = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
            $number = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
            $orderby = isset( $instance['order_by'] ) ? strip_tags( $instance['order_by'] ) : 'newness';

    ?>
            <p>
                <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of Topics to show:' ); ?></label>
                <input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" />
            </p>

            <p>
                <label for="<?php echo $this->get_field_id( 'order_by' ); ?>"><?php _e( 'Order By:',        'bbpress' ); ?></label>
                <select name="<?php echo $this->get_field_name( 'order_by' ); ?>" id="<?php echo $this->get_field_name( 'order_by' ); ?>">
                    <option <?php selected( $orderby, 'newness' );   ?> value="newness"><?php _e( 'Newest Topics',                'bbpress' ); ?></option>
                    <option <?php selected( $orderby, 'popular' );   ?> value="popular"><?php _e( 'Popular Topics',               'bbpress' ); ?></option>
                    <option <?php selected( $orderby, 'freshness' ); ?> value="freshness"><?php _e( 'Topics With Recent Replies', 'bbpress' ); ?></option>
                </select>
            </p>
    <?php
        }
    }
}

if ( ! function_exists( 'cb_bbp_recent_topics' ) ) {
    function cb_bbp_recent_topics () {
        register_widget( 'CB_Widget_bbp_recent_topics' );
    }
    add_action( 'widgets_init', 'cb_bbp_recent_topics' );
}
?>