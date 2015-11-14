<?php
if(is_single()){ ?>
	<div class="single-pagination hidden-sm hidden-xs">
		<?php
			
		if(get_previous_post()){

	        if($post_image = blu_get_post_image(get_previous_post()->ID, 'custom', false, false, array('width' => 150, 'height' => 150)))
	        {
	        	if(isset( $post_image['url'])){

	            	$previous_post_image = '<div class="post-image"><img height="150" width="150" src="'.$post_image['url'].'"></div>';
	        	
	        	}
	        }
	    ?>							
		<span class="nav-previous<?php echo (isset($previous_post_image) ? ' nav-image' : '') ?>">
			<a rel="next" href="<?php echo get_permalink(get_previous_post()->ID) ?>" title="<?php echo __('Previous', 'bluth').':' . get_previous_post()->post_title ?>">
				<div class="arrow"><i class="fa fa-angle-left"></i></div>
				<div class="post-title">
					<?php echo isset($previous_post_image) ? $previous_post_image : ''; ?>
					<span class="post-title-span"><?php echo blu_truncate(get_previous_post()->post_title, 52, ' '); ?></span>
					<span class="visible-xs visible-sm"><?php _e('Previous', 'bluth'); ?></span>
				</div>
			</a>
		</span>
		<?php } ?>

		<?php	

		if(get_next_post()){

	        if($post_image = blu_get_post_image(get_next_post()->ID, 'custom', false, false, array('width' => 150, 'height' => 150)))
	        {
	        	if(isset( $post_image['url'])){

	            	$next_post_image = '<div class="post-image"><img height="150" width="150" src="'.$post_image['url'].'"></div>';

	        	}
	        }
	    ?>
		<span class="nav-next<?php echo (isset($next_post_image) ? ' nav-image' : '') ?>">
			<a rel="next" href="<?php echo get_permalink(get_next_post()->ID) ?>" title="<?php echo __('Next', 'bluth').':'. get_next_post()->post_title ?>">
				<div class="arrow"><i class="fa fa-angle-right"></i></div>
				<div class="post-title">
					<span class="post-title-span"><?php echo blu_truncate(get_next_post()->post_title, 52, ' '); ?></span>
					<span class="visible-xs visible-sm"><?php _e('Next', 'bluth'); ?></span>
					<?php echo isset($next_post_image) ? $next_post_image : ''; ?>
				</div>
			</a>							
		</span>
		<?php } ?>
	</div><?php
}