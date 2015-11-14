<?php

if( ! defined( 'GLOBUS' ) || ! $user_group[$member_id['user_group']]['allow_m_albums']) {

	msg_page("error", "<strong>Error!</strong> You don't have permission in this place!", "");

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

if( $action == "edit" || $action == "add" ){
	
	if($action == "edit"){
	
		if(isset($_GET['id'])) $id = intval($_GET['id']);
		
		$MAKE_THUMBNAIL = false;
		
		if(isset($_POST['name'])){
			$allowedExts = array("jpg", "jpeg", "gif", "png");
			$extension = explode(".", $_FILES["file"]["name"]);
			$extension = end($extension);
			if($_FILES["file"]['name']){
				if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] > 20000) && in_array($extension, $allowedExts)) {
					if ($_FILES["file"]["error"] > 0){
						die("Error: " . $_FILES["file"]["error"]);
					}else{
						$MAKE_THUMBNAIL = true;
					}
				}else{
					msg_page("error", "<strong>Error!</strong> Invalid file, or size of file is too heavy!", "do=albums&action=edit&id=$id");
				}
			}
			$name = $db->safesql($_POST['name']);
			$descr = $db->safesql($_POST['descr']);
			$artist_id = intval($_POST['artist_id']);
			
			$active = ($_POST['approve']) ? 1:0;
			
			$db->query("UPDATE vass_albums SET name = '$name', artist_id = '$artist_id', descr = '$descr', active = '$active' WHERE id = '$id'");
			
			if( $MAKE_THUMBNAIL ){
				require_once INCLUDE_DIR . '/class/_class_thumb.php';
				$md5_rand =  md5(rand(1000, 9000));
				$res = @move_uploaded_file($_FILES["file"]["tmp_name"], ROOT_DIR . "/static/albums/" . $md5_rand);
				if ($res) {
					
					$thumb = new thumbnail(ROOT_DIR . "/static/albums/" . $md5_rand);
					$thumb->crop('500', '500');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/albums/" . $id . "_extralarge.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/albums/" . $md5_rand);
					$thumb->crop('250', '250');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/albums/" . $id . "_large.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/albums/" . $md5_rand);
					$thumb->crop('120', '120');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/albums/" . $id . "_medium.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/albums/" . $md5_rand);
					$thumb->crop('75', '75');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/albums/" . $id . "_small.jpg");
					
					@unlink(ROOT_DIR . "/static/albums/" . $md5_rand);
					$db->free();
				}
			}
			$message = '<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						<strong>Well done!</strong> Saved the <strong>album information</strong> successfully!.
						</div>';
		}
		
		$row = $db->super_query("SELECT vass_albums.name, vass_albums.descr, vass_albums.active, vass_albums.id, vass_artists.id AS artist_id, vass_artists.name AS artist FROM vass_albums LEFT JOIN vass_artists
	ON vass_albums.artist_id = vass_artists.id WHERE vass_albums.id = '$id'");
		if(!$row['id']) die("Album not exits");
		$checked = ($row['active']) ? "checked": "";
		
		$txt = "Edit album: {$row['name']}";
		$image = "<img src=\"{$config['siteurl']}/static/albums/{$row['id']}_extralarge.jpg?" . time() . "\" class=\"img-polaroid\">";
		
	}elseif($action == "add"){
		
		$checked = "checked";
		
		if(isset($_POST['name'])){
			
			$MAKE_THUMBNAIL = false;
			$allowedExts = array("jpg", "jpeg", "gif", "png");
			$extension = explode(".", $_FILES["file"]["name"]);
			$extension = end($extension);
			if($_FILES["file"]['name']){
				if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] > 20000) && in_array($extension, $allowedExts)) {
					if ($_FILES["file"]["error"] > 0){
						die("Error: " . $_FILES["file"]["error"]);
					}else{
						$MAKE_THUMBNAIL = true;
					}
				}else{
					msg_page("error", "<strong>Error!</strong> Invalid file, or size of file is too heavy!", "do=albums&action=add");
				}
			}
			
			$name = $db->safesql($_POST['name']);
			$descr = $db->safesql($_POST['descr']);
			$artist_id = intval($_POST['artist_id']);
			
			$active = ($_POST['approve']) ? 1:0;
			
			$db->query("INSERT INTO vass_albums SET name = '$name', artist_id = '$artist_id', descr = '$descr', active = '$active'");
			
			$album_id = $db->insert_id();
			
			if( $MAKE_THUMBNAIL ){
				require_once INCLUDE_DIR . '/class/_class_thumb.php';
				$md5_rand =  md5(rand(1000, 9000));
				$res = @move_uploaded_file($_FILES["file"]["tmp_name"], ROOT_DIR . "/static/albums/" . $md5_rand);
				if ($res) {
					
					$thumb = new thumbnail(ROOT_DIR . "/static/albums/" . $md5_rand);
					$thumb->crop('500', '500');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/albums/" . $album_id . "_extralarge.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/albums/" . $md5_rand);
					$thumb->crop('250', '250');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/albums/" . $album_id . "_large.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/albums/" . $md5_rand);
					$thumb->crop('120', '120');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/albums/" . $album_id . "_medium.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/albums/" . $md5_rand);
					$thumb->crop('75', '75');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/albums/" . $album_id . "_small.jpg");
					
					@unlink(ROOT_DIR . "/static/albums/" . $md5_rand);
					$db->free();
				}
			}
			
			
			$message = '<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						<strong>Well done!</strong> Added album successfully!.
						</div>';
			define ( 'SUBMIT', true );
		}
		
		$txt = "Add new album";
	}
	
	echo <<<HTML
		<div class="span6">
