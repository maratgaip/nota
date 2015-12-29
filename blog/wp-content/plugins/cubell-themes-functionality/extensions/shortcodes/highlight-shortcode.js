(function() {
	tinymce.create('tinymce.plugins.highlightPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mcehighlight', function() {
				ed.windowManager.open({
					file : url + '/highlight_popup.php', // file that contains HTML for our modal window
					width : 320 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
					height : 150 + parseInt(ed.getLang('button.delta_height', 0)), // size of our window
					inline : 1
				}, {
					plugin_url : url
				});
			});
			 
			// Register buttons
			ed.addButton('highlight', {title : 'Highlight Text', cmd : 'mcehighlight', image: url + '/images/highlight.png' });
		}
	});
	 
	// Register plugin
	// first parameter is the button ID and must match ID elsewhere
	// second parameter must match the first parameter of the tinymce.create() function above
	tinymce.PluginManager.add('highlight', tinymce.plugins.highlightPlugin);

})();