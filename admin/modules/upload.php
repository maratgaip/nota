<?php

if( ! defined( 'GLOBUS' ) || $member_id['user_group'] != 1 ) {

	msg_page("error", "<strong>Error!</strong> You don't have permission in this place!", "");

}

if (!@is_dir ( ROOT_DIR . '/static/songs/')) {
	die ( "Songs not found!" );
}

if(!is_writable(ROOT_DIR . '/static/songs/')) {
	
	$fail_upload = '<div class="alert alert-error">
				<button data-dismiss="alert" class="close" type="button">×</button>
				<strong>Warring!</strong> folder <strong>/static/songs/</strong> is not writable!.
			</div>';

} else $fail_upload = "";

if(isset($_POST['submit'])){
	
	$titles = $_POST['title'];
	$filename = $_POST['filename'];
	$album = $_POST['album'];
	$artist = $_POST['artist'];
	
	for($i=0;$i<count($titles);$i++){
		
		if($titles[$i] && $artist[$i]){
			
			$artist_id = $db->super_query("SELECT id FROM vass_artists WHERE name = '" . $db->safesql($artist[$i]) . "'");
			
			$album_id = $db->super_query("SELECT id FROM vass_albums WHERE name = '" . $db->safesql($album[$i]) . "'");
			
			if($artist_id['id']){
				
				$db->query("INSERT INTO  vass_songs (title, artist_id, album_id, user_id, active, created_on) VALUES ('" . $db->safesql($titles[$i]) . "', '" . $artist_id['id'] . "', '" . $album_id['id'] . "', '" . $member_id['user_id'] . "', '1', '$_TIME')");
				
				$song_id = $db->insert_id();
				
				@rename(ROOT_DIR . "/static/songs/temp/" . stripslashes($filename[$i]), ROOT_DIR . "/static/songs/" . $song_id . ".mp3");
				
			}
			
			
		}
		
	}
	
	msg_page("success", "<strong>Well done!</strong> Saved song!", "do=upload");
	
}


echo <<<HTML
<div class="container-fluid">
	<div class="row-fluid">
    <div class="page-header">
        <h2>Batch Files Upload</h2>
    </div>
    <!-- The file upload form used as target for the file upload widget -->
    <form id="fileupload" action="" method="POST" enctype="multipart/form-data">
        <!-- Redirect browsers with JavaScript disabled to the origin page -->
        <noscript><input type="hidden" name="redirect" value="http://blueimp.github.com/jQuery-File-Upload/"></noscript>
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="fileupload-buttonbar">
            <div class="span7">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="icon-plus icon-white"></i>
                    <span>Add files...</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="icon-upload icon-white"></i>
                    <span>Start upload</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="icon-ban-circle icon-white"></i>
                    <span>Cancel upload</span>
                </button>
                <button type="button" class="btn btn-danger delete">
                    <i class="icon-trash icon-white"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" class="toggle">
            </div>
            <!-- The global progress information -->
            <div class="span5 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="bar" style="width:0%;"></div>
                </div>
                <!-- The extended global progress information -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <!-- The loading indicator is shown during file processing -->
        <div class="fileupload-loading"></div>
        <br>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files" data-toggle="modal-gallery" data-target="#modal-gallery"></tbody></table>
        <div style="max-width: 400px; margin: 0 auto 10px;" class="well">
    		<input type="hidden" name="submit" value="yes">
            <button class="btn btn-large btn-block btn-success" type="submit">Save</button>
        </div>
    </form>
    <br>
    <blockquote>
        <h3>Upload Notes</h3>
        <ul>
            <li>The maximum file size for uploads is <strong>20 MB</strong>.</li>
            <li>Only music files (<strong>MP3</strong>) are allowed.</li>
            <li>You can <strong>drag &amp; drop</strong> files from your desktop on this webpage with Google Chrome, Mozilla Firefox and Apple Safari.</li>
        </ul>
    </blockquote>
</div>
</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td class="name"><span>{%=file.name%}</span></td>
        <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
        {% if (file.error) { %}
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else if (o.files.valid && !i) { %}
            <td>
                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
            </td>
            <td class="start">{% if (!o.options.autoUpload) { %}
                <button class="btn btn-primary">
                    <i class="icon-upload icon-white"></i>
                    <span>Start</span>
                </button>
            {% } %}</td>
        {% } else { %}
            <td colspan="2"></td>
        {% } %}
        <td class="cancel">{% if (!i) { %}
            <button class="btn btn-warning">
                <i class="icon-ban-circle icon-white"></i>
                <span>Cancel</span>
            </button>
        {% } %}</td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { 
	var rand_number = Math.round(Math.floor(Math.random() * 1000000000000));
%}
    <tr class="template-download fade">
        {% if (file.error) { %}
            <td></td>
            <td class="name"><span>{%=file.name%}</span></td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td class="error" colspan="2"><span class="label label-important">Error</span> {%=file.error%}</td>
        {% } else { %}
            <td class="name">
                 <input type="text" autocomplete="off" placeholder="Song name" id="title" required="" name="title[]" value="{%= file.name.replace('.mp3','') %}" style="margin-bottom:0;">
				<input type="hidden" name="filename[]" value="{%=file.name%}">
                <!--<a href="{%=file.url%}" title="{%=file.name%}" data-gallery="{%=file.thumbnail_url&&'gallery'%}" download="{%=file.name%}">{%=file.name%}</a>-->
            </td>
            <td class="album">
                 <input type="text" autocomplete="off" placeholder="Album of the song" id="album" name="album[]" class="auto_album" style="margin-bottom:0;" onclick="auto_album({%= rand_number %}); return false;">
            </td>
            <td class="artist">
                 <input type="text" autocomplete="off" placeholder="Artist who own the song" id="artist" required="" name="artist[]" class="auto_artist" style="margin-bottom:0;" onclick="auto_artist({%= rand_number %}); return false;">
				<input type="hidden" name="artist_id" id="artist_form_{%= rand_number %}">
            </td>
            <td class="size"><span>{%=o.formatFileSize(file.size)%}</span></td>
            <td colspan="2"></td>
        {% } %}
        <td class="delete">
            <button class="btn btn-danger" data-type="{%=file.delete_type%}" data-url="ajax.php?t=delete_temp&name={%=file.name%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                <i class="icon-trash icon-white"></i>
                <span>Delete</span>
            </button>
            <input type="checkbox" name="delete" value="1">
        </td>
    </tr>
{% } %}
</script>
<script type="text/javascript" src="js/bootstrap-typeahead-ajax.js"></script>
<script src="js/upload/vendor/jquery.ui.widget.js"></script>
<script src="js/upload//tmpl.min.js"></script>
<script src="js/upload/load-image.min.js"></script>
<script src="js/upload/canvas-to-blob.min.js"></script>
<script src="js/upload/bootstrap-image-gallery.min.js"></script>
<script src="js/upload/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="js/upload/jquery.fileupload.js"></script>
<!-- The File Upload file processing plugin -->
<script src="js/upload/jquery.fileupload-fp.js"></script>
<!-- The File Upload user interface plugin -->
<script src="js/upload/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<script src="js/upload/main.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE8+ -->
<!--[if gte IE 8]><script src="js/upload/cors/jquery.xdr-transport.js"></script><![endif]-->
HTML;

?>
