 <?php /* Slider 1 */

    $cb_cpt_output = cb_get_custom_post_types();

    $cb_module_type = 'cb-slider-a';
    $cb_slider_type = 'flexslider-1';

    if ( ( $cb_section == 'c') || ( $cb_section == 'a') ) {
          $cb_module_type = 'cb-slider-a cb-module-fw';
          $cb_slider_type = 'flexslider-1-fw';
    }

    $cb_qry = $cb_title_header = NULL;
    $cb_title_placeholder = 'cb-no-title ';
    $cb_count = 1;

    $j++;

    if ( is_rtl() ) {
        $cb_slider_ltr_rtl = ' style="direction:ltr;"';
    } else {
        $cb_slider_ltr_rtl = NULL;
    }

    $cb_qry = new WP_Query( array( 'posts_per_page' => '12', 'cat' => $cb_cat_id, 'tag__in' => $cb_tag_id, 'post__in' => $cb_post_ids, 'no_found_rows' => true, 'post_type' => $cb_cpt_output, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'offset' => $cb_offset, 'order' => $cb_order, 'orderby' => $cb_orderby ) );

    if( $cb_qry->have_posts() ) {

        while ($cb_qry->have_posts()) : $cb_qry->the_post();

        $cb_post_id = $post->ID;
        $cb_category_color = cb_get_cat_color($cb_post_id);

        if ($cb_title != NULL) {
                $cb_title_header = '<div class="cb-module-header" style="border-bottom-color:'. $cb_category_color.';"><h2 class="cb-module-title" >'.$cb_title.'</h2>'.$cb_subtitle.'</div>';
                $cb_title_placeholder = NULL;
        }

        if ( $cb_count == 1 ) {
             echo '<div class="'.$cb_module_type.' '.$cb_module_style.' '. $cb_title_placeholder .'clearfix"' . $cb_slider_ltr_rtl . '>'. $cb_title_header .'<div class="'.$cb_slider_type.' clearfix"><ul class="slides">';
        }

?>
        <li>

            <?php cb_thumbnail('282', '232'); ?>

            <div class="cb-meta">
                <h2><a href="<?php the_permalink(); ?>"><?php echo get_the_title(); ?></a></h2>
                <?php echo cb_byline(false, $cb_post_id, true); ?>
            </div>

            <?php echo cb_review_ext_box($cb_post_id, $cb_category_color); ?>

        </li>

<?php

        $cb_count++;
        endwhile;
        echo '</ul></div></div>';

    }

    wp_reset_postdata();
?>