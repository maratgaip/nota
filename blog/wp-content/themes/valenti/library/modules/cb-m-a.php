 <?php /* Module: A */

    $cb_cpt_output = cb_get_custom_post_types();

    if ( ( $cb_section == 'c' ) || ( $cb_section == 'a' ) ) {
            $cb_wrap = 'cb-module-a cb-module-fw';
            $cb_big_section = true;
    } else {
            $cb_wrap = 'cb-module-a';
            $cb_big_section = false;
    }

	$cb_qry = $cb_title_header = NULL;
	$cb_qry = new WP_Query( array( 'posts_per_page' => $cb_amount, 'cat' => $cb_cat_id, 'tag__in' => $cb_tag_id, 'post__in' => $cb_post_ids, 'no_found_rows' => true, 'post_type' => $cb_cpt_output, 'post_status' => 'publish', 'ignore_sticky_posts' => true, 'offset' => $cb_offset, 'order' => $cb_order, 'orderby' => $cb_orderby ) );
    $j++;
    $cb_count = 1;
    $cb_line_wrapper = 0;

    if ( $cb_qry->have_posts() ) {

        while ( $cb_qry->have_posts() ) : $cb_qry->the_post();

        $cb_post_id = $post->ID;
        $cb_post_format_icon = cb_post_format_check($cb_post_id);
        $cb_category_color = cb_get_cat_color($cb_post_id);

        if  ($cb_title != NULL ) {
             $cb_title_header = '<div class="cb-module-header" style="border-bottom-color:'. $cb_category_color.';"><h2 class="cb-module-title" >'.$cb_title.'</h2>'.$cb_subtitle.'</div>';
        }

        if ( $cb_count == 1 ) {  echo '<div class="'.$cb_wrap.' '.$cb_module_style.' clearfix">'.$cb_title_header;  }

        if ( $cb_big_section == true ) {

                        if ( $cb_count == 4 ) { $cb_count = 1; }

                        if ( $cb_line_wrapper  % 3 == 0 ) {
                                echo '<div class="cb-line clearfix">';
                                $cb_closer = true;
                        } elseif ( $cb_line_wrapper  % 3 == 2 ) {
                                $cb_closer = false;
                        } else {
                                $cb_closer = true;
                        }
        } else {
                      if ( $cb_count == 3 ) { $cb_count = 1; }

                      if ( $cb_line_wrapper  % 2 == 0 ) {
                          $cb_closer = true;
                            echo '<div class="cb-line clearfix">';
                      } else {
                          $cb_closer = false;
                      }

          }
?>
        <article class="cb-article <?php echo 'no-'.$cb_count; ?> clearfix" role="article">

            <div class="cb-mask" style="background:<?php echo $cb_category_color; ?>;">
                <?php
                        cb_thumbnail('360', '240');
                        echo cb_review_ext_box($cb_post_id, $cb_category_color);
                        echo $cb_post_format_icon;
                ?>
            </div>

            <div class="cb-meta">

                <h2 class="h4"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

                <?php echo cb_byline(); ?>

                <div class="cb-excerpt"><?php echo cb_clean_excerpt(120); ?></div>

            </div>

        </article>
<?php
        if ( $cb_big_section == true ) {

                if ( $cb_line_wrapper  % 3 == 2 ) {
                     echo '</div>';
                }
        } else {
                if ( $cb_line_wrapper  % 2 != 0 ) {
                     echo '</div>';
                }
        }

        $cb_count++;
        $cb_line_wrapper++;
        endwhile;

        if ( $cb_closer == true ) {
             echo '</div></div>';
        }

        if ( $cb_big_section == true ) {
            echo '</div>';
        } else {
            if ( $cb_closer == false) {
                 echo '</div>';
            }
        }
    }
	wp_reset_postdata();
?>