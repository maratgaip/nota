<?php
$featured_image_size = 'original';
$max_height = '';
// echo the sticky icon if it's a sticky
if(is_sticky() && (is_home() || is_front_page())){ ?> <a href="#" class="sticky-icon" title="<?php _e('Sticky Post', 'bluth'); ?>"><i class="<?php echo bl_utilities::get_option('sticky_post_icon'); ?>"></i></a> <?php }
// if the conditions are right then disable cropping
if((is_single() && in_array(bl_utilities::get_option('enable_cropping_on'), array('all','only_posts','only_posts_front','only_posts_pages'))) or (is_page() && in_array(bl_utilities::get_option('enable_cropping_on'), array('all','only_pages','only_pages_front','only_posts_pages'))) or (is_home() or is_front_page()) && in_array(bl_utilities::get_option('enable_cropping_on'), array('all','only_front','only_posts_front','only_pages_front'))){
	$featured_image_size = 'single-large';	
}else{
	$max_height = 'max-height: none;';
}

// if it's a page and it has a featured image, then display it.
if(is_page() && has_post_thumbnail() && get_post_format() == ''){ 
	// don't show it though if the user has checked the "hide original featured image"
	if(get_post_meta( get_the_ID(), 'blu_hide_page_featured', true ) != 'on'){ ?>
		<div class="entry-image" style="<?php echo $max_height; ?>">
			<?php the_post_thumbnail( $featured_image_size ); ?>
		</div><?php
	}
} 
elseif(!is_page()) {  
	
	#
	#	VIDEO
	#
	if(get_post_format() == 'video'){ 
		if($video_iframe = get_post_meta( $post->ID, 'blu_video_iframe', true ) or $video_url = get_post_meta( $post->ID, 'blu_video_url', true )){ ?>
			<div class="entry-video embed-responsive embed-responsive-16by9"><?php
				if($video_iframe){
					// ready the video iframe
					$video_iframe = html_entity_decode($video_iframe);
					if($video_iframe != strip_tags($video_iframe)){
						$content = preg_replace('#\<iframe(.*?)\ssrc\=\"(.*?)\"(.*?)\>#i', '<iframe$1 src="$2?wmode=opaque"$3>', $video_iframe);
						$content = preg_replace('#\<iframe(.*?)\ssrc\=\"(.*?)\?(.*?)\?(.*?)\"(.*?)\>#i', '<iframe$1 src="$2?$3&$4"$5>', $video_iframe);
					}else{
						$content = $video_iframe;
					}
					echo $content;
				
				}else if($video_url){

					global $wp_embed;
					$video_url = html_entity_decode($video_url);
					$post_icon = bl_utilities::get_option('video_icon', 'fa fa-youtube-play');
					
					// ready the video url
					if($video_url != strip_tags($video_url)){
						$content = preg_replace('#\<iframe(.*?)\ssrc\=\"(.*?)\"(.*?)\>#i', '<iframe$1 src="$2?wmode=opaque"$3>', $content);
						$content = preg_replace('#\<iframe(.*?)\ssrc\=\"(.*?)\?(.*?)\?(.*?)\"(.*?)\>#i', '<iframe$1 src="$2?$3&$4"$5>', $content);
					}else{ 
						$content = $wp_embed->run_shortcode('[embed]' . $video_url . '[/embed]' ); 
					}
					
					echo $content;
				} ?>
			</div><?php	
		}

	#
	#	AUDIO
	#
	}elseif(get_post_format() == 'audio'){ 
		if(has_post_thumbnail() and get_post_meta( get_the_ID(), 'blu_hide_featured', true ) != 'on'){ ?>
			<div class="entry-image mb30" style="<?php echo $max_height; ?>"><?php
				$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'original', false, '' ); 
				
				// if it's a single page then don't make the image a link
				if(is_single()){ 

					// don't show the image if the user has checked "Hide Original Featured Image"
					if(get_post_meta( get_the_ID(), 'blu_hide_featured', true ) != 'on'){ 
						if(get_post_meta($post->ID, 'blu_image_author', true)){
							$image_owner_url = '#';
							if(get_post_meta($post->ID, 'blu_image_author_url', true)){ $image_owner_url = get_post_meta($post->ID, 'blu_image_author_url', true); }
							echo '<a class="image-owner" href="' . $image_owner_url . '">' . get_post_meta($post->ID, 'blu_image_author', true) . '</a>';
						} ?>
						<div rel="bookmark" data-href="<?php echo $src[0]; ?>">
							<?php the_post_thumbnail( $featured_image_size ); ?>
						</div><?php
					}

				}else{ ?>
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark">
						<?php the_post_thumbnail( $featured_image_size ); ?>
					</a><?php
				} ?>
			</div><?php
		} ?>
		<div class="entry-audio"><?php
			$audio_url = get_post_meta( $post->ID, 'blu_audio_url', true );
			$audio_embed = get_post_meta( $post->ID, 'blu_audio_iframe', true );
			if(strpos($audio_url,'.mp3') !== false){ echo '<div class="shortcode-audio">' . do_shortcode('[audio mp3="'.$audio_url.'"][/audio]') . '</div>'; }
			else{ echo '<div class="shortcode-audio">' . apply_filters( 'the_content', $audio_url) . '</div>'; }
			echo html_entity_decode($audio_embed); ?>

		</div><?php

	#
	#	GALLERY
	#
	}elseif(get_post_format() == 'gallery' and $images = get_post_meta( $post->ID, 'blu_gallery', true )){  ?>
		<div class="swiper-container swiper-gallery">
	    	<a class="arrow-left" href="#"></a>
			<a class="arrow-right" href="#"></a>
    		<div class="swiper-pagination"></div>
			<div class="swiper-wrapper"><?php 
				foreach( $images as $image ){ 
					$image_info = blu_get_attachment($image['gallery_id']);
					?> 
					<div class="swiper-slide swiper-slide-large" style="width:763px;"> 
						<div class="entry-head" data-original="<?php echo $image['gallery_src']; ?>"> <?php 
							if(is_single()){ $featured_image_size = 'original'; }
							$gallery_image = blu_get_post_image( get_the_ID(), $featured_image_size, false, $image['gallery_id']); 
							if(!empty($image_info['caption'])){ ?>
								<a class="image-owner"><?php echo $image_info['caption']; ?></a><?php
							} ?>
							<img class="img-responsive" src="<?php echo $gallery_image[0]; ?>" alt="<?php echo $image_info['alt']; ?>" title="<?php echo $image_info['title']; ?>">
						</div>
					</div><?php 
				} ?>
			</div> <!-- swiper-wrapper -->
		</div> <!-- swiper-container --> <?php

	#
	#	QUOTE
	#
	}elseif(get_post_format() == 'quote'){ ?>
		<div class="entry-image" style="<?php echo $max_height; ?>"><?php
			if(get_post_meta($post->ID, 'blu_image_author', true)){
				$image_owner_url = '#';
				if(get_post_meta($post->ID, 'blu_image_author_url', true)){ $image_owner_url = get_post_meta($post->ID, 'blu_image_author_url', true); }
				echo '<a class="image-owner" href="' . $image_owner_url . '">' . get_post_meta($post->ID, 'blu_image_author', true) . '</a>';
			}
			$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large', false, '' ); 
			$quote_text = get_post_meta( $post->ID, 'blu_quote_text', true );
			$quote_author = get_post_meta( $post->ID, 'blu_quote_author', true );
			$quote_url = get_post_meta( $post->ID, 'blu_quote_src', true );

			if(!is_single()){ ?>
			<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark"><?php
			} ?>
			
				<div class="quote-area">
					<h1 class="quote-text"><?php echo $quote_text; ?></h1>
					<div class="quote-author"><a href="<?php echo $quote_url; ?>"><?php echo $quote_author; ?></a></div>
				</div>
				<?php the_post_thumbnail( $featured_image_size ); 
			if(!is_single()){ ?>
			</a><?php
			} ?>
		</div><?php

	#
	#	STATUS
	#
	}elseif(get_post_format() == 'status'){
		if(	get_post_meta( get_the_ID(), 'blu_hide_featured', true ) != 'on'){
			$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large', false, '' ); 
		}else{
			$src[0] = '';
		}
		if(empty($src)){ $status_class = ''; }else{ $status_class = ' background '; }

		// if it's a facebook status
		if( get_post_meta( $post->ID, 'bluth_facebook_status', true ) ){
			$status_class .= 'bl_facebook';
			$post_icon = bl_utilities::get_option('facebook_status_icon', 'fa fa-facebook');
			$post_icon_color = ' color: #3B5998; opacity: 1;';
		}
		else if( get_post_meta( $post->ID, 'bluth_twitter_status', true ) ){
			$status_class .= 'bl_twitter';
			$post_icon = bl_utilities::get_option('twitter_status_icon', 'fa fa-twitter');
			$post_icon_color = ' color: #1DCAFF; opacity: 1;';
		}
		else if( get_post_meta( $post->ID, 'bluth_google_status', true ) ){
			$status_class .= 'bl_google';
			$post_icon = bl_utilities::get_option('google_status_icon', 'fa fa-google-plus');
			$post_icon_color = ' color: #CD3C2A; opacity: 1;';
		} ?>

		<div class="entry-image <?php echo $status_class; ?>" style="<?php echo $max_height; ?> background-image:url('<?php echo $src[0]; ?>'); background-size: cover;"> <?php 
			if( get_post_meta( $post->ID, 'bluth_facebook_status', true ) ){
				if(is_single()){
					echo str_replace( '&#039;', '\'', html_entity_decode( get_post_meta( $post->ID, 'bluth_facebook_status', true ) ) );
				}else{
					$facebook_store = str_replace( '&#039;', '\'', html_entity_decode( get_post_meta( $post->ID, 'bluth_facebook_status', true ) ) );
					$facebook_store = htmlentities($facebook_store);
					echo '<div class="facebook-store" data-code="' . $facebook_store . '"></div>';
				}
			}
			else if( get_post_meta( $post->ID, 'bluth_twitter_status', true ) ){
				if(is_single()){
					echo html_entity_decode( get_post_meta( $post->ID, 'bluth_twitter_status', true ) );
				}else{
					$twitter_store = html_entity_decode( get_post_meta( $post->ID, 'bluth_twitter_status', true ) );
					$twitter_store = htmlentities($twitter_store);
					echo '<div class="twitter-store" data-code="' . $twitter_store . '"></div>';
				}
			}
			else if( get_post_meta( $post->ID, 'bluth_google_status', true ) ){
				if(is_single()){
					echo html_entity_decode( get_post_meta( $post->ID, 'bluth_google_status', true ) );
				}else{
					$google_store = html_entity_decode( get_post_meta( $post->ID, 'bluth_google_status', true ) );
					$google_store = htmlentities($google_store);
					echo '<div class="google-store" data-code="' . $google_store . '"></div>';
				}
			}?>
		</div><?php

	#
	#	LINK
	#
	}elseif(get_post_format() == 'link'){ 
		$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large', false, '' );
		if(empty($src)){ $link_class = ''; }else{ $link_class = 'background'; }
		$post_icon = bl_utilities::get_option('link_icon', 'fa fa-link');
		$link_url = html_entity_decode( get_post_meta($post->ID, 'blu_link_url', true ) ); ?>

		<div class="entry-image <?php echo $link_class; ?>" style="<?php echo $max_height; ?> background-image:url('<?php echo $src[0]; ?>'); background-size: cover;">
			<a href="<?php echo $link_url; ?>" title="<?php the_title(); ?>" rel="bookmark" target="_blank">
				<?php echo '<i class="'. $post_icon . '"></i> '; the_title(); ?>
			</a>
		</div><?php

	#
	#	STANDARD
	#
	}else{  ?>
		<div class="entry-image" style="<?php echo $max_height; ?>"><?php
			$src = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'original', false, '' ); 
			
			// if it's a single page then don't make the image a link
			if(is_single()){ // don't show the image if the user has checked "Hide Original Featured Image"
				if(get_post_meta( get_the_ID(), 'blu_hide_featured', true ) != 'on'){
					if(get_post_meta($post->ID, 'blu_image_author', true)){
						$image_owner_url = '#';
						if(get_post_meta($post->ID, 'blu_image_author_url', true)){ $image_owner_url = get_post_meta($post->ID, 'blu_image_author_url', true); }
						echo '<a class="image-owner" href="' . $image_owner_url . '">' . get_post_meta($post->ID, 'blu_image_author', true) . '</a>';
					} ?>
					<a class="lightbox" href="<?php echo $src[0]; ?>" rel="bookmark">
						<?php the_post_thumbnail( $featured_image_size ); ?>
					</a><?php
				}

			}else{ ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark">
					<?php the_post_thumbnail( $featured_image_size ); ?>
				</a><?php
			} ?>
		</div><?php
	}
}
 ?>