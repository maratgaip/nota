<?php
// this file contains the contents of the popup window
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Insert Button</title>
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
		var url = jQuery('#button-dialog input#button-url').val();
		var text = jQuery('#button-dialog input#button-text').val();
		var color = jQuery('#button-dialog select#button-color').val();		 
        var size = jQuery('#button-dialog select#button-size').val();   
        var bg = jQuery('#button-dialog select#button-bg').val();   
		var alignment = jQuery('#button-dialog select#button-align').val();		 
        var openin = jQuery('#button-dialog select#open-in').val();      
        var rel = jQuery('#button-dialog select#rel').val();      
		 
		var output = '';
		
		// setup the output of our shortcode
		output = '[button ';
			output += 'color="' + color + '" ';
			output += 'size="' + size + '" ';
            output += 'alignment="' + alignment + '" ';
            output += 'rel="' + rel + '" ';
			output += 'openin="' + openin + '" ';
			
			// only insert if the url field is not blank
			if(url)
				output += ' url="' + url + '"';
		// check to see if the TEXT field is blank
		if(text) {	
			output += ']'+ text + '[/button]';
		}
		// if it is blank, use the selected text, if present
		else {
			output += ']'+ButtonDialog.local_ed.selection.getContent() + '[/button]';
		}
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
				<label for="button-url">Button URL</label>
				<input type="text" name="button-url" value="" id="button-url" />
			</div>
			<div>
				<label for="button-text">Button Text</label>
				<input type="text" name="button-text" value="" id="button-text" />
			</div>
			<div>
				<label for="button-size">Size</label>
				<select name="button-size" id="button-size" size="1">
					<option value="normal" selected="selected">Normal</option>
					<option value="big">Big</option>
				</select>
			</div>
			<div>
				<label for="button-color">Color</label>
				<select name="button-color" id="button-color" size="1">
					<option value="white" selected="selected">White</option>
					<option value="pink"=>Pink</option>
					<option value="yellow">Yellow</option>
					<option value="green">Green</option>
                    <option value="red">Red</option>
                    <option value="grey">Grey</option>
					<option value="brown">Brown</option>
                    <option value="black">Black</option>
                    <option value="blue">Blue</option>
				</select>
			</div>
            <div>
                <label for="button-align">Alignment</label>
                <select name="button-color" id="button-align" size="1">
                    <option value="none" selected="selected">None</option>
                    <option value="center">Center</option>
                </select>
            </div>
            <div>
                <label for="button-align">Open In</label>
                <select name="button-color" id="open-in" size="1">
                    <option value="samewindow" selected="selected">Same Window</option>
                    <option value="newwindow">New Window</option>
                </select>
            </div>
            <div>
                <label for="button-align">Rel</label>
                <select name="button-color" id="rel" size="1">
                    <option value="follow" selected="selected">Follow (Default)</option>
                    <option value="nofollow">No Follow</option>
                </select>
            </div>
			<div>	
				<a href="javascript:ButtonDialog.insert(ButtonDialog.local_ed)" id="insert" style="display: block; line-height: 24px;">Insert</a>
			</div>
		</form>
	</div>
</body>
</html>