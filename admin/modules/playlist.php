<?php
 @session_start ();
 
if( ! defined( 'GLOBUS' ) || ! $user_group[$member_id['user_group']]['allow_m_playlist']) {

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
		
		if(isset($_POST['playlistname'])){
			$allowedExts = array("jpg", "jpeg", "gif", "png");
			$extension = explode(".", $_FILES["file"]['tmp_name']);
			$extension = end($extension);
			if($_FILES["file"]['tmp_name']){
				if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] < 20000) ) {
					if ($_FILES["file"]["error"] > 0){
						die("Error: " . $_FILES["file"]["error"]);
					}else{
						$MAKE_THUMBNAIL = true;
					}
				}else{
					msg_page("error", "<strong>Error!</strong> Invalid file, or size of file is too heavy!", "do=playlist&action=edit&id=$id");
				}
			}
                        $db->query("SELECT song_id FROM vass_song_playlist WHERE playlist_id = '$id'");
                        while($row=$db->get_row()){
                            $songid[]=$row['song_id'];
                            
                        }
                         
			$name = $db->safesql($_POST['playlistname']);
			$descr = $db->safesql($_POST['descr']);
			$tags = $db->safesql($_POST['hidden-tags']);
			$tags = explode(",",$tags);
			for($i=0; $i<count($tags); $i++){
				$tag = $db->super_query("SELECT id FROM vass_songs WHERE title = '" . $tags[$i] . "'");
				if($tag['id']) $tags_id[] = $tag['id'];
				
			}
                        for($j=0;$j<count($songid);$j++){
                         if(($key = array_search($songid[$j],$tags_id)) !== false){
            unset($tags_id[$key]);
                         }
                        }
                        
			$active = ($_POST['approve']) ? 1:0;
			
			
			$db->query("UPDATE vass_playlists SET name = '$name',  descr = '$descr', date = '$_TIME', user_access ='$active'  WHERE id = '$id'");
                     
                       foreach ($tags_id as $tVal){
                           $c = $db->query("INSERT INTO  vass_song_playlist SET song_id ='$tVal', playlist_id ='$id'");
                       }
