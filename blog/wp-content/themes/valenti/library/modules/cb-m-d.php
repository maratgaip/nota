 <?php /* Module: D */

    $cb_cpt_output = cb_get_custom_post_types();
    $j++;
    $cb_module_type = 'cb-module-d';
		$cb_qry = $cb_title_header = NULL;
		$cb_qry = new WP_Query( array( 'posts_per_page' => $cb_amount, 'cat' => $cb_cat_id, 'tag__in' => $cb_tag_id, 'post__in' => $cb_post_ids, 'no_found_rows' => true, 'post_type' => $cb_cpt_output, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'offset' => $cb_offset, 'order' => $cb_order, 'orderby' => $cb_orderby ) );
    $cb_count = 1;

		if ( $cb_qry->have_posts() ) {

		  while ( $cb_qry->have_posts() ) : $cb_qry->the_post();

          $cb_post_id = $post->ID;
          $cb_category_color = cb_get_cat_color( $cb_post_id );

          if ( $cb_title != NULL ) {
             $cb_title_header = '<div class="cb-module-header" style="border-bottom-color:' . $cb_category_color . ';"><h2 class="cb-module-title" >' . $cb_title . '</h2>' . $cb_subtitle . '</div>';
          }

       if ( $cb_count == 1 ) {
             echo '<div class="' . $cb_module_type . ' ' . $cb_module_style . ' cb-module-half clearfix">' . $cb_title_header;
       }
?>
        <article class="cb-article clearfix" role="article">

            <div class="cb-meta">
                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                <?php echo cb_byline(true); ?>

                <div class="cb-excerpt"><?php echo cb_clean_excerpt(120); ?></div>
            </div>

        </article>

<?php
          $cb_count++;
		  endwhile;

	   echo '</div>';
	 }

	wp_reset_postdata();
?>