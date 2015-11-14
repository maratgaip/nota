<?php

if( ! defined( 'GLOBUS' ) ) {

	die( "Hacking attempt!" );

}

if(isset($_GET['action'])) $action = $_GET['action'];

	echo <<<HTML
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span3">
			<div class="sidebar-nav">
				<ul class="nav nav-list bs-docs-sidenav affix-top">
					{$menu_li}
				</ul>
			</div>
		</div>
HTML;

if( $action == "edit" ){
	
	if(isset($_GET['id'])) $id = intval($_GET['id']);
	
	if(isset($_POST['title'])){
		$title = $db->safesql($_POST['title']);
		$artist_id = $db->safesql($_POST['artist_id']);
		$album_id = $db->safesql($_POST['album_id']);
//                 echo ($album_id);
		$url = $db->safesql($_POST['url']);
                $song_country = $db->safesql($_POST['country_id']);
//               echo ($song_country);
		$lyrics = $db->safesql($_POST['lyrics']);
		$recent = ($_POST['recent']) ? 1:0;
		//echo ($_POST['song_country']);
		$allowedExts = array("mp3");
		$extension = explode(".", $_FILES["file"]["name"]);
		$extension = end($extension);
		if($_FILES["file"]['name']){
			if (($_FILES["file"]["size"] > 20000) && in_array($extension, $allowedExts)) {
				if ($_FILES["file"]["error"] > 0){
					die("Error: " . $_FILES["file"]["error"]);
				}else{
					@move_uploaded_file($_FILES["file"]["tmp_name"], ROOT_DIR . "/static/songs/" . $id . ".mp3");
					$url = "";
				}
			}else{
				msg_page("error", "<strong>Error!</strong> Invalid file, or size of file is too heavy!", "do=songs&action=edit&id=$id");
			}
		}
		
		if($title){
//			echo ($song_country);die;
			$db->query("UPDATE vass_songs SET title='$title', recent='$recent', lyrics='$lyrics', artist_id = '$artist_id', album_id = '$album_id', song_country = '$song_country', url='$url' WHERE id='$id'");
//			echo ($song_country);die;
			msg_page("success", "<strong>Well done!</strong> Saved song!", "do=songs");
			
		}
	}
	
	$row = $db->super_query("SELECT vass_songs.recent, vass_songs.lyrics, vass_songs.url, vass_songs.played, vass_songs.loved, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
	vass_artists.id AS artist_id, vass_albums.id AS album_id, vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id , vass_songs.song_country AS country_id FROM vass_songs LEFT JOIN vass_albums ON 
	vass_songs.album_id = vass_albums.id LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.id = '$id'");
	
	if(!$row['song_id']) die("Song not exits");
	
	$recent = ($row['recent'])? "recent" : "";
        $row1=$db->super_query("SELECT vass_country.country_Name  FROM vass_country WHERE country_Id='".$row['country_id']."'");
	$row['song_country']=$row1['country_Name'];
	echo <<<HTML
	<div class="span9">
		<script type="text/javascript" src="js/bootstrap-typeahead-ajax.js"></script>
		<h3>Edit song: {$row['song_title']}</h3>
			<form method="post" action="" enctype="multipart/form-data">
				<fieldset>
					<label>Title</label>
					<input class="input-xxlarge" type="text" name="title" value="{$row['song_title']}" required autocomplete="off"/>
					<label>Aritst</label>
					<input class="input-xxlarge auto_artist" type="text" name="artist" value="{$row['song_artist']}" required autocomplete="off"/>
					<input type="hidden" name="artist_id" value="{$row['artist_id']}"/>
					<label>Album</label>
					<input class="input-xxlarge auto_album" type="text" name="album" value="{$row['song_album']}" autocomplete="off"/>
					<input type="hidden" name="album_id" value="{$row['album_id']}"/>
                                        <label>Country</label> 
                                        <input class="input-xxlarge auto_country" type="text" name="country" value="{$row['song_country']}" autocomplete="off"/>
                                        <input type="hidden" name="country_id" value="{$row['country_id']}"/>    
					<label>Mp3 url</label>
					<input class="input-xxlarge" type="text" name="url" placeholder="http://" value="{$row['url']}"/>
					<label>Mp3 file</label>
					<input type="file" name="file" class="input-file">
					<label>Lyrics</label>
					<textarea style="530px;height:200px" name="lyrics" class="input-xxlarge">{$row['lyrics']}</textarea>
					<label class="checkbox">
					<input type="checkbox" name="recent" value="1" {$recent}> Recent?
					</label>
					<label></label>
					<button type="submit" class="btn">Save</button>
				</fieldset>
			</form>
		</div>
	</div>
</div>
HTML;
}elseif($action == "del"){
	
	if(isset($_GET['id'])) $id = intval($_GET['id']);
	
	$db->query("DELETE FROM vass_songs WHERE id = '$id'");
	$db->query("DELETE FROM vass_analz WHERE song_id = '$id'");
	
	@unlink( ROOT_DIR . '/static/songs/' . $id . '.mp3' );
	
	msg_page("success", "<strong>Success!</strong> Deleted song!", "do=songs");
	
}else{
	if( isset( $_GET['p'] ) ) $page = intval( $_GET['p'] );
	if( !$page OR $page < 0 ) $page = 1;
	$start = ($page-1) * 20;
	
	if( isset( $_GET['q'] ) ) $q = $db->safesql( $_GET['q'] );
	
	if($q){
			$db->query("SELECT vass_songs.recent, vass_songs.played, vass_songs.loved, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
	vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id FROM vass_songs LEFT JOIN vass_albums ON 
	vass_songs.album_id = vass_albums.id LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id WHERE vass_songs.title LIKE '%$q%' LIMIT $start,20");
	}else{
	$db->query("SELECT vass_songs.recent, vass_songs.played, vass_songs.loved, vass_songs.id AS song_id, vass_songs.loved, vass_songs.title AS song_title, 
	vass_artists.name AS song_artist, vass_albums.name AS song_album, vass_albums.id AS album_id FROM vass_songs LEFT JOIN vass_albums ON 
	vass_songs.album_id = vass_albums.id LEFT JOIN vass_artists ON vass_songs.artist_id = vass_artists.id ORDER BY vass_songs.recent DESC LIMIT $start,20");
	}
	while($row = $db->get_row()){
	$recent = ($row['recent']) ? "<span class=\"label label-success\">Yes</span>" : "No";
	$song_list .= <<<HTML
              <tr>
                <td>{$row['song_title']}</td>
                <td>{$row['song_artist']}</td>
                <td>{$row['song_album']}</td>
               <td>{$row['played']}</td>
               <td>{$row['loved']}</td>
               	<td>{$recent}</td>
				<td><div class="btn-group">
                <button class="btn btn-info dropdown-toggle" data-toggle="dropdown">Action <span class="caret"></span></button>
                <ul class="dropdown-menu">
                  <li><a href="{$PHP_SELF}?do=songs&action=edit&id={$row['song_id']}">Edit</a></li>
                  <li><a onclick="var r=confirm('Are you sure by deleting this song?');if (r==true){window.location='{$PHP_SELF}?do=songs&action=del&id={$row['song_id']}'}; return false;" href="{$PHP_SELF}?do=songs&action=del&id={$row['song_id']}">Delete</a></li>
                </ul>
              </div>
				</td>
              </tr>
HTML;
}
	
	if($q){
		$total = $db->super_query("SELECT COUNT(*) AS count FROM vass_songs WHERE title LIKE '%$q%'");
		$pages = navigation("admin/index.php?do=songs&q=" . urlencode($q) . "&p={page}", $total['count'], 20);
	}else{
		$total = $db->super_query("SELECT COUNT(*) AS count FROM vass_songs");
		$pages = navigation("admin/index.php?do=songs&p={page}", $total['count'], 20);
	}
	
	$q = stripslashes($q);
	
	echo <<<HTML
<script type="text/javascript" src="js/bootstrap-typeahead-ajax.js"></script>
<div class="span9">
			<p style="float:right;20px;">
				<button class="btn btn-info" type="button" onclick="window.location='/admin/index.php?do=upload'"><i class="icon-upload icon-white"></i> Batch upload</button> <button id="upload_song_button" class="btn btn-success" type="button"><i class="icon-plus icon-white"></i>Add new song</button>
			</p>
	<h3>Songs Manager: Total {$total['count']} songs</h3>
	<form class="form-search" mothod="GET" action="{$PHP_SELF}index.php">
		<input name="do" type="hidden" value="songs">
    	<input name="q" type="text" class="input-medium search-query" value="$q">
    	<button type="submit" class="btn">Search</button>
    </form>
	<table class="table table-bordered table-striped">
		<colgroup>
		<col class="span1">
		<col class="span7">
		</colgroup>
		<thead>
			<tr>
				<th>Title</th>
				<th>Artist</th>
				<th>Album</th>
				<th>Played </th>
				<th>Loved</th>
				<th>Recent</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		{$song_list}
		</tbody>
		
	</table>
	<div class="pagination pagination-right">
		<ul>
			{$pages}
		</ul>
	</div>
</div>
</div>
</div>
<div id="addsongs" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-header">
		<button type="button" class="close" data-dismiss="modal" aria-hidden="true">�</button>
		<h3 id="myModalLabel">Add new song</h3>
	</div>
	<form id="add_song" action="{$PHP_SELF}ajax.php?t=upload_song" enctype="multipart/form-data" method="POST">
		<div class="modal-body"> 
			<!-- The async form to send and replace the modals content with its response -->
			<div class="control-group">
				<label class="control-label">Song Title</label>
				<div class="controls">
					<input type="text" name="title" required id="title" placeholder="Song name" autocomplete="off">
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Album</label>
				<div class="controls">
					<input type="text" class="auto_album" name="album" id="album" placeholder="Album of the song" autocomplete="off">
					<input type="hidden" name="album_id" value="{$row['album_id']}"/>
				</div>
			</div>
			<div class="control-group">
				<label class="control-label">Artist</label>
				<div class="controls">
					<input type="text" class="auto_artist" name="artist" required id="artist" placeholder="Artist who own the song" autocomplete="off">
					<input type="hidden" name="artist_id"/>
				</div>
			</div>
                      
                       <div class="control-group">
	               <label class="control-label">Country</label>
	              <div class="controls">
	                 <input type="text" class="auto_country"  name="country" required id="country" placeholder="choose Song country" autocomplete="off">
					<input type="hidden" name="country_id" value="{$row['country_id']}"/>
	                   </div>
	                  </div> 
			<div class="control-group">
				<label class="control-label">Url (mp3 file)</label>
				<div class="controls">
					<input type="text" name="url" id="url" placeholder="http://">
				</div>
			</div>
                        
                    
			<div class="control-group">
				<label class="control-label">Or upload MP3 file (<span class="percenter">0%</span >)</label>
				<div class="controls">
					<input name="uploadedfile" id="uploadedfile" type="file" >
				</div>
			</div>
		</div>
                 
              

                       
		<div class="modal-footer">
			<button type="submit" class="btn">Submit</button>
		</div>
	</form>
</div>
HTML;
}
?>
