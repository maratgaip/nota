<?php
// this file contains the contents of the popup window
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Insert Column</title>
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
		var size = jQuery('#button-dialog select#button-size').val();		 
		var position = jQuery('#button-dialog select#button-position').val();		 
		 
		var output = '';
		
		// setup the output of our shortcode
		output = '[column ';
			output += 'size=' + size + ' ';
			output += 'position=' + position + ' ';

			output += ']YOUR CONTENT HERE[/column]';
	
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
			<div>
				<label for="button-size">Size</label>
				<select name="button-size" id="button-size" size="1">
					<option value="one_half" selected="selected">One Half</option>
					<option value="one_third">One Third</option>
                    <option value="two_third">Two Thirds</option>
					<option value="one_quarter">One Quarter</option>
                    <option value="three_quarter">Three Quarters</option>              
                 </select>
			</div>
			<div>
				<label for="button-position">Position</label>
				<select name="button-position" id="button-position" size="1">
					<option value="first" selected="selected">First</option>
					<option value="middle">Middle</option>
					<option value="last">Last</option>
				</select>
			</div>
			<div>	
				<a href="javascript:ButtonDialog.insert(ButtonDialog.local_ed)" id="insert" style="display: block; line-height: 24px;">Insert</a>
			</div>
		</form>
	</div>
</body>
</html>