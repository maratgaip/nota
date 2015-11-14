<?php

if( ! defined( 'GLOBUS' ) || ! $user_group[$member_id['user_group']]['allow_m_radioprofile']) {

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
		
		if(isset($_POST['station'])){
			$allowedExts = array("jpg", "jpeg", "gif", "png");
			$extension = explode(".", $_FILES["file"]["tmp_name"]);
			$extension = end($extension);
			if($_FILES["file"]['tmp_name']){
				if ((($_FILES["file"]["type"] == "image/gif") || ($_FILES["file"]["type"] == "image/jpeg") || ($_FILES["file"]["type"] == "image/png") || ($_FILES["file"]["type"] == "image/pjpeg")) && ($_FILES["file"]["size"] < 200000)) {
					if ($_FILES["file"]["error"] > 0){
						die("Error: " . $_FILES["file"]["error"]);
					}else{
						$MAKE_THUMBNAIL = true;
					}
				}else{
					msg_page("error", "<strong>Error!</strong> Invalid file, or size of file is too heavy!", "do=radioprofile&action=edit&id=$id");
				}
			}
			$station = $db->safesql($_POST['station']);
			$descr = $db->safesql($_POST['descr']);
			$tags = $db->safesql($_POST['hidden-tags']);
			$tags = explode(",",$tags);
			for($i=0; $i<count($tags); $i++){
				$tag = $db->super_query("SELECT id FROM vass_songs WHERE title = '" . $tags[$i] . "'");
				if($tag['id']) $tags_id[] = $tag['id'];
				
			}
			
			if($tags_id) $tags_id = implode(",", $tags_id); else $tags_id = "";
			
			
			$db->query("UPDATE vass_radio SET station = '$station',  descr = '$descr', songs = '$tags_id' WHERE id = '$id'");
			
			if( $MAKE_THUMBNAIL ){
				require_once INCLUDE_DIR . '/class/_class_thumb.php';
				$md5_rand =  md5(rand(1000, 9000));
				$res = @move_uploaded_file($_FILES["file"]["tmp_name"], ROOT_DIR . "/static/stations/" . $md5_rand);
				if ($res) {
					
					$thumb = new thumbnail(ROOT_DIR . "/static/stations/" . $md5_rand);
					$thumb->crop('500', '500');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/stations/" . $id . "_extralarge.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/stations/" . $md5_rand);
					$thumb->crop('250', '250');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/stations/" . $id . "_large.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/stations/" . $md5_rand);
					$thumb->crop('120', '120');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/stations/" . $id . "_medium.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/stations/" . $md5_rand);
					$thumb->crop('75', '75');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/stations/" . $id . "_small.jpg");
					
					@unlink(ROOT_DIR . "/static/stations/" . $md5_rand);
					$db->free();
				}
			}
			$message = '<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						<strong>Well done!</strong> Saved the <strong>radio information</strong> successfully!.
						</div>';
                          define ( 'SUBMIT', true );
		}
		
		$row = $db->super_query("SELECT * FROM vass_radio WHERE vass_radio.id = '$id'");
		if(!$row['id']) die("station not exits");
	
                	if($row['songs']) {$tag_query = $db->query ( "SELECT title FROM vass_songs WHERE id IN(" . $row['songs'] . ")" );
			while($tag = $db->get_row($tag_query)){
				$prefilled[] = $tag['title'];
			}
			$prefilled = "prefilled:" . json_encode($prefilled) . ",";
		}
		
		$txt = "Edit Station: {$row['station']}";
		$image = "<img src=\"{$config['siteurl']}/static/stations/{$row['id']}_extralarge.jpg?" . time() . "\" class=\"img-polaroid\">";
		
	}elseif($action == "add"){
		
		
		
		if(isset($_POST['station'])){
                   
			$MAKE_THUMBNAIL = false;
			$allowedExts = array("jpg", "jpeg", "gif", "png");
			$extension = explode(".", $_FILES["file"]['tmp_name']);
			$extension = end($extension);
			if($_FILES["file"]['tmp_name']){
                           // print_r($_FILES);
                            
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
                      
			
			$station = $db->safesql($_POST['station']);
			$descr = $db->safesql($_POST['descr']);
			$tags = $db->safesql($_POST['hidden-tags']);
			$tags = explode(",",$tags);
			for($i=0; $i<count($tags); $i++){
				$tag = $db->super_query("SELECT id FROM vass_songs WHERE title = '" . $tags[$i] . "'");
				if($tag['id']) $tags_id[] = $tag['id'];
				
			}
			
			if($tags_id) $tags_id = implode(",", $tags_id); else $tags_id = "";
			
			
			$db->query("INSERT INTO vass_radio SET station = '$station', songs = '$tags_id', descr = ' $descr ' ");
			
			$station_id = $db->insert_id();
			
			if( $MAKE_THUMBNAIL ){
				require_once INCLUDE_DIR . '/class/_class_thumb.php';
				$md5_rand =  md5(rand(1000, 9000));
				$res = @move_uploaded_file($_FILES["file"]["tmp_name"], ROOT_DIR . "/static/stations/" . $md5_rand);
		
                                if ($res) {
					
					$thumb = new thumbnail(ROOT_DIR . "/static/stations/" . $md5_rand);
					$thumb->crop('500', '500');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/stations/" . $station_id . "_extralarge.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/stations/" . $md5_rand);
					$thumb->crop('250', '250');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/stations/" . $station_id . "_large.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/stations/" . $md5_rand);
					$thumb->crop('120', '120');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/stations/" . $station_id . "_medium.jpg");
					
					$thumb = new thumbnail(ROOT_DIR . "/static/stations/" . $md5_rand);
					$thumb->crop('75', '75');
					$thumb->jpeg_quality(90);
					$thumb->save(ROOT_DIR . "/static/stations/" . $station_id . "_small.jpg");
					
					@unlink(ROOT_DIR . "/static/stations/" . $md5_rand);
					$db->free();
				}
			}
			
			
			$message .= '<div class="alert alert-success">
						<button data-dismiss="alert" class="close" type="button">×</button>
						<strong>Well done!</strong> Added stations successfully!.
						</div>';
			define ( 'SUBMIT', true );
		}
		
		$txt = "Add new stations";
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
					<label>RadioStation Name</label>
					<input class="input-xxlarge" type="text" name="station" value="{$row['station']}" placeholder="Radio_Station name" autocomplete="off" required/>
					<label>Station Songs</label>
					<input type="text" name="tags" placeholder="Select one or multi songs" class="auto_songs" autocomplete="off"/>
                                        <label>Image</label>
					<input type="file" name="file" class="input-file">
					<label>Description</label>
					<textarea class="textarea" name="descr" style="width: 530px; height: 200px">{$row['descr']}</textarea>
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
	
	$db->query("DELETE FROM vass_radio WHERE id = '$id'");
	
	msg_page("success", "<strong>Well done!</strong> Deleted the station!", "do=radioprofile");
	
}else{
	if( isset( $_GET['p'] ) ) $page = intval( $_GET['p'] );
	if( !$page OR $page < 0 ) $page = 1;
	$start = ($page-1) * 20;
	
	if( isset( $_GET['q'] ) ) $q = $db->safesql( $_GET['q'] );
	
	if($q){
		$db->query("SELECT id, station, songs FROM vass_radio WHERE vass_radio.station LIKE '%$q%' LIMIT $start,20");
	}else{
		$db->query("SELECT id, station, songs FROM vass_radio LIMIT $start,20");
	}
	
	
	
	while($row = $db->get_row()){
	$descr = shorter($row['descr'],150);
		$entries .= "
              <tr>
                <td>
    				<div class=\"media\"> <a href=\"{$PHP_SELF}?do=radioprofile&action=edit&id={$row['id']}\" class=\"pull-left\"> <img class=\"media-object\" data-src=\"holder.js/64x64\" alt=\"64x64\" style=\"width: 64px; height: 64px;\" src=\"{$config['siteurl']}static/stations/{$row['id']}_medium.jpg\"> </a>
			<div class=\"media-body\">
				<h4 class=\"media-heading\"><a href=\"{$PHP_SELF}?do=radioprofile&action=edit&id={$row['id']}\">{$row['station']}</a></h4>
				{$descr}
			</div>
		</div>
                </td>
                <td><div class=\"btn-group\">
                <button class=\"btn dropdown-toggle\" data-toggle=\"dropdown\">Action <span class=\"caret\"></span></button>
                <ul class=\"dropdown-menu\">
                  
                  <li><a href=\"{$PHP_SELF}?do=radioprofile&action=edit&id={$row['id']}\">Edit</a></li>
                  <li><a onclick=\"var r=confirm('Are you sure by deleting this station?');if (r==true){window.location='{$PHP_SELF}?do=radioprofile&action=delete&id={$row['id']}'}; return false;\" href=\"{$PHP_SELF}?do=radioprofile&action=delete&id={$row['id']}\">Delete</a></li>
                </ul>
              </div>
				</td>
              </tr>";
}
	
	if($q){
		$total = $db->super_query("SELECT COUNT(*) AS count FROM vass_radio WHERE station LIKE '%$q%'");
		$pages = navigation("admin/index.php?do=radioprofile&q=" . urlencode($q) . "&p={page}", $total['count'], 20);
	}else{
		$total = $db->super_query("SELECT COUNT(*) AS count FROM vass_radio");
		$pages = navigation("admin/index.php?do=radioprofile&p={page}", $total['count'], 20);
	}
	
	echo <<<HTML
			<div class="span9">
			<p style="float:right;20px;">
				<button onClick="document.location='?do=radioprofile&action=add'; return(false)" id="upload_song_button" class="btn btn-success" type="button"><i class="icon-plus icon-white"></i>Add new Radiostation</button>
			</p>
			<h3>Radio_Stations Manager: Total {$total['count']} stations</h3>
	<form class="form-search" mothod="GET" action="{$PHP_SELF}index.php">
		<input name="do" type="hidden" value="radioprofile">
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