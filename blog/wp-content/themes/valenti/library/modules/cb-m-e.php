 <?php /* Module: E */

    $cb_cpt_output = cb_get_custom_post_types();
	$cb_qry = $cb_title_header = NULL;
    $cb_wrap = 'cb-module-e';
	$cb_qry = new WP_Query( array( 'posts_per_page' => $cb_amount, 'cat' => $cb_cat_id, 'tag__in' => $cb_tag_id, 'post__in' => $cb_post_ids, 'no_found_rows' => true, 'post_type' => $cb_cpt_output, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'offset' => $cb_offset, 'order' => $cb_order, 'orderby' => $cb_orderby ) );
    $j++;
    $cb_count = 1;

    if ( $cb_qry->have_posts() ) {

        while ( $cb_qry->have_posts() ) : $cb_qry->the_post();

        $cb_post_id = $post->ID;
        $cb_post_format_icon = cb_post_format_check( $cb_post_id );
        $cb_category_color = cb_get_cat_color( $cb_post_id );

        if  ( $cb_title != NULL ) {
             $cb_title_header = '<div class="cb-module-header" style="border-bottom-color:' . $cb_category_color . ';"><h2 class="cb-module-title" >' . $cb_title . '</h2>' . $cb_subtitle . '</div>';
        }

         if ( $cb_count == 1 ) {
          echo '<div class="' . $cb_wrap . ' ' . $cb_module_style . ' clearfix">' . $cb_title_header;
        }
?>

        <article class="cb-blog-style-a clearfix <?php echo $cb_module_style . '-blog' ?>" role="article">

          <div class="cb-mask" style="background-color:<?php echo $cb_category_color; ?>;">

            <?php
                cb_thumbnail('300', '200');
                echo cb_review_ext_box( $cb_post_id, $cb_category_color );
                echo $cb_post_format_icon;
            ?>

          </div>

          <div class="cb-meta">

              <h2 class="h4"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
              <?php echo cb_byline(); ?>
              <div class="cb-excerpt"><?php echo cb_clean_excerpt( 210, false ); ?></div>

          </div>

        </article>
<?php
        $cb_count++;
        endwhile;
        echo '</div>';

    }
	wp_reset_postdata();
?>