<?php

if( ! defined( 'GLOBUS' ) || ! $user_group[$member_id['user_group']]['allow_m_artists']) {

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

if( $action == "delete"){
	
	if(isset($_GET['id'])) $id = intval($_GET['id']);
	
	$db->query("DELETE FROM vass_artists WHERE id = '$id'");
	
	msg_page("success", "<strong>Well done!</strong> Deleted the artist!", "do=artists");
	
}elseif( $action == "edit" || $action == "add" ){
	
	if($action == "edit"){
	
		if(isset($_GET['id'])) $id = intval($_GET['id']);
		
		$MAKE_THUMBNAIL = false;
		
		if(isset($_POST['name'])){
			$allowedExts = array("jpg", "jpeg", "gif", "png");
			$extension = end(explode(".", $_FILES["file"]["name"]));
			if($_FILES["file"]['name']){
				if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] > 20000) && in_array($extension, $allowedExts)) {
					if ($_FILES["file"]["error"] > 0){
						die("Error: " . $_FILES["file"]["error"]);
					}else{
						$MAKE_THUMBNAIL = true;
					}
				}else{
					msg_page("error", "<strong>Error!</strong> Invalid file, or size of file is too heavy!", "do=artists&action=edit&id=$id");
				}
			}
			$name = $db->safesql($_POST['name']);
			$bio = $db->safesql($_POST['bio']);
			$tags = $db->safesql($_POST['hidden-tags']);
			$tags = explode(",",$tags);
			for($i=0; $i<count($tags); $i++){
				$tag = $db->super_query("SELECT id FROM vass_genres WHERE name = '" . $tags[$i] . "'");
				if($tag['id']) $tags_id[] = $tag['id'];
				
			}
			
			if($tags_id) $tags_id = implode(",", $tags_id); else $tags_id = "";
			
			$active = ($_POST['approve']) ? 1:0;
			
			$db->query("UPDATE vass_artists SET tag = '$tags_id', name = '$name', bio = '$bio', active = '$active' WHERE id = '$id'");
			
			if( $MAKE_THUMBNAIL ){
				require_once INCLUDE_DIR . '/class/_class_thumb.php';
				$md5_rand =  md5(rand(1000, 9000));
				$res = @move_uploaded_file($_FILES["file"]["tmp_name"], ROOT_DIR . "/static/artists/" . $md5_rand);
				if ($res) {
					
					$thumb = new thumbnail(ROOT_DIR . "/static/artists/" . $md5_rand);
					$thumb->crop('500', '500');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/artists/" . $id . "_extralarge.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/artists/" . $md5_rand);
					$thumb->crop('250', '250');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/artists/" . $id . "_large.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/artists/" . $md5_rand);
					$thumb->crop('120', '120');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/artists/" . $id . "_medium.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/artists/" . $md5_rand);
					$thumb->crop('75', '75');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/artists/" . $id . "_small.jpg");
					
					@unlink(ROOT_DIR . "/static/artists/" . $md5_rand);
					$db->free();
				}
			}
			$message = '<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						<strong>Well done!</strong> Saved the <strong>artist information</strong> successfully!.
						</div>';
		}
		
		$row = $db->super_query("SELECT * FROM vass_artists WHERE id = '$id'");
		if(!$row['id']) die("Artist not exits");
		$checked = ($row['active']) ? "checked": "";
		
		if($row['tag']) {$tag_query = $db->query ( "SELECT name FROM vass_genres WHERE id IN(" . $row['tag'] . ")" );
			while($tag = $db->get_row($tag_query)){
				$prefilled[] = $tag['name'];
			}
			$prefilled = "prefilled:" . json_encode($prefilled) . ",";
		}
		
		$txt = "Edit artist: {$row['name']}";
		$image = "<img src=\"{$config['siteurl']}/static/artists/{$row['id']}_extralarge.jpg??" . time() . "\" class=\"img-polaroid\">";
		
	}elseif($action == "add"){
		
		$checked = "checked";
		
		if(isset($_POST['name'])){
			
			$MAKE_THUMBNAIL = false;
			$allowedExts = array("jpg", "jpeg", "gif", "png");
			$extension = end(explode(".", $_FILES["file"]["name"]));
			if($_FILES["file"]['name']){
				
				if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] > 20000) && in_array($extension, $allowedExts)) {
					if ($_FILES["file"]["error"] > 0){
						die("Error: " . $_FILES["file"]["error"]);
					}else{
						$MAKE_THUMBNAIL = true;
					}
				}else{
					msg_page("error", "<strong>Error!</strong> Invalid file, or size of file is too heavy!", "do=artists&action=add");
				}
			}
			
			$name = $db->safesql($_POST['name']);
			$bio = $db->safesql($_POST['bio']);
			$tags = $db->safesql($_POST['hidden-tags']);
			$tags = explode(",",$tags);
			for($i=0; $i<count($tags); $i++){
				$tag = $db->super_query("SELECT id FROM vass_genres WHERE name = '" . $tags[$i] . "'");
				if($tag['id']) $tags_id[] = $tag['id'];
				
			}
			
			if($tags_id) $tags_id = implode(",", $tags_id); else $tags_id = "";
			
			$active = ($_POST['approve']) ? 1:0;
			
			$db->query("INSERT INTO vass_artists SET tag = '$tags_id', name = '$name', bio = '$bio', active = '$active'");
			$artist_id = $db->insert_id();
			if( $MAKE_THUMBNAIL ){
				require_once INCLUDE_DIR . '/class/_class_thumb.php';
				$md5_rand =  md5(rand(1000, 9000));
				$res = @move_uploaded_file($_FILES["file"]["tmp_name"], ROOT_DIR . "/static/artists/" . $md5_rand);
				if ($res) {
					
					$thumb = new thumbnail(ROOT_DIR . "/static/artists/" . $md5_rand);
					$thumb->crop('500', '500');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/artists/" . $artist_id . "_extralarge.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/artists/" . $md5_rand);
					$thumb->crop('250', '250');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/artists/" . $artist_id . "_large.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/artists/" . $md5_rand);
					$thumb->crop('120', '120');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/artists/" . $artist_id . "_medium.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/artists/" . $md5_rand);
					$thumb->crop('75', '75');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/artists/" . $artist_id . "_small.jpg");
					
					@unlink(ROOT_DIR . "/static/artists/" . $md5_rand);
					$db->free();
				}
			}
			
			
			$message = '<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						<strong>Well done!</strong> Add artist successfully!.
						</div>';
			define ( 'SUBMIT', true );
		}
		
		$txt = "Add new artist";
	}
	
	echo <<<HTML
		<div class="span6">
