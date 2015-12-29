

	<div class="bl_posts">
		<h3 class="widget-head clearfix" style="border-bottom: none;">
			<div class="title-area large"><?php

				if(is_search()){ ?>
					<span><?php _e('Nothing matched the search string', 'bluth'); ?><small><?php the_search_query(); ?></small></span><?php
				}else{ ?>
					<span><?php _e('404 | Page/Post not found', 'bluth'); ?><small><?php _e('We could not find the page you were looking for', 'bluth'); ?></small></span><?php
				} ?>
			</div>
		</h3>
		<hr> 
		<div class="text-center widget_search">	
			<p class="lead"><?php _e('Try searching', 'bluth'); ?></p><?php
			get_search_form(); ?>
		</div>
		<hr> <?php

		   
    	echo blu_ajaxload_posts('grid', array('posts_per_page' => 6)); ?>
	</div>