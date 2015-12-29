<?php
if ( ! class_exists( 'Jetpack_Media_Summary' ) && defined('IS_WPCOM') && IS_WPCOM )
	include WP_CONTENT_DIR . '/lib/class.wpcom-media-summary.php';

/**
 * Better OG Image Tags for Image Post Formats
 */
function enhanced_og_image( $tags ) {
	if ( !is_singular() || post_password_required() )
		return $tags;

	global $post;

	// Always favor featured images.
	if ( enhanced_og_has_featured_image( $post->ID ) )
		return $tags;

	$summary = Jetpack_Media_Summary::get( $post->ID );

	if ( 'image' != $summary['type'] )
		return $tags;

	$tags['og:image'] = $summary['image'];
	$tags['og:image:secure_url'] = $summary['secure']['image'];

	return $tags;
}
add_filter( 'jetpack_open_graph_tags', 'enhanced_og_image' );

/**
 * Better OG Image Tags for Gallery Post Formats
 */
function enhanced_og_gallery( $tags ) {
	if ( !is_singular() || post_password_required() )
		return $tags;

	global $post;

	// Always favor featured images.
	if ( enhanced_og_has_featured_image( $post->ID ) )
		return $tags;

	$summary = Jetpack_Media_Summary::get( $post->ID );

	if ( 'gallery' != $summary['type'] )
		return $tags;

	if( !isset( $summary['images'] ) || !is_array( $summary['images'] ) || empty( $summary['images'] ) )
		return $tags;

	$images = $secures = array();
	foreach ( $summary['images'] as $i => $image ) {
		$images[] = $image['url'];
		$secures[] = $summary['secure']['images'][$i]['url'];
	}

	$tags['og:image'] = $images;
	$tags['og:image:secure_url'] = $secures;

	return $tags;
}
add_filter( 'jetpack_open_graph_tags', 'enhanced_og_gallery' );

/**
 * Allows VideoPress, YouTube, and Vimeo videos to play inline on Facebook
 */
function enhanced_og_video( $tags ) {
	if ( !is_singular() || post_password_required() )
		return $tags;

	global $post;

	// Always favor featured images.
	if ( enhanced_og_has_featured_image( $post->ID ) )
		return $tags;

	$summary = Jetpack_Media_Summary::get( $post->ID );

	if ( 'video' != $summary['type'] ) {
		if ( $summary['count']['video'] > 0 && $summary['count']['image'] < 1 ) {
			$tags['og:image']            = $summary['image'];
			$tags['og:image:secure_url'] = $summary['secure']['image'];
		}
		return $tags;
	}

	$tags['og:image']            = $summary['image'];
	$tags['og:image:secure_url'] = $summary['secure']['image'];
	$tags['og:video:type']       = 'application/x-shockwave-flash';

	$video_url        = $summary['video'];
	$secure_video_url = $summary['secure']['video'];

	if ( preg_match( '/((youtube|vimeo)\.com|youtu.be)/', $video_url ) ) {
		if ( strstr( $video_url, 'youtube' ) ) {
			$id = jetpack_get_youtube_id( $video_url );
			$video_url = 'http://www.youtube.com/v/' . $id . '?version=3&autohide=1';
			$secure_video_u