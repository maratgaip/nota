<?php
/**
 * Valenti Top Reviews Widget
 */

if ( ! class_exists( 'CB_WP_Widget_top_reviews' ) ) {
    class CB_WP_Widget_top_reviews extends WP_Widget {

    	function __construct() {
    		$widget_ops = array('classname' => 'cb-top-reviews-widget', 'description' =>  "Show top reviews with different filters" );
    		parent::__construct('top-reviews', 'Valenti Top Reviews', $widget_ops);
    		$this->alt_option_name = 'widget_top_reviews';

    		add_action( 'save_post', array($this, 'flush_widget_cache') );
    		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
    		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
    	}

    	function widget($args, $instance) {

    		$cache = wp_cache_get('widget_top_reviews', 'widget');

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

    		$cb_title = apply_filters('widget_title', empty($instance['title']) ? __('Top Reviews', 'cubell') : $instance['title'], $instance, $this->id_base);
    		$cb_category = apply_filters('widget_category', empty($instance['category']) ? '' : $instance['category'], $instance, $this->id_base);
            $cb_filter = apply_filters('widget_filter', empty($instance['filter']) ? '' : $instance['filter'], $instance, $this->id_base);
            $cb_sortby = apply_filters('widget_filter', empty($instance['sortby']) ? '' : $instance['sortby'], $instance, $this->id_base);
            $cb_type = apply_filters('widget_type', empty($instance['type']) ? '' : $instance['type'], $instance, $this->id_base);

    		if ( $cb_filter == NULL ) {
                $cb_filter = 'alltime';
            }

    		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )  {$number = 5; }

            if ( $cb_category != 'cb-all' ) {
                $cb_cat_qry = $cb_category;
            } else {
                $cb_cat_qry = NULL;
            }

            if ( $cb_type == 'cb-reader-score' ) {
                $cb_type_filter = 'cb_user_score_output';
            } else {
                $cb_type_filter = 'cb_final_score';
            }

            if ( $cb_sortby == 'topscores' ) {
                $cb_sortby_orderby = 'meta_value_num';
                $cb_sortby_order = 'DESC';
            } elseif ( $cb_sortby == 'latestscores' ) {
                $cb_sortby_order = 'DESC';
                $cb_sortby_orderby = 'date';

            } elseif ( $cb_sortby == 'lowestscores' ) {
                $cb_sortby_orderby = 'meta_value_num';
                $cb_sortby_order = 'ASC';
            }

    		if ( $cb_filter == 'week' ) {

        		$cb_week = date('W');
        		$cb_year = date('Y');
        		$cb_qry = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $number,'category_name' => $cb_cat_qry, 'year' => $cb_year, 'w' => $cb_week , 'no_found_rows' => true, 'post_status' => 'publish', 'meta_key' => $cb_type_filter, 'orderby' => $cb_sortby_orderby, 'order' => $cb_sortby_order, 'ignore_sticky_posts' => true ) ) );

        	} elseif ( $cb_filter == 'alltime' ) {

    		  $cb_qry = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $number,'category_name' => $cb_cat_qry, 'no_found_rows' => true, 'post_status' => 'publish', 'meta_key' => $cb_type_filter, 'orderby' => $cb_sortby_orderby, 'order' => $cb_sortby_order, 'ignore_sticky_posts' => true ) ) );

    		} elseif ( $cb_filter == 'month' ) {

    		  $cb_month = date('m', strtotime('-30 days'));
    		  $cb_year = date('Y', strtotime('-30 days'));
    		  $cb_qry = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $number,'category_name' => $cb_cat_qry, 'year' => $cb_year, 'monthnum' => $cb_month ,  'no_found_rows' => true, 'post_status' => 'publish', 'meta_key' => $cb_type_filter, 'orderby' => $cb_sortby_order, 'order' => $cb_sortby_orderby, 'ignore_sticky_posts' => true ) ) );

    		} elseif ( ( $cb_filter == '2011' ) || ( $cb_filter == '2012' ) || ( $cb_filter == '2013' ) || ( $cb_filter == '2014' ) ) {

                $cb_qry = new WP_Query( apply_filters( 'widget_posts_args', array( 'posts_per_page' => $number,'category_name' => $cb_cat_qry, 'year' => $cb_filter, 'no_found_rows' => true, 'post_status' => 'publish', 'meta_key' => $cb_type_filter, 'orderby' => $cb_sortby_orderby, 'order' => $cb_sortby_order, 'ignore_sticky_posts' => true ) ) );
    		}

    		if ( $cb_qry->have_posts() ) :
                echo $before_widget;

                if ( $cb_title != NULL ) {
                    echo $before_title . $cb_title . $after_title;
                }
        		echo '<ul>';

                $i = 1;

                while ( $cb_qry->have_posts() ) : $cb_qry->the_post();
                        global $post;
                        $cb_meta_onoff = ot_get_option('cb_meta_onoff', 'on');
                        $cb_global_color = ot_get_option('cb_base_color', '#eb9812');
                        $cb_cat_id = get_the_category();
                        $cb_current_cat_id = $cb_cat_id[0]->term_id;
                        $cb_category_color = get_tax_meta($cb_current_cat_id, 'cb_color_field_id');

                        if ( ( $cb_category_color == "#" ) || ( $cb_category_color == NULL ) ) {
                            $cb_parent_cat_id = $cb_cat_id[0]->parent;

                            if ($cb_parent_cat_id != '0') {
                                $cb_category_color = get_tax_meta($cb_parent_cat_id, 'cb_color_field_id');
                            }

                            if (($cb_category_color == "#") || ($cb_category_color == NULL)) {
                                $cb_category_color = $cb_global_color;
                            }
                        }

                        $cb_post_id = $post->ID;
