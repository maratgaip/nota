 <?php /* Slider 2 */

    $cb_cpt_output = cb_get_custom_post_types();
    if ( is_category() ) {

        $cb_current_cat_id = get_query_var('cat');
        $cb_featured_qry = array( 'post_type' => $cb_cpt_output, 'meta_key' => 'cb_featured_cat_post', 'cat' => $cb_current_cat_id, 'posts_per_page' => 12, 'orderby' => 'date', 'order' => 'DESC',  'post_status' => 'publish', 'meta_value' => 'featured',  'meta_compare' => '==', 'ignore_sticky_posts' => true );

        $cb_qry = new WP_Query( $cb_featured_qry );

        if ( $cb_qry->post_count == 0 ) {
            $cb_qry = NULL;
            $cb_qry = new WP_Query(array( 'posts_per_page' => 12, 'no_found_rows' => true, 'post_type' => $cb_cpt_output, 'cat' => $cb_current_cat_id, 'post_status' => 'publish', 'ignore_sticky_posts' => true )  );
        }

    } else {
        $cb_qry = new WP_Query( array( 'posts_per_page' => '12', 'cat' => $cb_cat_id, 'tag__in' => $cb_tag_id, 'post__in' => $cb_post_ids, 'no_found_rows' => true, 'post_type' => $cb_cpt_output, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'offset' => $cb_offset, 'order' => $cb_order, 'orderby' => $cb_orderby ) );
    }

    if ( is_rtl() ) {
        $cb_slider_ltr_rtl = ' style="direction:ltr;"';
    } else {
        $cb_slider_ltr_rtl = NULL;
    }

    $cb_img_width = '750';
    $cb_img_height = '400';

    $cb_module_type = 'cb-slider-b';
    $cb_slider_type = 'flexslider-2';

    if ( ( $cb_section == 'c' ) || ( $cb_section == 'a' ) ) {
        $cb_slider_type = 'flexslider-2-fw';
        $cb_img_width = '1200';
        $cb_img_height = '520';
        $cb_module_type = 'cb-slider-b cb-module-fw';
    }

    $cb_title_header = NULL;
    $cb_count = 1;

    $j++;

    if( $cb_qry->have_posts() ) {

         while ($cb_qry->have_posts()) : $cb_qry->the_post();

         $cb_post_id = $post->ID;
         $cb_category_color = cb_get_cat_color($cb_post_id);

         if ( $cb_title != NULL ) {
             $cb_title_header = '<div class="cb-module-header" style="border-bottom-color:'. $cb_category_color.';"><h2 class="cb-module-title" >'.$cb_title.'</h2>'.$cb_subtitle.'</div>';
          }

         if ( $cb_count == 1 ) { echo '<div class="'.$cb_module_type.' '.$cb_module_style.' clearfix"' . $cb_slider_ltr_rtl . '>'.$cb_title_header.'<div class="'.$cb_slider_type.' clearfix"><ul class="slides">'; }
?>
        <li>

            <?php cb_thumbnail($cb_img_width, $cb_img_height); ?>
            <div class="cb-meta">

                    <h2><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                    <?php echo cb_byline(); ?>
            </div>

            <?php  if ($cb_slider_type == 'flexslider-2') {  echo cb_review_ext_box($cb_post_id, $cb_category_color); } ?>

        </li>

<?php
        $cb_count++;
        endwhile;
        echo '</ul></div></div>';
    }

    wp_reset_postdata();
?>