//                        for($i=0; $i<count($tags_id); $i++){
//				 $c = $db->query("INSERT INTO  vass_song_playlist SET song_id ='$tags_id[$i]', playlist_id ='$id'");
//                                 var_dump($c);
//			}
			
			if( $MAKE_THUMBNAIL ){
				require_once INCLUDE_DIR . '/class/_class_thumb.php';
				$md5_rand =  md5(rand(1000, 9000));
				$res = @move_uploaded_file($_FILES["file"]["tmp_name"], ROOT_DIR . "/static/playlists/" . $md5_rand);
				if ($res) {
					
					$thumb = new thumbnail(ROOT_DIR . "/static/playlists/" . $md5_rand);
					$thumb->crop('500', '500');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/playlists/" . $id . "_extralarge.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/playlists/" . $md5_rand);
					$thumb->crop('250', '250');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/playlists/" . $id . "_large.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/playlists/" . $md5_rand);
					$thumb->crop('120', '120');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/playlists/" . $id . "_medium.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/playlists/" . $md5_rand);
					$thumb->crop('75', '75');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/playlists/" . $id . "_small.jpg");
					
					@unlink(ROOT_DIR . "/static/playlists/" . $md5_rand);
					$db->free();
				}
			}
			$message  .= '<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						<strong>Well done!</strong> Saved the <strong>Playlist information</strong> successfully!.
						</div>';
                        define ( 'SUBMIT', true );
		}
		
		$row = $db->super_query("SELECT * FROM vass_playlists WHERE vass_playlists.id = '$id'");
		if(!$row['id']) die("playlist not exits");
                $checked = ($row['user_access']) ? "checked": "";
	                 $db->query("SELECT song_id FROM vass_song_playlist WHERE playlist_id = '$id' ");
                         while($row1 = $db->get_row()){
                             $songid[]=$row1['song_id'];
                         }
                        
                                   $songid =  array_filter($songid, "strlen");
                                                
                             if($songid) $songs_id = implode(",",$songid ); else $songs_id = "";
                             
                          
                	if($songs_id) {$tag_query = $db->query ( "SELECT title FROM vass_songs WHERE id IN(" . $songs_id . ")" );
			while($tag = $db->get_row($tag_query)){
				$prefilled[] = $tag['title'];
			}
			$prefilled = "prefilled:" . json_encode($prefilled) . ",";
		}
		
		$txt = "Edit Playlist: {$row['name']}";
		$image = "<img src=\"{$config['siteurl']}/static/playlists/{$row['id']}_extralarge.jpg?" . time() . "\" class=\"img-polaroid\">";
		
	}elseif($action == "add"){
            
		$checked = "checked";
		
		if(isset($_POST['playlistname'])){
                   
			$MAKE_THUMBNAIL = false;
			$allowedExts = array("jpg", "jpeg", "gif", "png");
			$extension = explode(".", $_FILES["file"]['tmp_name']);
			$extension = end($extension);
			if($_FILES["file"]['tmp_name']){
                         //   print_r($_FILES);
                            
				if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] < 200000) ) {
                               
					if ($_FILES["file"]["error"] > 0){
                                            
//                                            $MAKE_THUMBNAIL = true;
						die("Error: " . $_FILES["file"]["error"]);
					}else{
                                             
						$MAKE_THUMBNAIL = true;
					}
				}else{
                                    die;
					msg_page("error", "<strong>Error!</strong> Invalid file, or size of file is too heavy!", "do=radioprofile&action=add");
				}
			}
                      
			  
			$name = $db->safesql($_POST['playlistname']);
                        
			$descr = $db->safesql($_POST['descr']);
                        
			$tags = $db->safesql($_POST['hidden-tags']);
                    
			$tags = explode(",",$tags);
			for($i=0; $i<count($tags); $i++){
				$tag = $db->super_query("SELECT id FROM vass_songs WHERE title = '" . $tags[$i] . "'");
				if($tag['id']) $tags_id[] = $tag['id'];
				
			}
			  
			//if($tags_id) $tags_id = implode(",", $tags_id); else $tags_id = "";
			$active = ($_POST['approve']) ? 1:0;
			
			$db->query("INSERT INTO vass_playlists SET name = '$name', descr = ' $descr ', user_id = '".$_SESSION['user_id']."', date = '$_TIME', user_access = '$active' ");
			
			$playlist_id = $db->insert_id();
                    
                        for($i=0; $i<count($tags_id); $i++){
				 $db->query("INSERT INTO  vass_song_playlist SET song_id ='$tags_id[$i]', playlist_id ='$playlist_id'");
				
			}
			
			if( $MAKE_THUMBNAIL ){
				require_once INCLUDE_DIR . '/class/_class_thumb.php';
				$md5_rand =  md5(rand(1000, 9000));
				$res = @move_uploaded_file($_FILES["file"]["tmp_name"], ROOT_DIR . "/static/playlists/" . $md5_rand);
		
                                if ($res) {
					
					$thumb = new thumbnail(ROOT_DIR . "/static/playlists/" . $md5_rand);
					$thumb->crop('500', '500');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/playlists/" . $playlist_id . "_extralarge.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/playlists/" . $md5_rand);
					$thumb->crop('250', '250');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/playlists/" . $playlist_id . "_large.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/playlists/" . $md5_rand);
					$thumb->crop('120', '120');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/playlists/" . $playlist_id . "_medium.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/playlists/" . $md5_rand);
					$thumb->crop('75', '75');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/playlists/" . $playlist_id . "_small.jpg");
					
					@unlink(ROOT_DIR . "/static/playlists/" . $md5_rand);
					$db->free();
				}
			}
			
			
			$message .= '<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						<strong>Well done!</strong> Added playlist successfully!.
						</div>';
			define ( 'SUBMIT', true );
		}
		
		$txt = "Add new playlist";
	}
	
	echo <<<HTML
		<div class="span6">
