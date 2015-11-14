(function() {
	tinymce.create('tinymce.plugins.tabsPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mcetabs', function() {
				ed.windowManager.open({
					file : url + '/tab_popup.php', // file that contains HTML for our modal window
					width : 250 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
					height : 380 + parseInt(ed.getLang('button.delta_height', 0)), // size of our window
					inline : 1
				}, {
					plugin_url : url
				});
			});
			 
			// Register buttons
			ed.addButton('tabs', {title : 'Insert Tabs', cmd : 'mcetabs', image: url + '/images/tab.png' });
		}
	});
	 
	// Register plugin
	// first parameter is the button ID and must match ID elsewhere
	// second parameter must match the first parameter of the tinymce.create() function above
	tinymce.PluginManager.add('tabs', tinymce.plugins.tabsPlugin);

})();