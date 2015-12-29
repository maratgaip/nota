<?php 
/**
 * Valenti Popular Posts
 */
if ( ! class_exists( 'CB_WP_Widget_Popular_Posts' ) ) {
    class CB_WP_Widget_Popular_Posts extends WP_Widget {
    
        function __construct() {
            $widget_ops = array('classname' => 'widget-popular-posts widget-latest-articles', 'description' => "Shows the most popular posts (Big/Small Styles)" );
            parent::__construct('cb-popular-posts', 'Valenti Popular Posts', $widget_ops);
            $this->alt_option_name = 'widget_popular_posts';
    
            add_action( 'save_post', array($this, 'flush_widget_cache') );
            add_action( 'deleted_post', array($this, 'flush_widget_cache') );
            add_action( 'switch_theme', array($this, 'flush_widget_cache') );
        }
    
        function widget($args, $instance) {
            $cache = wp_cache_get('widget_popular_posts', 'widget');
    
            if ( !is_array($cache) )
                $cache = array();
    
            if ( ! isset( $args['widget_id'] ) )
                $args['widget_id'] = $this->id;
    
            if ( isset( $cache[ $args['widget_id'] ] ) ) {
                echo $cache[ $args['widget_id'] ];
                return;
            }
    
            ob_start();
            extract($args);
    
            $cb_title = apply_filters('widget_title', empty($instance['cb_title']) ? __('Popular Posts', 'cubell') : $instance['cb_title'], $instance, $this->id_base);
            $cb_category = apply_filters('widget_category', empty($instance['category']) ? '' : $instance['category'], $instance, $this->id_base);
            $cb_type = apply_filters('widget_type', empty($instance['cb_type']) ? 'cb-small' : $instance['cb_type'], $instance, $this->id_base);
            if ( empty( $instance['cb_number'] ) || ! $cb_number = absint( $instance['cb_number'] ) )$cb_number = 5;
            if ($cb_category != 'cb-all') { $cb_cat_qry = $cb_category;} else {$cb_cat_qry = NULL;}

            $cb_qry = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $cb_number, 'no_found_rows' => true, 'category_name' => $cb_cat_qry, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'orderby' => 'comment_count' ) ) );
            if ($cb_qry->have_posts()) :
    
    echo $before_widget; 
                  
            if ( $cb_title ) echo $before_title . $cb_title . $after_title; ?>
            
            <ul class="cb-light <?php echo $cb_type; ?>">
            <?php while ( $cb_qry->have_posts() ) : $cb_qry->the_post(); 
            
                    global $post;
                    $cb_custom_fields = get_post_custom();
                    $cb_global_color = ot_get_option('cb_base_color', '#eb9812');
                    $cb_meta_onoff = ot_get_option('cb_meta_onoff', 'on');
                    $cb_review_checkbox = get_post_meta(get_the_id(), "cb_review_checkbox"); 
                    
                    if ( $cb_type == 'cb-small' ) {
                         $width = '80'; 
                         $height = '60'; 
                         $cb_small_box = true;
                         $cb_class = NULL;
                    }
                    
                    if ( $cb_type == 'cb-big' ) {
                         $width = '360'; 
                         $height = '240'; 
                         $cb_small_box = false;
                         $cb_class = ' class="h2"';
                    }
                    
                    $cb_all_categories = get_the_category();
                    $cb_current_cat_id = $cb_all_categories[0]->term_id;               
                    $cb_category_color = get_tax_meta($cb_current_cat_id, 'cb_color_field_id');
                    
                    if (($cb_category_color == "#") || ($cb_category_color == NULL)) {
                            $cb_parent_cat_id = $cb_all_categories[0]->parent;
                        
                            if ($cb_parent_cat_id != '0') {
                                $cb_category_color = get_tax_meta($cb_parent_cat_id, 'cb_color_field_id');
                            } 
                            
                            if (($cb_category_color == "#") || ($cb_category_color == NULL)) {
                                $cb_category_color = $cb_global_color; 
                            }
                    }
                    
                    $cb_post_id = $post->ID;
                    $cb_post_format_icon = cb_post_format_check($cb_post_id);
    
             ?>
                
                <li class="cb-article clearfix">
                    <div class="cb-mask" style="background-color:<?php echo $cb_category_color;?>;">
                        <?php
                                cb_thumbnail($width, $height);
                                echo cb_review_ext_box($cb_post_id, $cb_category_color, $cb_small_box);
                                echo $cb_post_format_icon; 
                         ?>
                    </div>
                    <div class="cb-meta">    
                        <h4<?php echo $cb_class; ?>><a href="<?php the_permalink();?>"><?php the_title(); ?></a></h4>
                        <?php echo cb_byline(false, $cb_post_id, true); ?>
                        <?php if ($cb_type == 'cb-big') { echo '<div class="cb-excerpt">'. cb_clean_excerpt(120).'</div>'; } ?>
                   </div> 
                </li>
            <?php endwhile; ?>
            </ul>
            <?php echo $after_widget; ?>
    <?php
            // Reset the global $the_post as this query will have stomped on it
            wp_reset_postdata();
    
            endif;
    
            $cache[$args['widget_id']] = ob_get_flush();
            wp_cache_set('widget_popular_posts', $cache, 'widget');
        }
    
        function update( $new_instance, $old_instance ) {
            $instance = $old_instance;
            $instance['cb_type'] =  strip_tags($new_instance['cb_type']);
            $instance['cb_title'] = strip_tags($new_instance['cb_title']);
            $instance['category'] = strip_tags($new_instance['category']);
            $instance['cb_number'] = (int) $new_instance['cb_number'];
            $this->flush_widget_cache();
    
            $alloptions = wp_cache_get( 'alloptions', 'options' );
            if ( isset($alloptions['widget_popular_posts']) )
                delete_option('widget_popular_posts');
    
            return $instance;
        }
    
        function flush_widget_cache() {
            wp_cache_delete('widget_popular_posts', 'widget');
        }
    
        function form( $instance ) {
            $cb_title     = isset( $instance['cb_title'] ) ? esc_attr( $instance['cb_title'] ) : '';
            $cb_category     = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
            $cb_number    = isset( $instance['cb_number'] ) ? absint( $instance['cb_number'] ) : 5;
            $cb_type    = isset( $instance['cb_type'] ) ? esc_attr( $instance['cb_type'] ) : '';
            $cb_categories = get_categories();
    ?>
            <p><label for="<?php echo $this->get_field_id( 'cb_title' ); ?>"><?php _e( 'Title:', 'cubell' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'cb_title' ); ?>" name="<?php echo $this->get_field_name( 'cb_title' ); ?>" type="text" value="<?php echo $cb_title; ?>" /></p>
    
            <p><label for="<?php echo $this->get_field_id( 'cb_number' ); ?>"><?php _e( 'Number of posts to show:', 'cubell' ); ?></label>
            <input id="<?php echo $this->get_field_id( 'cb_number' ); ?>" name="<?php echo $this->get_field_name( 'cb_number' ); ?>" type="text" value="<?php echo $cb_number; ?>" size="3" /></p>
        
            <p><label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php  echo "Category:"; ?></label>
            <select id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>">
            <option value="cb-all" <?php if ($cb_category == 'all') echo 'selected="selected"'; ?>>All Categories</option> 
            <?php 
                foreach ($cb_categories as $cb_cat) {

                    if ($cb_category == $cb_cat->slug) {$selected = 'selected="selected"'; } else { $selected = NULL;}
                    echo '<option value="' . $cb_cat->slug . '" ' . $selected . '>' . $cb_cat->name . ' (' . $cb_cat->count . ')</option>';
                    
                } 
            ?>
            </select></p>
            
            <p><label for="<?php echo $this->get_field_id( 'cb_type' ); ?>"><?php  echo "Style:"; ?></label>
                
             <select id="<?php echo $this->get_field_id( 'cb_type' ); ?>" name="<?php echo $this->get_field_name( 'cb_type' ); ?>">
               <option value="cb-small" <?php if ($cb_type == 'cb-small') echo 'selected="selected"'; ?>>Small</option> 
               <option value="cb-big" <?php if ($cb_type == 'cb-big') echo 'selected="selected"'; ?>>Big</option> 
                    
             </select></p>
           
            
    <?php
        }
    }
}

if ( ! function_exists( 'cb_popular_posts_loader' ) ) {
    function cb_popular_posts_loader () {
     register_widget( 'CB_WP_Widget_Popular_Posts' );
    }
     add_action( 'widgets_init', 'cb_popular_posts_loader' );
}
?>