(function() {
	tinymce.create('tinymce.plugins.authorboxPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceauthorbox', function() {
				ed.windowManager.open({
					file : url + '/authorbox_popup.php', // file that contains HTML for our modal window
					width : 220 + parseInt(ed.getLang('button.delta_width', 0)), // size of our window
					height : 120 + parseInt(ed.getLang('button.delta_height', 0)), // size of our window
					inline : 1
				}, {
					plugin_url : url
				});
			});
			 
			ed.addButton('authorbox', {title : 'Insert Author Box', cmd : 'mceauthorbox', image: url + '/images/authorbox.png' });
		}
	});
	 
	tinymce.PluginManager.add('authorbox', tinymce.plugins.authorboxPlugin);

})();