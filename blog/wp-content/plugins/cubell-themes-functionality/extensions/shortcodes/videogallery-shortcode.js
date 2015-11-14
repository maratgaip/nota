(function() {
	tinymce.create('tinymce.plugins.videogalleryPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mcevideogallery', function() {
				ed.windowManager.open({
					file : url + '/videogallery_popup.php', // file that contains HTML for our modal window
					width : 350 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
					height : 580 + parseInt(ed.getLang('button.delta_height', 0)), // size of our window
					inline : 1
				}, {
					plugin_url : url
				});
			});
			 
			// Register buttons
			ed.addButton('videogallery', {title : 'Video Gallery', cmd : 'mcevideogallery', image: url + '/images/videogallery.png' });
		}
	});
	 
	// Register plugin
	// first parameter is the button ID and must match ID elsewhere
	// second parameter must match the first parameter of the tinymce.create() function above
	tinymce.PluginManager.add('videogallery', tinymce.plugins.videogalleryPlugin);

})();