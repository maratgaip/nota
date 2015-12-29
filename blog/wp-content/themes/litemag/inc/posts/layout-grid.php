<?php
    $post_meta_array = array('comments', 'author');

    if(isset($args['display_date']) and $args['display_date'] == 'false'){
        array_push( $post_meta_array, 'date' );
        
    }

	# get the post image
    # if it's a one column type then get a bigger image
    if( strpos( $args['css_class'] , 'lg-12' ) ){
        
        $post_image = blu_get_post_image( get_the_ID(), 'custom', false, false, array('width' => 900, 'height' => 430));
        
    }else{

        $post_image = blu_get_post_image( get_the_ID(), 'custom', false, false, array('width' => 400, 'height' => 190));

    }

	$output .= '<article data-categoryslug="' . (!empty($cat->slug) ? $cat->slug : '') . '" class="' . $css_class . '">';
    if(!empty($post_image['url'])){
    		$output .= '<div class="post-image">';
                if(get_post_format() != 'standard' and get_post_format() != null){
                    $output .=  '   <div class="post-icon"><i class="' . blu_get_post_icon(get_the_ID(), false) . '"></i></div>';
                }
                $output .=  '   <a class="post-image-link" href="' . get_the_permalink() . '"></a>';

    		$output .= 		blu_get_meta_info(get_the_ID(), $post_meta_array);
    		if(is_array($post_image)){
    			$output .= '    <img alt="" src="' . $post_image['url'] . '">';
    		}else{
    			$output .= '    <img alt="" src="' . $post_image . '">';
    		}
    		$output .= '</div>';
    }
    $output .= '		<div class="post-body box pad-xs-15 pad-sm-15 pad-md-20 pad-lg-20">';
    
    $output .= '            <h3 class="post-title"><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></h3>';
    if(get_post_meta( get_the_ID(), 'blu_post_subtitle', true )){
        $output .= '    		<p class="lead post-sub-title">' . get_post_meta( get_the_ID(), 'blu_post_subtitle', true ) . '</p>';
    }
    $output .= '    		<div class="post-content text-muted clearfix">' ;
  	
    if($args['display_excerpt'] === 'true'){

        ob_start('blu_keep_flush');
            the_excerpt(get_the_ID());
            $output .= ob_get_contents();
        ob_end_clean();
    }
   
    $output .= 				'</div>';
    
    /* check if we should display the footer or not */
    if($args['display_author'] != 'false' or get_comments_number(get_the_ID()) > 0){
        $output .= '            <div class="post-footer clearfix">';
        
        if(isset($args['display_author']) and $args['display_author'] != 'false'){

            $output .= '            <div class="post-author">';
            $output .= '                <img alt="" src="' . blu_get_avatar_url( get_avatar( get_the_author_meta( 'ID' ) , 'small' ), get_the_author_meta( 'ID' ) ) . '"><h4>' . blu_get_meta_info(get_the_ID(), array('categories', 'date', 'comments')) . '</h4>';
            $output .= '            </div>';

        }

        $output .= '				<a href="' . get_the_permalink() . '#comments" class="post-comments">';
        $output .= 					blu_get_meta_info(get_the_ID(), array('author', 'categories', 'date'));
        $output .= '				</a>';
        $output .= '			</div>';
    }

    $output .= '		</div>';
	$output .= '</article>';		