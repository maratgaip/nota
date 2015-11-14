(function() {
	tinymce.create('tinymce.plugins.dividerPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mcedivider', function() {
				ed.windowManager.open({
					file : url + '/divider_popup.php', // file that contains HTML for our modal window
					width : 320 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
					height : 100 + parseInt(ed.getLang('button.delta_height', 0)), // size of our window
					inline : 1
				}, {
					plugin_url : url
				});
			});
			 
			// Register buttons
			ed.addButton('divider', {title : 'Divider', cmd : 'mcedivider', image: url + '/images/divider.png' });
		}
	});
	 
	// Register plugin
	// first parameter is the button ID and must match ID elsewhere
	// second parameter must match the first parameter of the tinymce.create() function above
	tinymce.PluginManager.add('divider', tinymce.plugins.dividerPlugin);

})();