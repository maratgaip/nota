<?php
// this file contains the contents of the popup window
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Video Gallery</title>
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
		var size = jQuery('#button-dialog select#size-gallery').val();		

		var video1 = jQuery('#button-dialog select#video-1').val();		
		var url1 = jQuery('#button-dialog input#url-1').val();		
		var image1 = jQuery('#button-dialog input#image-1').val();	
		var caption1 = jQuery('#button-dialog input#caption-1').val();		
	
		
		var video2 = jQuery('#button-dialog select#video-2').val();		
		var url2 = jQuery('#button-dialog input#url-2').val();		
		var image2 = jQuery('#button-dialog input#image-2').val();	
		var caption2 = jQuery('#button-dialog input#caption-2').val();			
		
		var video3 = jQuery('#button-dialog select#video-3').val();		
		var url3 = jQuery('#button-dialog input#url-3').val();		
		var image3 = jQuery('#button-dialog input#image-3').val();			
		var caption3 = jQuery('#button-dialog input#caption-3').val();		
	
		var video4 = jQuery('#button-dialog select#video-4').val();		
		var url4 = jQuery('#button-dialog input#url-4').val();		
		var image4 = jQuery('#button-dialog input#image-4').val();		
		var caption4 = jQuery('#button-dialog input#caption-4').val();		
				 
		var output = '';
		
		// setup the output of our shortcode
		output = '[videogallery ';
			
			output += 'video1="' + video1 + '" ';
			output += 'url1="' + url1 + '" ';
			output += 'image1="' + image1 + '" ';
			output += 'caption1="' + caption1 + '" ';
						
			output += 'video2="' + video2 + '" ';
			output += 'url2="' + url2 + '" ';
			output += 'image2="' + image2 + '" ';
			output += 'caption2="' + caption2 + '" ';

			
			if(url3) {
			output += 'video3="' + video3 + '" ';
			output += 'url3="' + url3 + '" ';
			output += 'image3="' + image3 + '" ';
			output += 'caption3="' + caption3 + '" ';
			}
			
			if(url4) {			
			output += 'video4="' + video4 + '" ';
			output += 'url4="' + url4 + '" ';
			output += 'image4="' + image4 + '" ';
			output += 'caption4="' + caption4 + '" ';
			}
		
			output += ']';

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
				<label for="button-color">Video 1:</label>
				<select name="button-color" id="video-1" size="1">
					<option value="youtube" selected="selected">YouTube</option>
					<option value="vimeo">Vimeo</option>
				</select>
			</div>
			<div>
				<label for="button-url">1) Video ID</label>
				<input type="text" name="button-url" value="" id="url-1" />
			</div>
			<div>
				<label for="button-text">1) Preview Image url</label>
				<input type="text" name="button-text" value="" id="image-1" />
			</div>
			<div>
				<label for="button-text">1) Caption</label>
				<input type="text" name="button-text" value="" id="caption-1" />
			</div>
			<div>
				<label for="button-color">Video 2:</label>
				<select name="button-color" id="video-2" size="1">
					<option value="youtube" selected="selected">YouTube</option>
					<option value="vimeo">Vimeo</option>
				</select>
			</div>
			<div>
				<label for="button-url">2) Video ID</label>
				<input type="text" name="button-url" value="" id="url-2" />
			</div>
			<div>
				<label for="button-text">2) Preview Image url</label>
				<input type="text" name="button-text" value="" id="image-2" />
			</div>
			<div>
				<label for="button-text">2) Caption</label>
				<input type="text" name="button-text" value="" id="caption-2" />
			</div>
			<div>
				<label for="button-color">Video 3:</label>
				<select name="button-color" id="video-3" size="1">
					<option value="youtube" selected="selected">YouTube</option>
					<option value="vimeo">Vimeo</option>
				</select>
			</div>
			<div>
				<label for="button-url">3) Video ID</label>
				<input type="text" name="button-url" value="" id="url-3" />
			</div>
			<div>
				<label for="button-text">3) Preview Image url</label>
				<input type="text" name="button-text" value="" id="image-3" />
			</div>
			<div>
				<label for="button-text">3) Caption</label>
				<input type="text" name="button-text" value="" id="caption-3" />
			</div>
			<div>
				<label for="button-color">Video 4:</label>
				<select name="button-color" id="video-4" size="1">
					<option value="youtube" selected="selected">YouTube</option>
					<option value="vimeo">Vimeo</option>
				</select>
			</div>
			<div>
				<label for="button-url">4) Video ID</label>
				<input type="text" name="button-url" value="" id="url-4" />
			</div>
			<div>
				<label for="button-text">4) Preview Image url</label>
				<input type="text" name="button-text" value="" id="image-4" />
			</div>
			<div>
				<label for="button-text">4) Caption</label>
				<input type="text" name="button-text" value="" id="caption-4" />
			</div>
			
			<div>	
				<a href="javascript:ButtonDialog.insert(ButtonDialog.local_ed)" id="insert" style="display: block; line-height: 24px;">Insert</a>
			</div>
		</form>
	</div>
</body>
</html>