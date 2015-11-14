<?php
/**
 * Valenti Latest Comments With Avatar
 */

if ( ! class_exists( 'CB_widget_latest_comments_avatar' ) ) {

    class CB_widget_latest_comments_avatar extends WP_Widget {

        function __construct() {
            $widget_ops = array('classname' => 'widget-latest-comments-avatar', 'description' => __( 'The latest comments with avatars' ) );
            parent::__construct('latest-comments-avatar', __('Valenti Recent Comments With Avatar'), $widget_ops);
            $this->alt_option_name = 'widget_latest_comments_avatar';

            if ( is_active_widget(false, false, $this->id_base) )
                add_action( 'wp_head', array($this, 'recent_comments_style') );

            add_action( 'comment_post', array($this, 'flush_widget_cache') );
            add_action( 'transition_comment_status', array($this, 'flush_widget_cache') );
        }

        function recent_comments_style() {
            if ( ! current_theme_supports( 'widgets' ) // Temp hack #14876
                || ! apply_filters( 'show_recent_comments_widget_style', true, $this->id_base ) )
                return;
        }

        function flush_widget_cache() {
            wp_cache_delete('widget_latest_comments_avatar', 'widget');
        }

        function widget( $args, $instance ) {
            global $comments, $comment;

            $cache = wp_cache_get('widget_latest_comments_avatar', 'widget');

            if ( ! is_array( $cache ) )
                $cache = array();

            if ( ! isset( $args['widget_id'] ) )
                $args['widget_id'] = $this->id;

            if ( isset( $cache[ $args['widget_id'] ] ) ) {
                echo $cache[ $args['widget_id'] ];
                return;
            }

            extract($args, EXTR_SKIP);
            $output = '';

            $title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Recent Comments' );
            $title = apply_filters( 'widget_title', $title, $instance, $this->id_base );
            $number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
            if ( ! $number )
                $number = 5;

            $comments = get_comments( apply_filters( 'widget_comments_args', array( 'number' => $number, 'status' => 'approve', 'post_status' => 'publish' ) ) );
            $output .= $before_widget;
            if ( $title )
                $output .= $before_title . $title . $after_title;

            $output .= '<ul class="cb-recent-comments-avatar">';
            if ( $comments ) {
                // Prime cache for associated posts. (Prime post term cache if we need it for permalinks.)
                $post_ids = array_unique( wp_list_pluck( $comments, 'comment_post_ID' ) );
                _prime_post_caches( $post_ids, strpos( get_option( 'permalink_structure' ), '%category%' ), false );

                foreach ( (array) $comments as $comment) {

                    $output .=  '<li class="cb-comment-with-avatar"><div class="cb-avatar">'. get_avatar( $comment, 60 )  .'</div><div class="cb-comment">' .get_comment_author_link() . ' <i class="fa fa-long-arrow-right"></i> <a href="' . esc_url( get_comment_link($comment->comment_ID) ) . '">' . get_the_title($comment->comment_post_ID) . '</a></div></li>';

                }
            }
            $output .= '</ul>';
            $output .= $after_widget;

            echo $output;
            $cache[$args['widget_id']] = $output;
            wp_cache_set('widget_latest_comments_avatar', $cache, 'widget');
        }

        function update( $new_instance, $old_instance ) {
            $instance = $old_instance;
            $instance['title'] = strip_tags($new_instance['title']);
            $instance['number'] = absint( $new_instance['number'] );
            $this->flush_widget_cache();

            $alloptions = wp_cache_get( 'alloptions', 'options' );
            if ( isset($alloptions['widget_latest_comments_avatar']) )
                delete_option('widget_latest_comments_avatar');

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

if ( ! function_exists( 'cb_latest_comments_avatar' ) ) {
    function cb_latest_comments_avatar () {
        register_widget( 'CB_widget_latest_comments_avatar' );
    }
    add_action( 'widgets_init', 'cb_latest_comments_avatar' );
}
?>