HTML;
	if( defined( 'SUBMIT' ) ) {
		echo $message;
	}	
	
	echo <<<HTML
		<script type="text/javascript" src="js/bootstrap-typeahead-ajax.js"></script>
		<h3>{$txt}</h3>
			<form method="post" action="" enctype="multipart/form-data">
				<fieldset>
					<label>Name</label>
					<input class="input-xxlarge" type="text" name="name" value="{$row['name']}" placeholder="Album name" autocomplete="off" required/>
					<label>Artist</label>
					<input class="auto_artist input-xxlarge" type="text" placeholder="Artist who own this album" autocomplete="off" value="{$row['artist']}" required/>
					<input type="hidden" name="artist_id" value="{$row['artist_id']}"/>
					<label>Image</label>
					<input type="file" name="file" class="input-file">
					<label>Description</label>
					<textarea class="textarea" name="descr" style="width: 530px; height: 200px">{$row['descr']}</textarea>
					<label class="checkbox">
					<input type="checkbox" name="approve" value="1" {$checked}> Active?
					</label>
					<label></label>
					<button type="submit" class="btn">Save</button>
				</fieldset>
			</form>
		</div>
		<div class="span3">
			{$image}
		</div>
	</div>
</div>
HTML;
}elseif($action == "delete"){
	if(isset($_GET['id'])) $id = intval($_GET['id']);
	
	$db->query("DELETE FROM vass_albums WHERE id = '$id'");
	
	msg_page("success", "<strong>Well done!</strong> Deleted the album!", "do=albums");
	
}else{
	if( isset( $_GET['p'] ) ) $page = intval( $_GET['p'] );
	if( !$page OR $page < 0 ) $page = 1;
	$start = ($page-1) * 20;
	
	if( isset( $_GET['q'] ) ) $q = $db->safesql( $_GET['q'] );
	
	if($q){
		$db->query("SELECT vass_albums.name, vass_albums.id, vass_artists.name AS artist FROM vass_albums LEFT JOIN vass_artists
		ON vass_albums.artist_id = vass_artists.id WHERE vass_albums.name LIKE '%$q%' LIMIT $start,20");
	}else{
		$db->query("SELECT vass_albums.name, vass_albums.id, vass_artists.name AS artist FROM vass_albums LEFT JOIN vass_artists
		ON vass_albums.artist_id = vass_artists.id LIMIT $start,20");
	}
	
	
	
	while($row = $db->get_row()){
	$bio = shorter($row['bio'],150);
		$entries .= "
              <tr>
                <td>
    				<div class=\"media\"> <a href=\"{$PHP_SELF}?do=albums&action=edit&id={$row['id']}\" class=\"pull-left\"> <img class=\"media-object\" data-src=\"holder.js/64x64\" alt=\"64x64\" style=\"width: 64px; height: 64px;\" src=\"{$config['siteurl']}static/albums/{$row['id']}_medium.jpg\"> </a>
			<div class=\"media-body\">
				<h4 class=\"media-heading\"><a href=\"{$PHP_SELF}?do=albums&action=edit&id={$row['id']}\">{$row['name']}</a></h4>
				{$bio}
			</div>
		</div>
                </td>
                <td>{$row['artist']}</td>
                <td><div class=\"btn-group\">
                <button class=\"btn dropdown-toggle\" data-toggle=\"dropdown\">Action <span class=\"caret\"></span></button>
                <ul class=\"dropdown-menu\">
                  <li><a href=\"{$PHP_SELF}?do=albums&action=edit&id={$row['id']}\">Edit</a></li>
                  <li><a onclick=\"var r=confirm('Are you sure by deleting this album?');if (r==true){window.location='{$PHP_SELF}?do=albums&action=delete&id={$row['id']}'}; return false;\" href=\"{$PHP_SELF}?do=albums&action=delete&id={$row['id']}\">Delete</a></li>
                </ul>
              </div>
				</td>
              </tr>";
}
	
	if($q){
		$total = $db->super_query("SELECT COUNT(*) AS count FROM vass_albums WHERE name LIKE '%$q%'");
		$pages = navigation("admin/index.php?do=albums&q=" . urlencode($q) . "&p={page}", $total['count'], 20);
	}else{
		$total = $db->super_query("SELECT COUNT(*) AS count FROM vass_albums");
		$pages = navigation("admin/index.php?do=albums&p={page}", $total['count'], 20);
	}
	
	echo <<<HTML
			<div class="span9">
			<p style="float:right;20px;">
				<button onClick="document.location='?do=albums&action=add'; return(false)" id="upload_song_button" class="btn btn-success" type="button"><i class="icon-plus icon-white"></i>Add new album</button>
			</p>
			<h3>Albums Manager: Total {$total['count']} albums</h3>
	<form class="form-search" mothod="GET" action="{$PHP_SELF}index.php">
		<input name="do" type="hidden" value="albums">
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
				<th>Name</th>
				<th>Artist</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		{$entries}
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

HTML;
}
?>