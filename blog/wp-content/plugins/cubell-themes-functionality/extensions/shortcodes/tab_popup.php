<?php
// this file contains the contents of the popup window
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Insert Tab</title>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.js"></script>
<script language="javascript" type="text/javascript" src="tiny_mce_popup.js"></script>
<link rel="stylesheet" href="css/friendly_buttons_tinymce.css" />


<script type="text/javascript">
 
var ButtonDialog = {
	local_ed : 'ed',
	init : function(ed) {
		ButtonDialog.local_ed = ed;
		tinyMCEPopup.resizeToInnerSize();
	},
	insert : function insertButton(ed) {
	 
		// Try and remove existing style / blockquote
		tinyMCEPopup.execCommand('mceRemoveNode', false, null);
		 
		// set up variables to contain our input values
		var title = jQuery('#button-dialog input#button-title').val();
		var text1 = jQuery('#button-dialog input#button-text1').val();	
		var title2 = jQuery('#button-dialog input#button-title2').val();
		var text2 = jQuery('#button-dialog input#button-text2').val();
		var title3 = jQuery('#button-dialog input#button-title3').val();
		var text3 = jQuery('#button-dialog input#button-text3').val();	
		var title4 = jQuery('#button-dialog input#button-title4').val();
		var text4 = jQuery('#button-dialog input#button-text4').val();		 
		 
		var output = '';
		
		// setup the output of our shortcode

		output += '[cbtabs][cbtab ';
            output += 'title="' + title + '"]' + text1 + '[/cbtab]';            

		if ( title2 ) {
                output += '[cbtab title="' + title2 + '"]' + text2 + '[/cbtab]';         
		}
		if ( title3 ) {
            output += '[cbtab title="' + title3 + '"]' + text3 + '[/cbtab]';         
		}
		if ( title4 ) {
            output += '[cbtab title="' + title4 + '"]' + text4 + '[/cbtab]';         
			
		}	
		output += '[/cbtabs]';
			
		tinyMCEPopup.execCommand('mceReplaceContent', false, output);
		 
		// Return
		tinyMCEPopup.close();
	}
};
tinyMCEPopup.onInit.add(ButtonDialog.init, ButtonDialog);
 
</script>

</head>
<body>
	<div id="button-dialog">
		<form action="/" method="get" accept-charset="utf-8">
			<div class="clearfix">
				<label for="button-title">Tab 1 Title</label>
				<input type="text" name="button-title" value="" id="button-title" />
			</div>
			<div>
				<label for="button-text">Tab 1 Content</label>
				<input type="text" name="button-text" value="" id="button-text1" />
			</div>
			<div class="clearfix"></div>
            <div>
				<label for="button-title2">Tab 2 Title</label>
				<input type="text" name="button-title2" value="" id="button-title2" />
			</div>
			<div>
				<label for="button-text2">Tab 2 Content</label>
				<input type="text" name="button-text" value="" id="button-text2" />
			</div>
			<div class="clearfix"></div>
            <div>
				<label for="button-title3">Tab 3 Title</label>
				<input type="text" name="button-title3" value="" id="button-title3" />
			</div>
			<div>
				<label for="button-text3">Tab 3 Content</label>
				<input type="text" name="button-text" value="" id="button-text3" />
			</div>
			<div class="clearfix"></div>
            <div>
				<label for="button-title4">Tab 4 Title</label>
				<input type="text" name="button-title4" value="" id="button-title4" />
			</div>
			<div>
				<label for="button-text4">Tab 4 Content</label>
				<input type="text" name="button-text" value="" id="button-text4" />
			</div>
			<div>	
				<a href="javascript:ButtonDialog.insert(ButtonDialog.local_ed)" id="insert" style="display: block; line-height: 24px;">Insert</a>
			</div>
		</form>
	</div>
</body>
</html>