HTML;
	if( defined( 'SUBMIT' ) ) echo $message; 
	echo <<<HTML
	<script type="text/javascript" src="js/bootstrap-typeahead.js"></script>
		<h3>{$txt}</h3>
			<form method="post" action="" enctype="multipart/form-data">
				<fieldset>
					<label>Name</label>
					<input class="input-xxlarge" type="text" name="name" value="{$row['name']}" required/>
					<label>Image</label>
					<input type="file" name="file" class="input-file">
					<label>Genres</label>
					<input type="text" name="tags" placeholder="Select one or multi genres" class="auto_genre" autocomplete="off">
					<label>Bio</label>
					<textarea class="textarea" name="bio" style="width: 530px; height: 200px">{$row['bio']}</textarea>
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
<script>
$(".auto_genre").tagsManager({
	{$prefilled}
	typeahead: true,
	typeaheadAjaxSource: 'ajax.php?t=typeahead&action=genres',
	typeaheadAjaxPolling: true,
	blinkBGColor_1: '#FFFF9C',
	blinkBGColor_2: '#CDE69C',
});
</script>
HTML;
}else{
	if( isset( $_GET['p'] ) ) $page = intval( $_GET['p'] );
	if( !$page OR $page < 0 ) $page = 1;
	$start = ($page-1) * 20;
	
	$db->query("SELECT * FROM vass_artists LIMIT $start,20");
	while($row = $db->get_row()){
	$bio = shorter($row['bio'],150);
	
	$artist_list .= "
              <tr>
                <td>
    				<div class=\"media\"> <a href=\"{$PHP_SELF}?do=artists&action=edit&id={$row['id']}\" class=\"pull-left\"> <img class=\"media-object\" data-src=\"holder.js/64x64\" alt=\"64x64\" style=\"width: 64px; height: 64px;\" src=\"{$config['siteurl']}static/artists/{$row['id']}_medium.jpg\"> </a>
			<div class=\"media-body\">
				<h4 class=\"media-heading\"><a href=\"{$PHP_SELF}?do=artists&action=edit&id={$row['id']}\">{$row['name']}</a></h4>
				{$bio}
			</div>
		</div>
                </td>
                <td><div class=\"btn-group\">
                <button class=\"btn dropdown-toggle\" data-toggle=\"dropdown\">Action <span class=\"caret\"></span></button>
                <ul class=\"dropdown-menu\">
                  <li><a href=\"{$PHP_SELF}?do=artists&action=edit&id={$row['id']}\">Edit</a></li>
                  <li><a onclick=\"var r=confirm('Are you sure by deleting this artist?');if (r==true){window.location='{$PHP_SELF}?do=artists&action=delete&id={$row['id']}'}; return false;\" href=\"{$PHP_SELF}?do=artists&action=delete&id={$row['id']}\">Delete</a></li>
                </ul>
              </div>
				</td>
              </tr>";
	}
	$total = $db->super_query("SELECT COUNT(*) AS count FROM vass_artists");
	$pages = navigation("admin/index.php?do=artists&p={page}", $total['count'], 20);
	
	echo <<<HTML
			<div class="span9">
			<p style="float:right;20px;">
				<button onClick="document.location='?do=artists&action=add'; return(false)" id="upload_song_button" class="btn btn-success" type="button"><i class="icon-plus icon-white"></i>Add new artist</button>
			</p>
			<h3>Artists Manager: Total {$total['count']} artists</h3>
	<table class="table table-bordered table-striped">
		<colgroup>
		<col class="span1">
		<col class="span7">
		</colgroup>
		<thead>
			<tr>
				<th>Title</th>
				<th>Action</th>
			</tr>
		</thead>
		<tbody>
		{$artist_list}
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