 <?php  /* Module: Slider Full */

    $cb_cpt_output = cb_get_custom_post_types();
    echo '<div class="flexslider-2-fw cb-featured clearfix"><ul class="slides">';

    if ( is_home() == true ) {

        $cb_featured_qry = array( 'post_type' => $cb_cpt_output, 'cat' => $cb_cat_id, 'meta_key' => 'cb_featured_post', 'posts_per_page' => '12', 'orderby' => 'date', 'order' => 'DESC',  'post_status' => 'publish', 'meta_value' => 'featured',  'meta_compare' => '==', 'ignore_sticky_posts' => true );
        $cb_qry = new WP_Query( $cb_featured_qry );

        if ( ( $cb_qry->post_count == 0 ) || ( $cb_qry->post_count == NULL ) ) {
            $cb_qry = NULL;
            $cb_qry = new WP_Query(array( 'posts_per_page' => '12', 'cat' => $cb_cat_id, 'no_found_rows' => true, 'post_type' => $cb_cpt_output, 'post_status' => 'publish', 'ignore_sticky_posts' => true )  );
        }

    } elseif ( is_category() ) {

        $cb_current_cat_id = get_query_var('cat');
        $cb_featured_qry = array( 'post_type' => $cb_cpt_output, 'meta_key' => 'cb_featured_cat_post', 'cat' => $cb_current_cat_id, 'posts_per_page' => '12', 'orderby' => 'date', 'order' => 'DESC',  'post_status' => 'publish', 'meta_value' => 'featured',  'meta_compare' => '==', 'ignore_sticky_posts' => true );

        $cb_qry = new WP_Query( $cb_featured_qry );

        if ( $cb_qry->post_count == 0 ) {
            $cb_qry = NULL;
            $cb_qry = new WP_Query(array( 'posts_per_page' => '12', 'no_found_rows' => true, 'post_type' => $cb_cpt_output, 'cat' => $cb_current_cat_id, 'post_status' => 'publish', 'ignore_sticky_posts' => true )  );
        }

    } else {
            $cb_qry = NULL;
            $cb_qry = new WP_Query( array( 'posts_per_page' => '12', 'cat' => $cb_cat_id, 'tag__in' => $cb_tag_id, 'post__in' => $cb_post_ids, 'no_found_rows' => true, 'post_type' => $cb_cpt_output, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'offset' => $cb_offset, 'order' => $cb_order, 'orderby' => $cb_orderby ) );
    }

	if ( $cb_qry->have_posts() ) : while ( $cb_qry->have_posts() ) : $cb_qry->the_post();
?>
 			 <li>

                        <?php cb_thumbnail( '1200', '520' ); ?>
                        <div class="cb-meta">
                            <h2><a href="<?php the_permalink() ?>"><?php echo get_the_title(); ?></a></h2>
                            <?php echo cb_byline(); ?>
                       </div>
                       <a href="<?php the_permalink() ?>" class="cb-link"></a>

            </li>
<?php
			endwhile;
		endif;
        echo '</ul></div>';

	wp_reset_postdata();
?>