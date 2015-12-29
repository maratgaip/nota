(function() {
	tinymce.create('tinymce.plugins.alertPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mcealert', function() {
				ed.windowManager.open({
					file : url + '/alert_popup.php', // file that contains HTML for our modal window
					width : 220 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
					height : 120 + parseInt(ed.getLang('button.delta_height', 0)), // size of our window
					inline : 1
				}, {
					plugin_url : url
				});
			});
			 
			// Register buttons
			ed.addButton('alert', {title : 'Insert Alert', cmd : 'mcealert', image: url + '/images/alert.png' });
		}
	});
	 
	// Register plugin
	// first parameter is the button ID and must match ID elsewhere
	// second parameter must match the first parameter of the tinymce.create() function above
	tinymce.PluginManager.add('alert', tinymce.plugins.alertPlugin);

})();