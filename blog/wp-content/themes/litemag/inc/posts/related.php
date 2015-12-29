<?php
$orig_post = $post;
global $post;
$tags = wp_get_post_tags($post->ID);

if($tags){
	$tag_ids = array();
	foreach($tags as $individual_tag) $tag_ids[] = $individual_tag->term_id;

	$args = array(
	    'tag__in' => $tag_ids,
	    'post__not_in' => array($post->ID),
	    'posts_per_page'=> 4 ,
    );

    ?>
    	<div class="bl_posts col-md-12 col-lg-12 related_posts">
    		<?php echo blu_ajaxload_posts('grid', $args, 'col-sm-6 col-md-6 col-lg-6'); ?>
    	</div>
    <?php
}