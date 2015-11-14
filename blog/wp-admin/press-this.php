<?php
/**
 * Press This Display and Handler.
 *
 * @package WordPress
 * @subpackage Press_This
 */

define('IFRAME_REQUEST' , true);

/** WordPress Administration Bootstrap */
require_once( dirname( __FILE__ ) . '/admin.php' );

header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));

if ( ! current_user_can( 'edit_posts' ) || ! current_user_can( get_post_type_object( 'post' )->cap->create_posts ) )
	wp_die( __( 'Cheatin&#8217; uh?' ) );

/**
 * Press It form handler.
 *
 * @since 2.6.0
 *
 * @return int Post ID
 */
function press_it() {

	$post = get_default_post_to_edit();
	$post = get_object_vars($post);
	$post_ID = $post['ID'] = (int) $_POST['post_id'];

	if ( !current_user_can('edit_post', $post_ID) )
		wp_die(__('You are not allowed to edit this post.'));

	$post['post_category'] = isset($_POST['post_category']) ? $_POST['post_category'] : '';
	$post['tax_input'] = isset($_POST['tax_input']) ? $_POST['tax_input'] : '';
	$post['post_title'] = isset($_POST['title']) ? $_POST['title'] : '';
	$content = isset($_POST['content']) ? $_POST['content'] : '';

	$upload = false;
	if ( !empty($_POST['photo_src']) && current_user_can('upload_files') ) {
		foreach( (array) $_POST['photo_src'] as $key => $image) {
			// See if files exist in content - we don't want to upload non-used selected files.
			if ( strpos($_POST['content'], htmlspecialchars($image)) !== false ) {
				$desc = isset($_POST['photo_description'][$key]) ? $_POST['photo_description'][$key] : '';
				$upload = media_sideload_image($image, $post_ID, $desc);

				// Replace the POSTED content <img> with correct uploaded ones. Regex contains fix for Magic Quotes
				if ( !is_wp_error($upload) )
					$content = preg_replace('/<img ([^>]*)src=\\\?(\"|\')'.preg_quote(htmlspecialchars($image), '/').'\\\?(\2)([^>\/]*)\/*>/is', $upload, $content);
			}
		}
	}
	// Set the post_content and status.
	$post['post_content'] = $content;
	if ( isset( $_POST['publish'] ) && current_user_can( 'publish_posts' ) )
		$post['post_status'] = 'publish';
	elseif ( isset( $_POST['review'] ) )
		$post['post_status'] = 'pending';
	else
		$post['post_status'] = 'draft';

	// Error handling for media_sideload.
	if ( is_wp_error($upload) ) {
		wp_delete_post($post_ID);
		wp_die( esc_html( $upload->get_error_message() ) );
	} else {
		// Post formats.
		if ( isset( $_POST['post_format'] ) ) {
			if ( current_theme_supports( 'post-formats', $_POST['post_format'] ) )
				set_post_format( $post_ID, $_POST['post_format'] );
			elseif ( '0' == $_POST['post_format'] )
				set_post_format( $post_ID, false );
		}

		$post_ID = wp_update_post($post);
	}

	return $post_ID;
}

// For submitted posts.
if ( isset($_REQUEST['action']) && 'post' == $_REQUEST['action'] ) {
	check_admin_referer('press-this');
	$posted = $post_ID = press_it();
} else {
	$post = get_default_post_to_edit('post', true);
	$post_ID = $post->ID;
}

// Set Variables
$title = isset( $_GET['t'] ) ? trim( strip_tags( html_entity_decode( wp_unslash( $_GET['t'] ) , ENT_QUOTES) ) ) : '';

$selection = '';
if ( !empty($_GET['s']) ) {
	$selection = str_replace('&apos;', "'", wp_unslash($_GET['s']));
	$selection = trim( htmlspecialchars( html_entity_decode($selection, ENT_QUOTES) ) );
}

if ( ! empty($selection) ) {
	$selection = preg_replace('/(\r?\n|\r)/', '</p><p>', $selection);
	$selection = '<p>' . str_replace('<p></p>', '', $selection) . '</p>';
}

$url = isset($_GET['u']) ? esc_url($_GET['u']) : '';
$image = isset($_GET['i']) ? $_GET['i'] : '';