HTML;
	if( defined( 'SUBMIT' ) ) {
		echo $message;
	}	
	
	echo <<<HTML
		<script type="text/javascript" src="js/bootstrap-typeahead.js"></script>
		<h3>{$txt}</h3>
			<form method="post" action="" enctype="multipart/form-data">
				<fieldset>
					<label>Playlist Name</label>
					<input class="input-xxlarge" type="text" name="playlistname" value="{$row['name']}" placeholder="Playlist name" autocomplete="off" required/>
					<label>Playlist Songs</label>
					<input type="text" name="tags" placeholder="Select one or multi songs" class="auto_songs" autocomplete="off"/>
                                        <label>Image</label>
					<input type="file" name="file" class="input-file">
					<label>Description</label>
					<textarea class="textarea" name="descr" style="width: 530px; height: 200px">{$row['descr']}</textarea>
					<label class="checkbox">
					<input type="checkbox" name="approve" value="1" {$checked}> Public?
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
$(".auto_songs").tagsManager({
	{$prefilled}
	typeahead: true,
	typeaheadAjaxSource: 'ajax.php?t=typeahead&action=songs',
	typeaheadAjaxPolling: true,
	blinkBGColor_1: '#FFFF9C',
	blinkBGColor_2: '#CDE69C',
});
</script>
HTML;
}elseif($action == "delete"){
	if(isset($_GET['id'])) $id = intval($_GET['id']);
	
	$db->query("DELETE FROM vass_playlist WHERE id = '$id'");
        $db->query("DELETE FROM vass_song_playlist WHERE playlist_id = '$id'");
	
	msg_page("success", "<strong>Well done!</strong> Deleted the playlist!", "do=radioprofile");
	
}else{
	if( isset( $_GET['p'] ) ) $page = intval( $_GET['p'] );
	if( !$page OR $page < 0 ) $page = 1;
	$start = ($page-1) * 20;
	
	if( isset( $_GET['q'] ) ) $q = $db->safesql( $_GET['q'] );
	
	if($q){
		$db->query("SELECT id, name, descr  FROM vass_playlists WHERE vass_playlists.name LIKE '%$q%' and vass_playlists.user_id = '".$_SESSION['user_id']."'  LIMIT $start,20");
	}else{
		$db->query("SELECT id, name, descr FROM vass_playlists WHERE  vass_playlists.user_id = '".$_SESSION['user_id']."' LIMIT $start,20");
	}
	
	
	
	while($row = $db->get_row()){
	$descr = shorter($row['descr'],150);
		$entries .= "
              <tr>
                <td>
    				<div class=\"media\"> <a href=\"{$PHP_SELF}?do=playlist&action=edit&id={$row['id']}\" class=\"pull-left\"> <img class=\"media-object\" data-src=\"holder.js/64x64\" alt=\"64x64\" style=\"width: 64px; height: 64px;\" src=\"{$config['siteurl']}static/playlists/{$row['id']}_medium.jpg\"> </a>
			<div class=\"media-body\">
				<h4 class=\"media-heading\"><a href=\"{$PHP_SELF}?do=playlist&action=edit&id={$row['id']}\">{$row['name']}</a></h4>
				{$descr}
			</div>
		</div>
                </td>
                <td><div class=\"btn-group\">
                <button class=\"btn dropdown-toggle\" data-toggle=\"dropdown\">Action <span class=\"caret\"></span></button>
                <ul class=\"dropdown-menu\">
                  <li><a href=\"{$PHP_SELF}?do=playlist&action=edit&id={$row['id']}\">Edit</a></li>
                  <li><a onclick=\"var r=confirm('Are you sure by deleting this playlist?');if (r==true){window.location='{$PHP_SELF}?do=playlist&action=delete&id={$row['id']}'}; return false;\" href=\"{$PHP_SELF}?do=playlist&action=delete&id={$row['id']}\">Delete</a></li>
                </ul>
              </div>
				</td>
              </tr>";
}
	
	if($q){
		$total = $db->super_query("SELECT COUNT(*) AS count FROM vass_playlists WHERE name LIKE '%$q%' and vass_playlists.user_id = '".$_SESSION['user_id']."' ");
		$pages = navigation("admin/index.php?do=playlist&q=" . urlencode($q) . "&p={page}", $total['count'], 20);
	}else{
		$total = $db->super_query("SELECT COUNT(*) AS count FROM vass_playlists WHERE vass_playlists.user_id = '".$_SESSION['user_id']."'");
		$pages = navigation("admin/index.php?do=playlist&p={page}", $total['count'], 20);
	}
	
	echo <<<HTML
			<div class="span9">
			<p style="float:right;20px;">
				<button onClick="document.location='?do=playlist&action=add'; return(false)" id="upload_song_button" class="btn btn-success" type="button"><i class="icon-plus icon-white"></i>Add new Playlist</button>
			</p>
			<h3>Playlist Manager: Total {$total['count']} Playlists</h3>
	<form class="form-search" mothod="GET" action="{$PHP_SELF}index.php">
		<input name="do" type="hidden" value="playlist">
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