?>
                    <li class="cb-article clearfix"  style="border-top-color:<?php echo $cb_category_color; ?>;">

                       <div class="cb-mask"><?php cb_thumbnail('430', '270'); ?></div>

                        <div class="cb-meta">
                                 <h4><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></h4>
                                 <?php echo cb_byline(false); ?>
                        </div>

                        <div class="cb-countdown header-font"><?php echo $i; ?></div>

                        <?php
                            if ( $cb_type != 'cb-reader-score' ) {
                                echo cb_review_ext_box($cb_post_id, $cb_category_color);
                            }
                        ?>

                    </li>

<?php
                $i++;
                endwhile; echo '</ul>';
    		echo $after_widget;

    		// Reset the global $the_post as this query will have stomped on it
    		wp_reset_postdata();

    		endif;

    		$cache[$args['widget_id']] = ob_get_flush();
    		wp_cache_set('widget_top_reviews', $cache, 'widget');
    	}

    	function update( $new_instance, $old_instance ) {
    		$instance = $old_instance;
    		$instance['title'] = strip_tags($new_instance['title']);
    		$instance['category'] = strip_tags($new_instance['category']);
    		$instance['number'] = (int) $new_instance['number'];
            $instance['filter'] =  strip_tags($new_instance['filter']);
            $instance['sortby'] =  strip_tags($new_instance['sortby']);
            $instance['type'] =  strip_tags($new_instance['type']);
    		$this->flush_widget_cache();

    		$alloptions = wp_cache_get( 'alloptions', 'options' );
    		if ( isset($alloptions['widget_top_reviews']) )
    			delete_option('widget_top_reviews');

    		return $instance;
    	}

    	function flush_widget_cache() {
    		wp_cache_delete('widget_top_reviews', 'widget');
    	}

    	function form( $instance ) {
    		$cb_title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
    		$cb_category     = isset( $instance['category'] ) ? esc_attr( $instance['category'] ) : '';
    		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 4;
            $cb_filter    = isset( $instance['filter'] ) ? esc_attr( $instance['filter'] ) : '';
            $cb_sortby    = isset( $instance['sortby'] ) ? esc_attr( $instance['sortby'] ) : '';
            $cb_type    = isset( $instance['type'] ) ? esc_attr( $instance['type'] ) : '';
    		$cb_cats = get_categories();
    ?>

    		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'cubell' ); ?></label>
    		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $cb_title; ?>" /></p>

    		<p><label for="<?php echo $this->get_field_id( 'category' ); ?>"><?php  echo "Category:"; ?></label>
            <select id="<?php echo $this->get_field_id( 'category' ); ?>" name="<?php echo $this->get_field_name( 'category' ); ?>">
            <option value="cb-all" <?php if ($cb_category == 'all') echo 'selected="selected"'; ?>>All Categories</option>
            <?php foreach ($cb_cats as $cat) {
                    if ($cb_category == $cat->slug) {$selected = 'selected="selected"'; } else { $selected = NULL;}
                    echo '<option value="'.$cat->slug.'" '.$selected.'>'.$cat->name.' ('.$cat->count.')</option>';

              } ?>
            </select></p>

    		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of reviews to show:', 'cubell' ); ?></label>
    		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

    		<p>
                <label for="<?php echo $this->get_field_id( 'filter' ); ?>"><?php  echo "Filter:"; ?></label>
                <select id="<?php echo $this->get_field_id( 'filter' ); ?>" name="<?php echo $this->get_field_name( 'filter' ); ?>">
                    <option value="alltime" <?php if ( $cb_filter == 'alltime' ) { echo 'selected="selected"'; } ?>>All-time</option>
                    <option value="month" <?php if ( $cb_filter == 'month' ) { echo 'selected="selected"'; } ?>>Last Month</option>
                    <option value="week" <?php if ( $cb_filter == 'week' ) { echo 'selected="selected"'; } ?>>Past 7 Days</option>
                    <option value="2014" <?php if ( $cb_filter == '2014' ) { echo 'selected="selected"'; } ?>>Only 2014</option>
                    <option value="2013" <?php if ( $cb_filter == '2013' ) { echo 'selected="selected"'; } ?>>Only 2013</option>
                    <option value="2012" <?php if ( $cb_filter == '2012' ) { echo 'selected="selected"'; } ?>>Only 2012</option>
                    <option value="2011" <?php if ( $cb_filter == '2012' ) { echo 'selected="selected"'; } ?>>Only 2011</option>
                </select>
            </p>

            <p>
                <label for="<?php echo $this->get_field_id( 'sortby' ); ?>"><?php  echo "Sort by:"; ?></label>
                <select id="<?php echo $this->get_field_id( 'sortby' ); ?>" name="<?php echo $this->get_field_name( 'sortby' ); ?>">
                    <option value="topscores" <?php if ( $cb_sortby == 'topscores' ) { echo 'selected="selected"'; } ?>>Top Scores</option>
                    <option value="latestscores" <?php if ( $cb_sortby == 'latestscores' ) { echo 'selected="selected"'; } ?>>Newest Reviews</option>
                    <option value="lowestscores" <?php if ( $cb_sortby == 'lowestscores' ) { echo 'selected="selected"'; } ?>>Lowest Scores</option>
                </select>
            </p>


            <p>
                <label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php  echo "Review Type:"; ?></label>
                <select id="<?php echo $this->get_field_id( 'type' ); ?>" name="<?php echo $this->get_field_name( 'type' ); ?>">
                    <option value="cb-editor-score" <?php if ( $cb_type == 'cb-editor-score' ) { echo 'selected="selected"'; } ?>>Editor Score</option>
                    <option value="cb-reader-score" <?php if ( $cb_type == 'cb-reader-score' ) { echo 'selected="selected"'; } ?>>Reader Scores</option>
                </select>
            </p>
    <?php
    	}
    }
}
if ( ! function_exists( 'cb_top_reviews_loader' ) ) {
    function cb_top_reviews_loader (){
     register_widget( 'CB_WP_Widget_top_reviews' );
    }
     add_action( 'widgets_init', 'cb_top_reviews_loader' );
}
?>