if ( !empty($_REQUEST['ajax']) ) {
	switch ($_REQUEST['ajax']) {
		case 'video': ?>
			<script type="text/javascript">
			/* <![CDATA[ */
				jQuery('.select').click(function() {
					append_editor(jQuery('#embed-code').val());
					jQuery('#extra-fields').hide();
					jQuery('#extra-fields').html('');
				});
				jQuery('.close').click(function() {
					jQuery('#extra-fields').hide();
					jQuery('#extra-fields').html('');
				});
			/* ]]> */
			</script>
			<div class="postbox">
				<h2><label for="embed-code"><?php _e('Embed Code') ?></label></h2>
				<div class="inside">
					<textarea name="embed-code" id="embed-code" rows="8" cols="40"><?php echo esc_textarea( $selection ); ?></textarea>
					<p id="options"><a href="#" class="select button"><?php _e('Insert Video'); ?></a> <a href="#" class="close button"><?php _e('Cancel'); ?></a></p>
				</div>
			</div>
			<?php break;

		case 'photo_thickbox': ?>
			<script type="text/javascript">
				/* <![CDATA[ */
				jQuery('.cancel').click(function() {
					tb_remove();
				});
				jQuery('.select').click(function() {
					image_selector(this);
				});
				/* ]]> */
			</script>
			<h3 class="tb"><label for="tb_this_photo_description"><?php _e('Description') ?></label></h3>
			<div class="titlediv">
				<div class="titlewrap">
					<input id="tb_this_photo_description" name="photo_description" class="tb_this_photo_description tbtitle text" type="text" onkeypress="if(event.keyCode==13) image_selector(this);" value="<?php echo esc_attr($title);?>"/>
				</div>
			</div>

			<p class="centered">
				<input type="hidden" name="this_photo" value="<?php echo esc_attr($image); ?>" id="tb_this_photo" class="tb_this_photo" />
				<a href="#" class="select">
					<img src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr(__('Click to insert.')); ?>" title="<?php echo esc_attr(__('Click to insert.')); ?>" />
				</a>
			</p>

			<p id="options"><a href="#" class="select button"><?php _e('Insert Image'); ?></a> <a href="#" class="cancel button"><?php _e('Cancel'); ?></a></p>
			<?php break;
	case 'photo_images':
		/**
		 * Retrieve all image URLs from given URI.
		 *
		 * @since 2.6.0
		 *
		 * @param string $uri
		 * @return string
		 */
		function get_images_from_uri($uri) {
			$uri = preg_replace('/\/#.+?$/','', $uri);
			if ( preg_match( '/\.(jpe?g|jpe|gif|png)\b/i', $uri ) && !strpos( $uri, 'blogger.com' ) )
				return "'" . esc_attr( html_entity_decode($uri) ) . "'";
			$content = wp_remote_fopen($uri);
			if ( false === $content )
				return '';
			$host = parse_url($uri);
			$pattern = '/<img ([^>]*)src=(\"|\')([^<>\'\"]+)(\2)([^>]*)\/*>/i';
			$content = str_replace(array("\n","\t","\r"), '', $content);
			preg_match_all($pattern, $content, $matches);
			if ( empty($matches[0]) )
				return '';
			$sources = array();
			foreach ($matches[3] as $src) {

				// If no http in URL.
				if (strpos($src, 'http') === false)
					// If it doesn't have a relative URI.
					if ( strpos($src, '../') === false && strpos($src, './') === false && strpos($src, '/') === 0)
						$src = 'http://'.str_replace('//','/', $host['host'].'/'.$src);
					else
						$src = 'http://'.str_replace('//','/', $host['host'].'/'.dirname($host['path']).'/'.$src);
				$sources[] = esc_url($src);
			}
			return "'" . implode("','", $sources) . "'";
		}
		$url = wp_kses(urldecode($url), null);
		echo 'new Array('.get_images_from_uri($url).')';
		break;

	case 'photo_js': ?>
		// Gather images and load some default JS.
		var last = null
		var img, img_tag, aspect, w, h, skip, i, strtoappend = "";
		if(photostorage == false) {
		var my_src = eval(
			jQuery.ajax({
				type: "GET",
				url: "<?php echo esc_url($_SERVER['PHP_SELF']); ?>",
				cache : false,
				async : false,
				data: "ajax=photo_images&u=<?php echo urlencode($url); ?>",
				dataType : "script"
			}).responseText
		);
		if(my_src.length == 0) {
			var my_src = eval(
				jQuery.ajax({
					type: "GET",
					url: "<?php echo esc_url($_SERVER['PHP_SELF']); ?>",
					cache : false,
					async : false,
					data: "ajax=photo_images&u=<?php echo urlencode($url); ?>",
					dataType : "script"
				}).responseText
			);
			if(my_src.length == 0) {
				strtoappend = '<?php _e('Unable to retrieve images or no images on page.'); ?>';
			}
		}
		}
		for (i = 0; i < my_src.length; i++) {
			img = new Image();
			img.src = my_src[i];
			img_attr = 'id="img' + i + '"';
			skip = false;

			maybeappend = '<a href="?ajax=photo_thickbox&amp;i=' + encodeURIComponent(img.src) + '&amp;u=<?php echo urlencode($url); ?>&amp;height=400&amp;width=500" title="" class="thickbox"><img src="' + img.src + '" ' + img_attr + '/></a>';

			if (img.width && img.height) {
				if (img.width >= 30 && img.height >= 30) {
					aspect = img.width / img.height;
					scale = (aspect > 1) ? (71 / img.width) : (71 / img.height);

					w = img.width;
					h = img.height;

					if (scale < 1) {
						w = parseInt(img.width * scale);
						h = parseInt(img.height * scale);
					}
					img_attr += ' style="width: ' + w + 'px; height: ' + h + 'px;"';
					strtoappend += maybeappend;
				}
			} else {
				strtoappend += maybeappend;
			}
		}

		function pick(img, desc) {
			if (img) {
				if('object' == typeof jQuery('.photolist input') && jQuery('.photolist input').length != 0) length = jQuery('.photolist input').length;
				if(length == 0) length = 1;
				jQuery('.photolist').append('<input name="photo_src[' + length + ']" value="' + img +'" type="hidden"/>');
				jQuery('.photolist').append('<input name="photo_description[' + length + ']" value="' + desc +'" type="hidden"/>');
				insert_editor( "\n\n" + encodeURI('<p style="text-align: center;"><a href="<?php echo $url; ?>"><img src="' + img +'" alt="' + desc + '" /></a></p>'));
			}
			return false;
		}

		function image_selector(el) {
			var desc, src, parent = jQuery(el).closest('#photo-add-url-div');

			if ( parent.length ) {
				desc = parent.find('input.tb_this_photo_description').val() || '';
				src = parent.find('input.tb_this_photo').val() || ''
			} else {
				desc = jQuery('#tb_this_photo_description').val() || '';
				src = jQuery('#tb_this_photo').val() || ''
			}

			tb_remove();
			pick(src, desc);
			jQuery('#extra-fields').hide();
			jQuery('#extra-fields').html('');
			return false;
		}

		jQuery('#extra-fields').html('<div class="postbox"><h2><?php _e( 'Add Photos' ); ?> <small id="photo_directions">(<?php _e("click images to select") ?>)</small></h2><ul class="actions"><li><a href="#" id="photo-add-url" class="button button-small"><?php _e("Add from URL") ?> +</a></li></ul><div class="inside"><div class="titlewrap"><div id="img_container"></div></div><p id="options"><a href="#" class="close button"><?php _e('Cancel'); ?></a><a href="#" class="refresh button"><?php _e('Refresh'); ?></a></p></div>');
		jQuery('#img_container').html(strtoappend);
		<?php break;
}
die;
}

	wp_enqueue_style( 'colors' );
	wp_enqueue_script( 'post' );
	add_thickbox();
	_wp_admin_html_begin();
