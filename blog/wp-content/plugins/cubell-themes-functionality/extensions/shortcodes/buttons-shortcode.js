(function() {
	tinymce.create('tinymce.plugins.cb_buttonPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mcecb_button', function() {
				ed.windowManager.open({
					file : url + '/button_popup.php', // file that contains HTML for our modal window
					width : 250 + parseInt(ed.getLang('cb_button.delta_width', 0)), // size of our window
					height : 280 + parseInt(ed.getLang('cb_button.delta_height', 0)), // size of our window
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register cb_buttons
			ed.addButton('cb_button', {title : 'Insert Button', cmd : 'mcecb_button', image: url + '/images/button.png' });
		}
	});

	// Register plugin
	// first parameter is the cb_button ID and must match ID elsewhere
	// second parameter must match the first parameter of the tinymce.create() function above
	tinymce.PluginManager.add('cb_button', tinymce.plugins.cb_buttonPlugin);

})();