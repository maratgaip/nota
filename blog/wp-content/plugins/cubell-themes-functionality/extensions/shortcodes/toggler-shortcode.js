(function() {
	tinymce.create('tinymce.plugins.togglerPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mcetoggler', function() {
				ed.windowManager.open({
					file : url + '/toggler_popup.php', // file that contains HTML for our modal window
					width : 220 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
					height : 120 + parseInt(ed.getLang('button.delta_height', 0)), // size of our window
					inline : 1
				}, {
					plugin_url : url
				});
			});
			 
			// Register buttons
			ed.addButton('toggler', {title : 'Insert Toggler', cmd : 'mcetoggler', image: url + '/images/toggler.png' });
		}
	});
	 
	// Register plugin
	// first parameter is the button ID and must match ID elsewhere
	// second parameter must match the first parameter of the tinymce.create() function above
	tinymce.PluginManager.add('toggler', tinymce.plugins.togglerPlugin);

})();