?>
<title><?php _e('Press This') ?></title>
<script type="text/javascript">
//<![CDATA[
addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>', pagenow = 'press-this', isRtl = <?php echo (int) is_rtl(); ?>;
var photostorage = false;
//]]>
</script>

<?php
	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_enqueue_scripts', 'press-this.php' );

	/**
	 * Fires when styles are printed for the Press This admin page.
	 *
	 * @since 3.7.0
	 */
	do_action( 'admin_print_styles-press-this.php' );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_print_styles' );

	/**
	 * Fires when scripts are printed for the Press This admin page.
	 *
	 * @since 3.7.0
	 */
	do_action( 'admin_print_scripts-press-this.php' );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_print_scripts' );

	/**
	 * Fires in the head tag on the Press This admin page.
	 *
	 * @since 3.7.0
	 */
	do_action( 'admin_head-press-this.php' );

	/** This action is documented in wp-admin/admin-header.php */
	do_action( 'admin_head' );
?>
	<script type="text/javascript">
	var wpActiveEditor = 'content';

	function insert_plain_editor(text) {
		if ( typeof(QTags) != 'undefined' )
			QTags.insertContent(text);
	}
	function set_editor(text) {
		if ( '' == text || '<p></p>' == text )
			text = '<p><br /></p>';

		if ( tinyMCE.activeEditor )
			tinyMCE.execCommand('mceSetContent', false, text);
	}
	function insert_editor(text) {
		if ( '' != text && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden()) {
			tinyMCE.execCommand('mceInsertContent', false, '<p>' + decodeURI(tinymce.DOM.decode(text)) + '</p>', {format : 'raw'});
		} else {
			insert_plain_editor(decodeURI(text));
		}
	}
	function append_editor(text) {
		if ( '' != text && tinyMCE.activeEditor && ! tinyMCE.activeEditor.isHidden()) {
			tinyMCE.execCommand('mceSetContent', false, tinyMCE.activeEditor.getContent({format : 'raw'}) + '<p>' + text + '</p>');
		} else {
			insert_plain_editor(text);
		}
	}

	function show(tab_name) {
		jQuery('#extra-fields').html('');
		switch(tab_name) {
			case 'video' :
				jQuery('#extra-fields').load('<?php echo esc_url($_SERVER['PHP_SELF']); ?>', { ajax: 'video', s: '<?php echo esc_attr($selection); ?>'}, function() {
					<?php
					$content = '';
					if ( preg_match("/youtube\.com\/watch/i", $url) ) {
						list($domain, $video_id) = explode("v=", $url);
						$video_id = esc_attr($video_id);
						$content = '<object width="425" height="350"><param name="movie" value="http://www.youtube.com/v/' . $video_id . '"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/' . $video_id . '" type="application/x-shockwave-flash" wmode="transparent" width="425" height="350"></embed></object>';

					} elseif ( preg_ma